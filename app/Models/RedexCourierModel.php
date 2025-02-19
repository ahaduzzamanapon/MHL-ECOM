<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedexCourierModel extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_id',
        'tracking_id',
        'invoice',
        'customer_name',
        'customer_phone',
        'delivery_area',
        'customer_address',
    ];
}
