<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Package extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'customer_name',
        'phone_number',
        'mailbox_number',
        'num_packages',
        'tracking_number',
        'status',
    ];
}
