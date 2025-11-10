<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnToVendor extends Model
{
    use HasFactory;

    protected $table = 'return_to_vendor';

    protected $fillable = [
        'rtv_number',
        'customer_id',
        'location',
        'date_ordered',
        'date_returned',
        'qty_350ml',
        'qty_500ml',
        'qty_1l',
        'qty_6l',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
