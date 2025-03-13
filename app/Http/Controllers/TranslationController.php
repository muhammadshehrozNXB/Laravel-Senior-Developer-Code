<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\TranslationResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\StoreTranslationRequest;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;

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


    protected $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

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

    /**
     * Store a newly created translation.
     *
     * @param StoreTranslationRequest $request
     * @return JsonResponse
     */
    public function store(StoreTranslationRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $tags = $request->tags;

        $translation = $this->translationService->store($validated, $tags);
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

    /**
     * Update the specified translation.
     *
     * @param StoreTranslationRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(StoreTranslationRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        $translation = Translation::findOrFail($id);

        $updatedTranslation = $this->translationService->update($translation, $validated, $request->tags);

        return response()->json(new TranslationResource($updatedTranslation));
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

    /**
     * Display the specified translation.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $translationResource = $this->translationService->getTranslationById($id);
        return response()->json($translationResource);
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
    /**
     * Remove the specified translation from storage.
     *
     * @param Translation $translation
     * @return JsonResponse
     */
    public function destroy(Translation $translation): JsonResponse
    {
        $this->translationService->deleteTranslation($translation);

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
    /**
     * Export translations with pagination and caching.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function exportTranslations(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);

        $translations = $this->translationService->getTranslationsWithCache($page);

        return response()->json(TranslationResource::collection($translations));
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
    /**
     * Search for translations with optional filters and pagination.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $page = $request->query('page', 1);
        $metaKey = $request->query('meta_key');
        $tag = $request->query('tag');

        $translations = $this->translationService->searchTranslations($metaKey, $tag, $page);

        return response()->json(TranslationResource::collection($translations));
    }
}
