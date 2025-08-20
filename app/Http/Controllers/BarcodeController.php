<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;


class BarcodeController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->get();
        return view('barcode.index', compact('products'));
    }
    //ENDS

    public function sellerBarcode()
    {
        // if ($id != auth()->user()->id) {
        //     abort(403, 'Unauthorized access');
        // }
        // dd('ho');
        // dd(auth()->user()->id);
        $products = Product::where('user_id', operator: auth()->user()->id)->orderBy('created_at', 'desc')->get();
        // dd($products);
        return view('barcode.seller_barcode', compact('products'));
    }
}
