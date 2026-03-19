<?php

namespace App\Exceptions;

use RuntimeException;

class CannotDeletePermissionException extends RuntimeException
{
    public static function systemPermission(string $permissionDisplayName): self
    {
        return new self("Không thể xóa permission hệ thống: {$permissionDisplayName}.");
    }

    public static function permissionInUse(string $permissionDisplayName): self
    {
        return new self("Không thể xóa permission {$permissionDisplayName} vì đang được gán cho role.");
    }
}
