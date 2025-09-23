<?php

namespace App\Xendit\Exceptions;

interface ExceptionInterface extends \Throwable
{
    public function getErrorCode();
}
