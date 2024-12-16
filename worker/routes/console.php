<?php

declare(strict_types = 1);

use App\Services\BackendService;
use App\Services\BookService;
use App\Services\ComfyUI\ComfyUIService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;

Artisan::command('test', function () {

    //    $work = BackendService::resolve()->getWork();
    //    $data = BookService::resolve()->createBook($work);
    //    $workflowId = ComfyUIService::resolve()->execute('main.workflow.json', $data);
    //
    //    $assets = value(function () use ($workflowId) {
    //
    //        while (true) {
    //
    //            $response = ComfyUIService::resolve()->fetchOutputs($workflowId);
    //
    //            if ($response instanceof Collection) {
    //                return $response;
    //            }
    //
    //            if ($response === false) {
    //                return false;
    //            }
    //
    //            sleep(5);
    //
    //        }
    //
    //    });
    //
    //    BackendService::resolve()->finishWork($work, $data, $assets);

});
