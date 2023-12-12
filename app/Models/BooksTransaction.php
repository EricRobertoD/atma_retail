<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BooksTransaction extends Model
{
    use HasFactory;
    protected $table = 'books_transaction';
    protected $primaryKey = 'id_peminjaman';
    protected $fillable = [
        'id_peminjaman',
        'id_user',
        'id_buku',
        'tanggal_pinjam',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function book()
    {
        return $this->belongsTo(Books::class, 'id_buku');
    }
}
