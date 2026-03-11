<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingDetailsResource;
use App\Http\Resources\ListingResource;
use App\Models\AuctionBid;
use App\Models\Listing;
use App\Services\AiRecommendationService;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ListingController extends Controller
{
    private function normalizeAttributesPayload($raw): array
    {
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $raw = $decoded;
            }
        }

        if (! is_array($raw)) {
            return [];
        }

        $normalized = [];
        foreach ($raw as $item) {
            if (! is_array($item)) {
                continue;
            }

            $name = trim((string) ($item['name'] ?? $item['label'] ?? $item['title'] ?? ''));
            $value = $item['value'] ?? $item['selectedValue'] ?? $item['selectedOption'] ?? $item['option'] ?? null;

            if (is_array($value)) {
                $value = implode(', ', array_values(array_filter(array_map(fn ($v) => trim((string) $v), $value))));
            }

            $value = trim((string) $value);
            if ($name === '' || $value === '') {
                continue;
            }

            $normalized[] = [
                'name' => $name,
                'value' => $value,
                'image' => null,
            ];
        }

        return $normalized;
    }

    private function pickAttributeValue(array $attributes, array $candidates): ?string
    {
        foreach ($attributes as $attribute) {
            $name = strtolower(trim((string) ($attribute['name'] ?? '')));
            $value = trim((string) ($attribute['value'] ?? ''));

            if ($name === '' || $value === '') {
                continue;
            }

            foreach ($candidates as $candidate) {
                if ($name === strtolower($candidate)) {
                    return $value;
                }
            }
        }

        return null;
    }

    private function normalizeUploadedFiles($value): array
    {
        if ($value instanceof UploadedFile) {
            return [$value];
        }

        if (! is_array($value)) {
            return [];
        }

        $files = [];
        foreach ($value as $item) {
            foreach ($this->normalizeUploadedFiles($item) as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    private function extractGalleryFiles(Request $request): array
    {
        $allFiles = $request->allFiles();
        $raw = $allFiles['galleryImages']
            ?? $allFiles['galleryImages[]']
            ?? [];

        return $this->normalizeUploadedFiles($raw);
    }

    private function syncGalleryImagesToListoceanMedia(array $files): array
    {
        $galleryIds = [];

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $storedPath = $file->store('listings', 'public');
            $mediaId = $this->syncStoredImageToListoceanMedia($storedPath, $file);

            if ($mediaId) {
                $galleryIds[] = (int) $mediaId;
            }
        }

        return $galleryIds;
    }

    private function resolveListoceanMediaDirectory(): ?string
    {
        $configured = trim((string) env('LISTOCEAN_PUBLIC_PATH', ''));
        if ($configured !== '') {
            $normalized = rtrim(str_replace('\\', '/', $configured), '/');
            if (! str_ends_with($normalized, '/assets/uploads/media-uploader')) {
                $normalized .= '/assets/uploads/media-uploader';
            }

            if (is_dir($normalized)) {
                return $normalized;
            }
        }

        if (function_exists('listocean_core_path')) {
            $fromHelper = rtrim(str_replace('\\', '/', (string) listocean_core_path('public/assets/uploads/media-uploader')), '/');
            if ($fromHelper !== '' && is_dir($fromHelper)) {
                return $fromHelper;
            }
        }

        return null;
    }

    private function syncStoredImageToListoceanMedia(string $storedPath, UploadedFile $uploadedFile): ?int
    {
        try {
            $localAbsolutePath = Storage::disk('public')->path($storedPath);
            if (! is_file($localAbsolutePath)) {
                return null;
            }

            $targetDir = $this->resolveListoceanMediaDirectory();
            if (! $targetDir) {
                return null;
            }

            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $extension = strtolower((string) $uploadedFile->getClientOriginalExtension());
            if ($extension === '') {
                $extension = strtolower((string) pathinfo($storedPath, PATHINFO_EXTENSION));
            }

            $isHeic = in_array($extension, ['heic', 'heif'], true);
            $fileName = 'listing_' . date('Ymd_His') . '_' . Str::random(8) . ($extension !== '' ? '.' . $extension : '');
            $targetPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $fileName;

            if ($isHeic && extension_loaded('imagick')) {
                try {
                    $jpegName = 'listing_' . date('Ymd_His') . '_' . Str::random(8) . '.jpg';
                    $jpegPath = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $jpegName;

                    $imagick = new \Imagick();
                    $imagick->readImage($localAbsolutePath);
                    $imagick->setImageFormat('jpeg');
                    $imagick->setImageCompressionQuality(88);
                    $imagick->writeImage($jpegPath);
                    $imagick->clear();
                    $imagick->destroy();

                    $fileName = $jpegName;
                    $targetPath = $jpegPath;
                } catch (\Throwable $e) {
                    if (! @copy($localAbsolutePath, $targetPath)) {
                        return null;
                    }
                }
            } else {
                if (! @copy($localAbsolutePath, $targetPath)) {
                    return null;
                }
            }

            $db = DB::connection('listocean');
            $existing = $db->table('media_uploads')->where('path', $fileName)->value('id');
            if ($existing) {
                return (int) $existing;
            }

            return (int) $db->table('media_uploads')->insertGetId([
                'title' => pathinfo($fileName, PATHINFO_FILENAME),
                'alt' => '',
                'path' => $fileName,
                'type' => 'image',
                'size' => (string) max((int) filesize($targetPath), 0),
                'dimensions' => '[]',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to sync listing image into Listocean media_uploads', [
                'stored_path' => $storedPath,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function validateImageUpload($value): bool
    {
        if (! $value) {
            return true;
        }

        $mime = strtolower((string) $value->getMimeType());
        $ext = strtolower((string) $value->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'heic', 'heif', 'avif'];

        if ($mime !== '' && str_starts_with($mime, 'image/')) {
            return true;
        }

        if ($ext !== '' && in_array($ext, $allowedExtensions, true)) {
            return true;
        }

        return @getimagesize($value->getPathname()) !== false;
    }

    private function normalizeBool($value): ?bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_int($value) || is_float($value)) {
            return ((int) $value) === 1;
        }

        if (is_string($value)) {
            $v = strtolower(trim($value));
            if (in_array($v, ['1', 'true', 'yes', 'on'], true)) {
                return true;
            }
            if (in_array($v, ['0', 'false', 'no', 'off'], true)) {
                return false;
            }
        }

        return null;
    }

    private function normalizeLocationData(Request $request): array
    {
        $locationRaw = $request->input('location');
        $location = [];

        if (is_string($locationRaw) && $locationRaw !== '') {
            $decoded = json_decode($locationRaw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $location = $decoded;
            }
        } elseif (is_array($locationRaw)) {
            $location = $locationRaw;
        }

        return [
            'address' => $request->input('address')
                ?? ($location['fullAddress'] ?? null),
            'lat' => $request->input('lat')
                ?? $request->input('latitude')
                ?? ($location['latitude'] ?? null),
            'lon' => $request->input('lon')
                ?? $request->input('longitude')
                ?? ($location['longitude'] ?? null),
        ];
    }

    private function normalizeListingPayload(Request $request): array
    {
        $location = $this->normalizeLocationData($request);
        $negotiable = $request->input('negotiable');
        if ($negotiable === null) {
            $negotiable = $request->input('isOfferAllowed');
        }

        return [
            'category_id' => $request->input('category_id')
                ?? $request->input('categoryId')
                ?? $request->input('category')
                ?? null,
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'negotiable' => $this->normalizeBool($negotiable),
            'phone' => $request->input('phone')
                ?? $request->input('contactNumber')
                ?? null,
            'address' => $location['address'],
            'lat' => $location['lat'],
            'lon' => $location['lon'],
            'sub_category_id' => $request->input('sub_category_id')
                ?? $request->input('subCategoryId')
                ?? null,
            'child_category_id' => $request->input('child_category_id')
                ?? $request->input('childCategoryId')
                ?? null,
            'country_id' => $request->input('country_id')
                ?? $request->input('countryId')
                ?? null,
        ];
    }

    private function baseQuery(Request $request)
    {
        $search = $request->query('search');
        $categoryId = $request->query('category_id');
        $subCategoryId = $request->query('sub_category_id');
        $childCategoryId = $request->query('child_category_id');

        $userId = auth('api')->id();

        return Listing::query()
            ->with([
                'category',
                'user',
                'mediaUpload',
                'favorites' => function ($query) use ($userId) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->withCount('favorites')
            ->isActive()
            ->when($search, function ($builder) use ($search) {
                $builder->where(function ($nested) use ($search) {
                    $nested->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when($categoryId, fn ($builder) => $builder->where('category_id', $categoryId))
            ->when($subCategoryId, fn ($builder) => $builder->where('sub_category_id', $subCategoryId))
            ->when($childCategoryId, fn ($builder) => $builder->where('child_category_id', $childCategoryId));
    }

    private function paginatedResponse(Request $request, $query, string $message = 'listings')
    {
        // Accept 'start'/'limit' (Flutter) as aliases for 'page'/'per_page'
        $page    = max((int) ($request->query('page') ?? $request->query('start', 1)), 1);
        $perPage = max((int) ($request->query('per_page') ?? $request->query('limit', 10)), 1);
        $skip = ($page - 1) * $perPage;

        $total = $query->count();
        $listings = $query
            ->skip($skip)
            ->take($perPage)
            ->get();

        // Optional AI ranking for buyers (platform-wide ordering improvement)
        try {
            if (auth('api')->check()) {
                /** @var AiRecommendationService $ai */
                $ai = app(AiRecommendationService::class);
                if ($ai->isEnabled() && $listings->count() >= 2) {
                    $rows = $listings
                        ->map(fn (Listing $l) => $l->only(['id', 'title', 'sub_title', 'price', 'city', 'state', 'country']))
                        ->all();

                    $rankedIds = $ai->rankListingIdsForUser($rows, $request, 'listing_feed');
                    if (! empty($rankedIds)) {
                        $byId = $listings->keyBy('id');
                        $reordered = collect();
                        foreach ($rankedIds as $id) {
                            if ($byId->has($id)) {
                                $reordered->push($byId->get($id));
                            }
                        }
                        if ($reordered->count() === $listings->count()) {
                            $listings = $reordered;
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fail open: keep normal ordering
        }

        return $this->json($message, ListingResource::collection($listings)->toArray($request));
    }

    public function index(Request $request)
    {
        $query = $this->baseQuery($request)->latest('id');

        return $this->paginatedResponse($request, $query, 'listings');
    }

    // ── My Listings (authenticated owner view – all statuses) ─────────────────
    public function myListings(Request $request)
    {
        $userId = auth('api')->id();
        $type   = strtoupper($request->query('type', 'ALL'));

        $query = Listing::query()
            ->with([
                'category',
                'category.mediaUpload',
                'user',
                'user.mediaUpload',
                'mediaUpload',
            ])
            ->withCount('favorites')
            ->where('user_id', $userId)
            ->latest('id');

        switch ($type) {
            case 'FEATURED':
                $query->where('status', true)
                      ->where('is_published', true)
                      ->where('is_featured', true);
                break;
            case 'LIVE':
                $query->where('status', true)
                      ->where('is_published', true);
                break;
            case 'DEACTIVATED':
                $query->where('status', false);
                break;
            case 'UNDER_REVIEW':
                $query->where('status', true)
                      ->where('is_published', false);
                break;
            // 'ALL' and unknown types: no extra filter
        }

        return $this->paginatedResponse($request, $query, 'my listings');
    }

    public function categoryWise(Request $request)
    {
        $query = $this->baseQuery($request)->latest('id');

        return $this->paginatedResponse($request, $query, 'category wise listings');
    }

    public function popular(Request $request)
    {
        $query = $this->baseQuery($request)->orderByDesc('view')->orderByDesc('id');

        return $this->paginatedResponse($request, $query, 'popular listings');
    }

    public function mostLiked(Request $request)
    {
        $query = $this->baseQuery($request)->orderByDesc('favorites_count')->orderByDesc('id');

        return $this->paginatedResponse($request, $query, 'most liked listings');
    }

    public function show(Request $request)
    {
        $listingId = $request->query('listing_id') ?? $request->query('product_id');
        $slug = $request->query('slug');

        if (! $listingId && ! $slug) {
            return $this->json('The listing id or slug field is required.', [
                'errors' => [
                    'listing_id' => ['The listing id field is required when slug is not present.'],
                ],
            ], 422);
        }

        $listing = Listing::query()
            ->with([
                'category',
                'subCategory',
                'childCategory',
                'user',
                'mediaUpload',
                'favorites' => function ($query) {
                    $userId = auth('api')->id();
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                },
            ])
            ->withCount('favorites')
            ->isActive()
            ->when($listingId, fn ($builder) => $builder->where('id', $listingId))
            ->when($slug, fn ($builder) => $builder->where('slug', $slug))
            ->first();

        if (! $listing) {
            return $this->json('The selected listing id is invalid.', [
                'errors' => [
                    'listing_id' => ['The selected listing id is invalid.'],
                ],
            ], 422);
        }

        $listing->increment('view');

        return $this->json('listing details', ListingDetailsResource::make($listing)->toArray($request));
    }

    // ── Create ────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $galleryFiles = $this->extractGalleryFiles($request);

        Log::error('createAdListing request received', [
            'user_id' => auth('api')->id(),
            'content_type' => $request->header('content-type'),
            'has_image' => $request->hasFile('image'),
            'has_primary_image' => $request->hasFile('primaryImage'),
            'gallery_images_count' => count($galleryFiles),
            'all_keys' => array_keys($request->all()),
            'file_keys' => array_keys($request->allFiles()),
        ]);

        $request->merge($this->normalizeListingPayload($request));

        if (! $request->hasFile('image') && ! $request->hasFile('primaryImage')) {
            Log::error('createAdListing rejected: primary image missing', [
                'user_id' => auth('api')->id(),
                'all_keys' => array_keys($request->all()),
                'file_keys' => array_keys($request->allFiles()),
            ]);
            return $this->json('Primary image is required.', [
                'errors' => [
                    'primaryImage' => ['Primary image is required.'],
                ],
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'title'           => 'required|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'nullable|numeric|min:0',
            'negotiable'      => 'nullable|boolean',
            'phone'           => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:500',
            'lat'             => 'nullable|numeric',
            'lon'             => 'nullable|numeric',
            'category_id'     => 'nullable|integer|exists:categories,id',
            'sub_category_id' => 'nullable|integer|exists:categories,id',
            'child_category_id' => 'nullable|integer|exists:categories,id',
            'country_id'      => 'nullable|integer|exists:countries,id',
            'image'           => [
                'nullable',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    if (! $this->validateImageUpload($value)) {
                        $fail("The {$attribute} field must be an image.");
                    }
                },
            ],
            'primaryImage'    => [
                'nullable',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    if (! $this->validateImageUpload($value)) {
                        $fail("The {$attribute} field must be an image.");
                    }
                },
            ],
        ]);

        $validator->after(function ($validator) use ($galleryFiles) {
            foreach ($galleryFiles as $index => $file) {
                $attribute = "galleryImages.$index";
                if (! $file instanceof UploadedFile) {
                    $validator->errors()->add($attribute, 'Invalid gallery image file.');
                    continue;
                }

                if ($file->getSize() > 10240 * 1024) {
                    $validator->errors()->add($attribute, 'The gallery image may not be greater than 10240 kilobytes.');
                }

                if (! $this->validateImageUpload($file)) {
                    $validator->errors()->add($attribute, 'The gallery image field must be an image.');
                }
            }
        });

        if ($validator->fails()) {
            $primaryImage = $request->file('primaryImage');
            $image = $request->file('image');

            Log::error('createAdListing validation failed', [
                'user_id' => auth('api')->id(),
                'errors' => $validator->errors()->toArray(),
                'all_keys' => array_keys($request->all()),
                'file_keys' => array_keys($request->allFiles()),
                'primary_image_meta' => $primaryImage ? [
                    'name' => $primaryImage->getClientOriginalName(),
                    'ext' => $primaryImage->getClientOriginalExtension(),
                    'mime' => $primaryImage->getMimeType(),
                    'size' => $primaryImage->getSize(),
                ] : null,
                'image_meta' => $image ? [
                    'name' => $image->getClientOriginalName(),
                    'ext' => $image->getClientOriginalExtension(),
                    'mime' => $image->getMimeType(),
                    'size' => $image->getSize(),
                ] : null,
            ]);

            $firstError = $validator->errors()->first() ?: 'Validation failed.';

            return $this->json($firstError, [
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $data = $validator->validated();
        $normalizedAttributes = $this->normalizeAttributesPayload($request->input('attributes'));
        $condition = $request->input('condition')
            ?? $this->pickAttributeValue($normalizedAttributes, ['condition', 'usage', 'use']);
        $authenticity = $request->input('authenticity')
            ?? $this->pickAttributeValue($normalizedAttributes, ['authenticity', 'original', 'is original']);

        $userId = auth('api')->id();
        $slug   = Str::slug($data['title']) . '-' . Str::random(6);

        $imagePath = null;
        $listoceanImageId = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('listings', 'public');
            $listoceanImageId = $this->syncStoredImageToListoceanMedia($imagePath, $request->file('image'));
        } elseif ($request->hasFile('primaryImage')) {
            $imagePath = $request->file('primaryImage')->store('listings', 'public');
            $listoceanImageId = $this->syncStoredImageToListoceanMedia($imagePath, $request->file('primaryImage'));
        }

        $galleryImageIds = $this->syncGalleryImagesToListoceanMedia($galleryFiles);

        $listing = Listing::create([
            'user_id'          => $userId,
            'title'            => $data['title'],
            'slug'             => $slug,
            'description'      => $data['description'] ?? null,
            'price'            => $data['price'] ?? 0,
            'negotiable'       => $data['negotiable'] ?? false,
            'phone'            => $data['phone'] ?? null,
            'address'          => $data['address'] ?? null,
            'lat'              => $data['lat'] ?? null,
            'lon'              => $data['lon'] ?? null,
            'category_id'      => $data['category_id'] ?? null,
            'sub_category_id'  => $data['sub_category_id'] ?? null,
            'child_category_id' => $data['child_category_id'] ?? null,
            'country_id'       => $data['country_id'] ?? null,
            'image'            => $listoceanImageId ?: $imagePath,
            'gallery_images'   => ! empty($galleryImageIds) ? implode('|', $galleryImageIds) : null,
            'attributes_json'  => (! empty($normalizedAttributes) && Schema::hasColumn('listings', 'attributes_json'))
                ? json_encode($normalizedAttributes, JSON_UNESCAPED_UNICODE)
                : null,
            'condition'        => $condition,
            'authenticity'     => $authenticity,
            // Auction fields
            'sale_type'                => (int) ($request->input('saleType') ?? $request->input('sale_type') ?? 0),
            'is_auction_enabled'       => $this->normalizeBool($request->input('isAuctionEnabled') ?? $request->input('is_auction_enabled') ?? false),
            'auction_starting_price'   => $request->input('auctionStartingPrice') ?? $request->input('auction_starting_price'),
            'auction_duration_days'    => $request->input('auctionDurationDays') ?? $request->input('auction_duration_days'),
            'auction_start_date'       => ($request->input('saleType') == 2 || $request->input('sale_type') == 2) ? now() : null,
            'auction_end_date'         => ($request->input('saleType') == 2 || $request->input('sale_type') == 2)
                ? now()->addDays((int) ($request->input('auctionDurationDays') ?? $request->input('auction_duration_days') ?? 7))
                : null,
            'is_reserve_price_enabled' => $this->normalizeBool($request->input('isReservePriceEnabled') ?? $request->input('is_reserve_price_enabled') ?? false),
            'reserve_price_amount'     => $request->input('reservePriceAmount') ?? $request->input('reserve_price_amount'),
            // New listings should enter admin moderation queue.
            'status'           => true,
            'is_published'     => false,
            'published_at'     => null,
        ]);

        Log::error('createAdListing success', [
            'user_id' => auth('api')->id(),
            'listing_id' => $listing->id,
            'image' => $listing->image,
            'gallery_images' => $listing->gallery_images,
            'is_published' => $listing->is_published,
        ]);

        return $this->json('listing created', [
            'product' => ListingResource::make($listing->load('category')),
            'listing' => ListingResource::make($listing->load('category')),
        ], 201);
    }

    // ── Update ────────────────────────────────────────────────────────────────
    public function update(Request $request)
    {
        $listingId = $request->input('listing_id') ?? $request->input('product_id') ?? $request->input('adId');
        $listing = Listing::where('user_id', auth('api')->id())->findOrFail($listingId);
        $galleryFiles = $this->extractGalleryFiles($request);

        $request->merge($this->normalizeListingPayload($request));
        $normalizedAttributes = $request->has('attributes')
            ? $this->normalizeAttributesPayload($request->input('attributes'))
            : null;

        $validator = Validator::make($request->all(), [
            'title'           => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'nullable|numeric|min:0',
            'negotiable'      => 'nullable|boolean',
            'phone'           => 'nullable|string|max:30',
            'address'         => 'nullable|string|max:500',
            'lat'             => 'nullable|numeric',
            'lon'             => 'nullable|numeric',
            'category_id'     => 'nullable|integer|exists:categories,id',
            'sub_category_id' => 'nullable|integer|exists:categories,id',
            'child_category_id' => 'nullable|integer|exists:categories,id',
            'country_id'      => 'nullable|integer|exists:countries,id',
            'image'           => [
                'nullable',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    if (! $this->validateImageUpload($value)) {
                        $fail("The {$attribute} field must be an image.");
                    }
                },
            ],
            'primaryImage'    => [
                'nullable',
                'file',
                'max:10240',
                function ($attribute, $value, $fail) {
                    if (! $this->validateImageUpload($value)) {
                        $fail("The {$attribute} field must be an image.");
                    }
                },
            ],
        ]);

        $validator->after(function ($validator) use ($galleryFiles) {
            foreach ($galleryFiles as $index => $file) {
                $attribute = "galleryImages.$index";
                if (! $file instanceof UploadedFile) {
                    $validator->errors()->add($attribute, 'Invalid gallery image file.');
                    continue;
                }

                if ($file->getSize() > 10240 * 1024) {
                    $validator->errors()->add($attribute, 'The gallery image may not be greater than 10240 kilobytes.');
                }

                if (! $this->validateImageUpload($file)) {
                    $validator->errors()->add($attribute, 'The gallery image field must be an image.');
                }
            }
        });

        if ($validator->fails()) {
            return $this->json('Validation failed.', [
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $data = $validator->validated();

        if ($normalizedAttributes !== null && Schema::hasColumn('listings', 'attributes_json')) {
            $data['attributes_json'] = ! empty($normalizedAttributes)
                ? json_encode($normalizedAttributes, JSON_UNESCAPED_UNICODE)
                : null;

            $pickedCondition = $request->input('condition')
                ?? $this->pickAttributeValue($normalizedAttributes, ['condition', 'usage', 'use']);
            $pickedAuthenticity = $request->input('authenticity')
                ?? $this->pickAttributeValue($normalizedAttributes, ['authenticity', 'original', 'is original']);

            if ($pickedCondition !== null) {
                $data['condition'] = $pickedCondition;
            }
            if ($pickedAuthenticity !== null) {
                $data['authenticity'] = $pickedAuthenticity;
            }
        }

        if ($request->hasFile('image')) {
            if ($listing->image && ! is_numeric((string) $listing->image)) {
                Storage::disk('public')->delete($listing->image);
            }
            $storedPath = $request->file('image')->store('listings', 'public');
            $data['image'] = $this->syncStoredImageToListoceanMedia($storedPath, $request->file('image')) ?: $storedPath;
        } elseif ($request->hasFile('primaryImage')) {
            if ($listing->image && ! is_numeric((string) $listing->image)) {
                Storage::disk('public')->delete($listing->image);
            }
            $storedPath = $request->file('primaryImage')->store('listings', 'public');
            $data['image'] = $this->syncStoredImageToListoceanMedia($storedPath, $request->file('primaryImage')) ?: $storedPath;
        }

        if (!empty($data['title']) && $data['title'] !== $listing->title) {
            $data['slug'] = Str::slug($data['title']) . '-' . Str::random(6);
        }

        if (! empty($galleryFiles)) {
            $galleryImageIds = $this->syncGalleryImagesToListoceanMedia($galleryFiles);
            if (! empty($galleryImageIds)) {
                $data['gallery_images'] = implode('|', $galleryImageIds);
            }
        }

        // Auction fields on update
        $saleType = $request->input('saleType') ?? $request->input('sale_type');
        if ($saleType !== null) {
            $data['sale_type'] = (int) $saleType;
        }
        $isAuctionEnabled = $request->input('isAuctionEnabled') ?? $request->input('is_auction_enabled');
        if ($isAuctionEnabled !== null) {
            $data['is_auction_enabled'] = $this->normalizeBool($isAuctionEnabled);
        }
        $auctionStartingPrice = $request->input('auctionStartingPrice') ?? $request->input('auction_starting_price');
        if ($auctionStartingPrice !== null) {
            $data['auction_starting_price'] = (float) $auctionStartingPrice;
        }
        $auctionDurationDays = $request->input('auctionDurationDays') ?? $request->input('auction_duration_days');
        if ($auctionDurationDays !== null) {
            $data['auction_duration_days'] = (int) $auctionDurationDays;
        }
        // Recalculate auction dates if switching to auction or updating duration
        if ((int) ($saleType ?? $listing->sale_type) === 2 && $auctionDurationDays !== null) {
            $data['auction_start_date'] = $listing->auction_start_date ?? now();
            $data['auction_end_date']   = ($listing->auction_start_date ?? now())->copy()->addDays((int) $auctionDurationDays);
        }
        $isReservePriceEnabled = $request->input('isReservePriceEnabled') ?? $request->input('is_reserve_price_enabled');
        if ($isReservePriceEnabled !== null) {
            $data['is_reserve_price_enabled'] = $this->normalizeBool($isReservePriceEnabled);
        }
        $reservePriceAmount = $request->input('reservePriceAmount') ?? $request->input('reserve_price_amount');
        if ($reservePriceAmount !== null) {
            $data['reserve_price_amount'] = (float) $reservePriceAmount;
        }

        $listing->update(array_filter($data, fn($v) => $v !== null));

        return $this->json('listing updated', [
            'product' => ListingResource::make($listing->fresh()->load('category')),
            'listing' => ListingResource::make($listing->fresh()->load('category')),
        ]);
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function destroy(Request $request)
    {
        $listingId = $request->input('listing_id') ?? $request->input('product_id');
        $listing = Listing::where('user_id', auth('api')->id())->findOrFail($listingId);
        $listing->delete();

        return $this->json('listing deleted');
    }

    // ── Related by category ───────────────────────────────────────────────────
    public function relatedByCategory(Request $request)
    {
        $listingId  = $request->query('listing_id') ?? $request->query('product_id');
        $categoryId = $request->query('category_id');

        if (!$categoryId && $listingId) {
            $categoryId = Listing::find($listingId)?->category_id;
        }

        $query = $this->baseQuery($request)
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($listingId, fn ($q) => $q->where('id', '!=', $listingId))
            ->latest('id');

        return $this->paginatedResponse($request, $query, 'related listings');
    }

    // ── Auction listings ──────────────────────────────────────────────────────
    public function auctionListings(Request $request)
    {
        $userId = auth('api')->id();

        // Active auction listings (sale_type = 2) that haven't ended yet
        $query = Listing::query()
            ->with([
                'category',
                'auctionBids',
                'favorites' => function ($q) use ($userId) {
                    if ($userId) {
                        $q->where('user_id', $userId);
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                },
            ])
            ->withCount('favorites')
            ->where('sale_type', 2)
            ->where(function ($q) {
                $q->whereNull('auction_end_date')
                  ->orWhere('auction_end_date', '>', now());
            })
            ->isActive()
            ->latest('id');

        return $this->paginatedResponse($request, $query, 'auction listings');
    }

    // ── All listings of a specific seller ─────────────────────────────────────
    public function sellerListings(Request $request)
    {
        $sellerId = $request->query('seller_id') ?? $request->query('user_id');
        $userId   = auth('api')->id();

        $query = Listing::query()
            ->with([
                'category',
                'user',
                'user.media',
                'mediaUpload',
                'favorites' => function ($q) use ($userId) {
                    if ($userId) {
                        $q->where('user_id', $userId);
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                },
            ])
            ->withCount('favorites')
            ->isActive()
            ->when($sellerId, fn ($q) => $q->where('user_id', $sellerId))
            ->latest('id');

        return $this->paginatedResponse($request, $query, 'seller listings');
    }

    // ── Basic listing info for the authenticated seller (for video upload) ────
    public function sellerProductsBasicInfo(Request $request)
    {
        $userId   = auth('api')->id();
        $listings = Listing::query()
            ->where('user_id', $userId)
            ->isActive()
            ->with('mediaUpload')
            ->orderByDesc('id')
            ->get(['id', 'title', 'slug', 'image', 'price']);

        // Map to the shape the Flutter SellerProductInfoModel expects
        $mapped = $listings->map(fn ($l) => [
            '_id'          => (string) $l->id,
            'title'        => $l->title,
            'subTitle'     => $l->slug,
            'primaryImage' => $l->thumbnail,
            'price'        => $l->price,
        ])->values();

        return response()->json([
            'status'  => true,
            'message' => 'seller products basic info',
            'data'    => $mapped,
        ]);
    }

    // ── Promote ads ───────────────────────────────────────────────────────────
    public function promoteAds(Request $request)
    {
        $data = $request->validate([
            'adIds'      => 'required|string',  // comma-separated listing IDs
            'package_id' => 'nullable|integer|exists:featured_ad_packages,id',
            'days'       => 'nullable|integer|min:1|max:365',
        ]);

        $userId = auth('api')->id();
        $adIds  = array_filter(
            array_map('intval', explode(',', $data['adIds'])),
            fn ($id) => $id > 0
        );

        if (empty($adIds)) {
            return $this->json('No valid ad IDs provided', [], 422);
        }

        // Verify all ads belong to the authenticated user
        $owned = Listing::where('user_id', $userId)
            ->whereIn('id', $adIds)
            ->pluck('id')
            ->toArray();

        $notOwned = array_values(array_diff($adIds, $owned));
        if (! empty($notOwned)) {
            return $this->json('Some ads do not belong to you', ['invalid_ids' => $notOwned], 403);
        }

        $days  = $data['days'] ?? 7;
        $until = now()->addDays($days);

        Listing::whereIn('id', $owned)->update([
            'is_featured'        => true,
            'featured_until'     => $until,
            'featured_package_id'=> $data['package_id'] ?? null,
        ]);

        return $this->json('Ads promoted successfully', [
            'promoted_ids'   => array_values($owned),
            'featured_until' => $until->toDateTimeString(),
        ]);
    }

    // ── Place bid ─────────────────────────────────────────────────────────────
    public function placeBid(Request $request)
    {
        // Accept both Flutter param names (adId/bidAmount) and standard names (listing_id/amount)
        $request->merge([
            'listing_id' => $request->input('listing_id') ?? $request->input('adId'),
            'amount'     => $request->input('amount') ?? $request->input('bidAmount'),
        ]);

        $data = $request->validate([
            'listing_id' => 'required|integer|exists:listings,id',
            'amount'     => 'required|numeric|min:0.01',
        ]);

        $userId    = auth('api')->id();
        $listingId = $data['listing_id'];
        $amount    = (float) $data['amount'];

        // Verify the listing is actually an auction
        $listing = Listing::find($listingId);
        if (! $listing || $listing->sale_type != 2) {
            return $this->json('This listing is not an auction.', [], 422);
        }

        // Check auction hasn't ended
        if ($listing->auction_end_date && now()->greaterThan($listing->auction_end_date)) {
            return $this->json('This auction has ended.', [], 422);
        }

        // Prevent bidding on own listing
        if ($listing->user_id === $userId) {
            return $this->json('You cannot bid on your own listing.', [], 422);
        }

        // Check that the bid is higher than the current highest bid
        $highestBid = AuctionBid::where('listing_id', $listingId)
            ->where('status', 'active')
            ->max('amount');

        if ($highestBid && $amount <= $highestBid) {
            return $this->json(
                'Your bid must be higher than the current highest bid of ' . $highestBid,
                [],
                422
            );
        }

        // Mark all previous bids for this listing as outbid
        AuctionBid::where('listing_id', $listingId)
            ->where('status', 'active')
            ->update(['status' => 'outbid']);

        $bid = AuctionBid::create([
            'listing_id' => $listingId,
            'user_id'    => $userId,
            'amount'     => $amount,
            'status'     => 'active',
        ]);

        // Notify listing owner about new bid (skip if owner is the bidder)
        try {
            if ($listing->user_id !== $userId) {
                $bidder = auth('api')->user();
                PushNotificationService::sendToUsers(
                    $listing->user_id,
                    'New bid on your listing',
                    ($bidder->name ?? 'Someone') . ' placed a bid of ' . number_format($amount, 2),
                    ['type' => 'new_bid', 'listing_id' => (string) $listingId]
                );
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->json('Bid placed successfully', [
            'bid_id'     => $bid->id,
            'listing_id' => $listingId,
            'amount'     => $bid->amount,
            'status'     => $bid->status,
        ]);
    }
}
