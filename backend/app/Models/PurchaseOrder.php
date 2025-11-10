<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    public function items()
{
    return $this->hasMany(PurchaseOrderItem::class);
}
    protected $fillable = [
        'employee_id',
        'po_number',
        'supplier_name',
        'order_date',
        'expected_date',
        'status',
        'amount'
    ];
}
