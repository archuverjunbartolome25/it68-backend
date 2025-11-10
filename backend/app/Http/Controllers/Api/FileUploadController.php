<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // 5 MB limit
            'purchase_receipt_id' => 'nullable|integer|exists:purchase_receipts,id',
        ]);

        // Store file in public/uploads/receipts
        $path = $request->file('file')->store('uploads/receipts', 'public');

        // Optionally link to purchase_receipts table
        if ($request->purchase_receipt_id) {
            DB::table('purchase_receipts')
                ->where('id', $request->purchase_receipt_id)
                ->update(['image_path' => $path]);
        }

        return response()->json([
            'message' => 'File uploaded successfully!',
            'file_url' => asset('storage/' . $path),
        ]);
    }
}
