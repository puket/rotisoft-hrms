<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenantable;


class Holiday extends Model
{
    use Tenantable;
    protected $fillable = [
        'date',
        'name',
        'description'
    ];
}