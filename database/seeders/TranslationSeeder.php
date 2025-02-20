<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $language = Language::first();
        $translations = [];
        $batchSize = 1000;  // Number of translations to insert in each batch

        DB::beginTransaction();

        try {
            // Insert 100,000 translations in batches
            for ($i = 0; $i < 100000; $i++) {
                $translations[] = [
                    'language_id' => $language->id,
                    'meta_key' => 'keys_' . $i,
                    'content' => 'Content for translation ' . $i,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if (count($translations) >= $batchSize) {
                    Translation::insert($translations);
                    $translations = [];
                }
            }

            if (count($translations) > 0) {
                Translation::insert($translations);
            }

            $tags = Tag::inRandomOrder()->take(2)->pluck('id')->toArray();
            $translationsQuery = Translation::query();

            $translationsQuery->chunk(1000, function ($translations) use ($tags) {
                $tagIds = [];

                foreach ($translations as $translation) {
                    foreach ($tags as $tagId) {
                        $tagIds[] = [
                            'translation_id' => $translation->id,
                            'tag_id' => $tagId
                        ];
                    }
                }

                DB::table('translation_tag')->insert($tagIds);
            });

            DB::commit();

            echo "Translation seeding completed!";
        } catch (\Exception $e) {
            // Rollback transaction in case of error
            DB::rollBack();
            echo "Error: " . $e->getMessage();
        }

    }
}
