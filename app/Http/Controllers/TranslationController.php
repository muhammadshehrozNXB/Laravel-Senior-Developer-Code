<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\TranslationResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreTranslationRequest;

/**
 * @OA\Info(
 *     title="APIs For Translations",
 *     version="1.0.0",
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class TranslationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/translations",
     *     summary="Store a new translation",
     *     description="Store a new translation for a given meta_key and key.",
     *     tags={"Translations"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"language_id",
     *                     "meta_key":"greetings",
     *                     "content":"[1,2,3]"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *               @OA\Property(property="data", type="object",
     *                      @OA\Property(property="language_id", type="string", example="1"),
     *                      @OA\Property(property="meta_key", type="string", example="greetings"),
     *                      @OA\Property(property="content", type="string", example="text"),
     *                      @OA\Property(property="tags", type="object",
     *                          @OA\Property(property="id", type="number", example=1),
     *                          @OA\Property(property="name", type="string", example="name"),
     *                      ),
     *                  ),
     *
     *              ),
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="message", type="object",
     *                      @OA\Property(property="language_id", type="array", collectionFormat="multi",
     *                        @OA\Items(
     *                          type="string",
     *                          example="The language id field is required.",
     *                          )
     *                      ),
     *                      @OA\Property(property="meta_key", type="array", collectionFormat="multi",
     *                        @OA\Items(
     *                          type="string",
     *                          example="The meta key field is required.",
     *                          )
     *                      ),
     *                      @OA\Property(property="content", type="array", collectionFormat="multi",
     *                        @OA\Items(
     *                          type="string",
     *                          example="The contentfield is required.",
     *                          )
     *                      ),
     *                      @OA\Property(property="tags", type="array", collectionFormat="multi",
     *                        @OA\Items(
     *                          type="string",
     *                          example="The tags field must be an array.",
     *                          )
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(property="data", type="object", example={}),
     *          )
     *      )
     * )
     */
    public function store(StoreTranslationRequest $request)
    {
        $validated = $request->validated();
        $translation = Translation::create($validated);

        if ($request->tags) {
            $tags = Tag::find($request->tags);
            $translation->tags()->attach($tags);
        }

        return response()->json(new TranslationResource($translation), 201);
    }


    /**
     * @OA\Put(
     *     path="/api/translations/id",
     *     summary="update a new translation",
     *     description="update a new translation for a given meta_key and key.",
     *     tags={"Translations"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="name",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="email",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="password",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "name":"language_id",
     *                     "meta_key":"greetings",
     *                     "content":"[1,2,3]"
     *                }
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *               @OA\Property(property="data", type="object",
     *                      @OA\Property(property="language_id", type="string", example="1"),
     *                      @OA\Property(property="meta_key", type="string", example="greetings"),
     *                      @OA\Property(property="content", type="string", example="text"),
     *                      @OA\Property(property="tags", type="object",
     *                          @OA\Property(property="id", type="number", example=1),
     *                          @OA\Property(property="name", type="string", example="name"),
     *                      ),
     *                  ),
     *
     *              ),
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="message", type="object",
     *                      @OA\Property(property="language_id", type="array", collectionFormat="multi",
     *                        @OA\Items(
     *                          type="string",
     *                          example="The language id field is required.",
     *                          )
     *                      ),
     *                      @OA\Property(property="meta_key", type="array", collectionFormat="multi",
     *                        @OA\Items(
     *                          type="string",
     *                          example="The meta key field is required.",
     *                          )
     *                      ),
     *                      @OA\Property(property="content", type="array", collectionFormat="multi",
     *                        @OA\Items(
     *                          type="string",
     *                          example="The contentfield is required.",
     *                          )
     *                      ),
     *                      @OA\Property(property="tags", type="array", collectionFormat="multi",
     *                        @OA\Items(
     *                          type="string",
     *                          example="The tags field must be an array.",
     *                          )
     *                      ),
     *                  ),
     *              ),
     *              @OA\Property(property="data", type="object", example={}),
     *          )
     *      )
     * )
     */
    public function update(StoreTranslationRequest $request, $id)
    {
        $validated = $request->validated();

        $translation = Translation::findOrFail($id);
        $translation->update($validated);

        if ($request->tags) {
            $tags = Tag::find($request->tags);
            $translation->tags()->sync($tags);
        }

        return response()->json(new TranslationResource($translation));
    }

    /**
     * @OA\Get(
     *     path="/api/translations/id",
     *     summary="Get translation detail",
     *     description="Get translation using id.",
     *     tags={"Translations"},
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *               @OA\Property(property="data", type="object",
     *                      @OA\Property(property="language_id", type="string", example="1"),
     *                      @OA\Property(property="meta_key", type="string", example="greetings"),
     *                      @OA\Property(property="content", type="string", example="text"),
     *                      @OA\Property(property="tags", type="object",
     *                          @OA\Property(property="id", type="number", example=1),
     *                          @OA\Property(property="name", type="string", example="name"),
     *                      ),
     *                  ),
     *
     *              ),
     *      ),
     * )
     */
    public function show($id)
    {
        $translation = Translation::findOrFail($id);
        return new TranslationResource($translation);
    }

    /**
     * @OA\Delete(
     *     path="/api/translations/id",
     *     summary="Delete translation",
     *     description="Delete translation",
     *     tags={"Translations"},
     *     @OA\Parameter(
     *          description="Translation id",
     *          in="path",
     *          name="page",
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Deleted Successfully"),
     *              ),
     *      ),
     * )
     */
    public function destroy($id)
    {
        $translation = Translation::findOrFail($id);
        $translation->delete();

        return response()->json(["message" => "Deleted Successfully"], 204);
    }

    /**
     * @OA\Get(
     *     path="/api/translations-export",
     *     summary="Get all translations",
     *     description="Get all translations",
     *     tags={"Translations"},
     *     @OA\Parameter(
     *          description="if page parameter is not added it will be default 1",
     *          in="path",
     *          name="page",
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          ),
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *               @OA\Property(property="data", type="object",
     *                      @OA\Property(property="language_id", type="string", example="1"),
     *                      @OA\Property(property="meta_key", type="string", example="greetings"),
     *                      @OA\Property(property="content", type="string", example="text"),
     *                      @OA\Property(property="tags", type="object",
     *                          @OA\Property(property="id", type="number", example=1),
     *                          @OA\Property(property="name", type="string", example="name"),
     *                      ),
     *                  ),
     *
     *              ),
     *      ),
     * )
     */
    public function exportTranslations(Request $request)
    {
        $page = $request->query('page', 1);

        $translations = Cache::remember("page-{$page}", 4 * 60 * 60, function () {
            return Translation::with('language')->paginate(900);
        });

        return TranslationResource::collection($translations);
    }

    /**
     * @OA\Get(
     *     path="/api/translations-search",
     *     summary="Search translation by meta_key or tag",
     *     description="Search translation by meta_key or tag",
     *     tags={"Translations"},
     *     @OA\Parameter(
     *          description="If the search query is based on a <strong>meta_key</strong>, the search will be filtered by the <strong>meta_key</strong>.",
     *          in="path",
     *          name="meta_key",
     *          example="greetings",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *      ),
     *     @OA\Parameter(
     *          description="If the search query is based on a <strong>tag</strong>, the search will be filtered by the <strong>tag</strong>.",
     *          in="path",
     *          name="tag",
     *          example="mobile",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          description="If page parameter is not added it will be default 1",
     *          in="path",
     *          name="page",
     *          example="1",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          ),
     *      ),
     *     @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *               @OA\Property(property="data", type="object",
     *                      @OA\Property(property="language_id", type="string", example="1"),
     *                      @OA\Property(property="meta_key", type="string", example="greetings"),
     *                      @OA\Property(property="content", type="string", example="text"),
     *                      @OA\Property(property="tags", type="object",
     *                          @OA\Property(property="id", type="number", example=1),
     *                          @OA\Property(property="name", type="string", example="name"),
     *                      ),
     *                  ),
     *
     *              ),
     *      ),
     * )
     */
    public function search(Request $request)
    {
        $page = $request->query('page', 1);
        $query = Translation::query();

        if ($request->has('meta_key')) {
            $query->where('meta_key', 'like', '%' . $request->meta_key . '%');
        }

        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        $translations = Cache::remember("search-page-{$page}-meta_key-{$request->meta_key}-tag-{$request->tag}", 60 * 60, function () use ($query) {
            return $query->paginate(800);
        });

        return TranslationResource::collection($translations);
    }
}
