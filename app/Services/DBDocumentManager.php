<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\Deleteable;
use Nagi\LaravelWopi\Contracts\Concerns\HasHash;
use Nagi\LaravelWopi\Contracts\Concerns\HasMetadata;
use Nagi\LaravelWopi\Contracts\Concerns\HasUrlProprties;
use Nagi\LaravelWopi\Contracts\Concerns\Renameable;
use Nagi\LaravelWopi\Contracts\Concerns\StopRelayingOnBaseNameToGetFileExtension;
use Nagi\LaravelWopi\Contracts\Concerns\InteractsWithUserInfo;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use App\Models\User;
use Nagi\LaravelWopi\Contracts\Concerns\OverridePermissions;

class DBDocumentManager extends AbstractDocumentManager implements Deleteable, Renameable, HasHash, HasMetadata, StopRelayingOnBaseNameToGetFileExtension, InteractsWithUserInfo, OverridePermissions
{
    protected File $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function userCanNotWriteRelative(): bool
    {
        // You can enable/disable this to complete the tests
        return false;
    }

    public function getUserInfo(): string
    {
        // login
        Auth::setUser(User::find(1));

        return (string) auth()->user()->info;
    }

    public function supportUserInfo(): bool
    {
        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        return $config->supportUserInfo();
    }

    public static function putUserInfo(string $userInfo, ?string $fileId, ?string $accessToken): void
    {
        // login
        Auth::setUser(User::find(1));

        auth()->user()->update(['info' => $userInfo]);
    }

    public function sha256Hash(): string
    {
        return $this->file->hash;
    }

    public function lastModifiedTime(): string
    {
        return Carbon::parse($this->file->updated_at, 'UTC')->toIso8601String();
    }

    public function extension(): string
    {
        return ".".$this->file->extension;
    }

    public static function find(string $fileId): AbstractDocumentManager
    {
        $file =  File::findorFail($fileId);
        return new static($file);
    }

    public static function findByName(string $filename): AbstractDocumentManager
    {
        $file = File::whereName($filename)->firstOrFail();
        return new static($file);
    }

    public static function create(array $properties): AbstractDocumentManager
    {
        $hash = hash('sha256', base64_encode($properties['content']));

        $file = File::create([
            'name' => $properties['basename'],
            'size' => $properties['size'],
            'path' => $properties['basename'],
            'lock' => '',
            'hash' => $hash,
            'version' => '1',
            'extension' => $properties['extension'],
            'user_id' => 1,
        ]);

        file_put_contents(Storage::disk('public')->path($properties['basename']), $properties['content']);

        return new static($file);
    }

    public function id(): string
    {
        return $this->file->id;
    }

    public function userFriendlyName(): string
    {
        $user = Auth::user();

        return is_null($user) ? 'Guest' : $user->name;
    }

    public function basename(): string
    {
        return $this->file->name;
    }

    public function owner(): string
    {
        return $this->file->user->id;
    }

    public function size(): int
    {
        return $this->file->size;
    }

    public function version(): string
    {
        return $this->file->version;
    }

    public function content(): string
    {
        return file_get_contents(Storage::disk('public')->path($this->file->path));
    }

    public function isLocked(): bool
    {
        return !empty($this->file->lock);
    }

    public function getLock(): string
    {
        return $this->file->lock;
    }

    public function put(string $content, array $editorsIds = []): void
    {
        // calculate content size and hash, be carefull with large contents!
        $size = strlen($content);
        $hash = hash('sha256', base64_encode($content));
        $newVersion = uniqid();

        file_put_contents(Storage::disk('public')->path($this->file->path), $content);
        $this->file->fill(['size' => $size, 'hash' => $hash, 'version' => $newVersion])->update();
    }

    public function deleteLock(): void
    {
        $this->file->fill(['lock' => ''])->update();
    }

    public function lock(string $lockId): void
    {
        $this->file->fill(['lock' => $lockId])->update();
    }

    public function delete(): void
    {
        Storage::disk('public')->delete($this->file->path);
        $this->file->delete();
    }

    public function rename(string $newName): void
    {
        $oldPath = $this->file->path;
        $this
            ->file
            ->fill(['name' => "{$newName}.{$this->file->extension}", 'path' => "{$newName}.{$this->file->extension}"])
            ->update();

        $newPath = $this->file->path;


        Storage::disk('public')->move($oldPath, $newPath);
    }

    public function canUserRename(): bool
    {
        return true;
    }
}
