<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Language;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

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
        $response = $this->postJson('/api/translations', [
            'language_id' => $this->language->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
            'tags' => [$this->tags->id, $this->tags2->id],
        ], [
            'Authorization' => 'Bearer ' . $this->token
        ]);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment([
                'meta_key' => 'greeting',
                'content' => 'Hello!',
            ]);
    }

    /** @test */
    public function it_can_update_a_translation()
    {
        $translation = Translation::create([
            'language_id' => $this->language->id,
            'meta_key' => 'greeting',
            'content' => 'Hello!',
        ]);

        $updatedData = [
            'meta_key' => 'greeting_updated',
            'language_id' => $this->language->id,
            'content' => 'Hello, World!',
            'tags' => [$this->tags->id],
        ];

        $token = [
            'Authorization' => 'Bearer ' . $this->token  // Pass the token in the header
        ];

        $response = $this->putJson("/api/translations/{$translation->id}", $updatedData, $token);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment([
                'meta_key' => 'greeting_updated',
                'content' => 'Hello, World!',
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
    }

    /** @test */
    public function it_can_fetch_translations_by_key()
    {
        Translation::create([
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
    }
}

