<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductApiService;
use App\Models\Entreprise;

class KazisafeProductController extends Controller
{
    protected $productApiService;

    public function __construct(ProductApiService $productApiService)
    {
        $this->productApiService = $productApiService;
    }

    public function showAllProducts(Request $request)
    {
        $products = $this->productApiService->getKaziSafeProducts($request->access_token);

        return response()->json($products);
    }

    public function saveKaziSafeProduct(Request $request)
    {
        // Validate the request
        $request->validate([
            'entrepriseId'           => 'required',
            'products'               => 'required|array',
            'products.*.id'          => 'required|string',
            'products.*.name'        => 'required|string',
            'products.*.description' => 'required|string',
            'products.*.price'       => 'required|numeric',
            // 'products.*.images' => 'required|array',
            'products.*.id_marque'   => 'required',
            'products.*.id_category' => 'required',
            'products.*.quantity'    => 'required|integer',
            'products.*.price_red'   => 'nullable|numeric',
        ]);

        $entreprise = Entreprise::findOrFail($request->entrepriseId);
        $result = $this->productApiService->saveKazisafeProductInNunua( $request->products, $entreprise, $request->access_token);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json(['success' => $result['success'], 'data' => $result['data']]);
    }
}



