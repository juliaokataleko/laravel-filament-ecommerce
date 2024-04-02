<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("brand_id")->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId("category_id")->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId("tax_id")->nullable()->constrained()->cascadeOnDelete();

            $table->string("name");
            $table->string("slug")->unique();
            $table->string("sku")->unique();
            $table->longText("description")->nullable();
            $table->unsignedBigInteger("quantity")->default(0);
            $table->unsignedBigInteger("sold")->default(0);
            $table->unsignedBigInteger("bought")->default(0);

            $table->decimal("price", 10, 2)->nullable();
            $table->boolean("is_visible")->default(false);
            $table->boolean("is_featured")->default(false);
            $table->enum('type', ["deliverable", "downloadable", "product", "service"])->default("product");
            $table->boolean("control_stock")->default(false);
            $table->integer("stock_alert_limit")->default(20);
            $table->date("published_at")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
