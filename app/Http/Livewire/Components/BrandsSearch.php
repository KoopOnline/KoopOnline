<?php

namespace App\Http\Livewire\Components;

use App\Models\Product;
use Livewire\Component;

class BrandsSearch extends Component
{

    public $search = null;
    public $count;

    public function render()
    {
        $brands = [];

        if($this->search) {
            $brands = Product::where('brand', 'LIKE', '%' . $this->search . '%')
            ->select('brand')
            ->distinct()
            ->limit(20)
            ->get();

            $this->count = Product::where('brand', 'LIKE', '%' . $this->search . '%')
            ->select('brand')
            ->distinct()
            ->count();
        } else {

            $this->count = 0;
            
        }

        return view('livewire.components.brands-search', ['brands' => $brands]);
    }

    public function setSearch($clickedBrand) {
        $this->search = $clickedBrand;
    }
}
