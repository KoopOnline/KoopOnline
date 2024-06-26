<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Butschster\Head\Facades\Meta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use PhpScience\TextRank\TextRankFacade;
use GuzzleHttp\Exception\RequestException;
use PhpScience\TextRank\Tool\StopWords\Dutch;
use Butschster\Head\Packages\Entities\OpenGraphPackage;

class ProductController extends Controller
{

    public function index($product_name)
    {

        $product_name = str_replace('-', ' ', $product_name);
        $product = Product::where(['normalised_name' => $product_name])->orderBy('price')->get();


        Meta::setTitle('Koop ' . $product_name . ' online!');
        Meta::setDescription("Bekijk een vergelijking van aanbieders voor het product " . $product_name . ".");

        $og = new OpenGraphPackage('OG');
        $og->setType('website')
            ->setSiteName('Koop ' . $product_name . ' online!')
            ->setTitle('Bekijk een vergelijking van aanbieders voor het product ' . $product_name . '.');
        $og->addImage("https://www.kooponline.com/imageCache.php?src=" . base64_encode($product[0]->image_url), ['type' => 'image/jpeg']);
        $og->addMeta('image:alt', $product_name . ' image');

        if (count($product) == 0) {
            return view('pages.productNotFound');
        }

        $text = '';

        foreach ($product as $p) {
            $text = $text . ' ' . $p->description;
        }

        $api = new TextRankFacade();

        $stopWords = new Dutch();

        $api->setStopWords($stopWords);

        $relevantProducts = DB::table('pt_products as t')->join(Product::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
            ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price', 't.normalised_name')
            ->where('t.category', '=', $product[0]->category)
            ->where('t.price', '>=', $product[0]->price - ($product[0]->price * 0.15))
            ->where('t.price', '<=', $product[0]->price + ($product[0]->price * 0.15))
            ->where('t.ean', '!=', $product[0]->ean)
            ->orderBy('g.count', 'DESC')
            ->limit(4)
            ->get();

        if (count($relevantProducts) != 4) {
            $relevantProducts = DB::table('pt_products as t')->join(Product::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
                ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price', 't.normalised_name')
                ->where('t.category', '=', $product[0]->category)
                ->where('t.ean', '!=', $product[0]->ean)
                ->orderBy('g.count', 'DESC')
                ->limit(4)
                ->get();
        }

        $bolData = $this->makeApiRequest($product[0]->ean);

        return view('pages.product', ['product' => $product, 'description' => implode(" ", $api->summarizeTextFreely($text, 10, 5, 0)), 'relevantProducts' => $relevantProducts, 'bolData' => $bolData]);
    }

    public function makeApiRequest($ean)
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return null;
        }

        $client = new Client();

        try {
            $response = $client->get("https://api.bol.com/catalog/v4/search", [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => "Bearer {$accessToken}",
                ],
                'query' => ['q' => $ean, 'limit' => 4],
            ]);

            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getBody(), true);
                return $responseData;
            } else {
                return null;
            }
        } catch (RequestException $e) {
            return null;
        }
    }

    public function getAccessToken()
    {
        $cachedAccessToken = Cache::get('access_token');

        if ($cachedAccessToken) {
            return $cachedAccessToken;
        }

        $clientId = config('app.client_id');
        $clientSecret = config('app.client_secret');
        $base64EncodedCredentials = base64_encode($clientId . ':' . $clientSecret);

        $client = new Client();

        try {

            $response = $client->post('https://login.bol.com/token?grant_type=client_credentials', [
                'headers' => [
                    'Authorization' => 'Basic ' . $base64EncodedCredentials,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getBody(), true);
                $accessToken = $responseData['access_token'];

                Cache::put('access_token', $accessToken, now()->addMinutes(4));

                return $accessToken;
            } else {
                return null;
            }
        } catch (RequestException $e) {
            return null;
        }
    }
}
