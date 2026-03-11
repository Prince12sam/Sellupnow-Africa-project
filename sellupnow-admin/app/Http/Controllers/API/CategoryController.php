<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * GET retrieveCategoryList
     * Returns all top-level (root) categories.
     * Flutter model: AllCategoryResponseModel — reads json["data"] as list of {_id, name, image}
     */
    public function index(Request $request)
    {
        $categories = CategoryRepository::query()
            ->whereNull('parent_id')
            ->active()
            ->latest('id')
            ->get();

        $data = $categories->map(fn ($c) => [
            '_id'   => (string) $c->id,
            'name'  => $c->name,
            'image' => $this->categoryImageUrl($c->image),
        ])->values()->all();

        return $this->json('categories', $data);
    }

    /**
     * GET fetchSubcategoriesByParent?parentId={id}
     * Returns sub-categories for the given parent.
     * Flutter model: SubCategoryResponseModel — reads json["data"] as list of {_id, name, image, parent}
     */
    public function fetchSubcategoriesByParent(Request $request)
    {
        $parentId = $request->query('parentId');

        if (! $parentId) {
            return $this->json('sub_categories', []);
        }

        $subs = CategoryRepository::query()
            ->where('parent_id', $parentId)
            ->active()
            ->latest('id')
            ->get();

        $data = $subs->map(fn ($c) => [
            '_id'    => (string) $c->id,
            'name'   => $c->name,
            'image'  => $this->categoryImageUrl($c->image),
            'parent' => (string) $c->parent_id,
        ])->values()->all();

        return $this->json('sub_categories', $data);
    }

    public function getCategoryAttributes(Request $request)
    {
        $categoryId = $request->query('category_id') ?? $request->query('categoryId');
        $category = $categoryId ? CategoryRepository::find($categoryId) : null;

        if (! $category) {
            return $this->json('attributes', [
                'attributes' => [],
            ]);
        }

        $resolvedCategory = $category;
        $attributes = collect();

        // If a leaf category has no attributes, walk up to the nearest parent
        // where attributes are configured in admin/category-attribute.
        while ($resolvedCategory) {
            $attributes = $resolvedCategory->attributes()
                ->active()
                ->whereNull('parent_id')
                ->orderBy('position')
                ->get();

            if ($attributes->isNotEmpty()) {
                break;
            }

            if (! $resolvedCategory->parent_id) {
                break;
            }

            $resolvedCategory = CategoryRepository::find($resolvedCategory->parent_id);
        }

        return $this->json('attributes', [
            'attributes' => $this->formatCategoryAttributeTree($attributes),
            'resolved_category_id' => $resolvedCategory?->id,
        ]);
    }

    private function formatCategoryAttributeTree($attributes)
    {
        $array = [];
        foreach ($attributes as $attribute) {
            $array[] = [
                'id'             => $attribute->id,
                'name'           => $attribute->name,
                'sub_attributes' => $this->formatCategoryAttributeTree($attribute->subAttributes()->active()->get()),
            ];
        }

        return $array;
    }

    private function categoryImageUrl(?string $image): string
    {
        if (! $image) {
            return asset('default/default.jpg');
        }

        // Numeric ID — resolve path from listocean media_uploads table
        if (ctype_digit(trim($image))) {
            $path = DB::connection('listocean')
                ->table('media_uploads')
                ->where('id', (int) $image)
                ->value('path');

            if ($path) {
                $base = rtrim((string) env('CUSTOMER_WEB_URL', config('app.url')), '/');
                return $base . '/assets/uploads/media-uploader/' . ltrim((string) $path, '/');
            }

            return asset('default/default.jpg');
        }

        // Legacy: storage-relative file path
        if (Storage::disk('public')->exists($image)) {
            return Storage::disk('public')->url($image);
        }

        return asset('default/default.jpg');
    }
}
