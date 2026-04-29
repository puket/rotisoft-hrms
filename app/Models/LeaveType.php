<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenantable;

class LeaveType extends Model
{
    use Tenantable;
    protected $fillable = ['name', 'is_unpaid'];
}