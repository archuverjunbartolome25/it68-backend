<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    // Add the $fillable property to allow mass assignment
    protected $fillable = [
        'name',
        'billing_address',
        'shipping_address',
        'bank_details',
        'tin',  
        'status', 
    ];
}