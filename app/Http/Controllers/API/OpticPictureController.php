<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePictureRequest;
use App\Models\Optic;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OpticPictureController extends PictureControllerBase
{
    public function index(Optic $optic): JsonResponse
    {
        $this->authorize('view', $optic);

        return $this->entityPictures($optic);
    }

    public function store(StorePictureRequest $request, Optic $optic): JsonResponse
    {
        $this->authorize('update', $optic);

        return $this->storeEntityPicture($request, $optic);
    }

    public function attach(Request $request, Optic $optic, Picture $picture): JsonResponse
    {
        $this->authorize('update', $optic);

        return $this->attachEntityPicture($optic, $picture);
    }

    public function detach(Optic $optic, Picture $picture): JsonResponse
    {
        $this->authorize('update', $optic);

        return $this->detachEntityPicture($optic, $picture);
    }

    public function setPrimary(Optic $optic, Picture $picture): JsonResponse
    {
        $this->authorize('update', $optic);

        return $this->setEntityPrimaryPicture($optic, $picture);
    }

    public function reorder(Request $request, Optic $optic): JsonResponse
    {
        $this->authorize('update', $optic);

        return $this->reorderEntityPictures($request, $optic);
    }
}
