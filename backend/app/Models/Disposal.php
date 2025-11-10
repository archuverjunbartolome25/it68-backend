<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DisposalItem; // ✅ Import it here

class Disposal extends Model
{
    use HasFactory;
    protected $table = 'disposals';
    protected $fillable = [
        'disposal_number',
        'disposal_date',
        'employee_id',
        'item_type',
        'item',
        'quantity',
        'status',
        'disposed_date',
        'disposed_time',
        'reason',
    ];
//     public function items()
// {
//     return $this->hasMany(DisposalItem::class);
// }

}
