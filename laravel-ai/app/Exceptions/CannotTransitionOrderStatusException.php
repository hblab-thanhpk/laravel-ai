<?php

namespace App\Exceptions;

use App\Enums\OrderStatus;
use RuntimeException;

class CannotTransitionOrderStatusException extends RuntimeException
{
    public static function invalid(OrderStatus $from, OrderStatus $to): self
    {
        return new self(
            "Không thể chuyển trạng thái đơn hàng từ \"{$from->label()}\" sang \"{$to->label()}\".",
        );
    }

    public static function finalState(OrderStatus $status): self
    {
        return new self(
            "Đơn hàng đã ở trạng thái \"{$status->label()}\" và không thể thay đổi.",
        );
    }
}
