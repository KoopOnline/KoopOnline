<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Support\Facades\DB;
use Butschster\Head\Packages\Entities\OpenGraphPackage;

class CategoryController extends Controller
{
    
    public function show(Request $request, $category) {

        Meta::setTitle('KoopOnline.com - Categorie '. $category);
        Meta::setDescription("Bekijk een vergelijking van alle producten en prijzen in de categorie ".$category.".");
        
        $og = new OpenGraphPackage('OG');
        $og->setType('website')
        ->setSiteName('kooponline.com')
        ->setTitle("Bekijk een vergelijking van alle producten en prijzen in de categorie ".$category.".");
        $og->addImage(asset('imgs/logo.PNG'), [ 'type' => 'image/png' ]);
        $og->addMeta('image:alt', 'KoopOnline.com logo');

        $category = str_replace('-', ' ', $category);
        $query = DB::table('pt_products as t')->join(Product::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
        ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price', 't.description', 't.brand', 't.category', 't.normalised_name')
        ->where(['category' => $category]) 
        ->orderBy('g.count', 'DESC');
        
        $firstResult = $query->get();

        $filterBrands = $firstResult->pluck('brand')->unique();
        $filterCategory = $firstResult->pluck('category')->unique();


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

        return view('pages.category', ['productResults' => $productResults, 'filterBrands' => $filterBrands, 'search' => $category, 'url' => $request->url()]);

    }

}
