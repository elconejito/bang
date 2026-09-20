<?php

namespace App\Http\Controllers\API;

use App\Enums\OrderAssetType;
use App\Http\Controllers\Controller;
use App\Models\Ammunition;
use App\Models\Order;
use App\Models\OrderAsset;
use App\Transformers\OrderItemOptionTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderItemOptionsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $this->authorize('viewAny', Order::class);
        $request->validate(['order_id' => ['nullable', 'integer', 'min:1']]);
        $order = $request->filled('order_id') ? Order::findOrFail($request->integer('order_id')) : null;
        if ($order) {
            $this->authorize('view', $order);
        }
        $ammunitionIds = $order?->inventories()->pluck('ammunition_id') ?? collect();
        $links = OrderAsset::where('user_id', $userId)->get()->keyBy(fn (OrderAsset $link): string => $link->asset_type.':'.$link->asset_id);
        $options = Ammunition::select(['id', 'manufacturer', 'label', 'weight', 'shot_weight_id', 'caliber_id'])
            ->with(['caliber:id,label', 'shotWeight:id,label'])->get()->map(fn (Ammunition $ammunition): array => [
                'type' => 'ammunition',
                'id' => $ammunition->id,
                'label' => implode(' · ', array_filter([$ammunition->caliber?->label, $ammunition->manufacturer, $ammunition->label, $ammunition->shotWeight?->label ?? ($ammunition->weight ? $ammunition->weight.' gr' : null)])),
                'secondary_label' => null,
                'order_id' => $ammunitionIds->contains($ammunition->id) ? $order?->id : null,
            ]);
        foreach (OrderAssetType::models() as $type => $class) {
            $unavailableIds = $links->filter(fn (OrderAsset $link): bool => $link->asset_type === $class && $link->order_id !== $order?->id)->pluck('asset_id');
            $columns = match ($type) {
                'firearm' => ['model', 'serial'],
                'magazine' => ['model_name', 'model_number', 'serial_number', 'id_marking', 'capacity'],
                default => ['model_number', 'serial'],
            };
            $options = $options->concat($class::select(['id', 'manufacturer', 'label', ...$columns])
                ->whereNotIn('id', $unavailableIds)->get()->map(fn ($asset): array => [
                    'type' => $type,
                    'id' => $asset->id,
                    'label' => implode(' · ', array_filter([$asset->manufacturer, $asset->label ?: ($asset->model ?? $asset->model_name), $asset->model_number])),
                    'secondary_label' => implode(' · ', array_filter([$asset->serial ?? $asset->serial_number, $asset->id_marking, $asset->capacity ? $asset->capacity.' rounds' : null])),
                    'order_id' => $links->get($class.':'.$asset->id)?->order_id,
                ]));
        }

        return fractal($options->sortBy('label')->values(), OrderItemOptionTransformer::class)->respond();
    }
}
