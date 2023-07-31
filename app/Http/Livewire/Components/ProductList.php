<?php

namespace App\Http\Livewire\Components;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class ProductList extends Component
{
    use WithPagination;

    public $search;

    public function render()
    {
        $productResults = DB::table('pt_products as t')->join(Product::raw('(SELECT ean, COUNT(ean) AS count, MIN(price) AS price FROM pt_products GROUP BY ean) g'), 'g.ean', '=', 't.ean')
        ->select('t.name', 't.image_url', 't.ean', 'g.count', 'g.price')
        ->where('name', 'LIKE', '%' . $this->search . '%')        
        // ->orderBy('g.count', 'DESC')
        ->paginate(20);
        return view('livewire.components.product-list', ['productResults' => $productResults]);
    }
}
