<?php

namespace App\Http\Controllers\api;
use App\Models\Produits;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Services\ProductApiService;
use App\Http\Controllers\Controller;
use Exception;

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

    public function salePrices(Request $request)
    {
        try{
            $products = $this->productApiService->getKaziSafeSalesPrice($request->access_token, $request->product_id);
           return response()->json($products);
        }catch(Exception $e){
            return response()->json(["error" => $e->getMessage()]);
        }
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
            'products.*.price_red'   => 'nullable|numeric',
        ]);

        $entreprise = Entreprise::findOrFail($request->entrepriseId);
        $result = $this->productApiService->saveKazisafeProductInNunua( $request->products, $entreprise, $request->access_token);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }
        return response()->json(['success' => $result['success'], 'data' => $result['data']]);
    }

    public function searchProduct(Request $request){
        try {
            
            $products = $this->productApiService->searchProductKazisafe($request->access_token, $request->keyword);

            return response()->json(['data'=> $products], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error occurred while searching: ' . $e->getMessage(), 'status' => 500]);
        }
    }
}



