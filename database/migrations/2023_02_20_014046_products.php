<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->unsignedTinyInteger('status_id')->nullable();
            $table->unsignedTinyInteger('type_id')->nullable();
            $table->unsignedInteger('quantity')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('image')->nullable();
            $table->string('thumbnail_image')->nullable();
            $table->text('short_description')->nullable();
            $table->text('detail_description')->nullable();
            $table->unsignedBigInteger('original_price')->nullable();
            $table->unsignedBigInteger('agency_price')->nullable();
            $table->unsignedBigInteger('customer_price')->nullable();
            $table->unsignedBigInteger('price')->nullable();

            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
