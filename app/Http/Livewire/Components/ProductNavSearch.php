<?php

namespace App\Http\Livewire\Components;

use App\Models\Product;
use Livewire\Component;

class ProductNavSearch extends Component
{

    public $search;
    public $count;

    public function render()
    {
        $products = [];

        if($this->search) {
            $products = Product::where('name', 'LIKE', '%' . $this->search . '%')
            ->select('name')
            ->distinct()
            ->limit(20)
            ->get();

            $this->count = Product::where('name', 'LIKE', '%' . $this->search . '%')
            ->select('name')
            ->distinct()
            ->count();
        } else {

            $this->count = 0;
            
        }

        return view('livewire.components.product-nav-search', ['products' => $products]);
    }

    public function searchProduct() {

        return redirect()->route('search', ['search' => $this->search]);

    }
}
