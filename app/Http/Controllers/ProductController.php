<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();

                $folderPath = public_path('images/products');
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0755, true);
                }

                $image->move($folderPath, $imageName);
                $imagePath = 'images/products/' . $imageName;
            }

            $product = Product::create([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'status' => $request->status ?? 0,
                'image' => $imagePath,
            ]);

            DB::commit();

            return response()->json($product, 201);
        } catch (\Exception $e) {

            DB::rollBack();

            if (isset($imagePath) && File::exists(public_path($imagePath))) {
                File::delete(public_path($imagePath));
            }

            return response()->json([
                'message' => 'Failed to create product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'sometimes|required',
            'price' => 'sometimes|required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->all();
            $oldImagePath = $product->image;

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();

                $folderPath = public_path('images/products');
                if (!File::exists($folderPath)) {
                    File::makeDirectory($folderPath, 0755, true);
                }

                $image->move($folderPath, $imageName);
                $data['image'] = 'images/products/' . $imageName;

                if ($oldImagePath && File::exists(public_path($oldImagePath))) {
                    File::delete(public_path($oldImagePath));
                }
            }

            $product->update($data);

            DB::commit();

            return response()->json($product);
        } catch (\Exception $e) {
            DB::rollBack();


            if (isset($data['image']) && File::exists(public_path($data['image']))) {
                File::delete(public_path($data['image']));
            }

            return response()->json([
                'message' => 'Failed to update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try {
            if ($product->image && File::exists(public_path($product->image))) {
                File::delete(public_path($product->image));
            }
            $product->delete();

            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to delete product',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
