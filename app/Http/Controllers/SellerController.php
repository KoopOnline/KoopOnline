<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SellerController extends Controller
{
    public function index() {
        $publicPath = public_path('imgs/sellers');
        $images = File::files($publicPath);
        $imagesSrc = [];
        foreach($images as $image) {
            $imagesSrc[] = $image->getFilename();
        }
        return view('pages.sellers', ['imagesSrc' => $imagesSrc]);
    }

    public function show(Request $request, $seller) {

        $query = DB::table('pt_products as t')->join(Product::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
        ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price', 't.description', 't.brand', 't.category', 't.normalised_name')
        ->where(['merchant' => $seller]);        

        $firstResult = $query->get();

        $filterBrands = $firstResult->pluck('brand')->unique();
        $filterCategory = $firstResult->pluck('category')->unique();

        // Apply filters
        if ($request->has('categorie')) {
            $query->where('t.category', $request->input('categorie'));
        }

        if ($request->has('merk')) {
            $query->where('t.brand', $request->input('merk'));
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
        $filterBrands = $productResults->pluck('brand')->unique();
        $filterCategory = $productResults->pluck('category')->unique();

        return view('pages.seller', ['productResults' => $productResults, 'filterBrands' => $filterBrands, 'filterCategory' => $filterCategory, 'search' => $seller, 'url' => $request->url()]);

    }
}
