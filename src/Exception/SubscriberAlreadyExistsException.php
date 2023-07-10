<?php

namespace App\Exception;

use RuntimeException;

class SubscriberAlreadyExistsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Subscriber with that email already exists');
    }
}
