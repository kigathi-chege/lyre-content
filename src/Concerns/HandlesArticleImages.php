<?php

namespace Lyre\Content\Concerns;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Lyre\Content\Models\Article;
use Lyre\File\Concerns\UploadsFilesFromUrl;
use Lyre\File\Models\File as FileModel;

trait HandlesArticleImages
{
    use UploadsFilesFromUrl;
    /**
     * Generate image using DALL-E
     */
    protected function generateImage(string $prompt): ?string
    {
        try {
            Log::info('🎨 Calling DALL-E API', [
                'prompt_preview' => substr($prompt, 0, 100),
            ]);

            $response = openai()->request('post', 'images/generations', [
                'model'   => 'dall-e-3',
                'prompt'  => $prompt,
                'n'       => 1,
                'size'    => '1024x1024',
                'quality' => 'standard',
            ]);

            $imageUrl = $response['data'][0]['url'] ?? null;

            Log::info('✅ DALL-E image generated', ['has_url' => ! empty($imageUrl)]);

            return $imageUrl;
        } catch (\Exception $e) {
            Log::error('❌ Failed to generate image', [
                'prompt' => substr($prompt, 0, 100),
                'error'  => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Search for image using OpenVerse
     */
    protected function searchOpenVerseImage(string $query): ?array
    {
        try {
            Log::info('🔍 Searching OpenVerse', [
                'query' => $query,
            ]);

            $response = openverse()->searchImages([
                'q' => $query,
                'page_size' => 5,
                'license_type' => 'commercial', // Prefer commercially usable images
            ]);

            if ($response->failed()) {
                Log::error('❌ OpenVerse API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $results = $response->json()['results'] ?? [];

            if (empty($results)) {
                Log::warning('⚠️ No images found on OpenVerse', ['query' => $query]);
                return null;
            }

            // Return the first result with all metadata
            $image = $results[0];

            Log::info('✅ OpenVerse image found', [
                'id' => $image['id'] ?? null,
                'title' => $image['title'] ?? null,
                'provider' => $image['provider'] ?? null,
            ]);

            return [
                'url' => $image['url'] ?? null,
                'thumbnail' => $image['thumbnail'] ?? null,
                'title' => $image['title'] ?? 'Untitled',
                'creator' => $image['creator'] ?? null,
                'creator_url' => $image['creator_url'] ?? null,
                'license' => $image['license'] ?? null,
                'license_version' => $image['license_version'] ?? null,
                'license_url' => $image['license_url'] ?? null,
                'provider' => $image['provider'] ?? null,
                'source' => $image['source'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('❌ Failed to search OpenVerse', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Search for image using Unsplash
     */
    protected function searchUnsplashImage(string $query): ?array
    {
        try {
            Log::info('🔍 Searching Unsplash', [
                'query' => $query,
            ]);

            $response = unsplash()->searchPhotos([
                'query' => $query,
                'per_page' => 5,
                'order_by' => 'relevant',
                'content_filter' => 'high', // Safe content only
            ]);

            if ($response->failed()) {
                Log::error('❌ Unsplash API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $results = $response->json()['results'] ?? [];

            if (empty($results)) {
                Log::warning('⚠️ No images found on Unsplash', ['query' => $query]);
                return null;
            }

            // Return the first result with all required metadata
            $photo = $results[0];

            Log::info('✅ Unsplash image found', [
                'id' => $photo['id'] ?? null,
                'description' => $photo['alt_description'] ?? null,
                'photographer' => $photo['user']['name'] ?? null,
            ]);

            return [
                'id' => $photo['id'] ?? null,
                'urls' => [
                    'raw' => $photo['urls']['raw'] ?? null,
                    'full' => $photo['urls']['full'] ?? null,
                    'regular' => $photo['urls']['regular'] ?? null,
                    'small' => $photo['urls']['small'] ?? null,
                    'thumb' => $photo['urls']['thumb'] ?? null,
                ],
                'description' => $photo['description'] ?? $photo['alt_description'] ?? 'Untitled',
                'alt_description' => $photo['alt_description'] ?? null,
                'width' => $photo['width'] ?? null,
                'height' => $photo['height'] ?? null,
                // Photographer information (REQUIRED for attribution per Unsplash guidelines)
                'photographer_name' => $photo['user']['name'] ?? null,
                'photographer_username' => $photo['user']['username'] ?? null,
                'photographer_url' => $photo['user']['links']['html'] ?? null,
                // Links (REQUIRED for download tracking per Unsplash guidelines)
                'photo_page' => $photo['links']['html'] ?? null,
                'download_location' => $photo['links']['download_location'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('❌ Failed to search Unsplash', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Download and save OpenVerse image with attribution
     * Uses FileRepository for proper file processing
     */
    protected function downloadOpenVerseImage(array $imageData, string $name): ?FileModel
    {
        try {
            // Prefer thumbnail for memory efficiency, fall back to full URL
            // Thumbnails are usually 600x400px which is perfect for web articles
            $imageUrl = $imageData['thumbnail'] ?? $imageData['url'] ?? null;
            $fullUrl = $imageData['url'] ?? null;

            if (!$imageUrl) {
                Log::warning('⚠️ No image URL provided', ['image_data' => $imageData]);
                return null;
            }

            Log::info('📥 Downloading OpenVerse image', [
                'name' => $name,
                'provider' => $imageData['provider'] ?? null,
                'using_thumbnail' => $imageUrl === $imageData['thumbnail'],
            ]);

            // Prepare metadata for attribution
            $metadata = [
                'source' => 'openverse',
                'title' => $imageData['title'] ?? null,
                'creator' => $imageData['creator'] ?? null,
                'creator_url' => $imageData['creator_url'] ?? null,
                'license' => $imageData['license'] ?? null,
                'license_version' => $imageData['license_version'] ?? null,
                'license_url' => $imageData['license_url'] ?? null,
                'provider' => $imageData['provider'] ?? null,
                'source_url' => $imageData['source'] ?? null,
                'original_url' => $fullUrl,
            ];

            // Upload using the reusable trait method with 3MB limit for images
            $fileModel = $this->uploadFileFromUrl(
                url: $imageUrl,
                name: $name,
                description: "OpenVerse image: {$imageData['title']}",
                metadata: $metadata,
                maxSizeBytes: 3145728 // 3MB max for images (balanced between quality and memory)
            );

            if (!$fileModel) {
                Log::warning('⚠️ Failed to download image, possibly too large', [
                    'url' => $imageUrl,
                    'name' => $name,
                ]);
                return null;
            }

            // Associate with tenant
            $config = $this->getConfig();
            $this->associateFileWithTenant($fileModel, $config['tenant_id'] ?? null);

            Log::info('✅ OpenVerse image saved with attribution', [
                'file_id' => $fileModel->id,
                'license' => $metadata['license'],
            ]);

            return $fileModel;
        } catch (\Exception $e) {
            Log::error('❌ Failed to download OpenVerse image', [
                'name' => $name,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Download and save Unsplash image with attribution
     * Uses FileRepository for proper file processing
     * CRITICAL: Tracks download per Unsplash API guidelines
     */
    protected function downloadUnsplashImage(array $imageData, string $name): ?FileModel
    {
        try {
            // Prefer 'small' or 'regular' for balance between quality and size
            // Regular: ~1080px, Small: ~400px, Thumb: ~200px
            $imageUrl = $imageData['urls']['regular'] ?? $imageData['urls']['small'] ?? $imageData['urls']['thumb'] ?? null;

            if (!$imageUrl) {
                Log::warning('⚠️ No image URL provided in Unsplash data', ['image_data' => $imageData]);
                return null;
            }

            Log::info('📥 Downloading Unsplash image', [
                'name' => $name,
                'photographer' => $imageData['photographer_name'] ?? null,
                'photo_id' => $imageData['id'] ?? null,
            ]);

            // Prepare metadata for attribution (REQUIRED per Unsplash guidelines)
            // Include UTM parameters for proper tracking
            $appName = config('app.name', 'nipate');
            $photographerUrl = $imageData['photographer_url'] . "?utm_source={$appName}&utm_medium=referral";
            $photoPage = $imageData['photo_page'] . "?utm_source={$appName}&utm_medium=referral";

            $metadata = [
                'source' => 'unsplash',
                'unsplash_id' => $imageData['id'] ?? null,
                'description' => $imageData['description'] ?? null,
                'alt_description' => $imageData['alt_description'] ?? null,

                // Photographer attribution (REQUIRED)
                'photographer_name' => $imageData['photographer_name'] ?? null,
                'photographer_username' => $imageData['photographer_username'] ?? null,
                'photographer_url' => $photographerUrl,

                // Photo page (REQUIRED)
                'photo_page' => $photoPage,

                // Download location for tracking (REQUIRED)
                'download_location' => $imageData['download_location'] ?? null,

                // Image dimensions
                'width' => $imageData['width'] ?? null,
                'height' => $imageData['height'] ?? null,
                'urls' => $imageData['urls'] ?? null,
            ];

            // Upload using the reusable trait method with 3MB limit for images
            $fileModel = $this->uploadFileFromUrl(
                url: $imageUrl,
                name: $name,
                description: "Unsplash photo by {$imageData['photographer_name']}",
                metadata: $metadata,
                maxSizeBytes: 3145728 // 3MB max for images
            );

            if (!$fileModel) {
                Log::warning('⚠️ Failed to download Unsplash image, possibly too large', [
                    'url' => $imageUrl,
                    'name' => $name,
                ]);
                return null;
            }

            // Associate with tenant
            $config = $this->getConfig();
            $this->associateFileWithTenant($fileModel, $config['tenant_id'] ?? null);

            // CRITICAL: Track download per Unsplash API guidelines
            // This is REQUIRED when a user chooses to use an Unsplash image
            if (!empty($imageData['download_location'])) {
                try {
                    unsplash()->trackDownload($imageData['download_location']);
                    Log::info('✅ Unsplash download tracked', [
                        'photo_id' => $imageData['id'],
                    ]);
                } catch (\Exception $e) {
                    Log::warning('⚠️ Failed to track Unsplash download (non-fatal)', [
                        'photo_id' => $imageData['id'],
                        'error' => $e->getMessage(),
                    ]);
                    // Non-fatal: continue even if tracking fails
                }
            }

            Log::info('✅ Unsplash image saved with attribution', [
                'file_id' => $fileModel->id,
                'photographer' => $metadata['photographer_name'],
            ]);

            return $fileModel;
        } catch (\Exception $e) {
            Log::error('❌ Failed to download Unsplash image', [
                'name' => $name,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Upload DALL-E generated image from URL
     * Uses FileRepository for proper file processing
     */
    protected function uploadDalleImage(string $imageUrl, string $name): ?FileModel
    {
        try {
            // Metadata for DALL-E generated images
            $metadata = [
                'source' => 'dalle',
                'generated_by' => 'dall-e-3',
            ];

            // Upload using the reusable trait method
            $fileModel = $this->uploadFileFromUrl(
                url: $imageUrl,
                name: $name,
                description: "DALL-E generated image",
                metadata: $metadata
            );

            if (!$fileModel) {
                return null;
            }

            // Associate with tenant
            $config = $this->getConfig();
            $this->associateFileWithTenant($fileModel, $config['tenant_id'] ?? null);

            return $fileModel;
        } catch (\Exception $e) {
            Log::error('❌ Failed to upload DALL-E image', [
                'name' => $name,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Find safe insertion point for image in HTML content
     * Ensures images are placed between HTML elements, not inside them
     */
    protected function findSafeInsertionPoint(string $content, int $desiredPosition): int
    {
        // Check if content is HTML
        $isHtml = preg_match('/<[a-z][\s\S]*>/i', $content);

        if (!$isHtml) {
            // For plain text, find the nearest paragraph or sentence break
            return $this->findTextBreakPoint($content, $desiredPosition);
        }

        // For HTML, find the nearest safe position between block elements
        // Safe positions are after closing tags like </p>, </div>, </h1>, etc.
        $blockElements = ['p', 'div', 'section', 'article', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'pre', 'ul', 'ol', 'li'];

        // Build regex to match closing tags of block elements
        $pattern = '/<\/(' . implode('|', $blockElements) . ')>/i';

        // Find all closing tag positions
        preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

        if (empty($matches[0])) {
            // No block elements found, append at the end
            return strlen($content);
        }

        $closestPosition = null;
        $closestDistance = PHP_INT_MAX;

        // Find the closest closing tag to desired position
        foreach ($matches[0] as $match) {
            $tagEnd = $match[1] + strlen($match[0]);
            $distance = abs($tagEnd - $desiredPosition);

            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closestPosition = $tagEnd;
            }
        }

        Log::debug('Found safe insertion point', [
            'desired' => $desiredPosition,
            'actual' => $closestPosition,
            'distance' => $closestDistance,
        ]);

        return $closestPosition ?? strlen($content);
    }

    /**
     * Find break point in plain text (between sentences or paragraphs)
     */
    protected function findTextBreakPoint(string $content, int $desiredPosition): int
    {
        // Look for paragraph breaks (double newlines) or sentence endings near the desired position
        $searchRadius = 200; // Search within 200 characters
        $startPos = max(0, $desiredPosition - $searchRadius);
        $endPos = min(strlen($content), $desiredPosition + $searchRadius);
        $searchText = substr($content, $startPos, $endPos - $startPos);

        // Find all paragraph breaks and sentence endings in the search area
        $breakPoints = [];

        // Paragraph breaks (double newlines)
        if (preg_match_all('/\n\n/', $searchText, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $breakPoints[] = $startPos + $match[1] + 2;
            }
        }

        // Sentence endings (. ! ?) followed by space or newline
        if (preg_match_all('/[.!?]\s+/', $searchText, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $breakPoints[] = $startPos + $match[1] + strlen($match[0]);
            }
        }

        if (empty($breakPoints)) {
            // No break points found, use end of content
            return strlen($content);
        }

        // Find closest break point to desired position
        $closestPosition = $breakPoints[0];
        $closestDistance = abs($closestPosition - $desiredPosition);

        foreach ($breakPoints as $breakPoint) {
            $distance = abs($breakPoint - $desiredPosition);
            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closestPosition = $breakPoint;
            }
        }

        return $closestPosition;
    }

    /**
     * Add images to article content
     *
     * Images are intelligently placed at safe positions that respect HTML structure
     * and sentence boundaries.
     */
    protected function addImagesToContent(array $articleData): array
    {
        $config         = $this->getConfig();
        $imageSource    = $config['image_source'] ?? 'dalle';
        $content        = $articleData['content'];
        $imagePositions = $articleData['image_positions'] ?? [];

        // Determine which field to use based on image source
        if ($imageSource === 'openverse' || $imageSource === 'unsplash') {
            $imageData = $articleData['image_queries'] ?? [];
            $featuredImageData = $articleData['featured_image_query'] ?? null;
        } else {
            $imageData = $articleData['image_prompts'] ?? [];
            $featuredImageData = $articleData['featured_image_prompt'] ?? null;
        }

        // Generate and insert inline images (if enabled in config)
        if ($this->shouldAddInlineImages()) {
            $offset = 0;
            foreach ($imageData as $index => $imageDataItem) {
                Log::info("🎨 Processing inline image " . ($index + 1) . "/" . count($imageData), [
                    'source' => $imageSource,
                    'data_preview' => substr($imageDataItem, 0, 50),
                ]);

                $fileModel = null;
                $altText = $imageDataItem;

                if ($imageSource === 'openverse') {
                    // Search OpenVerse for image
                    $openVerseImage = $this->searchOpenVerseImage($imageDataItem);

                    if ($openVerseImage) {
                        $fileModel = $this->downloadOpenVerseImage($openVerseImage, "inline-image-{$index}");
                        $altText = $openVerseImage['title'] ?? $imageDataItem;
                    }

                    // Fallback to Unsplash if OpenVerse fails
                    if (!$fileModel) {
                        Log::info('📸 OpenVerse failed, trying Unsplash as fallback', ['query' => $imageDataItem]);
                        $unsplashImage = $this->searchUnsplashImage($imageDataItem);

                        if ($unsplashImage) {
                            $fileModel = $this->downloadUnsplashImage($unsplashImage, "inline-image-{$index}");
                            $altText = $unsplashImage['description'] ?? $imageDataItem;
                        }
                    }
                } elseif ($imageSource === 'unsplash') {
                    // Search Unsplash for image
                    $unsplashImage = $this->searchUnsplashImage($imageDataItem);

                    if ($unsplashImage) {
                        $fileModel = $this->downloadUnsplashImage($unsplashImage, "inline-image-{$index}");
                        $altText = $unsplashImage['description'] ?? $imageDataItem;
                    }

                    // Fallback to OpenVerse if Unsplash fails
                    if (!$fileModel) {
                        Log::info('📸 Unsplash failed, trying OpenVerse as fallback', ['query' => $imageDataItem]);
                        $openVerseImage = $this->searchOpenVerseImage($imageDataItem);

                        if ($openVerseImage) {
                            $fileModel = $this->downloadOpenVerseImage($openVerseImage, "inline-image-{$index}");
                            $altText = $openVerseImage['title'] ?? $imageDataItem;
                        }
                    }
                } else {
                    // Generate image with DALL-E
                    $imageUrl = $this->generateImage($imageDataItem);

                    if ($imageUrl) {
                        $fileModel = $this->uploadDalleImage($imageUrl, "inline-image-{$index}");
                    }
                }

                if ($fileModel) {
                    // Create HTML for image using file link
                    $imageUrl  = $fileModel->link ?? Storage::disk($fileModel->storage)->url($fileModel->path);
                    $imageHtml = sprintf(
                        "\n" . '<p><img src="%s" alt="%s" style="max-width: 100%%; height: auto;" /></p>' . "\n",
                        $imageUrl,
                        htmlspecialchars($altText)
                    );

                    // Find safe insertion position
                    $desiredPosition = $imagePositions[$index] ?? null;
                    if ($desiredPosition !== null) {
                        $desiredPosition += $offset;
                        $safePosition = $this->findSafeInsertionPoint($content, $desiredPosition);
                        $content = substr_replace($content, $imageHtml, $safePosition, 0);
                        $offset += strlen($imageHtml);

                        Log::debug('Image inserted', [
                            'desired_position' => $desiredPosition,
                            'safe_position' => $safePosition,
                            'adjustment' => $safePosition - $desiredPosition,
                        ]);
                    } else {
                        // Append if no position specified
                        $content .= "\n" . $imageHtml;
                    }

                    Log::info('✅ Inline image added', [
                        'index'   => $index + 1,
                        'source'  => $imageSource,
                        'file_id' => $fileModel->id,
                    ]);
                }
            }
        }

        $articleData['content'] = $content;

        // Generate featured image (if enabled in config)
        if ($this->shouldAddFeaturedImage() && ! empty($featuredImageData)) {
            Log::info('🎨 Processing featured image', [
                'source' => $imageSource,
                'data_preview' => substr($featuredImageData, 0, 50),
            ]);

            $fileModel = null;

            if ($imageSource === 'openverse') {
                // Search OpenVerse for featured image
                $openVerseImage = $this->searchOpenVerseImage($featuredImageData);

                if ($openVerseImage) {
                    $fileModel = $this->downloadOpenVerseImage($openVerseImage, 'featured-image');
                }

                // Fallback to Unsplash if OpenVerse fails
                if (!$fileModel) {
                    Log::info('📸 OpenVerse failed for featured image, trying Unsplash as fallback');
                    $unsplashImage = $this->searchUnsplashImage($featuredImageData);

                    if ($unsplashImage) {
                        $fileModel = $this->downloadUnsplashImage($unsplashImage, 'featured-image');
                    }
                }
            } elseif ($imageSource === 'unsplash') {
                // Search Unsplash for featured image
                $unsplashImage = $this->searchUnsplashImage($featuredImageData);

                if ($unsplashImage) {
                    $fileModel = $this->downloadUnsplashImage($unsplashImage, 'featured-image');
                }

                // Fallback to OpenVerse if Unsplash fails
                if (!$fileModel) {
                    Log::info('📸 Unsplash failed for featured image, trying OpenVerse as fallback');
                    $openVerseImage = $this->searchOpenVerseImage($featuredImageData);

                    if ($openVerseImage) {
                        $fileModel = $this->downloadOpenVerseImage($openVerseImage, 'featured-image');
                    }
                }
            } else {
                // Generate featured image with DALL-E
                $imageUrl = $this->generateImage($featuredImageData);

                if ($imageUrl) {
                    $fileModel = $this->uploadDalleImage($imageUrl, 'featured-image');
                }
            }

            if ($fileModel) {
                $articleData['featured_image']     = $fileModel;
                $articleData['featured_image_url'] = $fileModel->link ?? Storage::disk($fileModel->storage)->url($fileModel->path);

                Log::info('✅ Featured image processed', [
                    'source'  => $imageSource,
                    'file_id' => $fileModel->id,
                ]);
            }
        }

        return $articleData;
    }

    /**
     * Attach featured image to article from URL
     * Uses UploadsFilesFromUrl trait for proper file handling
     */
    protected function attachFeaturedImageFromUrl(Article $article, string $imageUrl): void
    {
        Log::info('🖼️ Attaching featured image from URL', [
            'article_id' => $article->id,
        ]);

        try {
            $filename = 'featured-' . $article->slug . '-' . time();

            // Use the trait method for proper file handling
            $fileModel = $this->uploadFileFromUrl(
                url: $imageUrl,
                name: $filename,
                description: "Featured image for article: {$article->title}",
                metadata: ['source' => 'article_featured'],
                maxSizeBytes: 3145728 // 3MB max for images
            );

            if (!$fileModel) {
                throw new \Exception('Failed to upload image from URL');
            }

            // Associate with tenant
            $config = $this->getConfig();
            $this->associateFileWithTenant($fileModel, $config['tenant_id'] ?? null);

            // Attach to article (single file, so we detach existing first)
            $article->files()->detach();
            $article->files()->attach($fileModel->id);

            Log::info('✅ Featured image attached', [
                'article_id' => $article->id,
                'file_id'    => $fileModel->id,
            ]);
        } catch (\Exception $e) {
            // TODO: Kigathi - January 28 2026 - Fix this error:
            // [2026-01-27 19:45:56] production.ERROR: ❌ Failed to attach featured image {"article_id":54,"error":"Call to undefined method Illuminate\\Database\\Eloquent\\Relations\\HasManyThrough::detach()"} 
            Log::error('❌ Failed to attach featured image', [
                'article_id' => $article->id,
                'error'      => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if inline images should be added based on config
     */
    protected function shouldAddInlineImages(): bool
    {
        $config = $this->getConfig();
        return ($config['add_images'] ?? false) && ($config['add_inline_images'] ?? true);
    }

    /**
     * Check if featured image should be added based on config
     */
    protected function shouldAddFeaturedImage(): bool
    {
        $config = $this->getConfig();
        return ($config['add_images'] ?? false) && ($config['add_featured_image'] ?? true);
    }

    /**
     * Get configuration - must be implemented by using class
     */
    abstract protected function getConfig(): array;
}
