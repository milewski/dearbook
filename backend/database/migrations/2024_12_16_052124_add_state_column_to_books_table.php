<?php

use App\Enums\BookState;
use App\Models\Book;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->string('illustrations')->nullable();
            $table->string('state')->nullable()->default(BookState::New);
            $table->string('fetched_at')->nullable();
        });

        Book::query()->whereNotNull('assets')->update([ 'state' => BookState::Completed ]);
        Book::query()->whereNull('assets')->update([ 'state' => BookState::New ]);

        Book::query()->where('failed', true)->update([ 'state' => BookState::Failed ]);

        Schema::table('books', function (Blueprint $table) {
            $table->string('state')->default(BookState::New)->change();
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('state');
            $table->dropColumn('fetched_at');
        });
    }
};
