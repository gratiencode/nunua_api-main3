<?php

namespace App\Http\Controllers\api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductApiService;

class ProductController extends Controller
{
    protected $productApiService;

    public function __construct(ProductApiService $productApiService)
    {
        $this->productApiService = $productApiService;
    }

    public function showAllProducts()
    {
        $products = $this->productApiService->getProducts();

        return response()->json($products);
    }
}
