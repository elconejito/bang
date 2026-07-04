<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePictureRequest;
use App\Models\Picture;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StorePictureController extends PictureControllerBase
{
    public function index(Store $store): JsonResponse
    {
        $this->authorize('view', $store);

        return $this->entityPictures($store);
    }

    public function store(StorePictureRequest $request, Store $store): JsonResponse
    {
        $this->authorize('update', $store);

        return $this->storeEntityPicture($request, $store);
    }

    public function attach(Request $request, Store $store, Picture $picture): JsonResponse
    {
        $this->authorize('update', $store);

        return $this->attachEntityPicture($store, $picture);
    }

    public function detach(Store $store, Picture $picture): JsonResponse
    {
        $this->authorize('update', $store);

        return $this->detachEntityPicture($store, $picture);
    }

    public function setPrimary(Store $store, Picture $picture): JsonResponse
    {
        $this->authorize('update', $store);

        return $this->setEntityPrimaryPicture($store, $picture);
    }

    public function reorder(Request $request, Store $store): JsonResponse
    {
        $this->authorize('update', $store);

        return $this->reorderEntityPictures($request, $store);
    }
}
