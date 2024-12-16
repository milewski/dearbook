<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {

            $table->boolean('failed')->default(false)->after('user_prompt');
            $table->string('failure')->nullable()->after('failed');

        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {

            $table->dropColumn('failed');
            $table->dropColumn('failure');

        });
    }
};
