<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\OtherController;
use App\Http\Controllers\api\marqueController;
use App\Http\Controllers\api\MesureController;
use App\Http\Controllers\api\ProductController;
use App\Http\Controllers\api\CategoryController;
use App\Http\Controllers\api\CommandeController;
use App\Http\Controllers\api\ProduitsController;
use App\Http\Controllers\api\CollectionController;
use App\Http\Controllers\api\EngagementController;
use App\Http\Controllers\api\EntrepriseController;
use App\Http\Controllers\api\NewsLetterController;
use App\Http\Controllers\api\PermissionController;
use App\Http\Controllers\api\AppSettingsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//les routes publiques
Route::post('/login', [UserController::class, 'login']);
Route::post('/provider/auth', [UserController::class, 'AuthProvider']);
Route::post('/create_account', [UserController::class, 'register']);
Route::post('/ask_otp', [UserController::class, 'ask']);
Route::post('/resendEmail', [UserController::class, 'resendEmail']);
Route::get('/search-user/{query}', [UserController::class, 'seachUser']);
Route::post('/changePswd', [UserController::class, 'changePswd']);

Route::get('/listcat', [CategoryController::class, 'liste']);
Route::get('/listproduit', [ProduitsController::class, 'productList']);
Route::get('/DetailProduct/{id}', [ProduitsController::class, 'productDetail']);
Route::get('/listCountry', [OtherController::class, 'getCountry']);
Route::post('/listCity', [OtherController::class, 'getCity']);
Route::get('/ListTop', [ProduitsController::class, 'topSellerDiscount']);
Route::get('/featured', [CategoryController::class, 'featured']);
Route::get('/produits/by/{category}', [ProduitsController::class, 'byCategory']);
Route::post('/entreprise_account', [EntrepriseController::class, 'account']);
Route::get('/marque', [marqueController::class, 'allMarque']);

Route::get("/global/settings", [AppSettingsController::class, 'getSettings']);
Route::post("/global/newsletter", [NewsLetterController::class, 'create']);
Route::get('/migrate', function () {
    Artisan::call('migrate:fresh --seed');
    return response()->json([
        "message" => "successfully migrated"
    ]);
});

//les routes privees
Route::group(['middleware' => ['auth:sanctum']], function () {
    //compte vendeur avec un utilisateur existant
    Route::post('/entreprise', [EntrepriseController::class, 'create']);
    Route::get('/user/entreprises', [EntrepriseController::class, 'userEntreprises']);
    Route::put('/validate-entreprise', [EntrepriseController::class, 'changeStatus']);
    Route::get('/allEntreprises', [EntrepriseController::class, 'allEntreprises']);
    Route::get('/entreprise/{id}', [EntrepriseController::class, 'oneEntreprises']);

    //routes pour les categories
    Route::post('/category', [CategoryController::class, 'create']);
    Route::post('/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/category/{id}', [CategoryController::class, 'delete']);
    Route::get('/category', [CategoryController::class, 'allCategory']);

    //routes pour les marques
    Route::post('/marque', [marqueController::class, 'create']);
    Route::post('/marque/{id}', [marqueController::class, 'update']);
    Route::delete('/marque/{id}', [marqueController::class, 'delete']);
    

    //routes pour les unités de mésures
    Route::post('/entreprise/mesure/{entreprise}', [MesureController::class, 'create']);
    Route::put('/entreprise/mesure/{entreprise}/{id}', [MesureController::class, 'update']);
    Route::delete('/entreprise/mesure/{entreprise}/{id}', [MesureController::class, 'delete']);
    Route::get('/mesures/{entreprise}', [MesureController::class, 'liste']);

    //routes pour les produits
    Route::post('/entreprise/produit/{entreprise}', [ProduitsController::class, 'create']);
    Route::post('/entreprise/produit/{entreprise}/{id}', [ProduitsController::class, 'update']);
    Route::delete('/entreprise/produit/{entreprise}/{id}', [ProduitsController::class, 'delete']);
    Route::get('/entreprise/produit/{entreprise}', [ProduitsController::class, 'liste']);
    Route::put('/change-produit-status', [ProduitsController::class, 'changeStatus']);

    //routes pour les permissions
    Route::get('/permissions', [PermissionController::class, 'getPermissions']);

    //routes pour les roles de l'entreprise
    Route::post('/role', [RoleController::class, 'create']);
    Route::put('/role/{id}', [RoleController::class, 'update']);
    Route::delete('/role/{entreprise}/{id}', [RoleController::class, 'delete']);
    Route::get('/role/{entreprise}', [RoleController::class, 'liste']);

    //routes pours les engagements
    Route::post('/engagement', [EngagementController::class, 'create']);
    Route::get('/engagement/{entreprise}', [EngagementController::class, 'liste']);

    //routes pour les commandes
    Route::post('/order', [CommandeController::class, 'store']);
    Route::get('/order/{id}', [CommandeController::class, 'oneInvoice']);
    Route::get('/order', [CommandeController::class, 'CustomerOrders']);
    Route::get('/admin/orders', [CommandeController::class, 'allOrders']);
    Route::get('/entreprise/orders/{entreprise}', [CommandeController::class, 'entrepriseOrders']);
    Route::put('/change-order-status', [CommandeController::class, 'changeStatus']);

    //routes pour les collection des produits
    Route::post('/collection', [CollectionController::class, 'store']);
    //users
    Route::put('user/edit-profile', [UserController::class,'editProfile']);

    //routes pour l'entreprise

    //route pour les parametres
    Route::post("/global/settings", [AppSettingsController::class, 'create']);
    Route::post("/global/about_us", [AppSettingsController::class, 'createAbout']);
    Route::post("/global/social", [AppSettingsController::class, 'createSocial']);

});

##Inserer l'utilisateur kazisafe en nunua
Route::post("/register-kazisafe/user", [UserController::class, 'connectWithKaziSafe]']);
##Product api
Route::get('/products', [ProductController::class, 'showAllProducts']);

//optimization routes
Route::get('/optimize', function(){
    $exitCode = Artisan::call('optimize');
    return 'DONE';
});
Route::get('/cache', function(){
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    return 'DONE';
});