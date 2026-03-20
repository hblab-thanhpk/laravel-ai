<?php

namespace App\Exceptions;

use RuntimeException;

class CannotDeleteRoleException extends RuntimeException
{
    public static function systemRole(string $roleDisplayName): self
    {
        return new self("Không thể xóa role hệ thống: {$roleDisplayName}.");
    }

    public static function roleInUse(string $roleDisplayName): self
    {
        return new self("Không thể xóa role {$roleDisplayName} vì đang có user sử dụng.");
    }
}
