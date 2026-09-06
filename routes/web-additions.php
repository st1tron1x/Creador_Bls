<?php
/**
 * AGREGAR estas líneas a tu routes/web.php existente
 * (no reemplaza el archivo completo, solo se añade).
 */

use App\Livewire\BillOfLadingForm;

Route::middleware('auth')->group(function () {
    Route::get('/bl/create', BillOfLadingForm::class)->name('bl.create');
    // (el resto de rutas de listado/pdf/anular se agregan en el siguiente bloque, con el controller)
});
