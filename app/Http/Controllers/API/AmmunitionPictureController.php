<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePictureRequest;
use App\Models\Ammunition;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmmunitionPictureController extends PictureControllerBase
{
    public function index(Ammunition $ammunition): JsonResponse
    {
        $this->authorize('view', $ammunition);

        return $this->entityPictures($ammunition);
    }

    public function store(StorePictureRequest $request, Ammunition $ammunition): JsonResponse
    {
        $this->authorize('update', $ammunition);

        return $this->storeEntityPicture($request, $ammunition);
    }

    public function attach(Request $request, Ammunition $ammunition, Picture $picture): JsonResponse
    {
        $this->authorize('update', $ammunition);

        return $this->attachEntityPicture($ammunition, $picture);
    }

    public function detach(Ammunition $ammunition, Picture $picture): JsonResponse
    {
        $this->authorize('update', $ammunition);

        return $this->detachEntityPicture($ammunition, $picture);
    }

    public function setPrimary(Ammunition $ammunition, Picture $picture): JsonResponse
    {
        $this->authorize('update', $ammunition);

        return $this->setEntityPrimaryPicture($ammunition, $picture);
    }

    public function reorder(Request $request, Ammunition $ammunition): JsonResponse
    {
        $this->authorize('update', $ammunition);

        return $this->reorderEntityPictures($request, $ammunition);
    }
}
