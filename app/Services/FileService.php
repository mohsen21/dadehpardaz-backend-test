<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileService
{
    public function upload(UploadedFile $file, string $directory = 'attachments'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($directory, $filename, 'minio');

        return $path;
    }

    public function getPresignedUrl(string $path, int $expirationMinutes = 60): string
    {
        /** @var \Illuminate\Filesystem\AwsS3V3Adapter $disk */
        $disk = Storage::disk('minio');
        
        return $disk->temporaryUrl($path, now()->addMinutes($expirationMinutes));
    }

    public function delete(string $path): bool
    {
        return Storage::disk('minio')->delete($path);
    }

    public function exists(string $path): bool
    {
        return Storage::disk('minio')->exists($path);
    }
}

