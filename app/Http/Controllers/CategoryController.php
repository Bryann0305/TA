<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Kategori::all();
        return view('category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'Nama_Kategori' => 'required|string|max:100|unique:kategori,Nama_Kategori',
        ]);

        Kategori::create($validated);

        return redirect()->route('category.index')
                         ->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Kategori::with('barang')->findOrFail($id);
        return view('category.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Kategori::findOrFail($id);
        return view('category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Kategori::findOrFail($id);
        
        $validated = $request->validate([
            'Nama_Kategori' => 'required|string|max:100|unique:kategori,Nama_Kategori,' . $id . ',Id_Kategori',
        ]);

        $category->update($validated);

        return redirect()->route('category.index')
                         ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Kategori::findOrFail($id);
        
        // Cek apakah kategori memiliki barang
        if ($category->barang()->count() > 0) {
            return redirect()->route('category.index')
                             ->with('error', 'Tidak dapat menghapus kategori yang memiliki barang!');
        }

        $category->delete();

        return redirect()->route('category.index')
                         ->with('success', 'Kategori berhasil dihapus!');
    }
}
