<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;


class ImageService
{
    protected ImageManager $image;
    public function __construct()
    {
        $this->image = new ImageManager(new Driver());
    }
    protected function processImage(UploadedFile $file, string $directory, string $fileName, int $maxWidth, int $quality, ?string $oldPath): string
    {
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $path = "{$directory}/{$fileName}.$extension"; // paksa JPG agar konsisten

        $image = $this->image->read($file);

        $image->scaleDown($maxWidth);

        Storage::disk('public')->put($path, $image->toJpeg($quality));
        return $path;
    }
    public function uploadAssetImage(UploadedFile $file, string $assetCode, ?string $oldPath = null): string
    {
        return $this->processImage(
            file: $file,
            directory: "asset-images",
            fileName: $assetCode,
            maxWidth: 1024,
            quality: 85,
            oldPath: $oldPath
        );
    }
    public function uploadDamageAssetImage(UploadedFile $file, string $damageAssetId, ?string $oldPath = null): string
    {
        return $this->processImage(
            file: $file,
            directory: "asset-damages",
            fileName: $damageAssetId,
            maxWidth: 1024,
            quality: 85,
            oldPath: $oldPath
        );
    }
    public function uploadRepairAssetImage(UploadedFile $file, string $repairAssetId, ?string $oldPath = null): string
    {
        return $this->processImage(
            file: $file,
            directory: "asset-repairs",
            fileName: $repairAssetId,
            maxWidth: 1024,
            quality: 85,
            oldPath: $oldPath
        );
    }
}
