<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Language;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $language;
    protected $tags;
    protected $tags2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('laravelTest')->plainTextToken;

        $this->language = Language::create(['code' => 'en', 'name' => 'English']);

        // Create some tags
        $this->tags = Tag::create(['name' => 'mobile']);
        $this->tags2 = Tag::create(['name' => 'web']);
    }

    /** @test */
    public function it_can_create_a_translation()
    {
        // Create the translation via API
        $response = $this->postJson('/api/translations', [
            'language_id' => $this->language->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
            'tags' => [$this->tags->id, $this->tags2->id],
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        // Assert that the response status is HTTP Created
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
                'meta_key' => 'greeting',
                'content' => 'Hello!',
            ]);

        // Assert the translation is actually in the database
        $this->assertDatabaseHas('translations', [
            'meta_key' => 'greeting',
            'content' => 'Hello!',
            'language_id' => $this->language->id,  // Check if the translation is associated with the correct language
        ]);

        // Assert that the tags are correctly associated with the translation
        $translation = \App\Models\Translation::where('meta_key', 'greeting')->first();

        $this->assertDatabaseHas('translation_tag', [
            'translation_id' => $translation->id,
            'tag_id' => $this->tags->id,  // Ensure the first tag is associated
        ]);

        $this->assertDatabaseHas('translation_tag', [
            'translation_id' => $translation->id,
            'tag_id' => $this->tags2->id,  // Ensure the second tag is associated
        ]);
    }


    /** @test */
    public function it_can_update_a_translation()
    {
        // Create the initial translation
        $translation = Translation::create([
            'language_id' => $this->language->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
        ]);

        // Prepare updated data
        $updatedData = [
            'meta_key' => 'greeting_updated',
            'language_id' => $this->language->id,
            'content' => 'Hello, World!',
            'tags' => [$this->tags->id],  // Attach a tag to the updated translation
        ];

        $token = [
            'Authorization' => 'Bearer ' . $this->token  // Pass the token in the header
        ];

        // Send the update request
        $response = $this->putJson("/api/translations/{$translation->id}", $updatedData, $token);

        // Assert the response status and data
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'meta_key' => 'greeting_updated',
                'content' => 'Hello, World!',
            ]);

        // Assert the translation data was updated in the database
        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'meta_key' => 'greeting_updated',  // Ensure the meta_key is updated
            'content' => 'Hello, World!',      // Ensure the content is updated
            'language_id' => $this->language->id,  // Ensure the language_id is unchanged
        ]);

        // Assert that the previous tag association is removed and new tag association is added
        $this->assertDatabaseMissing('translation_tag', [
            'translation_id' => $translation->id,
            'tag_id' => $this->tags2->id,  // Ensure previous tag is not associated
        ]);

        $this->assertDatabaseHas('translation_tag', [
            'translation_id' => $translation->id,
            'tag_id' => $this->tags->id,  // Ensure the new tag is associated
        ]);
    }

    /** @test */
    public function it_can_fetch_a_translation_by_id()
    {
        $translation = Translation::create([
            'language_id' => $this->language->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
        ]);

        $token = [
            'Authorization' => 'Bearer ' . $this->token  // Pass the token in the header
        ];

        $response = $this->getJson("/api/translations/{$translation->id}", $token);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'meta_key' => 'greeting',
                'content' => 'Hello!',
            ]);
        // Assert the translation exists in the database
        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
            'language_id' => $this->language->id,
        ]);
    }

    /** @test */
    public function it_can_fetch_translations_by_key()
    {
        $translation = Translation::create([
            'language_id' => $this->language->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
        ]);

        $token = [
            'Authorization' => 'Bearer ' . $this->token
        ];

        $response = $this->getJson("/api/translations-search?meta_key=greeting", $token);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'meta_key' => 'greeting',
                'content' => 'Hello!',
            ]);

        // Assert the translation exists in the database
        $this->assertDatabaseHas('translations', [
            'id' => $translation->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
            'language_id' => $this->language->id,
        ]);
    }

    /** @test */
    public function it_can_search_translations_by_tag()
    {
        $translation = Translation::create([
            'language_id' => $this->language->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
        ]);
        $translation->tags()->attach([$this->tags->id]);

        $token = [
            'Authorization' => 'Bearer ' . $this->token
        ];

        $response = $this->getJson('/api/translations-export?tag=mobile', $token);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'meta_key' => 'greeting',
            ]);

        $this->assertDatabaseHas('translation_tag', [
            'translation_id' => $translation->id,
            'tag_id' => $this->tags->id,
        ]);

        $this->assertDatabaseHas('tags', [
            'id' => $this->tags->id,
            'name' => 'mobile',  // Assuming 'mobile' is the tag name
        ]);
    }

    /** @test */
    public function it_can_export_translations()
    {
        $translation1 = Translation::create([
            'language_id' => $this->language->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
        ]);

        $translation2 = Translation::create([
            'language_id' => $this->language->id,
            'meta_key' => 'farewell',
            'content' => 'Goodbye!',
        ]);


        $token = [
            'Authorization' => 'Bearer ' . $this->token
        ];

        $translations = collect([$translation1, $translation2]);
        $paginator = new LengthAwarePaginator(
            $translations,
            $translations->count(),
            2,
            1,
            ['path' => url('/api/translations-export')] // The URL path for pagination
        );

        Cache::shouldReceive('remember')
            ->once()
            ->with('page-1', 4 * 60 * 60, \Closure::class)
            ->andReturn($paginator); // Return the paginator instead of a collection

        $response = $this->getJson('/api/translations-export?page=1', $token);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'meta_key' => 'greeting',
                'content' => 'Hello!',
            ])
            ->assertJsonFragment([
                'meta_key' => 'farewell',
                'content' => 'Goodbye!',
            ]);

        $this->assertDatabaseHas('translations', [
            'id' => $translation1->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
        ]);

        $this->assertDatabaseHas('translations', [
            'id' => $translation2->id,
            'meta_key' => 'farewell',
            'content' => 'Goodbye!',
        ]);
    }
}

