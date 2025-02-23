<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PathaoCourier extends Model
{
    use HasFactory;
    protected $fillable = [
        'store_id',
        'merchant_order_id',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'recipient_city',
        'recipient_zone',
        'recipient_area',
        'delivery_type',
        'item_type',
        'special_instruction',
        'item_quantity',
        'item_weight',
        'item_description',
        'amount_to_collect',
        'consignment_id',
        'order_status',
        'delivery_fee',
    ];
}
