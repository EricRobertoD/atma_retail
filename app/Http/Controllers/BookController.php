<?php

namespace App\Http\Controllers;

use App\Models\Books;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function index(){
        $books = Books::all();

        if(count($books) > 0){
            return response([
                'data' => $books
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400); 
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'judul_buku' => 'required',
            'penulis_buku' => 'required',
            'tahun_buku' => 'required',
            'stock_buku' => 'required',
        ]);
        

        if ($validator->fails()) {
            return response([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }

        $books = Books::create([
            'judul_buku' => $request->input('judul_buku'),
            'penulis_buku' => $request->input('penulis_buku'),
            'tahun_buku' => $request->input('tahun_buku'),
            'stock_buku' => $request->input('stock_buku'),
        ]);

        return response([
            'status' => 'success',
            'message' => 'Buku created successfully',
            'data' => $books
        ], 201);
    }

    public function update(Request $request, Books $books) {
        $id = $books->id_buku;
    
        $books->update([
            'judul_buku' => $request->input('judul_buku'),
            'penulis_buku' => $request->input('penulis_buku'),
            'tahun_buku' => $request->input('tahun_buku'),
            'stock_buku' => $request->input('stock_buku'),
        ]);
    
        // Reload the model instance after the update
        $books->refresh();
    
        return response([
            'status' => 'success',
            'message' => 'Buku updated successfully',
            'data' => $books
        ], 200);
    }
    

    public function destroy(Books $books){
        $books->delete();
    
        return response([
            'status' => 'success',
            'message' => 'Buku deleted successfully',
            'data' => $books
        ], 200);
    }
}
