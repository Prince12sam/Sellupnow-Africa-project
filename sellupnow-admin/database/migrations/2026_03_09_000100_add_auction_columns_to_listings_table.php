<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->tinyInteger('sale_type')->default(0)->after('escrow_enabled');             // 0 = regular, 1 = buy-now, 2 = auction
            $table->boolean('is_auction_enabled')->default(false)->after('sale_type');
            $table->double('auction_starting_price')->nullable()->after('is_auction_enabled');
            $table->integer('auction_duration_days')->nullable()->after('auction_starting_price');
            $table->timestamp('auction_start_date')->nullable()->after('auction_duration_days');
            $table->timestamp('auction_end_date')->nullable()->after('auction_start_date');
            $table->boolean('is_reserve_price_enabled')->default(false)->after('auction_end_date');
            $table->double('reserve_price_amount')->nullable()->after('is_reserve_price_enabled');

            $table->index('sale_type');
            $table->index('auction_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex(['sale_type']);
            $table->dropIndex(['auction_end_date']);
            $table->dropColumn([
                'sale_type',
                'is_auction_enabled',
                'auction_starting_price',
                'auction_duration_days',
                'auction_start_date',
                'auction_end_date',
                'is_reserve_price_enabled',
                'reserve_price_amount',
            ]);
        });
    }
};
