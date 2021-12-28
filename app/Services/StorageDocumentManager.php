<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Str;
use Nagi\LaravelWopi\Contracts\Concerns\Deleteable;
use Nagi\LaravelWopi\Contracts\Concerns\Renameable;

class StorageDocumentManager extends AbstractDocumentManager implements Deleteable, Renameable
{
    public string $fileId;
    public string $internalPath;

    public function __construct(?string $fileId = null, ?string $internalPath = null)
    {
        if (is_null($fileId)) {
            return $this;
        }

        $this->fileId = $fileId;
        $this->internalPath = $internalPath;
    }

    public function userFriendlyName(): string
    {
        return get_current_user();
    }

    public function rename(string $newName): void
    {
        $newName = Str::replace(basename($this->internalPath), $newName, $this->internalPath);
        Storage::disk('public')->move($this->internalPath, $newName);
    }

    public function canUserRename(): bool
    {
        return true;
    }


    public function delete(): void
    {
        Storage::disk('public')->delete($this->internalPath);
    }

    public static function find(string $fileId): AbstractDocumentManager
    {
        $internalPath = collect(Storage::disk('public')->files($fileId))
                ->filter(fn ($path) => ! Str::contains(basename($path), '-version-'))
                ->first();


        $directoryExists = Storage::disk('public')->exists($fileId);

        throw_if(!$directoryExists || is_null($internalPath), NotFoundHttpException::class);

        return new static($fileId, $internalPath);
    }

    public static function findByName(string $filename): AbstractDocumentManager
    {
        $filePath = collect(Storage::disk('public')->files(null, true))
        ->first(function ($file) use ($filename) {
            return basename($file) === $filename ;
        });

        throw_if(is_null($filePath), NotFoundHttpException::class);

        return new static(dirname($filePath), $filePath);
    }

    public static function create(array $properties): AbstractDocumentManager
    {
        $superUniqueId = uniqid();

        $internalPath = "{$superUniqueId}/{$properties['name']}";
        Storage::disk('public')->put($internalPath, $properties['content']);

        return new static($superUniqueId, $internalPath);
    }

    public function id(): string
    {
        return $this->fileId;
    }

    public function basename(): string
    {
        return basename($this->internalPath);
    }

    public function owner(): string
    {
        return get_current_user();
    }

    public function size(): int
    {
        return Storage::disk('public')->size($this->internalPath);
    }

    public function version(): string
    {
        $previousVersions = Storage::disk('public')->files(dirname($this->internalPath));

        if (count($previousVersions) === 1) {
            return 'version-1';
        }

        $latestVersion = collect($previousVersions)
        ->transform(fn ($path) => Str::afterLast(basename($path), '-version-'))
        ->sortDesc()
        ->first();

        $versionNumber = $latestVersion + 1;
        return "version-{$versionNumber}";
    }

    public function content(): string
    {
        return Storage::disk('public')->get($this->internalPath);
    }

    public function isLocked(): bool
    {
        return Str::contains(basename($this->internalPath), '-locked-');
    }

    public function getLock(): string
    {
        $filename = basename($this->internalPath);
        return Str::after($filename, '-locked-');
    }

    public function put(string $content, array $editorsIds = []): void
    {
        Storage::disk('public')->put($this->internalPath, $content);
    }

    public function deleteLock(): void
    {
        $filenameWithoutLock = Str::beforeLast($this->internalPath, '-locked-');
        Storage::disk('public')->move($this->internalPath, $filenameWithoutLock);
    }

    public function lock(string $lockId): void
    {
        $filenameWithLock = "{$this->internalPath}-locked-{$lockId}";

        Storage::disk('public')->move($this->internalPath, $filenameWithLock);
    }
}
