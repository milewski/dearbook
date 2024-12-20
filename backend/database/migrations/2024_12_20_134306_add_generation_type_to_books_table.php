<?php

declare(strict_types = 1);

use App\Enums\GenerationType;
use App\Models\Book;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {

            $table->string('generation_type')->nullable();
            $table->json('generation_data')->nullable();

        });

        Book::query()->update([
            'generation_type' => GenerationType::Simple,
            'generation_data' => DB::raw("jsonb_build_object('prompt', user_prompt::text)::jsonb"),
        ]);

        Schema::table('books', function (Blueprint $table) {

            $table->string('generation_type')->change();
            $table->json('generation_data')->change();
            $table->dropColumn('user_prompt');

        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {

            $table->dropColumn('generation_type');
            $table->dropColumn('generation_data');
            $table->text('user_prompt')->nullable();

        });
    }
};
