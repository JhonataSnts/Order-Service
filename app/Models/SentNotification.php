<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SentNotification extends Model
{
    protected $fillable = [
        'order_id',
        'status',
    ];
}
