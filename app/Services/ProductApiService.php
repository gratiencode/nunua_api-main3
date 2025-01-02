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
    protected $baseUrl, $allProductEndpoint, $measureProductEndpoint;

    public function __construct()
    {
        $this->baseUrl = 'https://app.kazisafe.com/v1/';
        $this->allProductEndpoint = 'produit/showall';
        $this->measureProductEndpoint = 'mesures/show/for/product/';
        $this->marqueProductEndpoint = 'marque/show/for/product/';

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
            $mesure_product = $this->measureProductEndpoint;

            foreach ($products as $product):
                $existingProduct = Produits::where('name_produit', $product['name'])
                    ->where('id_entrep', $entreprise->id)
                    ->first();

                if ($existingProduct) {
                    return ['error' => "Le produit " . $product['name'] . " existe déjà dans Nunua."];
                }

                // Fetch and save mesure data of the  selected products
                $id_mesure = null;
                $response_mesure = Http::withoutVerifying()
                    ->withToken($access_token)
                    ->accept('application/json')
                    ->get($baseUrl . $mesure_product . $product['id']);

                if ($response_mesure->successful()):
                    $mesures = $response_mesure->json();

                    foreach ($mesures as $mesure) {
                        $savedMesure = Mesure::firstOrCreate(
                            [
                                'name' => $mesure['description'],
                                'id_entrep' => $entreprise->id
                            ],
                            [
                                'id' => $mesure['uid'],
                                'name' => $mesure['description'],
                                'id_entrep' => $entreprise->id,
                            ]
                        );
                        // Assign the `id` of the last saved measure
                        $id_mesure = $savedMesure->id;
                    }

                    // Save product images
                    $images = UtilController::uploadMultipleImage($product['images'], '/uploads/products/');

                    $newProduct = $entreprise->produits()->create([
                        'name_produit' => $product['name'],
                        'description' => $product['description'],
                        'price' => $product['price'],
                        'image' => $images[0],
                        'qte' => $product['quantity'],
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
                if ($response_mesure->clientError() || $response_mesure->serverError()):
                    \Log::error('API Error: ', [
                        'status' => $response_mesure->status(),
                        'body' => $response_mesure->body(),
                        'headers' => $response_mesure->headers(),
                    ]);

                    return [
                        'status' => 'error',
                        'message' => $baseUrl . $mesure_product . ' API request failed with status: ' . $response_mesure->status(),
                        'details' => $response_mesure->json(),
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



