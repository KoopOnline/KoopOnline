<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Support\Facades\DB;
use Butschster\Head\Packages\Entities\OpenGraphPackage;

class SearchController extends Controller
{
    public function index(Request $request, $search)
    {

        Meta::setTitle('KoopOnline.com - Zoeken '.$search);
        Meta::setDescription("Bekijk alle resultaten voor de zoekopdracht '".$search."'.");

        $og = new OpenGraphPackage('OG');
        $og->setType('website')
        ->setSiteName('kooponline.com')
        ->setTitle('Bekijk alle resultaten voor de zoekopdracht '.$search.'.');
        $og->addImage(asset('https://www.kooponline.com/imgs/kooponline-logo-big.png'), [ 'type' => 'image/png' ]);
        $og->addMeta('image:alt', 'KoopOnline.com image');

        $query = DB::table('pt_products as t')
            ->join(DB::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
            ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price', 't.description', 't.brand', 't.category', 't.normalised_name')
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
