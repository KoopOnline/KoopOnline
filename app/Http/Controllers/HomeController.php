<?php

namespace App\Http\Controllers;

use stdClass;
use App\Models\Product;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Support\Facades\DB;
use Butschster\Head\Packages\Entities\OpenGraphPackage;

class HomeController extends Controller
{
    public function index() {

        Meta::setTitle('KoopOnline.com - De nummer één Prijsvergelijker!');
        Meta::setDescription("Ontdek op KoopOnline.com miljoenen producten, vergelijk prijzen snel en bespaar tijd en geld. Elektronica of huishoudelijke apparaten, vind de beste deals!");

        $og = new OpenGraphPackage('OG');
        $og->setType('website')
        ->setSiteName('kooponline.com')
        ->setTitle('Ontdek op KoopOnline.com miljoenen producten, vergelijk prijzen snel en bespaar tijd en geld.');
        $og->addImage(asset('https://www.kooponline.com/kooponline-logo-big.png'), [ 'type' => 'image/png' ]);
        $og->addMeta('image:alt', 'KoopOnline.com logo');

        $categories = [['t.category = "Mobiele telefoons"', 't.price > 300'], ['t.category = "Laptops"', 't.price > 500'], ['t.category = "Lego"'], ['t.category = "Barbecues"'], ['t.category = "Drones"', 't.price > 200']];
        $categoryNames = ['Mobiele telefoons', 'Laptops', 'Lego', 'Barbecues', 'Drones'];

        $displayProducts = collect();

        $i = 0;

        foreach ($categories as $category) {

            $query = DB::table('pt_products as t')
            ->join(Product::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
            ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price', 't.normalised_name');

            foreach ($category as $criteria) {
                if (strpos($criteria, 'category') !== false) {
                    $query->whereRaw($criteria); // Directly using the SQL criteria for 'category'
                } else {
                    // Extracting the column name, operator, and value from the criteria string
                    list($column, $operator, $value) = explode(' ', $criteria, 3);
                    $query->where($column, $operator, $value);
                }
            }

            $query->orderBy('g.count', 'DESC')
                ->limit(5);
            $productResults = $query->get();


            $displayProducts->put($categoryNames[$i], [
                'products' => $productResults,
            ]);
            $i++;
        }


        return view('pages.home', ['productRows' => $displayProducts]);
    }
}
