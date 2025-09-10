<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorHTML;

class BarcodeController extends Controller
{
    public function show()
    {
        $generator = new BarcodeGeneratorPNG();
        $image = $generator->getBarcode('000005263635', $generator::TYPE_CODE_128);

        return response($image)->header('Content-Type', 'image/png');
    }

    public function save()
    {
        $generator = new BarcodeGeneratorPNG();
        $image = $generator->getBarcode('000005263635', $generator::TYPE_CODE_128);

        $path = 'barcodes/demo-' . time() . '.png';
        Storage::put($path, $image);

        return response($image)->header('Content-Type', 'image/png');
    }

    public function blade(): View
    {
        $generator = new BarcodeGeneratorHTML();
        $barcode = $generator->getBarcode('0001245259636', $generator::TYPE_CODE_128);

        return view('barcode', compact('barcode'));
    }
} 