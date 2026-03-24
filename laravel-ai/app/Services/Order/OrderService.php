<?php

namespace App\Services\Order;

use App\DTOs\Order\OrderQueryData;
use App\Enums\OrderStatus;
use App\Exceptions\CannotTransitionOrderStatusException;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function paginate(OrderQueryData $queryData): LengthAwarePaginator
    {
        $query = Order::query()->with('user');

        if ($queryData->search !== null) {
            $normalizedSearch = mb_strtolower($queryData->search);

            $query->where(function (Builder $builder) use ($normalizedSearch): void {
                $builder
                    ->whereRaw('LOWER(CAST(id AS TEXT)) LIKE ?', ["%{$normalizedSearch}%"])
                    ->orWhereHas('user', function (Builder $userBuilder) use ($normalizedSearch): void {
                        $userBuilder
                            ->whereRaw('LOWER(name) LIKE ?', ["%{$normalizedSearch}%"])
                            ->orWhereRaw('LOWER(email) LIKE ?', ["%{$normalizedSearch}%"]);
                    });
            });
        }

        if ($queryData->status !== null) {
            $query->where('status', $queryData->status);
        }

        $query
            ->orderBy($queryData->sortBy, $queryData->sortDirection)
            ->orderBy('id');

        return $query
            ->paginate($queryData->perPage)
            ->withQueryString();
    }

    public function updateStatus(Order $order, string $newStatusValue): Order
    {
        $newStatus = OrderStatus::from($newStatusValue);
        $currentStatus = $order->status;

        if ($currentStatus->isFinal()) {
            throw CannotTransitionOrderStatusException::finalState($currentStatus);
        }

        if (! $currentStatus->canTransitionTo($newStatus)) {
            throw CannotTransitionOrderStatusException::invalid($currentStatus, $newStatus);
        }

        return DB::transaction(function () use ($order, $currentStatus, $newStatus): Order {
            if ($newStatus === OrderStatus::Cancelled) {
                $this->restoreStock($order);

                Log::info('order.cancelled_stock_restored', [
                    'order_id' => $order->id,
                    'from' => $currentStatus->value,
                ]);
            }

            $order->status = $newStatus;
            $order->save();

            return $order->refresh();
        });
    }

    public function delete(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $order->items()->delete();
            $order->delete();
        });
    }

    private function restoreStock(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            if ($item->product_variant_id !== null) {
                ProductVariant::query()
                    ->whereKey($item->product_variant_id)
                    ->increment('stock', $item->quantity);
            } elseif ($item->product_id !== null) {
                Product::query()
                    ->whereKey($item->product_id)
                    ->increment('stock', $item->quantity);
            }
        }
    }
}
