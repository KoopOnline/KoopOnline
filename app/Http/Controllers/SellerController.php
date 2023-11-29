<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Butschster\Head\Packages\Entities\OpenGraphPackage;

class SellerController extends Controller
{
    public function index() {

        Meta::setTitle('KoopOnline.com - Verkopers');
        Meta::setDescription("Ontdek de verkopers van de vergeleken producten op onze site.");

        $og = new OpenGraphPackage('OG');
        $og->addImage(asset('https://www.kooponline.com/imgs/kooponline-logo-big.png'), [ 'type' => 'image/png' ]);
        $og->addMeta('image:alt', 'KoopOnline.com image');

        $publicPath = public_path('imgs/sellers');
        $images = File::files($publicPath);
        $imagesSrc = [];
        foreach($images as $image) {
            $imagesSrc[] = $image->getFilename();
        }
        return view('pages.sellers', ['imagesSrc' => $imagesSrc]);
    }

    public function show(Request $request, $seller) {

        Meta::setTitle('KoopOnline.com - Verkopers');
        Meta::setDescription("Vergelijk alle producten van de verkoper ".$seller.". Ontdek de beste deals.");

        $og = new OpenGraphPackage('OG');
        $og->setType('website')
        ->setSiteName('kooponline.com')
        ->setTitle('Vergelijk alle producten van de verkoper '.$seller.'.');
        $og->addImage(asset('imgs/logo.PNG'), [ 'type' => 'image/png' ]);
        $og->addMeta('image:alt', 'KoopOnline.com image');

        $query = DB::table('pt_products as t')->join(Product::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
        ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price', 't.description', 't.brand', 't.category', 't.normalised_name')
        ->where(['merchant' => $seller])
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
        $filterBrands = $productResults->pluck('brand')->unique();
        $filterCategory = $productResults->pluck('category')->unique();

        return view('pages.seller', ['productResults' => $productResults, 'filterBrands' => $filterBrands, 'filterCategory' => $filterCategory, 'search' => $seller, 'url' => $request->url()]);

    }
}
