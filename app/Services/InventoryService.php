<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Audit;
use App\Models\InventoryCount;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(private readonly CodeGeneratorService $codes) {}

    public function completeCount(array $data, User $user): InventoryCount
    {
        return DB::transaction(function () use ($data, $user) {
            $products = Product::active()
                ->whereIn('id', array_keys($data['items']))
                ->orderBy('name')
                ->get();

            $count = InventoryCount::create([
                'code' => $this->codes->next('INV', InventoryCount::class),
                'user_id' => $user->id,
                'status' => 'completado',
                'notes' => $data['notes'] ?? null,
                'counted_at' => now(),
            ]);

            $differences = 0;

            foreach ($products as $product) {
                $physical = (int) $data['items'][$product->id];
                $difference = $physical - (int) $product->stock;
                $balanced = $difference === 0;

                if (! $balanced) {
                    $differences++;
                }

                $count->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'system_stock' => (int) $product->stock,
                    'physical_stock' => $physical,
                    'difference' => $difference,
                    'balanced' => $balanced,
                ]);
            }

            if ($differences > 0) {
                $audit = Audit::create([
                    'inventory_count_id' => $count->id,
                    'code' => $this->codes->next('AUD', Audit::class),
                    'reason' => 'Desbalance detectado en el conteo '.$count->code,
                    'status' => 'pendiente',
                    'requested_by' => $user->id,
                    'notes' => 'Se detectaron '.$differences.' productos con diferencias entre el stock del sistema y el conteo físico.',
                ]);

                Alert::create([
                    'type' => 'desbalance',
                    'title' => 'Desbalance en conteo '.$count->code,
                    'message' => 'La auditoría '.$audit->code.' requiere revisión: '.$differences.' productos presentan diferencias.',
                    'alertable_type' => InventoryCount::class,
                    'alertable_id' => $count->id,
                    'status' => 'pendiente',
                ]);
            }

            return $count->fresh(['items', 'audits']);
        });
    }

    public function resolveAudit(Audit $audit, array $data, User $user): Audit
    {
        return DB::transaction(function () use ($audit, $data, $user) {
            $status = $data['status'];

            $audit->update([
                'status' => $status,
                'notes' => $data['notes'] ?? $audit->notes,
                'resolved_by' => $status === 'resuelta' ? $user->id : $audit->resolved_by,
                'resolved_at' => $status === 'resuelta' ? now() : $audit->resolved_at,
            ]);

            if ($status === 'resuelta' && $audit->inventoryCount) {
                foreach ($audit->inventoryCount->items()->where('balanced', false)->get() as $item) {
                    if ($item->product_id) {
                        Product::whereKey($item->product_id)->update(['stock' => $item->physical_stock]);
                    }
                }

                Alert::where('alertable_type', InventoryCount::class)
                    ->where('alertable_id', $audit->inventory_count_id)
                    ->update(['status' => 'leida']);
            }

            return $audit->fresh();
        });
    }

    public function receivePurchase(PurchaseOrder $order, User $user, array $data = []): PurchaseOrder
    {
        return DB::transaction(function () use ($order, $data) {
            foreach ($order->items as $item) {
                if (! $item->product_id) {
                    continue;
                }

                $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

                if (! $product) {
                    continue;
                }

                $product->increment('stock', $item->quantity);

                if ((float) $item->unit_cost > 0) {
                    $product->update(['cost' => $item->unit_cost]);
                }
            }

            $order->update([
                'status' => 'recibida',
                'received_at' => $data['received_at'] ?? now()->toDateString(),
                'notes' => $data['notes'] ?? $order->notes,
            ]);

            Alert::where('alertable_type', PurchaseOrder::class)
                ->where('alertable_id', $order->id)
                ->update(['status' => 'leida']);

            return $order->fresh(['items', 'supplier']);
        });
    }
}
