<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePictureRequest;
use App\Models\Firearm;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FirearmPictureController extends PictureControllerBase
{
    public function index(Firearm $firearm): JsonResponse
    {
        $this->authorize('view', $firearm);

        return $this->entityPictures($firearm);
    }

    public function store(StorePictureRequest $request, Firearm $firearm): JsonResponse
    {
        $this->authorize('update', $firearm);

        return $this->storeEntityPicture($request, $firearm);
    }

    public function attach(Request $request, Firearm $firearm, Picture $picture): JsonResponse
    {
        $this->authorize('update', $firearm);

        return $this->attachEntityPicture($firearm, $picture);
    }

    public function detach(Firearm $firearm, Picture $picture): JsonResponse
    {
        $this->authorize('update', $firearm);

        return $this->detachEntityPicture($firearm, $picture);
    }

    public function setPrimary(Firearm $firearm, Picture $picture): JsonResponse
    {
        $this->authorize('update', $firearm);

        return $this->setEntityPrimaryPicture($firearm, $picture);
    }

    public function reorder(Request $request, Firearm $firearm): JsonResponse
    {
        $this->authorize('update', $firearm);

        return $this->reorderEntityPictures($request, $firearm);
    }
}
