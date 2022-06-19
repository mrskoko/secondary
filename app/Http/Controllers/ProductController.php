<?php

namespace App\Http\Controllers;

use App\Jobs\ProductLiked;
use App\Models\Product;
use App\Models\ProductUser;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function like(Request $request, $id)
    {
        $response = \Http::get('http://host.docker.internal:3000/api/user');

        $user = $response->json();

        try {
            $productUser = ProductUser::create([
                'user_id' => $user['id'],
                'product_id' => $id
            ]);

            ProductLiked::dispatch($productUser->toArray())->onQueue('primary_queue');

            return response([
                'message' => 'success'
            ]);
            
        }catch(Exception $exception)
        {
            return response([
                'error' => 'You already liked this product.'
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
