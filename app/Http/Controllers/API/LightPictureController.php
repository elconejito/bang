<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePictureRequest;
use App\Models\Light;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LightPictureController extends PictureControllerBase
{
    public function index(Light $light): JsonResponse
    {
        $this->authorize('view', $light);

        return $this->entityPictures($light);
    }

    public function store(StorePictureRequest $request, Light $light): JsonResponse
    {
        $this->authorize('update', $light);

        return $this->storeEntityPicture($request, $light);
    }

    public function attach(Request $request, Light $light, Picture $picture): JsonResponse
    {
        $this->authorize('update', $light);

        return $this->attachEntityPicture($light, $picture);
    }

    public function detach(Light $light, Picture $picture): JsonResponse
    {
        $this->authorize('update', $light);

        return $this->detachEntityPicture($light, $picture);
    }

    public function setPrimary(Light $light, Picture $picture): JsonResponse
    {
        $this->authorize('update', $light);

        return $this->setEntityPrimaryPicture($light, $picture);
    }

    public function reorder(Request $request, Light $light): JsonResponse
    {
        $this->authorize('update', $light);

        return $this->reorderEntityPictures($request, $light);
    }
}
