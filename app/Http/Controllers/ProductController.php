<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use PhpScience\TextRank\TextRankFacade;
use GuzzleHttp\Exception\RequestException;
use PhpScience\TextRank\Tool\StopWords\Dutch;

class ProductController extends Controller
{
    
    public function index($product_name) {
        
        $product_name = str_replace('-', ' ', $product_name);
        $product = Product::where(['normalised_name' => $product_name])->orderBy('price')->get();

        if(count($product) == 0) {
            return view('pages.productNotFound');
        }

        $text = '';

        foreach($product as $p) {
            $text = $text. ' '. $p->description;
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

        if(count($relevantProducts) != 4) {
            $relevantProducts = DB::table('pt_products as t')->join(Product::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
            ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price')
            ->where('t.category', '=', $product[0]->category)
            ->where('t.ean', '!=', $product[0]->ean)
            ->orderBy('g.count', 'DESC')
            ->limit(4)
            ->get();
        }

        $bolData = $this->makeApiRequest($product[0]->ean);

        return view('pages.product', ['product' => $product, 'description' => implode(" ",$api->summarizeTextFreely($text, 10, 5, 0 )), 'relevantProducts' => $relevantProducts, 'bolData' => $bolData]);

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
    
        $clientId = 'fc8563f6-7386-45e1-acc3-d7d87d94b2bc';
        $clientSecret = '33Go07Es0dWVjF1URjiwoIINCXFmSP44qJSvBawsQ95IHcxIIPTqt+xr@c71V60k';
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
