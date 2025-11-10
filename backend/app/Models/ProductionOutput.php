<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOutput extends Model
{
    use HasFactory;

    protected $table = 'production_outputs';

    protected $fillable = [
        'employee_id',
        'product_name',
        'quantity',
        'qty_350ml',
        'qty_500ml',
        'qty_1l',
        'qty_6l',
        'batch_number',
        'production_date',
    ];
}
