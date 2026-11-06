<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // GET: Menampilkan semua produk, pencarian, filter, dan pagination
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Pencarian berdasarkan nama produk
        $keyword = $request->input(
            'keyword',
            $request->input('search')
        );

        if ($keyword) {
            $query->where(
                'name',
                'like',
                '%' . $keyword . '%'
            );
        }

        // Filter berdasarkan kategori
        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        // Pagination
        $perPage = $request->input(
            'limit',
            $request->input('per_page', 10)
        );

        // Membatasi jumlah data per halaman
        $perPage = max(1, min((int) $perPage, 100));

        return ProductResource::collection(
            $query->paginate($perPage)
        );
    }

    // POST: Menambahkan produk
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,category_id',
            'price' => 'required|numeric|min:0',
            'img' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'stock' => 'required|integer|min:0',
            'slug' => 'required|string|unique:products,slug',
        ]);

        if ($request->hasFile('img')) {
            $validated['img'] = $request->file('img')
                ->store('products', 'public');
        }

        $product = Product::create($validated);

        return new ProductResource($product);
    }

    // GET: Menampilkan detail satu produk
    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);

        return new ProductResource($product);
    }

    // PUT/PATCH: Mengubah produk
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'description' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,category_id',
            'price' => 'sometimes|required|numeric|min:0',
            'img' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'stock' => 'sometimes|required|integer|min:0',
            'slug' => 'sometimes|required|string|unique:products,slug,' . $product->product_id . ',product_id',
        ]);

        if ($request->hasFile('img')) {
            if ($product->img) {
                Storage::disk('public')->delete($product->img);
            }

            $validated['img'] = $request->file('img')
                ->store('products', 'public');
        } else {
            unset($validated['img']);
        }

        $product->update($validated);

        return new ProductResource($product);
    }

    // DELETE: Menghapus produk
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        if ($product->img) {
            Storage::disk('public')->delete($product->img);
        }

        $product->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus',
        ]);
    }
}
