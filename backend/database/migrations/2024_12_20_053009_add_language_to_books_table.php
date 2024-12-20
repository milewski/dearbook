<?php

use App\Models\Book;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PrinsFrank\Standards\Language\LanguageAlpha2;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('language')->after('paragraphs')->nullable();
        });

        Book::query()->update([ 'language' => LanguageAlpha2::English ]);
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('language');
        });
    }
};
