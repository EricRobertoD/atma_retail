<?php

namespace App\Http\Controllers;

use App\Models\Books;
use App\Models\BooksTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookTransactionController extends Controller
{
    public function index(){
            $id = auth()->user()->id;
            $booksTransaction = BooksTransaction::where('id_user', $id)->get();
    
            if(count($booksTransaction) > 0){
                return response([
                    'status' => 'success',
                    'data' => $booksTransaction
                ], 200);
            }
    
        return response([
            'status' => 'error',
            'message' => 'Empty',
            'data' => null
        ], 400); 
    }
    
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'id_user' => 'required',
            'id_buku' => 'required',
            'tanggal_pinjam' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $book = Books::findOrFail($request->input('id_buku'));

        if ($book->stock_buku <= 0) {
            return response([
                'message' => 'Not enough stock available for the book',
            ], 400);
        }

        $booksTransaction = BooksTransaction::create([
            'id_user' => $request->input('id_user'),
            'id_buku' => $request->input('id_buku'),
            'tanggal_pinjam' => $request->input('tanggal_pinjam'),
            'status' => "Sedang Dipinjam",
        ]);

        $book->decrement('stock_buku');

        return response([
            'status' => 'success',
            'message' => 'Transaksi Buku created successfully',
            'data' => $booksTransaction,
        ], 201);
    }

    public function pengembalian(BooksTransaction $booksTransaction){
        $book = Books::findOrFail($booksTransaction->id_buku);
        $book->increment('stock_buku');

        $booksTransaction->update(['status' => 'Dikembalikan']);

        return response([
            'status' => 'success',
            'message' => 'Buku dikembalikan successfully',
            'data' => $booksTransaction
        ], 200);
    }

    public function destroy(BooksTransaction $booksTransaction){
        $booksTransaction->delete();
    
        return response([
            'status' => 'success',
            'message' => 'Transaksi Buku deleted successfully',
            'data' => $booksTransaction
        ], 200);
    }
}
