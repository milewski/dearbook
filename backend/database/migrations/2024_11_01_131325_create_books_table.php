<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS ai CASCADE');
        DB::statement('CREATE EXTENSION IF NOT EXISTS vectorscale CASCADE');

        Schema::create('books', function (Blueprint $table) {

            $table->ulid('id')->primary();

            $table->text('user_prompt')->nullable();

            $table->string('title')->nullable();
            $table->string('synopsis')->nullable();
            $table->json('paragraphs')->nullable();

            $table->json('assets')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
