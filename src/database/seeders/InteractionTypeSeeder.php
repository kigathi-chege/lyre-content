<?php

namespace Lyre\Content\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InteractionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $interactionTypes = [
            [
                'name' => 'view',
                'icon' => [
                    "name" => "eye",
                    "is_svg" => true,
                    "content" => "<svg width='34' height='34' viewBox='0 0 34 34' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M30.6048 15.339C30.9669 15.7945 31.1673 16.3864 31.1673 17C31.1673 17.6137 30.9669 18.2055 30.6048 18.6611C28.312 21.4625 23.093 26.9167 17.0007 26.9167C10.9083 26.9167 5.6894 21.4625 3.39658 18.6611C3.03444 18.2055 2.83398 17.6137 2.83398 17C2.83398 16.3864 3.03444 15.7945 3.39658 15.339C5.6894 12.5375 10.9083 7.08333 17.0007 7.08333C23.093 7.08333 28.312 12.5375 30.6048 15.339Z' stroke='#98989A' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/><path d='M17.0014 21.4072C19.4134 21.4072 21.3687 19.4339 21.3687 16.9998C21.3687 14.5657 19.4134 12.5925 17.0014 12.5925C14.5894 12.5925 12.6341 14.5657 12.6341 16.9998C12.6341 19.4339 14.5894 21.4072 17.0014 21.4072Z' stroke='#98989A' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/></svg>"
                ]
            ],
            [
                'name' => 'click',
            ],
            [
                'name' => 'like',
                'antonym' => 'dislike',
            ],
            [
                'name' => 'dislike',
                'antonym' => 'like',
            ],
            [
                'name' => 'favorite',
                'antonym' => 'unfavorite',
                'icon' => [
                    "name" => "heart",
                    "is_svg" => true,
                    "content" => "<svg width='20' height='21' viewBox='0 0 20 21' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M10.0003 5.58672C10.5426 4.95703 11.4807 4.25 12.9092 4.25C15.4073 4.25 17.0837 6.57813 17.0837 8.74609C17.0837 13.2781 11.3997 16.75 10.0003 16.75C8.60097 16.75 2.91699 13.2781 2.91699 8.74609C2.91699 6.57813 4.59338 4.25 7.09144 4.25C8.51991 4.25 9.45806 4.95703 10.0003 5.58672Z' stroke='#666666' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/></svg>"
                ]
            ],
            [
                'name' => 'unfavorite',
                'antonym' => 'favorite',
            ],
            [
                'name' => 'share',
                'icon' => [
                    "name" => "send",
                    "is_svg" => true,
                    "content" => "<svg width='20' height='21' viewBox='0 0 20 21' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M8.36554 12.1346L3.47679 9.91247C2.80399 9.60665 2.83269 8.64135 3.52248 8.37605L15.7501 3.6731C16.4241 3.41389 17.0863 4.07609 16.8271 4.75004L12.1241 16.9777C11.8588 17.6675 10.8935 17.6962 10.5877 17.0234L8.36554 12.1346ZM8.36554 12.1346L12.0195 8.4808' stroke='#666666' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/></svg>"
                ]
            ],
            [
                'name' => 'comment',
                'icon' => [
                    "name" => "message",
                    "is_svg" => true,
                    "content" => "<svg width='24' height='25' viewBox='0 0 24 25' fill='none' xmlns='http://www.w3.org/2000/svg'><path d='M8.48581 19.6888C9.54657 20.2083 10.7392 20.5 12 20.5C16.4183 20.5 20 16.9183 20 12.5C20 8.08172 16.4183 4.5 12 4.5C7.58172 4.5 4 8.08172 4 12.5C4 14.1401 4.49356 15.665 5.34026 16.9341M8.48581 19.6888L4 20.5L5.34026 16.9341M8.48581 19.6888L8.49231 19.6877M5.34026 16.9341L5.34154 16.9308' stroke='#666666' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/></svg>"
                ]
            ],
            [
                'name' => 'subscribe',
                'antonym' => 'unsubscribe',
            ],
            [
                'name' => 'unsubscribe',
                'antonym' => 'subscribe',
            ],
            [
                'name' => 'download',
            ],
        ];

        $this->command->info('Seeding interaction types');

        foreach ($interactionTypes as $interactionType) {
            $antonym = null;
            if (isset($interactionType['antonym'])) {
                $this->command->comment('Creating antonym for ' . $interactionType['name']);
                $antonym = \Lyre\Content\Models\InteractionType::firstOrCreate(
                    ['name' => $interactionType['antonym']],
                    ['name' => $interactionType['antonym']],
                )->id;
            }

            $icon = null;
            if (isset($interactionType['icon'])) {
                $this->command->comment('Creating icon for ' . $interactionType['name']);
                $icon = \Lyre\Content\Models\Icon::updateOrCreate(

                    ['name' => $interactionType['icon']['name'], 'is_svg' => $interactionType['icon']['is_svg'], 'content' => $interactionType['icon']['content']],
                    ['name' => $interactionType['icon']['name'], 'is_svg' => $interactionType['icon']['is_svg'], 'content' => $interactionType['icon']['content']],
                )->id;
            }

            $this->command->comment('Creating interaction type: ' . $interactionType['name']);
            \Lyre\Content\Models\InteractionType::updateOrCreate(
                ['name' => $interactionType['name']],
                [
                    'name' => $interactionType['name'],
                    'antonym_id' => $antonym,
                    'icon_id' => $icon,
                ]
            );
        }
    }
}
