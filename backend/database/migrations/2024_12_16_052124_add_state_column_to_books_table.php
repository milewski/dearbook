<?php

declare(strict_types = 1);

use App\Enums\BookState;
use App\Models\Book;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {

            $table->json('illustrations')->nullable()->after('paragraphs');

            $table->string('state')->nullable()->default(BookState::PendingStoryLine);
            $table->string('fetched_at')->nullable()->after('assets');

            $table->string('reason')->nullable()->after('failed');

        });

        Book::query()->whereNotNull('assets')->update([ 'state' => BookState::Completed ]);
        Book::query()->whereNull('assets')->update([ 'state' => BookState::PendingStoryLine ]);

        Book::query()->where('failed', true)->update([ 'state' => BookState::Failed ]);

        Schema::table('books', function (Blueprint $table) {
            $table->string('state')->default(BookState::PendingStoryLine)->change();
            $table->dropColumn('failed');
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
