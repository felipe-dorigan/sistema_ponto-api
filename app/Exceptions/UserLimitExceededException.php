<?php

namespace App\Exceptions;

use Exception;

class UserLimitExceededException extends Exception
{
    public function __construct(int $limit)
    {
        parent::__construct("Limite máximo de {$limit} usuários foi atingido.");
    }
}