<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    // ✅ Add 'low_stock_alert' here
    protected $fillable = ['item', 'unit', 'quantity', 'low_stock_alert'];

    public $timestamps = true; 
}
