<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePictureRequest;
use App\Models\MiscAccessory;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MiscAccessoryPictureController extends PictureControllerBase
{
    public function index(MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('view', $miscAccessory);

        return $this->entityPictures($miscAccessory);
    }

    public function store(StorePictureRequest $request, MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('update', $miscAccessory);

        return $this->storeEntityPicture($request, $miscAccessory);
    }

    public function attach(Request $request, MiscAccessory $miscAccessory, Picture $picture): JsonResponse
    {
        $this->authorize('update', $miscAccessory);

        return $this->attachEntityPicture($miscAccessory, $picture);
    }

    public function detach(MiscAccessory $miscAccessory, Picture $picture): JsonResponse
    {
        $this->authorize('update', $miscAccessory);

        return $this->detachEntityPicture($miscAccessory, $picture);
    }

    public function setPrimary(MiscAccessory $miscAccessory, Picture $picture): JsonResponse
    {
        $this->authorize('update', $miscAccessory);

        return $this->setEntityPrimaryPicture($miscAccessory, $picture);
    }

    public function reorder(Request $request, MiscAccessory $miscAccessory): JsonResponse
    {
        $this->authorize('update', $miscAccessory);

        return $this->reorderEntityPictures($request, $miscAccessory);
    }
}
