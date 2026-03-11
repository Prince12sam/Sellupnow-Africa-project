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
    // ─── Public Actions ───────────────────────────────────────────────────────

    /**
     * Display the full category tree (admin panel view).
     */
    public function index(Request $request)
    {
        $all = DB::table('categories')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $byId = $all->keyBy('id');
        $roots = $all->filter(fn ($c) => is_null($c->parent_id));

        $htmlTree = $this->buildHtmlTree($roots, $byId);

        return view('admin.category.index', compact('htmlTree'));
    }

    public function create()
    {
        // creation is handled via the store() POST
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'image'       => ['nullable', 'string', 'max:2048'],
            'description' => ['nullable', 'string'],
            'parent_id'   => ['nullable', 'string', 'max:255'],
        ]);

        $name       = $validated['name'];
        $status     = $request->boolean('is_active');
        $now        = now();
        $imagePath  = $this->resolveImagePath($validated['image'] ?? null);

        // Resolve parent primary-key from node key (e.g. "c-3" → 3, "s-7" → 7)
        $parentId = null;
        if (! empty($validated['parent_id'])) {
            [, $pid] = $this->parseNodeKey($validated['parent_id']);
            $parentId = $pid ?: null;
        }

        $slug = $this->generateUniqueSlug($name);
        $newId = DB::table('categories')->insertGetId([
            'name'        => $name,
            'slug'        => $slug,
            'parent_id'   => $parentId,
            'image'       => $imagePath,
            'description' => $validated['description'] ?? null,
            'status'      => $status ? 1 : 0,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $nodeType = $this->nodeTypeForId($newId);

        return to_route('admin.category.index')
            ->withSuccess(__('Category created successfully'))
            ->with('newNodeKey', $nodeType . '-' . $newId);
    }

    public function show()
    {
        $nodeKey = (string) request('category_id');
        if (! $nodeKey) {
            return $this->json('category not found', [], 422);
        }

        [$type, $id] = $this->parseNodeKey($nodeKey);
        if (! $id) {
            return $this->json('category not found', [], 422);
        }

        $record = DB::table('categories')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (! $record) {
            return $this->json('category not found', [], 422);
        }

        $imageValue  = $record->image ?? null;
        $thumbnailUrl = $this->resolveListoceanThumbnailUrl($imageValue) ?? asset('default/default.jpg');

        $parentNodeKey = null;
        if ($record->parent_id) {
            $parentType    = $this->nodeTypeForId((int) $record->parent_id);
            $parentNodeKey = $parentType . '-' . $record->parent_id;
        }

        return $this->json('details', [
            'category' => [
                'id'          => $id,
                'node_key'    => $type . '-' . $id,
                'type'        => $type,
                'parent_id'   => $parentNodeKey,
                'name'        => $record->name ?? '',
                'image'       => $imageValue,
                'description' => $record->description ?? null,
                'thumbnail'   => $thumbnailUrl,
                'is_active'   => (bool) ($record->status ?? false),
            ],
        ]);
    }

    public function update(Request $request, string $category)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'image'       => ['nullable', 'string', 'max:2048'],
            'description' => ['nullable', 'string'],
        ]);

        [$type, $id] = $this->parseNodeKey($category);
        if (! $id) {
            return back()->withError(__('Category not found'));
        }

        $record = DB::table('categories')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (! $record) {
            return back()->withError(__('Category not found'));
        }

        $imagePath = $record->image ?? null;
        $rawImage  = $request->input('image');
        if (is_string($rawImage) && trim($rawImage) !== '') {
            $resolved = $this->resolveImagePath($rawImage);
            if ($resolved !== null) {
                $imagePath = $resolved;
            }
        }

        $newSlug = $this->generateUniqueSlug($validated['name'], $id);
        DB::table('categories')->where('id', $id)->update([
            'name'        => $validated['name'],
            'slug'        => $newSlug,
            'image'       => $imagePath,
            'description' => $validated['description'] ?? null,
            'status'      => $request->boolean('is_active') ? 1 : 0,
            'updated_at'  => now(),
        ]);

        return to_route('admin.category.index')
            ->withSuccess(__('Category updated successfully'));
    }

    public function statusToggle(string $category)
    {
        [$type, $id] = $this->parseNodeKey($category);
        if (! $id) {
            return back()->withError(__('Category not found'));
        }

        $record = DB::table('categories')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (! $record) {
            return back()->withError(__('Category not found'));
        }

        DB::table('categories')->where('id', $id)->update([
            'status'     => (int) (! (bool) $record->status),
            'updated_at' => now(),
        ]);

        return back()->withSuccess(__('Status updated successfully'));
    }

    public function destroy(string $category)
    {
        [$type, $id] = $this->parseNodeKey($category);
        if (! $id) {
            return back()->withError(__('Category not found'));
        }

        $exists = DB::table('categories')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->exists();

        if (! $exists) {
            return back()->withError(__('Category not found'));
        }

        $this->cascadeDelete($id);

        return back()->withSuccess(__('Category deleted successfully'));
    }

    public function menuUpdate(Request $request)
    {
        // Drag-drop ordering not persisted; kept for JS compatibility.
        return $this->json('success', [], 200);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    /**
     * Build the nested HTML tree for the admin category view.
     */
    private function buildHtmlTree($roots, $byId): string
    {
        $html = '<ul class="category-level-0">';

        foreach ($roots as $category) {
            $nodeType = 'c';
            $nodeKey  = $nodeType . '-' . $category->id;

            $html .= '<li id="' . e($nodeKey) . '" data-id="' . e($nodeKey) . '" data-type="c">';
            $html .= '<div class="category-item"><i class="fa-solid fa-folder-open mr-2"></i> ' . e($category->name) . '</div>';

            $subs = $byId->filter(fn ($c) => $c->parent_id == $category->id);
            if ($subs->count()) {
                $html .= '<ul class="category-level-1">';
                foreach ($subs as $sub) {
                    $subKey = 's-' . $sub->id;
                    $html .= '<li id="' . e($subKey) . '" data-id="' . e($subKey) . '" data-type="s">';
                    $html .= '<div class="category-item"><i class="fa-solid fa-folder-open mr-2"></i> ' . e($sub->name) . '</div>';

                    $children = $byId->filter(fn ($c) => $c->parent_id == $sub->id);
                    if ($children->count()) {
                        $html .= '<ul class="category-level-2">';
                        foreach ($children as $child) {
                            $childKey = 'ch-' . $child->id;
                            $html .= '<li id="' . e($childKey) . '" data-id="' . e($childKey) . '" data-type="ch">';
                            $html .= '<div class="category-item"><i class="fa-solid fa-folder-open mr-2"></i> ' . e($child->name) . '</div>';
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

    /**
     * Determine the node type ('c', 's', 'ch') by walking the parent chain.
     */
    private function nodeTypeForId(int $id): string
    {
        $record = DB::table('categories')->where('id', $id)->first(['parent_id']);
        if (! $record || $record->parent_id === null) {
            return 'c';
        }

        $parent = DB::table('categories')->where('id', $record->parent_id)->first(['parent_id']);

        return ($parent && $parent->parent_id !== null) ? 'ch' : 's';
    }

    /**
     * Parse a node-key string like "c-12", "s-5", "ch-99" into [type, id].
     */
    private function parseNodeKey(string $nodeKey): array
    {
        $nodeKey = trim($nodeKey);

        if (preg_match('/^(c|s|ch)-(\d+)$/', $nodeKey, $m)) {
            return [$m[1], (int) $m[2]];
        }

        // Bare numeric ID treated as a root category for compatibility
        if (ctype_digit($nodeKey)) {
            return ['c', (int) $nodeKey];
        }

        return ['invalid', 0];
    }

    /**
     * Recursively delete a category and all its descendants.
     */
    private function cascadeDelete(int $id): void
    {
        $children = DB::table('categories')
            ->where('parent_id', $id)
            ->get(['id']);

        foreach ($children as $child) {
            $this->cascadeDelete($child->id);
        }

        DB::table('categories')->where('id', $id)->delete();
    }

    /**
     * Resolve an image value to a storable string for categories.image.
     *
     * - Numeric string  → existing media_uploads ID, returned as-is.
     * - Storage path    → file is copied into Listocean's media-uploader,
     *                     a media_uploads row is inserted, and the new ID
     *                     is returned as a string.
     *
     * @return string|null
     */
    private function resolveImagePath(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = trim($value);

        // Numeric = existing Listocean media_uploads ID — accept as-is.
        if (ctype_digit($value)) {
            $exists = DB::table('media_uploads')->where('id', (int) $value)->exists();
            return $exists ? $value : null;
        }

        // Normalize the LFM storage path (strips leading /storage/ etc.)
        $relative = $this->normalizeStorageRelativePath($value);
        if ($relative && Storage::disk('public')->exists($relative)) {
            $newId = $this->importToListoceanMedia($relative);
            return $newId ? (string) $newId : null;
        }

        // Strip leading "public/" if caller passed a local-disk-style path
        if (str_starts_with($value, 'public/')) {
            $relative = substr($value, 7);
            if (Storage::disk('public')->exists($relative)) {
                $newId = $this->importToListoceanMedia($relative);
                return $newId ? (string) $newId : null;
            }
        }

        return null;
    }

    /**
     * Strip URL prefixes to get a Storage::disk('public')-relative path.
     */
    private function normalizeStorageRelativePath(string $value): ?string
    {
        $value = trim(rawurldecode($value));
        $value = str_replace('\\', '/', $value);

        $pos = stripos($value, '/storage/');
        if ($pos !== false) {
            return ltrim(substr($value, $pos + strlen('/storage/')), '/');
        }

        $value = ltrim($value, '/');
        if (str_starts_with($value, 'storage/')) {
            $value = substr($value, strlen('storage/'));
        }

        return $value !== '' ? $value : null;
    }

    /**
     * Copy a file from the admin public disk into Listocean's media-uploader
     * directory, insert a media_uploads record, and return the new ID.
     */
    private function importToListoceanMedia(string $storagePath): ?int
    {
        try {
            $sourcePath = Storage::disk('public')->path($storagePath);
            if (! File::exists($sourcePath)) {
                return null;
            }

            $ext      = strtolower((string) pathinfo($storagePath, PATHINFO_EXTENSION));
            $baseName = (string) pathinfo($storagePath, PATHINFO_FILENAME);
            $slug     = Str::slug($baseName) ?: 'category';
            $fileName = $slug . time() . '-' . Str::lower(Str::random(4)) . ($ext ? '.' . $ext : '');

            $targetDir = $this->listoceanMediaUploaderDir();
            if (! $targetDir) {
                return null;
            }

            if (! File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0775, true);
            }

            File::copy($sourcePath, $targetDir . DIRECTORY_SEPARATOR . $fileName);

            $bytes      = (int) File::size($sourcePath);
            $size       = $bytes < 1048576
                ? number_format($bytes / 1024, 2) . ' KB'
                : number_format($bytes / 1048576, 2) . ' MB';

            $dimensions = null;
            try {
                $info = @getimagesize($sourcePath);
                if (is_array($info) && isset($info[0], $info[1])) {
                    $dimensions = $info[0] . ' x ' . $info[1] . ' pixels';
                }
            } catch (\Throwable $e) {}

            $now = now();
            return (int) DB::table('media_uploads')->insertGetId([
                'title'      => basename($storagePath),
                'path'       => $fileName,
                'alt'        => null,
                'size'       => $size,
                'dimensions' => $dimensions,
                'user_id'    => null,
                'type'       => 'admin',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Resolve a Listocean media_uploads ID (numeric string or int) to its
     * full public thumbnail URL, or return null if not found.
     */
    private function resolveListoceanThumbnailUrl(mixed $imageId): ?string
    {
        if ($imageId === null || trim((string) $imageId) === '') {
            return null;
        }

        $val = trim((string) $imageId);

        if (ctype_digit($val)) {
            $path = DB::table('media_uploads')->where('id', (int) $val)->value('path');
            if (! $path) {
                return null;
            }
            return Storage::disk('listocean_media')->url((string) $path);
        }

        // Legacy: storage-relative path still in DB → serve from admin storage
        if (Storage::disk('public')->exists($val)) {
            return Storage::disk('public')->url($val);
        }

        return null;
    }

    /**
     * Resolve the absolute path to Listocean's media-uploader directory.
     * Uses the listocean_media filesystem disk (works with config caching).
     */
    private function listoceanMediaUploaderDir(): ?string
    {
        // Prefer the configured filesystem disk — safe with config:cache
        try {
            $root = config('filesystems.disks.listocean_media.root', '');
            if ($root !== '') {
                return rtrim(str_replace('\\', '/', (string) $root), '/');
            }
        } catch (\Throwable $e) {}

        return null;
    }
    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = \Illuminate\Support\Str::slug($name);
        $slug = $base;
        $i = 1;
        while (true) {
            $q = \Illuminate\Support\Facades\DB::table('categories')->where('slug', $slug);
            if ($excludeId) { $q->where('id', '!=', $excludeId); }
            if (!$q->exists()) { break; }
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

}
