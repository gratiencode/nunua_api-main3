<?php

namespace App\Http\Controllers\api;

use App\Models\Engagement;
use App\Models\Entreprise;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\api\UtilController;
use App\Models\Category;
use App\Models\Produits;

class ProduitsController extends Controller
{
    public function create(Request $request, $entreprise)
    {
        $entreprises = Entreprise::find($entreprise);
        if ($entreprises) {
            $request->validate([
                "name" => "required",
                "id_mesure" => "required",
                "images" => "required|array",
                "description" => "required",
                "price" => "required",
                "qte" => "required",
                "price_red" => "required",
                "id_category" => "required"
            ]);
            if (Auth::user()->Is(['Vendor'])) {
                $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $entreprises->id)->where('status', 1)->first();
                if ($engagement) {
                    if ($engagement->can('create_product')) {

                        $images = UtilController::uploadMultipleImage($request->images, "/uploads/products/");
                        $product = $entreprises->produits()->create([
                            "name_produit" => $request->name,
                            "id_mesure" => $request->id_mesure,
                            "image" => $images[0],
                            "description" => $request->description,
                            "price" => $request->price,
                            "qte" => $request->qte,
                            "price_red" => $request->price_red,
                            "id_category" => $request->id_category,
                            "id_marque" => $request->id_marque ? $request->id_marque : NULL
                        ]);
                        foreach ($images as $image) {
                            $product->images()->create([
                                "images" => $image
                            ]);
                        }
                        return response()->json([
                            "message" => 'Produit crée'
                        ], 200);
                    } else {
                        return response()->json([
                            "message" => "Vous ne pouvez pas éffectuer cette action"
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "Vous ne pouvez pas éffectuer cette action"
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action"
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "id not found"
            ], 404);
        }
    }
    public function update(Request $request, $entreprise, $id)
    {
        $entreprises = Entreprise::find($entreprise);
        if ($entreprises) {
            $request->validate([
                "name" => "required",
                "id_mesure" => "required",
                "images" => "nullable|array",
                "description" => "required",
                "price" => "required",
                "qte" => "required",
                "price_red" => "required",
                "id_category" => "required"
            ]);
            $produit = $entreprises->produits()->where('deleted', 0)->find($id);
            if ($produit) {
                if (Auth::user()->Is(['Vendor'])) {
                    $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $entreprises->id)->where('status', 1)->first();
                    if ($engagement) {
                        if ($engagement->can('update_product')) {



                            $produit->name_produit = $request->name;
                            $produit->id_mesure = $request->id_mesure;
                            $produit->description = $request->description;
                            $produit->price = $request->price;
                            $produit->qte = $request->qte;
                            $produit->price_red = $request->price_red;
                            $produit->id_category = $request->id_category;
                            $produit->id_marque = $request->id_marque ? $request->id_marque : NULL;
                            $produit->save();
                            if ($request->images) {
                                $images = UtilController::uploadMultipleImage($request->images, "/uploads/products/");
                                foreach ($images as $image) {
                                    $produit->images()->create([
                                        "images" => $image
                                    ]);
                                }
                            }



                            return response()->json([
                                "message" => 'Produit modifié'
                            ], 200);
                        } else {
                            return response()->json([
                                "message" => "Vous ne pouvez pas éffectuer cette action"
                            ], 402);
                        }
                    } else {
                        return response()->json([
                            "message" => "Vous ne pouvez pas éffectuer cette action"
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "Vous ne pouvez pas éffectuer cette action"
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "id product not found"
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "id not found"
            ], 404);
        }
    }
    public function delete(Request $request, $entreprise, $id)
    {
        $entreprises = Entreprise::find($entreprise);
        if ($entreprises) {
            if (Auth::user()->Is(['Vendor'])) {
                $engagement = Engagement::where('id_user', Auth::user()->id)->where('id_entrep', $entreprises->id)->where('status', 1)->first();
                if ($engagement) {
                    if ($engagement->can('delete_product')) {
                        $prod = $entreprises->produits()->where('deleted', 0)->find($id);
                        if ($prod) {
                            //UtilController::removeMultipleImage($prod->images, "/uploads/products/");
                            $prod->deleted = 1;
                            $prod->save();
                            return response()->json([
                                "message" => "Produit supprimé"
                            ], 200);
                        } else {
                            return response()->json([
                                "message" => "id produit not found"
                            ], 404);
                        }
                    } else {
                        return response()->json([
                            "message" => "Vous ne pouvez pas éffectuer cette action"
                        ], 402);
                    }
                } else {
                    return response()->json([
                        "message" => "Vous ne pouvez pas éffectuer cette action"
                    ], 402);
                }
            } else {
                return response()->json([
                    "message" => "Vous ne pouvez pas éffectuer cette action"
                ], 402);
            }
        } else {
            return response()->json([
                "message" => "id not found"
            ], 404);
        }
    }

    public function productList()
    {
        $products = Produits::with(['images', 'entreprise'])->where('t_produits.status', 1)->where('t_produits.deleted', 0)
            ->leftJoin('t_entreprise', 't_produits.id_entrep', '=', 't_entreprise.id')
            ->leftJoin('t_categorie', 't_produits.id_category', '=', 't_categorie.id')
            ->leftJoin('t_mesure', 't_produits.id_mesure', '=', 't_mesure.id')
            ->where('t_entreprise.status', 1)
            ->select(
                't_produits.id',
                't_produits.id_entrep',
                't_produits.name_produit',
                't_produits.description',
                't_produits.image',
                't_produits.price',
                't_mesure.name AS mesure',
                't_produits.price_red',
                't_produits.etat_top',
                't_produits.qte',
                't_categorie.name AS name_sous',
                't_categorie.id AS id_cat_sous'
            )
            ->get();
        //->leftjoin('vendors', 'products.vendor_id', '=', 'vendors.id')->where('vendors.isActive', 1)->where('vendors.validated', 1)->inRandomOrder()->get('products.*');
        return response()->json([
            "message" => "success",
            "code" => 200,
            "data" => $products
        ], 200);
    }

    public function productDetail($id)
    {
        $pr = Produits::with('images', 'entreprise')->where('deleted', 0)->find($id);
        if ($pr) {
            $products = Produits::with(['images', 'entreprise'])->where('t_produits.deleted', 0)
                ->leftJoin('t_entreprise', 't_produits.id_entrep', '=', 't_entreprise.id')
                ->leftJoin('t_categorie', 't_produits.id_category', '=', 't_categorie.id')
                ->leftJoin('t_mesure', 't_produits.id_mesure', '=', 't_mesure.id')
                ->where('t_produits.id', $id)
                ->where('t_entreprise.status', 1)
                ->select(
                    't_produits.id',
                    't_produits.id_entrep',
                    't_produits.name_produit',
                     't_produits.status',
                    't_produits.name_produit AS name',
                    't_produits.description',
                    't_produits.image',
                    't_produits.price',
                    't_produits.id_category',
                    't_mesure.name AS mesure',
                    't_produits.id_marque',
                        't_produits.id_mesure',
                    't_produits.price_red',
                    't_produits.etat_top',
                    't_produits.qte',
                    't_categorie.name AS name_sous',
                    't_categorie.id AS id_cat_sous'
                )->first();
            $related = Produits::with(['entreprise', 'images'])->where('t_produits.status', 1)->where('t_produits.deleted', 0)
                ->leftJoin('t_entreprise', 't_produits.id_entrep', '=', 't_entreprise.id')
                ->leftJoin('t_categorie', 't_produits.id_category', '=', 't_categorie.id')
                ->leftJoin('t_mesure', 't_produits.id_mesure', '=', 't_mesure.id')
                ->where('t_produits.id', '!=', $id)
                ->where('t_produits.id_category', $products->id_category)
                ->where('t_entreprise.status', 1)
                ->select(
                    't_produits.id',
                    't_produits.id_entrep',
                    't_produits.name_produit',
                    't_produits.description',
                    't_produits.image',
                    't_produits.price',
                    't_produits.price_red',
                    't_mesure.name AS mesure',
                    't_produits.etat_top',
                    't_produits.qte',
                    't_categorie.name AS name_sous',
                    't_categorie.id AS id_cat_sous'
                )
                ->get();
            //->leftjoin('vendors', 'products.vendor_id', '=', 'vendors.id')->where('vendors.isActive', 1)->where('vendors.validated', 1)->inRandomOrder()->get('products.*');

            if ($products) {
                return response()->json([
                    "message" => "success",
                    "code" => 200,
                    "data" => [
                        "Detail_Product" => $products,
                        "ImageProduit" => $products->images,
                        "Produit_Meme_cate" => $related
                    ]
                ], 200);
            } else {
                return response()->json([
                    "message" => "id not found"
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "id not found"
            ], 404);
        }
    }

    public function topSellerDiscount()
    {
        $discount = Produits::with(['images', 'entreprise'])->where('t_produits.status', 1)->whereColumn("t_produits.price_red", "<", "t_produits.price")->where('t_produits.deleted', 0)
            ->leftJoin('t_entreprise', 't_produits.id_entrep', '=', 't_entreprise.id')
            ->leftJoin('t_categorie', 't_produits.id_category', '=', 't_categorie.id')
            ->leftJoin('t_mesure', 't_produits.id_mesure', '=', 't_mesure.id')
            ->where('t_entreprise.status', 1)
            ->select(
                't_produits.id',
                't_produits.id_entrep',
                't_produits.name_produit',
                't_produits.description',
                't_produits.image',
                't_produits.price',
                't_produits.price_red',
                't_produits.etat_top',
                't_mesure.name AS mesure',
                't_produits.qte',
                't_categorie.name AS name_sous',
                't_categorie.id AS id_cat_sous'
            )
            ->inRandomOrder()
            ->get();
        $top = Produits::with(['entreprise', 'images'])->where('t_produits.status', 1)->where('t_produits.deleted', 0)
            ->leftJoin('t_entreprise', 't_produits.id_entrep', '=', 't_entreprise.id')
            ->leftJoin('t_categorie', 't_produits.id_category', '=', 't_categorie.id')
            ->leftJoin('t_mesure', 't_produits.id_mesure', '=', 't_mesure.id')
            ->where('t_entreprise.status', 1)
            ->where('t_produits.etat_top', 1)
            ->select(
                't_produits.id',
                't_produits.id_entrep',
                't_produits.name_produit',
                't_produits.description',
                't_produits.image',
                't_produits.price',
                't_produits.price_red',
                't_mesure.name AS mesure',
                't_produits.etat_top',
                't_produits.qte',
                't_categorie.name AS name_sous',
                't_categorie.id AS id_cat_sous'
            )
            ->inRandomOrder()
            ->get();
        return response()->json([
            "code" => 200,
            "data" => [
                [
                    "title" => "Top seller",
                    "data" => $top
                ],
                [
                    "title" => "Réduction",
                    "data" => $discount
                ]
            ]
        ], 200);
    }

    public function liste($entreprise)
    {
        $entreprises = Entreprise::find($entreprise);
        if ($entreprises) {
            return response()->json([
                "data" => $entreprises->produits()->with('images')->where('t_produits.deleted', 0)
                    ->leftJoin('t_categorie', 't_produits.id_category', '=', 't_categorie.id')
                    ->leftJoin('t_mesure', 't_produits.id_mesure', '=', 't_mesure.id')
                    ->select(
                        't_produits.id',
                        't_produits.name_produit AS name',
                        't_produits.description',
                        't_produits.image',
                        't_produits.price',
                        't_produits.status',
                        't_produits.id_marque',
                        't_produits.id_category',
                        't_produits.id_mesure',
                        't_produits.price_red',
                        't_produits.etat_top',
                        't_mesure.name AS mesure',
                        't_produits.qte',
                        't_categorie.name AS category',
                        't_categorie.id AS id_cat_sous'
                    )
                    ->get()
            ], 200);
        } else {
            return response()->json([
                "message" => "id not found"
            ]);
        }
    }
    public function byCategory($category)
    {
        
        if (isset($_GET['query'])) {
            if($category == 'all'){
                $cat = Category::with('children')->where('deleted', 0)->whereNull('parent_id')->get();
                if (count($cat) > 0) {
                    $array = Category::getAllTree($cat);
                    $products = Produits::with(['images', 'entreprise'])->where('t_produits.status', 1)->where('t_produits.deleted', 0)
                        ->leftJoin('t_entreprise', 't_produits.id_entrep', '=', 't_entreprise.id')
                        ->leftJoin('t_categorie', 't_produits.id_category', '=', 't_categorie.id')
                        ->leftJoin('t_mesure', 't_produits.id_mesure', '=', 't_mesure.id')
                        ->where('t_entreprise.status', 1)
                        ->whereIn('t_produits.id_category', $array)
                        ->where('t_produits.name_produit', 'LIKE', '%' . $_GET['query'] . '%')
                        ->orWhere('t_categorie.name', 'LIKE', '%' . $_GET['query'] . '%')
                        ->select(
                            't_produits.id',
                            't_produits.id_entrep',
                            't_produits.name_produit',
                            't_produits.description',
                            't_produits.image',
                            't_produits.price',
                            't_mesure.name AS mesure',
                            't_produits.price_red',
                            't_produits.etat_top',
                            't_produits.qte',
                            't_categorie.name AS name_sous',
                            't_categorie.id AS id_cat_sous'
                        )
                        ->get();
    
                    return response()->json([
                        "message" => "success",
                        "code" => 200,
                        "data" => ['produits' => $products, 'categorie' => $cat]
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "id not found"
                    ], 404);
                }
            } else {
                $cat = Category::with('children')->where('deleted', 0)->find($category);
                if ($cat) {
                    $array = Category::getProductTree($cat);
                    $products = Produits::with(['images', 'entreprise'])->where('t_produits.status', 1)->where('t_produits.deleted', 0)
                        ->leftJoin('t_entreprise', 't_produits.id_entrep', '=', 't_entreprise.id')
                        ->leftJoin('t_categorie', 't_produits.id_category', '=', 't_categorie.id')
                        ->leftJoin('t_mesure', 't_produits.id_mesure', '=', 't_mesure.id')
                        ->where('t_entreprise.status', 1)
                        ->whereIn('t_produits.id_category', $array)
                        ->where('t_produits.name_produit', 'LIKE', '%' . $_GET['query'] . '%')
                        ->orWhere('t_categorie.name', 'LIKE', '%' . $_GET['query'] . '%')
                        ->select(
                            't_produits.id',
                            't_produits.id_entrep',
                            't_produits.name_produit',
                            't_produits.description',
                            't_produits.image',
                            't_produits.price',
                            't_mesure.name AS mesure',
                            't_produits.price_red',
                            't_produits.etat_top',
                            't_produits.qte',
                            't_categorie.name AS name_sous',
                            't_categorie.id AS id_cat_sous'
                        )
                        ->get();
    
                    return response()->json([
                        "message" => "success",
                        "code" => 200,
                        "data" => ['produits' => $products, 'categorie' => $cat]
                    ], 200);
                } else {
                    return response()->json([
                        "message" => "id not found"
                    ], 404);
                }
            }

        } else {
            $cat = Category::with('children')->where('deleted', 0)->find($category);
            if ($cat) {
                $array = Category::getProductTree($cat);
                $products = Produits::with(['images', 'entreprise'])->where('t_produits.status', 1)->where('t_produits.deleted', 0)
                    ->leftJoin('t_entreprise', 't_produits.id_entrep', '=', 't_entreprise.id')
                    ->leftJoin('t_categorie', 't_produits.id_category', '=', 't_categorie.id')
                    ->leftJoin('t_mesure', 't_produits.id_mesure', '=', 't_mesure.id')
                    ->where('t_entreprise.status', 1)
                    ->whereIn('t_produits.id_category', $array)
                    ->select(
                        't_produits.id',
                        't_produits.id_entrep',
                        't_produits.name_produit',
                        't_produits.description',
                        't_produits.image',
                        't_produits.price',
                        't_mesure.name AS mesure',
                        't_produits.price_red',
                        't_produits.etat_top',
                        't_produits.qte',
                        't_categorie.name AS name_sous',
                        't_categorie.id AS id_cat_sous'
                    )
                    ->get();

                return response()->json([
                    "message" => "success",
                    "code" => 200,
                    "data" => ['produits' => $products, 'categorie' => $cat]
                ], 200);
            } else {
                return response()->json([
                    "message" => "id not found"
                ], 404);
            }
        }
    }

    public function changeStatus(Request $request)
    {
        if (Auth::user()->Is(['Admin', 'Super admin'])) {
            $request->validate([
                "status" => "required",
                "id_produit" => "required"
            ]);
            $produit = Produits::find($request->id_produit);
            if ($produit) {
                $produit->status = $request->status;
                $produit->save();
                return response()->json([
                    "message" => "Status du produit changé avec succès"
                ], 200);
            } else {
                return response()->json([
                    "message" => "id not found"
                ], 404);
            }
        } else {
            return response()->json([
                "message" => "Vous n'avez pas cette autorisation"
            ], 402);
        }
    }
}
