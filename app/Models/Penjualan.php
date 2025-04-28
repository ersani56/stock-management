<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'jumlah',
        'total_harga',
    ];

    protected static function booted()
    {
        static::creating(function ($penjualan) {
            $barang = $penjualan->barang;
            $penjualan->total_harga = $barang->harga_jual * $penjualan->jumlah;
        });

        static::created(function ($penjualan) {
            $barang = $penjualan->barang;
            $barang->stok -= $penjualan->jumlah;
            $barang->save();
        });
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
