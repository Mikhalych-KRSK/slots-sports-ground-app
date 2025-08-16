<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Exceptions\ForbiddenException;

abstract class BaseService
{
    protected function authorizeResourceOwnership($model, string $userIdField = 'user_id'): void
    {
        if ($model->{$userIdField} !== Auth::id()) {
            throw new ForbiddenException();
        }
    }

    protected function withAuthorization($model, \Closure $callback, string $userIdField = 'user_id')
    {
        $this->authorizeResourceOwnership($model, $userIdField);
        return $callback();
    }
}
