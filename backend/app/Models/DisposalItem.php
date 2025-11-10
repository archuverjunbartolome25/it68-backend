<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'disposal_id', // foreign key
        'item_type',
        'item',
        'quantity',
    ];

    public function disposal()
    {
        return $this->belongsTo(Disposal::class);
    }
}
