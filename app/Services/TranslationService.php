<?php


namespace App\Services;

use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use App\Http\Resources\TranslationResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TranslationService
{

    /**
     * Store a new translation.
     *
     * @param array $data
     * @param array|null $tagIds
     * @return Translation
     */
    public function store(array $data, ?array $tagIds = null): Translation
    {
        try {
            return DB::transaction(function () use ($data, $tagIds) {
                $translation = Translation::create($data);

                if ($tagIds) {
                    $tags = Tag::find($tagIds);
                    $translation->tags()->attach($tags);
                }

                return $translation;
            });
        } catch (\Exception $e) {
            throw new \Exception("Failed to create translation and attach tags: " . $e->getMessage());
        }
    }

    /**
     * Update an existing translation.
     *
     * @param Translation $translation
     * @param array $data
     * @param array|null $tagIds
     * @return Translation
     */
    public function update(Translation $translation, array $data, ?array $tagIds = null): Translation
    {
        return DB::transaction(function () use ($translation, $data, $tagIds) {
            $translation->update($data);

            if ($tagIds) {
                $tags = Tag::findMany($tagIds);
                $translation->tags()->sync($tags);
            }

            return $translation;
        });
    }

    /**
     * Get the translation by ID and return as a resource.
     *
     * @param int $id
     * @return TranslationResource
     */
    public function getTranslationById(int $id): TranslationResource
    {
        $translation = Translation::findOrFail($id);

        return new TranslationResource($translation);
    }

    /**
     * Delete the translation by ID.
     *
     * @param Translation $translation
     * @return bool
     */
    public function deleteTranslation(Translation $translation): bool
    {
        return $translation->delete();
    }

    /**
     * Get the translations with caching and pagination.
     *
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getTranslations(int $page): LengthAwarePaginator
    {
        return Translation::with('language')
            ->paginate(900, ['*'], 'page', $page);
    }

    /**
     * Get translations with caching.
     *
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function getTranslationsWithCache(int $page): LengthAwarePaginator
    {
        return cache()->remember("page-{$page}", 4 * 60 * 60, function () use ($page) {
            return $this->getTranslations($page);
        });
    }

    /**
     * Perform a search query for translations with pagination and caching.
     *
     * @param string|null $metaKey
     * @param string|null $tag
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function searchTranslations(?string $metaKey, ?string $tag, int $page): LengthAwarePaginator
    {
        $query = Translation::query();

        if ($metaKey) {
            $query->where('meta_key', 'like', '%' . $metaKey . '%');
        }

        if ($tag) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('name', $tag);
            });
        }

        // Cache the result for 1 hour
        return cache()->remember("search-page-{$page}-meta_key-{$metaKey}-tag-{$tag}", 60 * 60, function () use ($query) {
            return $query->paginate(800);
        });
    }
}
