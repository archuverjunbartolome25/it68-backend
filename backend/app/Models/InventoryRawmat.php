<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryRawMat extends Model
{
    protected $table = 'inventory_rawmats';

    protected $fillable = [
        'item',
        'unit',
        'quantity',
        'quantity_pieces',
        'conversion',
        'low_stock_alert',
        'supplier_id',
        'unit_cost',
    ];

    public $timestamps = false;

    /**
     * ✅ Direct supplier linked via supplier_id
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * ✅ Many-to-many: rawmat ↔ suppliers (via supplier_offers)
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'supplier_offers', 'rawmat_id', 'supplier_id')
            ->withPivot('price', 'lead_time_days', 'notes');
    }

    /**
     * ✅ One-to-many: supplier offers
     */
    public function supplierOffers()
    {
        return $this->hasMany(SupplierOffer::class, 'rawmat_id');
    }
}
