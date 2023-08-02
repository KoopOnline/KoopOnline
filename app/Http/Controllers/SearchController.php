<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request, $search)
    {

        $query = DB::table('pt_products as t')
            ->join(DB::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
            ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price', 't.description', 't.brand', 't.category')
            ->where('t.name', 'LIKE', '%' . $search . '%')
            ->orderBy('g.count', 'DESC');
    
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

    
        return view('pages.search', ['productResults' => $productResults, 'filterBrands' => $filterBrands, 'filterCategory' => $filterCategory, 'search' => $search, 'url' => $request->url()]);
    }
    
}
