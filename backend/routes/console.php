<?php

declare(strict_types = 1);

use App\Models\Book;
use App\Services\ComfyUI\ComfyUIService;
use App\Services\OllamaService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('test', function () {

    $book = Book::find('01JF1VQ1QD26N1Q88VHVD559B3');

    ComfyUIService::resolve()->execute('main.workflow.json', $book);

});
