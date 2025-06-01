<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $categories = Category::orderBy('name')->get();

        $ingredients = Ingredient::orderBy('name')->get();

        $orders = Order::whereHas('details', function($query){
            $query->with(['pizza'=> function($query){
                $query->with('type');
            }]);
        })->get();

        return response()->json([$orders, $categories, $ingredients],200);
    }

   
}
