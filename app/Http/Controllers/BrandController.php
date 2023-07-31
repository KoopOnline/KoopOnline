<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function index() {

        $brands = Product::select('brand')->distinct()->where('brand', '!=', '')->get();

        $categories = [];

        foreach ($brands as $brand) {
            if($brand->brand != 0) {
                $firstLetter = strtoupper(substr($brand->brand, 0, 1));
                if (!array_key_exists($firstLetter, $categories)) {
                    $categories[$firstLetter] = [];
                }
                array_push($categories[$firstLetter], $brand->brand);
            }
        }

        ksort($categories);

        return view('pages.brands', ['categories' => $categories]);
    }

    public function show(Request $request, $brand) {

        $brand = str_replace('-', ' ', $brand);
        $query = DB::table('pt_products as t')->join(Product::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
        ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price', 't.description', 't.brand', 't.category', 't.normalised_name')
        ->where(['brand' => $brand]);    

        $firstResult = $query->get();

        $filterBrands = $firstResult->pluck('brand')->unique();
        $filterCategory = $firstResult->pluck('category')->unique();

        // Apply filters
        if ($request->has('categorie')) {
            $query->where('t.category', $request->input('categorie'));
        }

        if ($request->has('prijs_tot')) {
            $query->where('g.price', '<=', $request->input('prijs_tot'));
        }

        if ($request->has('prijs_vanaf')) {
            $query->where('g.price', '>=', $request->input('prijs_vanaf'));
        }

        if ($request->has('sort')) {
            if($request->input('sort') == 'hoog_laag') {
                $query->orderBy('g.price', 'desc');
            }
            if($request->input('sort') == 'laag_hoog') {
                $query->orderBy('g.price', 'asc');
            }
        }

        $productResults = $query->paginate(20);


        $filterCategory = $productResults->pluck('category')->unique();

        return view('pages.brand', ['productResults' => $productResults, 'filterCategory' => $filterCategory, 'search' => $brand, 'url' => $request->url()]);

    }
}
