<?php

namespace Modules\SupportTicket\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\SupportTicket\Database\factories\DepartmentFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name','status'];
    protected $casts = ['status'=>'integer'];

    protected static function newFactory(): DepartmentFactory
    {

    }
}
