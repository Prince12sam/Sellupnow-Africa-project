<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingDetailsResource;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Services\ListOceanAdminAdapter;
use Illuminate\Http\Request;

class ListingAdminController extends Controller
{
    public function __construct(private readonly ListOceanAdminAdapter $listOceanAdminAdapter) {}

    public function index(Request $request)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'GET', '/listings');
        }

        $page = max((int) $request->query('page', 1), 1);
        $perPage = max((int) $request->query('per_page', 20), 1);
        $skip = ($page - 1) * $perPage;

        $query = Listing::query()
            ->with(['category', 'user'])
            ->withCount(['favorites', 'reports'])
            ->when($request->filled('search'), function ($builder) use ($request) {
                $search = $request->query('search');
                $builder->where(function ($nested) use ($search) {
                    $nested->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('slug', 'like', '%'.$search.'%');
                });
            })
            ->when($request->filled('category_id'), fn ($builder) => $builder->where('category_id', $request->query('category_id')))
            ->when($request->filled('status'), fn ($builder) => $builder->where('status', (bool) $request->query('status')))
            ->when($request->filled('is_published'), fn ($builder) => $builder->where('is_published', (bool) $request->query('is_published')))
            ->latest('id');

        $total = $query->count();
        $listings = $query->skip($skip)->take($perPage)->get();

        return $this->json('admin listings', [
            'total' => $total,
            'listings' => ListingResource::collection($listings),
        ]);
    }

    public function show(int $id)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward(request(), 'GET', '/listings/'.$id);
        }

        $listing = Listing::query()
            ->with(['category', 'subCategory', 'childCategory', 'country', 'user'])
            ->withCount(['favorites', 'reports'])
            ->findOrFail($id);

        return $this->json('admin listing details', [
            'listing' => ListingDetailsResource::make($listing),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'sometimes|nullable|integer|exists:categories,id',
            'sub_category_id' => 'sometimes|nullable|integer|exists:categories,id',
            'child_category_id' => 'sometimes|nullable|integer|exists:categories,id',
            'country_id' => 'sometimes|nullable|integer|exists:countries,id',
            'phone' => 'sometimes|nullable|string|max:255',
            'address' => 'sometimes|nullable|string',
            'status' => 'sometimes|boolean',
            'is_published' => 'sometimes|boolean',
            'negotiable' => 'sometimes|boolean',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'PUT', '/listings/'.$id, $data);
        }

        $listing = Listing::query()->findOrFail($id);
        $listing->update($data);

        return $this->json('listing updated successfully', [
            'listing' => ListingDetailsResource::make($listing->fresh(['category', 'subCategory', 'childCategory', 'user'])),
        ]);
    }

    public function destroy(int $id)
    {
        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward(request(), 'DELETE', '/listings/'.$id);
        }

        $listing = Listing::query()->findOrFail($id);
        $listing->delete();

        return $this->json('listing deleted successfully');
    }

    public function updatePublishStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'is_published' => 'required|boolean',
        ]);

        if ($this->listOceanAdminAdapter->enabled()) {
            return $this->listOceanAdminAdapter->forward($request, 'PATCH', '/listings/'.$id.'/publish', $data);
        }

        $listing = Listing::query()->findOrFail($id);
        $listing->update([
            'is_published' => $data['is_published'],
            'published_at' => $data['is_published'] ? now() : $listing->published_at,
        ]);

        return $this->json('listing publish status updated successfully', [
            'listing_id' => $listing->id,
            'is_published' => (bool) $listing->is_published,
        ]);
    }
}
