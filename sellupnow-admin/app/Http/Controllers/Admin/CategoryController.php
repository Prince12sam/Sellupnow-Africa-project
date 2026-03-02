<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a category listing.
     */

    public function index(Request $request)
    {
        $categories = $this->listocean()->table('categories')->orderBy('id')->get();
        $subCategoriesByCategory = $this->listocean()
            ->table('sub_categories')
            ->orderBy('id')
            ->get()
            ->groupBy('category_id');

        $childCategoriesBySub = $this->listocean()
            ->table('child_categories')
            ->orderBy('id')
            ->get()
            ->groupBy('sub_category_id');

        $htmlTree = $this->formatListoceanCategoryTree($categories, $subCategoriesByCategory, $childCategoriesBySub);

        return view('admin.category.index', compact('htmlTree'));
    }

    /**
     * create a new category
     */
    public function create()
    {

    }

    /**
     * store a new category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Sellupnow image-picker posts a storage-relative path (e.g. photos/foo.jpg).
            // Listocean expects a numeric media_uploads attachment ID.
            'image' => ['nullable', 'string', 'max:2048'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'string', 'max:255'],
        ]);

        $parentNodeKey = $validated['parent_id'] ?? null;
        $name = $validated['name'];
        $slug = $this->generateUniqueSlug('categories', $name);

        $status = $request->boolean('is_active');
        $now = now();

        $imageAttachmentId = $this->resolveListoceanAttachmentId($validated['image'] ?? null);

        if (! $parentNodeKey) {
            $newId = $this->listocean()->table('categories')->insertGetId([
                'name' => $name,
                'slug' => $slug,
                'icon' => null,
                'mobile_icon' => null,
                'image' => $imageAttachmentId,
                'description' => $validated['description'] ?? null,
                'status' => $status ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return to_route('admin.category.index')
                ->withSuccess(__('Category created successfully'))
                ->with('newNodeKey', 'c-' . $newId);
        }

        [$type, $id] = $this->parseNodeKey($parentNodeKey);

        if ($type === 'c') {
            $newId = $this->listocean()->table('sub_categories')->insertGetId([
                'category_id' => (int) $id,
                'name' => $name,
                'slug' => $this->generateUniqueSlug('sub_categories', $name),
                'image' => $imageAttachmentId,
                'description' => $validated['description'] ?? null,
                'status' => $status ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return to_route('admin.category.index')
                ->withSuccess(__('Category created successfully'))
                ->with('newNodeKey', 's-' . $newId);
        }

        if ($type === 's') {
            $subCategory = $this->listocean()->table('sub_categories')->where('id', (int) $id)->first();
            if (! $subCategory) {
                return back()->withError(__('Parent category not found'));
            }

            $newId = $this->listocean()->table('child_categories')->insertGetId([
                'category_id' => (int) $subCategory->category_id,
                'sub_category_id' => (int) $subCategory->id,
                'name' => $name,
                'slug' => $this->generateUniqueSlug('child_categories', $name),
                'image' => $imageAttachmentId,
                'description' => $validated['description'] ?? null,
                'status' => $status ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return to_route('admin.category.index')
                ->withSuccess(__('Category created successfully'))
                ->with('newNodeKey', 'ch-' . $newId);
        }

        return back()->withError(__('You cannot add a child under this node'));
    }

    public function show()
    {
        $nodeKey = (string) request('category_id');
        if (! $nodeKey) {
            return $this->json('category not found', [], 422);
        }

        [$type, $id] = $this->parseNodeKey($nodeKey);
        $data = $this->fetchNode($type, (int) $id);
        if (! $data) {
            return $this->json('category not found', [], 422);
        }

        return $this->json('details', [
            'category' => $data,
        ]);
    }

    /**
     * update a category
     */
    public function update(Request $request, string $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);

        $nodeKey = $category;
        [$type, $id] = $this->parseNodeKey($nodeKey);
        $name = $validated['name'];
        $status = $request->boolean('is_active');

        $table = $this->tableForType($type);
        if (! $table) {
            return back()->withError(__('Category not found'));
        }

        $exists = $this->listocean()->table($table)->where('id', (int) $id)->exists();
        if (! $exists) {
            return back()->withError(__('Category not found'));
        }

        $existing = $this->listocean()->table($table)->where('id', (int) $id)->first();

        $imageAttachmentId = $existing->image ?? null;
        $rawImage = $request->input('image');
        if (is_string($rawImage) && trim($rawImage) !== '') {
            $imageAttachmentId = $this->resolveListoceanAttachmentId($rawImage) ?? $imageAttachmentId;
        }

        $this->listocean()->table($table)->where('id', (int) $id)->update([
            'name' => $name,
            'slug' => $this->generateUniqueSlug($table, $name, (int) $id),
            'image' => $imageAttachmentId,
            'description' => $validated['description'] ?? null,
            'status' => $status ? 1 : 0,
            'updated_at' => now(),
        ]);

        return to_route('admin.category.index')->withSuccess(__('Category updated successfully'));
    }

    /**
     * category status toggle
     */
    public function statusToggle(string $category)
    {
        [$type, $id] = $this->parseNodeKey($category);
        $table = $this->tableForType($type);
        if (! $table) {
            return back()->withError(__('Category not found'));
        }

        $record = $this->listocean()->table($table)->where('id', (int) $id)->first();
        if (! $record) {
            return back()->withError(__('Category not found'));
        }

        $this->listocean()->table($table)->where('id', (int) $id)->update([
            'status' => (int) (! (bool) $record->status),
            'updated_at' => now(),
        ]);

        return back()->withSuccess(__('Status updated successfully'));
    }

    public function destroy(string $category)
    {
        [$type, $id] = $this->parseNodeKey($category);
        $table = $this->tableForType($type);
        if (! $table) {
            return back()->withError(__('Category not found'));
        }

        $exists = $this->listocean()->table($table)->where('id', (int) $id)->exists();
        if (! $exists) {
            return back()->withError(__('Category not found'));
        }

        $this->deleteNode($type, (int) $id);

        return back()->withSuccess(__('Category deleted successfully'));
    }


    public function menuUpdate(Request $request)
    {
        // Listocean category tables do not have a position/parent_id structure like Sellupnow's.
        // Keep the endpoint to avoid JS errors, but do not persist drag/drop ordering.
        return $this->json('success', [], 200);
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function formatListoceanCategoryTree($categories, $subCategoriesByCategory, $childCategoriesBySub): string
    {
        $html = '<ul class="category-level-0">';

        foreach ($categories as $category) {
            $nodeKey = 'c-' . $category->id;
            $html .= '<li id="' . e($nodeKey) . '" data-id="' . e($nodeKey) . '" data-type="c">';
            $html .= '<div class="category-item">';
            $html .= '<i class="fa-solid fa-folder-open mr-2"></i> ' . e($category->name);
            $html .= '</div>';

            $subCategories = $subCategoriesByCategory[$category->id] ?? collect();
            if ($subCategories->count()) {
                $html .= '<ul class="category-level-1">';
                foreach ($subCategories as $subCategory) {
                    $subKey = 's-' . $subCategory->id;
                    $html .= '<li id="' . e($subKey) . '" data-id="' . e($subKey) . '" data-type="s">';
                    $html .= '<div class="category-item">';
                    $html .= '<i class="fa-solid fa-folder-open mr-2"></i> ' . e($subCategory->name);
                    $html .= '</div>';

                    $children = $childCategoriesBySub[$subCategory->id] ?? collect();
                    if ($children->count()) {
                        $html .= '<ul class="category-level-2">';
                        foreach ($children as $childCategory) {
                            $childKey = 'ch-' . $childCategory->id;
                            $html .= '<li id="' . e($childKey) . '" data-id="' . e($childKey) . '" data-type="ch">';
                            $html .= '<div class="category-item">';
                            $html .= '<i class="fa-solid fa-folder-open mr-2"></i> ' . e($childCategory->name);
                            $html .= '</div>';
                            $html .= '</li>';
                        }
                        $html .= '</ul>';
                    }

                    $html .= '</li>';
                }
                $html .= '</ul>';
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }

    private function parseNodeKey(string $nodeKey): array
    {
        $nodeKey = trim($nodeKey);

        if (preg_match('/^(c|s|ch)-(\d+)$/', $nodeKey, $matches)) {
            return [$matches[1], (int) $matches[2]];
        }

        return ['invalid', 0];
    }

    private function tableForType(string $type): ?string
    {
        return match ($type) {
            'c' => 'categories',
            's' => 'sub_categories',
            'ch' => 'child_categories',
            default => null,
        };
    }

    private function fetchNode(string $type, int $id): ?array
    {
        $table = $this->tableForType($type);
        if (! $table) {
            return null;
        }

        $record = $this->listocean()->table($table)->where('id', $id)->first();
        if (! $record) {
            return null;
        }

        // Backward-compat: if the DB contains a Sellupnow storage path (string), import it once
        // into Listocean media_uploads and replace with the numeric attachment ID.
        $imageValue = $record->image ?? null;
        if (is_string($imageValue) && trim($imageValue) !== '' && ! ctype_digit(trim($imageValue))) {
            $newId = $this->resolveListoceanAttachmentId($imageValue);
            if ($newId) {
                $this->listocean()->table($table)->where('id', $id)->update([
                    'image' => $newId,
                    'updated_at' => now(),
                ]);
                $imageValue = (string) $newId;
            }
        }

        $thumbnailUrl = $this->listoceanAttachmentUrl($imageValue) ?? asset('default/default.jpg');

        $parentId = null;
        if ($type === 's') {
            $parentId = 'c-' . $record->category_id;
        } elseif ($type === 'ch') {
            $parentId = 's-' . $record->sub_category_id;
        }

        return [
            'id' => $id,
            'node_key' => $type . '-' . $id,
            'type' => $type,
            'parent_id' => $parentId,
            'name' => $record->name ?? '',
            'image' => $imageValue,
            'description' => $record->description ?? null,
            'thumbnail' => $thumbnailUrl,
            'is_active' => (bool) ($record->status ?? false),
        ];
    }

    private function resolveListoceanAttachmentId(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (ctype_digit($value)) {
            return (int) $value;
        }

        $relative = $this->normalizePublicStorageRelativePath($value);
        if (! $relative) {
            return null;
        }

        if (! Storage::disk('public')->exists($relative)) {
            return null;
        }

        $sourcePath = Storage::disk('public')->path($relative);
        return $this->storeListoceanMediaFromLocalPath($sourcePath, basename($relative));
    }

    private function normalizePublicStorageRelativePath(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        // Decode URL-encoded paths (e.g. %20 → space) so filenames with spaces work
        $value = rawurldecode($value);
        $value = str_replace('\\', '/', $value);

        // Full URL -> take part after /storage/
        $pos = stripos($value, '/storage/');
        if ($pos !== false) {
            return ltrim(substr($value, $pos + strlen('/storage/')), '/');
        }

        // Already relative
        $value = ltrim($value, '/');
        if (str_starts_with($value, 'storage/')) {
            $value = substr($value, strlen('storage/'));
        }

        return $value !== '' ? $value : null;
    }

    private function storeListoceanMediaFromLocalPath(string $sourcePath, string $originalName): ?int
    {
        if (! File::exists($sourcePath)) {
            return null;
        }

        $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        $baseName = (string) pathinfo($originalName, PATHINFO_FILENAME);

        $slug = Str::slug($baseName);
        if (! $slug) {
            $slug = 'category';
        }

        $fileName = $slug . time() . '-' . Str::lower(Str::random(4)) . ($extension ? ('.' . $extension) : '');

        $targetDir = $this->resolveListoceanMediaUploaderDirectory();
        if (! $targetDir) {
            return null;
        }

        if (! File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0775, true);
        }

        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        File::copy($sourcePath, $targetPath);

        $bytes = (int) File::size($targetPath);
        $size = $this->formatSize($bytes);

        $dimensions = null;
        try {
            $info = @getimagesize($targetPath);
            if (is_array($info) && isset($info[0], $info[1])) {
                $dimensions = $info[0] . ' x ' . $info[1] . ' pixels';
            }
        } catch (\Throwable $e) {
            $dimensions = null;
        }

        $now = now();

        return (int) $this->listocean()->table('media_uploads')->insertGetId([
            'title' => $originalName,
            'path' => $fileName,
            'alt' => null,
            'size' => $size,
            'dimensions' => $dimensions,
            'user_id' => null,
            'type' => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function resolveListoceanMediaUploaderDirectory(): ?string
    {
        $configured = trim((string) env('LISTOCEAN_MEDIA_UPLOADER_DIR', ''));
        if ($configured !== '') {
            $configured = rtrim(str_replace('\\', '/', $configured), '/');
            if (! str_ends_with($configured, 'media-uploader')) {
                $configured .= '/assets/uploads/media-uploader';
            }

            return $configured;
        }

        $candidates = [
            base_path('../main-file/listocean/assets/uploads/media-uploader'),
        ];

        foreach ($candidates as $candidate) {
            $candidate = rtrim(str_replace('\\', '/', (string) $candidate), '/');
            if ($candidate === '') {
                continue;
            }

            $parent = dirname($candidate);
            if (File::exists($candidate) || File::exists($parent)) {
                return $candidate;
            }
        }

        return null;
    }

    private function listoceanAttachmentUrl($imageId): ?string
    {
        if ($imageId === null) {
            return null;
        }

        $id = is_int($imageId) ? $imageId : trim((string) $imageId);
        if (! (is_int($id) || (is_string($id) && ctype_digit($id)))) {
            return null;
        }

        $path = $this->listocean()->table('media_uploads')->where('id', (int) $id)->value('path');
        if (! $path) {
            return null;
        }

        $customerWebUrl = rtrim(env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090'), '/');
        return $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim((string) $path, '/');
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return number_format($bytes / (1024 * 1024), 2) . ' MB';
    }

    private function deleteNode(string $type, int $id): void
    {
        if ($type === 'c') {
            $this->listocean()->table('child_categories')->where('category_id', $id)->delete();
            $this->listocean()->table('sub_categories')->where('category_id', $id)->delete();
            $this->listocean()->table('categories')->where('id', $id)->delete();
            return;
        }

        if ($type === 's') {
            $this->listocean()->table('child_categories')->where('sub_category_id', $id)->delete();
            $this->listocean()->table('sub_categories')->where('id', $id)->delete();
            return;
        }

        if ($type === 'ch') {
            $this->listocean()->table('child_categories')->where('id', $id)->delete();
        }
    }

    private function generateUniqueSlug(string $table, string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug ?: Str::random(8);
        $baseSlug = $baseSlug ?: $slug;

        $query = $this->listocean()->table($table)->where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $suffix = 2;
        while ($query->exists()) {
            $slug = $baseSlug . '-' . $suffix;
            $query = $this->listocean()->table($table)->where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            $suffix++;
        }

        return $slug;
    }
}
