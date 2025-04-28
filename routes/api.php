<?php

use App\Models\Barang;
Route::get('/barang-by-kode/{kode}', function ($kode) {
    $barang = Barang::where('kode_barang', $kode)->first();
    return response()->json($barang);
});
