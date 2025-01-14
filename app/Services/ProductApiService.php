<?php

namespace App\Services;

use Exception;
use App\Models\Marque;
use App\Models\Mesure;
use App\Models\Produits;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\api\UtilController;

class ProductApiService
{
    protected $baseUrl, $allProductEndpoint, $measureProductEndpoint, $etatStockEndPoint, $salePriceRecquisitionEndpoint;

    public function __construct()
    {
        $this->baseUrl = 'https://app.kazisafe.com/v1/';
        $this->allProductEndpoint = 'produit/showall';
        $this->etatStockEndPoint = 'req/rem/inv/';
        $this->marqueProductEndpoint = 'marque/show/for/product/';
        $this->salePriceRecquisitionEndpoint = 'prices/for/recq/';
    }
    /**
     * Summary of getKaziSafeProducts
     * @param mixed $access_token
     * @return mixed
     */
    public function getKaziSafeProducts($access_token)
    {
        $baseUrl = $this->baseUrl;
        $product_endpoint = $this->allProductEndpoint;

        try {
            // Make the GET request
            $response = Http::withoutVerifying()
                ->timeout(60)
                ->withToken($access_token)
                ->accept('application/json')
                ->get($baseUrl . $product_endpoint);

            // Check if the response is successful
            if ($response->successful()) {
                return $response->json();
            }

            // Handle client or server errors
            if ($response->clientError() || $response->serverError()) {
                \Log::error('API Error: ', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'headers' => $response->headers(),
                ]);

                return [
                    'status' => 'error',
                    'message' => $baseUrl . $product_endpoint . ' API request failed with status: ' . $response->status(),
                    'details' => $response->json(),
                ];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Connection Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'Connection timeout or unreachable server.',
            ];
        } catch (Exception $e) {
            \Log::error('General Exception', ['message' => $e->getMessage()]);
            return [
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * @param mixed @access_token
     * @param mixed @product id
     */

    public function getKaziSafeSalesPrice($access_token, $product_id)
    {
        $baseUrl = $this->baseUrl;
        $sale_price_endpoint = $this->salePriceRecquisitionEndpoint;
        $etat_stock_endpoint = $this->etatStockEndPoint;

        try {
            $response_etat_stock = Http::withoutVerifying()
                    ->withToken($access_token)
                    ->accept('application/json')
                    ->get($baseUrl . $etat_stock_endpoint . $product_id);
                    
                // Check if the response is successful
                if ($response_etat_stock ->successful()):
                    $etat_stock = $response_etat_stock->json();

                    // Make the GET request to retrieve sales prices 
                    $response_sale_prices = Http::withoutVerifying()
                        ->timeout(60)
                        ->withToken($access_token)
                        ->accept('application/json')
                        ->get($baseUrl . $sale_price_endpoint . $etat_stock['currentReqUid']);

                    // Check if the response is successful
                    if ($response_sale_prices->successful()) {
                        return $response_sale_prices->json();
                    }
                endif;

            // Handle client or server errors
            if ($response_etat_stock->clientError() || $response_etat_stock->serverError()) {
                
                return [
                    'status' => 'error',
                    'message' => $baseUrl . $etat_stock_endpoint . ' API request failed with status: ' . $response_etat_stock->status(),
                    'details' => $response_etat_stock->json(),
                ];
            }

            if ($response_sale_prices->clientError() || $response_sale_prices->serverError()) {
                
                return [
                    'status' => 'error',
                    'message' => $baseUrl . $sale_price_endpoint . ' API request failed with status: ' . $response_sale_prices->status(),
                    'details' => $response_sale_prices->json(),
                ];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return [
                'status' => 'error',
                'message' => 'Connection timeout or unreachable server.',
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Summary of saveKazisafeProductInNunua
     * @param array $products
     * @param mixed $entreprise
     * @param mixed $access_token
     * @return array
     */
    public function saveKazisafeProductInNunua(array $products, $entreprise, $access_token)
    {
        try {
            $savedProducts = [];
            $baseUrl = $this->baseUrl;
            $etat_stock_endpoint = $this->etatStockEndPoint;

            foreach ($products as $product):
                $existingProduct = Produits::where('name_produit', $product['name'])
                    ->where('id_entrep', $entreprise->id)
                    ->first();

                if ($existingProduct) {
                    return ['error' => "Le produit " . $product['name'] . " existe déjà dans Nunua."];
                }

                // Fetch and save etat de stock data of the  selected products
                $id_mesure = null;
                $response_etat_stock = Http::withoutVerifying()
                    ->withToken($access_token)
                    ->accept('application/json')
                    ->get($baseUrl . $etat_stock_endpoint . $product['id']);

                if ($response_etat_stock ->successful()):
                    $etat_stock = $response_etat_stock->json();
                    
                    $savedEtatStock = Mesure::firstOrCreate(
                        [
                            'name' => $etat_stock['mesure']['description'],
                            'id_entrep' => $entreprise->id
                        ],
                        [
                            'id' => $etat_stock['mesure']['uid'],
                            'name' => $etat_stock['mesure']['description'],
                            'id_entrep' => $entreprise->id,
                        ]
                    );
                    // Assign the `id` of the last saved measure
                    $id_mesure = $savedEtatStock->id;

                    // Save product images
                    $images = UtilController::uploadMultipleImage($product['images'], '/uploads/products/');

                    $newProduct = $entreprise->produits()->create([
                        'name_produit' => $product['name'],
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'image' => $images[0],
                        'qte' => $etat_stock['quantStock'],
                        'id_mesure' => $id_mesure,
                        'id_marque' => $product['id_marque'],
                        'id_category' => $product['id_category'],
                        'price_red' => $product['price_red']
                    ]);

                    // Create product images
                    foreach ($images as $image) {
                        $newProduct->images()->create([
                            'images' => $image,
                        ]);
                    }
                    $savedProducts[] = $newProduct;
                endif;

                 // Handle client or server errors
                if ($response_etat_stock->clientError() || $response_etat_stock->serverError()):
                    return [
                        'status' => 'error',
                        'message' => $baseUrl . $etat_stock_endpoint . ' API request failed with status: ' . $response_etat_stock->status(),
                        'details' => $response_etat_stock->json(),
                    ];
                endif;

            endforeach;
            
            return ['success' => 'Produits enregistrés avec succès.', 'data' => $savedProducts];
        } catch (Exception $e) {
            Log::error('Error saving products: ' . $e->getMessage());
            return ['error' => 'Une erreur inattendue s\'est produite. ' . $e->getMessage()];
        }
    }

    // Search for a product
    public function searchProductKazisafe($access_token, $keyword){
        try {
            $baseUrl = $this->baseUrl;
            $product_endpoint = $this->allProductEndpoint;

            try {
                // call kazisafe product before making a search
                $all_products = Http::withoutVerifying()
                    ->timeout(60)
                    ->withToken($access_token)
                    ->accept('application/json')
                    ->get($baseUrl . $product_endpoint);
    
                // Check if the response is successful
                if ($all_products->successful()) {
                    $products = $all_products->json();
                
                    // Filter the products array using array_filter
                    $produits = array_filter($products, function ($product) use ($keyword) {
                        return stripos($product['nomProduit'], $keyword) !== false;
                    });
                
                    // Check if any products were found
                    if (empty($produits)) {
                        return response()->json([
                            'message' => 'Aucun produit trouvé qui correspond à ' . $keyword,
                        ], 400);
                    }
                
                    return $produits;
                }
    
                // Handle client or server errors
                if ($all_products->clientError() || $all_products->serverError()) {
                    \Log::error('API Error: ', [
                        'status' => $all_products->status(),
                        'body' => $all_products->body(),
                        'headers' => $all_products->headers(),
                    ]);
    
                    return [
                        'status' => 'error',
                        'message' => $baseUrl . $product_endpoint . ' API request failed with status: ' . $all_products->status(),
                        'details' => $all_products->json(),
                    ];
                }
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                \Log::error('Connection Exception', ['message' => $e->getMessage()]);
                return [
                    'status' => 'error',
                    'message' => 'Connection timeout or unreachable server.',
                ];
            } catch (Exception $e) {
                \Log::error('General Exception', ['message' => $e->getMessage()]);
                return [
                    'status' => 'error',
                    'message' => 'An unexpected error occurred: ' . $e->getMessage(),
                ];
            }
            
        } catch (Exception $e) {
            return response()->json(['message' => 'Error occurred while searching: ' . $e->getMessage(), 'status' => 500]);
        }
    }
}



