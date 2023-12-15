<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    use HasFactory;
    protected $table = 'books';
    protected $primaryKey = 'id_buku';
    protected $fillable = [
        'id_buku',
        'judul_buku',
        'penulis_buku',
        'tahun_buku',
        'stock_buku',
    ];
    
    public function BooksTransaction()
    {
        return $this->hasMany(BooksTransaction::class, 'id_buku');
    }
}
