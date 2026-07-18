<?php

namespace App\Http\Controllers\API;

use App\Actions\Assets\ArchiveAsset;
use App\Actions\Assets\UnarchiveAsset;
use App\Enums\ArchiveReason;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArchiveAssetRequest;
use App\Models\Accessory;
use App\Models\Light;
use App\Models\Magazine;
use App\Models\MiscAccessory;
use App\Models\Optic;
use App\Models\Suppressor;
use App\Transformers\LightTransformer;
use App\Transformers\MagazineTransformer;
use App\Transformers\MiscAccessoryTransformer;
use App\Transformers\OpticTransformer;
use App\Transformers\SuppressorTransformer;
use Illuminate\Http\JsonResponse;

class AssetLifecycleController extends Controller
{
    public function archiveSuppressor(ArchiveAssetRequest $request, Suppressor $suppressor, ArchiveAsset $action): JsonResponse
    {
        return $this->archive($request, $suppressor, $action);
    }

    public function unarchiveSuppressor(Suppressor $suppressor, UnarchiveAsset $action): JsonResponse
    {
        return $this->unarchive($suppressor, $action);
    }

    public function archiveOptic(ArchiveAssetRequest $request, Optic $optic, ArchiveAsset $action): JsonResponse
    {
        return $this->archive($request, $optic, $action);
    }

    public function unarchiveOptic(Optic $optic, UnarchiveAsset $action): JsonResponse
    {
        return $this->unarchive($optic, $action);
    }

    public function archiveLight(ArchiveAssetRequest $request, Light $light, ArchiveAsset $action): JsonResponse
    {
        return $this->archive($request, $light, $action);
    }

    public function unarchiveLight(Light $light, UnarchiveAsset $action): JsonResponse
    {
        return $this->unarchive($light, $action);
    }

    public function archiveMiscAccessory(ArchiveAssetRequest $request, MiscAccessory $miscAccessory, ArchiveAsset $action): JsonResponse
    {
        return $this->archive($request, $miscAccessory, $action);
    }

    public function unarchiveMiscAccessory(MiscAccessory $miscAccessory, UnarchiveAsset $action): JsonResponse
    {
        return $this->unarchive($miscAccessory, $action);
    }

    public function archiveMagazine(ArchiveAssetRequest $request, Magazine $magazine, ArchiveAsset $action): JsonResponse
    {
        return $this->archive($request, $magazine, $action);
    }

    public function unarchiveMagazine(Magazine $magazine, UnarchiveAsset $action): JsonResponse
    {
        return $this->unarchive($magazine, $action);
    }

    private function archive(ArchiveAssetRequest $request, Accessory|Magazine $asset, ArchiveAsset $action): JsonResponse
    {
        $this->authorize('update', $asset);
        $validated = $request->validated();
        $asset = $action->execute($asset, ArchiveReason::from($validated['reason']), $validated['description'] ?? null, $request->user()->id);

        return $this->respond($asset);
    }

    private function unarchive(Accessory|Magazine $asset, UnarchiveAsset $action): JsonResponse
    {
        $this->authorize('update', $asset);
        $asset = $action->execute($asset, request()->user()->id);

        return $this->respond($asset);
    }

    private function respond(Accessory|Magazine $asset): JsonResponse
    {
        $transformer = match (true) {
            $asset instanceof Suppressor => SuppressorTransformer::class,
            $asset instanceof Optic => OpticTransformer::class,
            $asset instanceof Light => LightTransformer::class,
            $asset instanceof MiscAccessory => MiscAccessoryTransformer::class,
            $asset instanceof Magazine => MagazineTransformer::class,
        };

        return fractal()->item($asset, $transformer)->respond();
    }
}
