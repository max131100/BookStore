<?php

namespace App\Exception;

use RuntimeException;

class BookCategoryAlreadyExistsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('book category already exists');
    }
}
