<?php

namespace App\Exceptions;

use RuntimeException;

class CannotDeleteCategoryException extends RuntimeException
{
    public static function categoryInUse(string $categoryName): self
    {
        return new self("Không thể xóa danh mục {$categoryName} vì đang có sản phẩm sử dụng.");
    }
}
