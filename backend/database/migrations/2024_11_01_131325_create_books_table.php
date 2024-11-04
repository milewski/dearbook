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

            $table->id();
            $table->uuid('batch_id')->index();
            $table->text('user_prompt')->nullable();

            $table->string('title');
            $table->string('subject');
            $table->json('tags');
            $table->json('paragraphs');

            $table->json('assets')->nullable();
            $table->json('illustrations')->nullable();

            $table->vector('embedding', 1024);

            $table->timestamps();

        });

        DB::statement('CREATE INDEX document_embedding_idx ON books USING diskann (embedding)');
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
