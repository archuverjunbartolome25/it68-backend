<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'contact_person', 'email', 'phone', 'address', 'tin'];
    public function offers()
{
    return $this->hasMany(SupplierOffer::class);
}

public function rawMaterials()
{
    return $this->belongsToMany(InventoryRawmat::class, 'supplier_offers', 'supplier_id', 'rawmat_id')
                ->withPivot('price', 'lead_time_days', 'notes');
}

}
