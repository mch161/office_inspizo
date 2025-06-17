<?php

namespace App\Traits;

use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

trait Authenticatable
{
    use AuthenticatableTrait;

    public function getAuthIdentifierName()
    {
        return 'kd_karyawan'; // or 'kd_pelanggan'
    }

    public function getAuthIdentifier()
    {
        return $this->{ $this->getAuthIdentifierName() };
    }
}