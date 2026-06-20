<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePictureRequest;
use App\Models\Magazine;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MagazinePictureController extends PictureControllerBase
{
    public function index(Magazine $magazine): JsonResponse
    {
        $this->authorize('view', $magazine);

        return $this->entityPictures($magazine);
    }

    public function store(StorePictureRequest $request, Magazine $magazine): JsonResponse
    {
        $this->authorize('update', $magazine);

        return $this->storeEntityPicture($request, $magazine);
    }

    public function attach(Request $request, Magazine $magazine, Picture $picture): JsonResponse
    {
        $this->authorize('update', $magazine);

        return $this->attachEntityPicture($magazine, $picture);
    }

    public function detach(Magazine $magazine, Picture $picture): JsonResponse
    {
        $this->authorize('update', $magazine);

        return $this->detachEntityPicture($magazine, $picture);
    }

    public function setPrimary(Magazine $magazine, Picture $picture): JsonResponse
    {
        $this->authorize('update', $magazine);

        return $this->setEntityPrimaryPicture($magazine, $picture);
    }

    public function reorder(Request $request, Magazine $magazine): JsonResponse
    {
        $this->authorize('update', $magazine);

        return $this->reorderEntityPictures($request, $magazine);
    }
}
