<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransectionChack extends Model
{
    use HasFactory;
       protected $fillable = ['transaction_id', 'order','success_url','if_old'];
}
