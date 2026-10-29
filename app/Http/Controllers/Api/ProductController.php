<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return ProductResource::collection(
            Product::with('category')->get()
        );
    }

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

    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);

        return new ProductResource($product);
    }

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
