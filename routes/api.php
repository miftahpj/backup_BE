<?php

use App\Models\ReachUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ArticleController, ArticleItemController, StatusController, CourseController,
    CompanyController, CurriculumTypeController,AuthController, UserController,
    GalleryController,ReachUsController, FileController,
    KalenderController,RoleController,CartController
};

// Route default
Route::get('/', fn() => response()->json(['message' => 'API is working']));



// API Resource Routes
Route::apiResource('status', StatusController::class);
Route::apiResource('companies', CompanyController::class);
Route::apiResource('course', CourseController::class);
Route::apiResource('curriculum-types', CurriculumTypeController::class);
Route::apiResource('articles', ArticleController::class);
Route::apiResource('article-items', ArticleItemController::class);
Route::apiResource('users', UserController::class);
Route::apiResource('galleries', GalleryController::class);
Route::apiResource('reach-us', ReachUsController::class);
Route::apiResource('files', FileController::class);
Route::apiResource('kalenders', KalenderController::class);
Route::apiResource('roles', RoleController::class);
Route::apiResource('cart', CartController::class);


Route::get('/article-items/{id}/files', function ($id) {
    $files = \App\Models\File::where('article_item_id', $id)->get();
    return response()->json($files);
});


Route::post('/send-message', function (Request $request) {
    $message = ReachUs::create([
        'username' => $request->name,
        'whatsapp' => $request->number,
        'message' => $request->message,
    ]);

    return response()->json(['message' => 'Pesan berhasil dikirim!'], 201);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/admin', function () {
        return response()->json(['message' => 'Welcome to admin panel']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin', function () {
        return response()->json(['message' => 'Welcome to admin panel']);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


// Update Article Items (PUT & PATCH)
Route::match(['put', 'patch'], '/article-items/{id}', [ArticleItemController::class, 'update']);

// Route tambahan
Route::get('/companies/{id}/courses', [CompanyController::class, 'getCoursesByCompany']);
Route::get('/courses/status/{statusId}', [CourseController::class, 'getCoursesByStatus']);
Route::get('/curriculum-types/{id}/courses', [CurriculumTypeController::class, 'getCoursesByCurriculumType']);
Route::get('/articles/{id}/items', [ArticleController::class, 'getArticleItems']);
