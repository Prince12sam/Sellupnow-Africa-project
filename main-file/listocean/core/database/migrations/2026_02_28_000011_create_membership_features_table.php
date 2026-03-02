<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembershipFeaturesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('membership_features')) {
            return;
        }

        Schema::create('membership_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            // feature_key: e.g. video_reels | verified_badge | priority_support | auto_feature
            $table->string('feature_key', 100);
            $table->string('feature_label', 255);
            // value: true/false or a number (e.g. "3" for auto_feature count) or free text
            $table->string('value', 255)->default('true');
            $table->timestamps();

            $table->index('plan_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('membership_features');
    }
}
