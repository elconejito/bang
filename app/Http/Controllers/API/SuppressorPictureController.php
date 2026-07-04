<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePictureRequest;
use App\Models\Picture;
use App\Models\Suppressor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SuppressorPictureController extends PictureControllerBase
{
    public function index(Suppressor $suppressor): JsonResponse
    {
        $this->authorize('view', $suppressor);

        return $this->entityPictures($suppressor);
    }

    public function store(StorePictureRequest $request, Suppressor $suppressor): JsonResponse
    {
        $this->authorize('update', $suppressor);

        return $this->storeEntityPicture($request, $suppressor);
    }

    public function attach(Request $request, Suppressor $suppressor, Picture $picture): JsonResponse
    {
        $this->authorize('update', $suppressor);

        return $this->attachEntityPicture($suppressor, $picture);
    }

    public function detach(Suppressor $suppressor, Picture $picture): JsonResponse
    {
        $this->authorize('update', $suppressor);

        return $this->detachEntityPicture($suppressor, $picture);
    }

    public function setPrimary(Suppressor $suppressor, Picture $picture): JsonResponse
    {
        $this->authorize('update', $suppressor);

        return $this->setEntityPrimaryPicture($suppressor, $picture);
    }

    public function reorder(Request $request, Suppressor $suppressor): JsonResponse
    {
        $this->authorize('update', $suppressor);

        return $this->reorderEntityPictures($request, $suppressor);
    }
}
