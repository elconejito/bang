<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StorePictureRequest;
use App\Models\Location;
use App\Models\Picture;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationPictureController extends PictureControllerBase
{
    public function index(Location $location): JsonResponse
    {
        $this->authorize('view', $location);

        return $this->entityPictures($location);
    }

    public function store(StorePictureRequest $request, Location $location): JsonResponse
    {
        $this->authorize('update', $location);

        return $this->storeEntityPicture($request, $location);
    }

    public function attach(Request $request, Location $location, Picture $picture): JsonResponse
    {
        $this->authorize('update', $location);

        return $this->attachEntityPicture($location, $picture);
    }

    public function detach(Location $location, Picture $picture): JsonResponse
    {
        $this->authorize('update', $location);

        return $this->detachEntityPicture($location, $picture);
    }

    public function setPrimary(Location $location, Picture $picture): JsonResponse
    {
        $this->authorize('update', $location);

        return $this->setEntityPrimaryPicture($location, $picture);
    }

    public function reorder(Request $request, Location $location): JsonResponse
    {
        $this->authorize('update', $location);

        return $this->reorderEntityPictures($request, $location);
    }
}
