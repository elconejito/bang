<?php

namespace App\Http\Controllers\API;

use App\Actions\Firearms\ArchiveFirearm;
use App\Actions\Firearms\UnarchiveFirearm;
use App\Enums\ArchiveReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArchiveFirearmRequest;
use App\Models\Firearm;
use App\Transformers\FirearmTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FirearmLifecycleController extends Controller
{
    public function archive(ArchiveFirearmRequest $request, Firearm $firearm, ArchiveFirearm $archiveFirearm): JsonResponse
    {
        $this->authorize('archive', $firearm);
        $validated = $request->validated();
        $firearm = $archiveFirearm->execute(
            $firearm,
            ArchiveReason::from($validated['reason']),
            $validated['description'] ?? null,
            Auth::id(),
            $validated['unmount_all_accessories'] ?? false,
            $validated['unmount_accessories'] ?? [],
        );

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }

    public function unarchive(Firearm $firearm, UnarchiveFirearm $unarchiveFirearm): JsonResponse
    {
        $this->authorize('unarchive', $firearm);
        $firearm = $unarchiveFirearm->execute($firearm, Auth::id());

        return fractal()->item($firearm, FirearmTransformer::class)->respond();
    }
}
