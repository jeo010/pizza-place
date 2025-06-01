<?php

namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Detail;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Pizza;
use App\Models\Type;
use Illuminate\Http\Request;

class PizzaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $pizzas = Type::with(['pizzas', 'category', 'ingredients'])->get();

        $categories = Category::orderBy('name')->get();

        $ingredients = Ingredient::orderBy('name')->get();

        return response()->json([$pizzas, $categories, $ingredients],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $categories = [];
        $category_list = [];
        $ingredients = [];
        $ingredient_list = [];
        $ingredient_mixed_list = [];
        $ingredient_ids = [];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();
            $data = [];
            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                $header = fgetcsv($handle, 100000, ',');
                while (($row = fgetcsv($handle, 100000, ',')) !== false) {
                    $data[] = array_combine($header, $row);
                }
                fclose($handle);
            }

            //save types table

            if($filename == 'pizza_types.csv'){

                foreach($data as $d){
                    if(isset($d['category'])){
                        $categories[] = $d['category'];
                    }

                    if(isset($d['ingredients'])){
                        $ingredients[] = $d['ingredients'];
                    }
                }



                $categories = array_unique($categories);

                foreach($categories as $category){
                    $category_list[] = [
                        'name' => $category,
                    ];
                }

                if (!empty($category_list)) {
                    Category::insert($category_list);
                }

                foreach($ingredients as $ingredient){
                    $ingredient_array = array_map(function($inc) {
                        return mb_convert_encoding(trim($inc), 'UTF-8', 'UTF-8');
                    }, explode(',', $ingredient));
                    foreach($ingredient_array as $inc){
                        $ingredient_mixed_list[] = preg_replace('/^\?/', "'", $inc);
                    }
                }

                $ingredient_mixed_list = array_unique($ingredient_mixed_list);

                foreach($ingredient_mixed_list as $inc){

                    $ingredient_list[] = [
                        'name' => $inc,
                    ];

                }

                if (!empty($ingredient_list)){
                    Ingredient::insert($ingredient_list);
                }

                foreach($data as $d){
                    $category = $d['category'];

                    $db_category = Category::where('name', $category)->first();

                    $pizza_type = Type::create([
                        'slug' => $d['pizza_type_id'],
                        'name' => $d['name'],
                        'category_id' => $db_category->id,
                    ]);

                    $ingredient_array = array_map(function($inc) {
                        return mb_convert_encoding(trim($inc), 'UTF-8', 'UTF-8');
                    }, explode(',', $d['ingredients']));


                    foreach($ingredient_array as $inc){
                        $ingredient = preg_replace('/^\?/', "'", $inc);
                        $db_ingredient = Ingredient::where('name', $ingredient)->first();
                        $ingredient_ids[] = $db_ingredient->id;
                    }

                    $pizza_type->ingredients()->syncWithoutDetaching($ingredient_ids);
                }
            }elseif($filename == 'pizzas.csv'){

            //save pizzas table
                foreach($data as $d){
                    if(isset($d['pizza_id'])){
                        $db_type = Type::where('slug',$d['pizza_type_id'])->first();
                        $pizza = Pizza::create([
                            'slug' => $d['pizza_id'],
                            'type_id' => $db_type->id,
                            'size' => $d['size'],
                            'price' => $d['price'],
                        ]);
                    }
                }
            }elseif($filename == 'orders.csv'){

                $order_list = [];

                foreach($data as $d){
                    $order_list[] = [
                        'id' => $d['order_id'],
                        'created_at' => $d['date'] . ' ' . $d['time'],
                    ];
                }

                $order = Order::insert($order_list);
            }elseif($filename == 'order_details.csv'){

                foreach($data as $d){
                    $pizza = Pizza::where('slug', $d['pizza_id'])->first();

                    $details = Detail::create([
                        'order_id' => $d['order_id'],
                        'pizza_id' => $pizza->id,
                        'quantity' => $d['quantity'],
                    ]);

                }

            }

            return response()->json([
                'filename' => $filename
            ], 200);
        } else {
            return response()->json(['error' => 'No file uploaded'], 400);
        }
    }

    
}
