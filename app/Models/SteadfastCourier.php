<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SteadfastCourier extends Model
{
    use HasFactory;
    protected $fillable = [
        'consignment_id',
        'invoice',
        'tracking_code',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'cod_amount',
        'status',
        'note',
    ];
}
