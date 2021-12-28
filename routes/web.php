<?php

use App\Services\ExampleDocumentManager;
use App\Services\StorageDocumentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Nagi\LaravelWopi\Contracts\DocumentManagerInterface;
use Nagi\LaravelWopi\Facades\Discovery;
use Nagi\LaravelWopi\Services\DefaultConfigRepository;
use Illuminate\Support\Facades\Storage;
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\LaravelWopi;

Route::get('/{id?}', function (Request $request, $id = null) {
    if (empty($id)) {
        $id = 2;
    }

    $document = app(AbstractDocumentManager::class)::find($id);

    $accessToken = 'MyToken';
    $ttl = 0;

    return view('welcome', ['accessToken' => $accessToken, 'ttl' => $ttl, 'url' => $document->generateUrl('en')]);
});
