<?php

use App\Http\Controllers\API\PizzaController;
use App\Http\Controllers\API\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('pizzas', PizzaController::class);
Route::apiResource('orders', OrderController::class);
