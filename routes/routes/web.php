<?php

use App\Filament\Pages\Forms\PublicForms\ConsortiumForm;
use App\Filament\Pages\Forms\PublicForms\CreditPropertyGuaranteeForm;
use App\Filament\Pages\Forms\PublicForms\RealEstateCreditForm;
use App\Mail\UserApprovedMail;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PDFController;

\URL::forceScheme('https');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('{partner_code}/simulations')->as('partner.simulations.')->group(function () {
    Route::get('credito-imobiliario', RealEstateCreditForm::class)
        ->name('real-estate-credit');
    Route::get('credito-garantia', CreditPropertyGuaranteeForm::class)
        ->name('credit-property-guarantee');
    Route::get('consorcio', ConsortiumForm::class)
        ->name('consortium');
});

Route::prefix('/simulations')->as('simulations.')->group(function () {
    Route::get('credito-imobiliario', RealEstateCreditForm::class)
        ->name('real-estate-credit');
    Route::get('credito-garantia', CreditPropertyGuaranteeForm::class)
        ->name('credit-property-guarantee');
    Route::get('consorcio', ConsortiumForm::class)
        ->name('consortium');
});

Route::post('/generate-pdf/{view}', [PDFController::class, 'generatePDF'])->name('generate.pdf');
