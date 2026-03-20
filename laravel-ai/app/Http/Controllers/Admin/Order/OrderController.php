<?php

namespace App\Http\Controllers\Admin\Order;

use App\DTOs\Order\OrderQueryData;
use App\Exceptions\CannotTransitionOrderStatusException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Order\IndexOrderRequest;
use App\Http\Requests\Admin\Order\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(IndexOrderRequest $request, OrderService $orderService): View
    {
        $queryData = OrderQueryData::fromArray($request->validated());

        return view('admin.orders.index', [
            'orders'  => $orderService->paginate($queryData),
            'filters' => $queryData->toArray(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product', 'items.variant']);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function edit(Order $order): View
    {
        $order->load('user');

        return view('admin.orders.edit', [
            'order'              => $order,
            'allowedTransitions' => $order->status->allowedTransitions(),
        ]);
    }

    public function update(UpdateOrderStatusRequest $request, Order $order, OrderService $orderService): RedirectResponse
    {
        try {
            $orderService->updateStatus($order, $request->validated('status'));
        } catch (CannotTransitionOrderStatusException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    public function destroy(Order $order, OrderService $orderService): RedirectResponse
    {
        $orderService->delete($order);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Xóa đơn hàng thành công.');
    }
}
