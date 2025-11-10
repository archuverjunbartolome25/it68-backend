<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposalProduct extends Model
{
    use HasFactory;

    protected $table = 'disposal_products';

    protected $fillable = [
        'disposal_date',
        'item_type',   // Added to store Finished Goods / Raw Materials
        'item',
        'quantity',
        'employee_id',
    ];
}
