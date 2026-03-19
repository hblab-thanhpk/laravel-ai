<?php

namespace App\Exceptions;

use RuntimeException;

class CannotModifyLastAdminException extends RuntimeException
{
    public static function whenDemoting(): self
    {
        return new self('Không thể hạ quyền admin cuối cùng.');
    }

    public static function whenDeleting(): self
    {
        return new self('Không thể xóa admin cuối cùng.');
    }
}