<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Country;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ListingSeeder extends Seeder
{
    public function run(): void
    {
        if (Listing::query()->exists()) {
            return;
        }

        $user = User::query()->first();
        $category = Category::query()->first();
        $country = Country::query()->first();

        foreach ([
            ['title' => 'iPhone 13 - Excellent Condition', 'price' => 550, 'city' => 'Downtown'],
            ['title' => 'Honda Civic 2019 - Low Mileage', 'price' => 12800, 'city' => 'Uptown'],
            ['title' => '2BHK Apartment for Rent', 'price' => 900, 'city' => 'West End'],
            ['title' => 'Gaming Laptop RTX 3060', 'price' => 980, 'city' => 'Central'],
            ['title' => 'Office Chair Ergonomic', 'price' => 120, 'city' => 'East Side'],
        ] as $index => $item) {
            $slug = Str::slug($item['title']).'-'.($index + 1);

            Listing::query()->create([
                'user_id' => $user?->id,
                'category_id' => $category?->id,
                'sub_category_id' => null,
                'child_category_id' => null,
                'country_id' => $country?->id,
                'title' => $item['title'],
                'slug' => $slug,
                'description' => $item['title'].' available now. Contact for details.',
                'price' => $item['price'],
                'negotiable' => true,
                'phone' => $user?->phone,
                'address' => $item['city'],
                'status' => true,
                'is_published' => true,
                'published_at' => now()->subDays($index),
            ]);
        }
    }
}
