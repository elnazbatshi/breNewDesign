<?php
use Illuminate\Http\Request;
use App\Http\Controllers\TrackCodeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoreController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [TrackCodeController::class, 'index'])->name('trackCode');
    Route::get('/terms', [TrackCodeController::class, 'terms'])->name('terms');
    Route::get('/getTerms', [TrackCodeController::class, 'getTerms'])->name('getTerms');
    Route::get('/getTerms', [TrackCodeController::class, 'getTerms'])->name('getTerms');
    Route::post('/setTerm', [TrackCodeController::class, 'setTerm'])->name('setTerm');
    Route::post('/updateTerm/{id}', [TrackCodeController::class, 'updateTerm'])->name('updateTerm');
    Route::delete('/deleteTerm/{id}', [TrackCodeController::class, 'deleteTerm'])->name('deleteTerm');
});

Route::post('wp-transport-rest',[CoreController::class,'proccessor']);
Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
