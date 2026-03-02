<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipFeaturesTable extends Migration
{
    public function up()
    {
        try {
            if (! Schema::hasTable('membership_features')) {
                Schema::create('membership_features', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->string('key')->unique();
                    $table->string('label');
                    $table->text('description')->nullable();
                    $table->boolean('is_active')->default(true);
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {
            logger()->warning('CreateMembershipFeaturesTable skipped: '.$e->getMessage());
        }
    }

    public function down()
    {
        try {
            if (Schema::hasTable('membership_features')) {
                Schema::dropIfExists('membership_features');
            }
        } catch (\Throwable $e) {
            logger()->warning('Drop membership_features skipped: '.$e->getMessage());
        }
    }
}
