<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

abstract class TenantModel extends Model
{
    // Force every tenant‑scoped model to use the 'tenant' connection
    // protected $connection = 'tenant';
}