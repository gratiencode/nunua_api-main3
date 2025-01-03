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
    protected $baseUrl, $allProductEndpoint, $measureProductEndpoint, $etatStockEndPoint, $salePriceRecquisition;

    public function __construct()
    {
        $this->baseUrl = 'https://app.kazisafe.com/v1/';
        $this->allProductEndpoint = 'produit/showall';
        $this->etatStockEndPoint = 'req/rem/inv/';
        $this->marqueProductEndpoint = 'marque/show/for/product/';
        $this->salePriceRecquisition = 'prices/for/recq/';
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

            \Log::info('Request URL', ['url' => $baseUrl . $product_endpoint]);
            \Log::info('Response Status', ['status' => $response->status()]);
            \Log::info('Response Headers', ['headers' => $response->headers()]);
            \Log::info('Response Body', ['body' => $response->body()]);

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
            $sale_price_endpoint = $this->salePriceRecquisition;

            foreach ($products as $product):
                $existingProduct = Produits::where('name_produit', $product['name'])
                    ->where('id_entrep', $entreprise->id)
                    ->first();

                if ($existingProduct) {
                    return ['error' => "Le produit " . $product['name'] . " existe déjà dans Nunua."];
                }

                // Fetch and save etat de staock data of the  selected products
                $id_mesure = null;
                $response_etat_stock = Http::withoutVerifying()
                    ->withToken($access_token)
                    ->accept('application/json')
                    ->get($baseUrl . $etat_stock_endpoint . $product['id']);

                if ($response_etat_stock ->successful()):
                    $etat_stock = $response_etat_stock->json();

                    //Fetch the sale price endpoint
                    $response_sale_price = Http::withoutVerifying()
                                            ->withToken($access_token)
                                            ->accept('application/json')
                                            ->get($baseUrl . $sale_price_endpoint . $etat_stock['currentReqUid']);

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

                    if($response_sale_price->successful()):
                        $sale_price = $response_sale_price->json();

                        $newProduct = $entreprise->produits()->create([
                            'name_produit' => $product['name'],
                            'description' => $product['description'],
                            'price' => $sale_price[0]['prixUnitaire'],
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
                endif;

                 // Handle client or server errors
                if ($response_etat_stock->clientError() || $response_etat_stock->serverError()):
                    return [
                        'status' => 'error',
                        'message' => $baseUrl . $etat_stock_endpoint . ' API request failed with status: ' . $response_etat_stock->status(),
                        'details' => $response_etat_stock->json(),
                    ];
                endif;

                if ($response_sale_price ->clientError() || $response_sale_price->serverError()):
                    return [
                        'status' => 'error',
                        'message' => $baseUrl . $sale_price_endpoint . ' API request failed with status: ' . $response_sale_price->status(),
                        'details' => $response_sale_price->json(),
                    ];
                endif;

            endforeach;
            
            return ['success' => 'Produits enregistrés avec succès.', 'data' => $savedProducts];
        } catch (Exception $e) {
            Log::error('Error saving products: ' . $e->getMessage());
            return ['error' => 'Une erreur inattendue s\'est produite. ' . $e->getMessage()];
        }
    }

}



