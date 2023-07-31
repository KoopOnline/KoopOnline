<?php

namespace App\Http\Livewire\Components\Filter;

use Livewire\Component;

class Seller extends Component
{
    public $filterBrands;
    public $filterCategory;
    public $category;
    public $brand;
    public $price_to;
    public $price_from;
    public $query;
    public $url;

    public function render()
    {
        return view('livewire.components.filter.seller');
    }

    public function filter() {

        $parameters = array();

        if($this->category) {
            $parameters[] = 'categorie='.$this->category;
        }

        if($this->brand) {
            $parameters[] = 'merk='.$this->brand;
        }

        if($this->price_to) {
            $parameters[] = 'prijs_tot='.$this->price_to;
        }

        if($this->price_from) {
            $parameters[] = 'prijs_vanaf='.$this->price_from;
        }

        redirect(asset('').'verkoper/'.$this->query.'?'.implode('&', $parameters));

    }

    public function clear() {
        redirect(asset('').'verkoper/'.$this->query);
    }

}
