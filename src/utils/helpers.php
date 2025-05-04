<?php

if (!function_exists('basic_fields')) {
    function basic_fields(Illuminate\Database\Schema\Blueprint $table, $tableName)
    {
        if (!\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'id')) {
            $table->id();
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'created_at') && !\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'updated_at')) {
            $table->timestamps();
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'link')) {
            $table->string('link')->nullable();
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'slug')) {
            $table->string('slug')->unique()->index();
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn($tableName, 'description')) {
            $table->text('description')->nullable();
        }
    }
}


if (!function_exists('get_file_name_without_extension')) {
    function get_file_name_without_extension($file, $name = null)
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = $name ? $name : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        if (substr_compare($fileName, $extension, -strlen($extension)) === 0) {
            $fileName = str_replace($extension, '', $fileName);
            if (substr($fileName, -1) === '.') {
                $fileName = substr($fileName, 0, -1);
            }
        }
        return $fileName;
    }
}

if (!function_exists('get_file_extension')) {
    function get_file_extension($file, $extension = null)
    {
        $extension = $extension ? $extension : ($file->getClientOriginalExtension() ? $file->getClientOriginalExtension() : "jpg");
        return strtolower($extension);
    }
}

if (!function_exists('generate_resized_versions')) {
    function generate_resized_versions($file, $mimeType)
    {
        $image = \Intervention\Image\Laravel\Facades\Image::read($file->getRealPath());
        $disk = config('filesystems.default');

        $variants = [];
        $sizes = [
            'sm' => 150,
            'md' => 300,
            'lg' => 600,
        ];

        foreach ($sizes as $label => $width) {
            $resized = $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            $filename = 'uploads/' . $mimeType . '/' . \Illuminate\Support\Str::uuid() . "_{$label}." . $file->getClientOriginalExtension();

            \Illuminate\Support\Facades\Storage::disk($disk)->put($filename, (string) $resized->encode());

            $variants[$label] = $filename;
        }

        return $variants;
    }
}
