<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePictureRequest;
use App\Models\Picture;
use App\Models\Range;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RangePictureController extends PictureControllerBase
{
    public function index(Range $range): JsonResponse
    {
        $this->authorize('view', $range);

        return $this->entityPictures($range);
    }

    public function store(StorePictureRequest $request, Range $range): JsonResponse
    {
        $this->authorize('update', $range);

        return $this->storeEntityPicture($request, $range);
    }

    public function attach(Request $request, Range $range, Picture $picture): JsonResponse
    {
        $this->authorize('update', $range);

        return $this->attachEntityPicture($range, $picture);
    }

    public function detach(Range $range, Picture $picture): JsonResponse
    {
        $this->authorize('update', $range);

        return $this->detachEntityPicture($range, $picture);
    }

    public function setPrimary(Range $range, Picture $picture): JsonResponse
    {
        $this->authorize('update', $range);

        return $this->setEntityPrimaryPicture($range, $picture);
    }

    public function reorder(Request $request, Range $range): JsonResponse
    {
        $this->authorize('update', $range);

        return $this->reorderEntityPictures($request, $range);
    }
}
