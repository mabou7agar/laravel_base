<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

function uploadFile(?UploadedFile $image, $storeFolder = 'images')
{
    if ($image == null) {
        return $image;
    }

    if ($path = Cache::get($image->getRealPath())) {
        return $path;
    }

    $imageName = time() . '.' . $image->extension();
    if (env('USE_S3', false)) {
        $imagePath = $image->storeAs($storeFolder, $imageName, 's3');
        Cache::put($image->getRealPath(), $imagePath, now()->addMinutes(5));

        return $imagePath;
    }
    $imagePath = str_replace('public/', '', $image->storeAs($storeFolder, $imageName,'public'));
    Cache::put($image->getRealPath(), $imagePath, now()->addMinutes(5));

    return $imagePath;
}

function getImageUrl($imagePath)
{
    if (env('USE_S3', false)) {
        return $imagePath;
    }

    return url('storage/'.$imagePath,[],true);
}
