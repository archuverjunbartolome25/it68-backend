<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryActivityLog extends Model
{
    protected $table = 'inventory_activity_logs'; // table name
    protected $fillable = [
        'employee_id',
        'module',
        'type',
        'item_name',
        'quantity',
        'processed_at',
    ];

    public $timestamps = true; // if you have created_at & updated_at
}
