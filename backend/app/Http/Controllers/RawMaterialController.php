<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryRawmat;

class RawMaterialController extends Controller
{
    public function index()
    {
        return InventoryRawmat::all();
    }
}
