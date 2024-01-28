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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string("street", 164)->nullable();
            $table->string("city", 128)->nullable();
            $table->string("province", 96)->nullable();
            $table->string("country", 96)->nullable(false);
            $table->string("postal_code", 12)->nullable();
            $table->unsignedBigInteger("contact_id")->nullable(false);
            $table->timestamps();

            $table->foreign("contact_id")->on("contacts")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
