<?php

namespace App\Http\Livewire\Components\Filter;

use Livewire\Component;

class Category extends Component
{
    public $filterBrands;
    public $brand;
    public $price_to;
    public $price_from;
    public $query;
    public $url;

    public function render()
    {
        return view('livewire.components.filter.category');
    }

    public function filter() {

        $parameters = array();

        if($this->brand) {
            $parameters[] = 'merk='.$this->brand;
        }

        if($this->price_to) {
            $parameters[] = 'prijs_tot='.$this->price_to;
        }

        if($this->price_from) {
            $parameters[] = 'prijs_vanaf='.$this->price_from;
        }

        redirect(asset('').'categorie/'.$this->query.'?'.implode('&', $parameters));

    }

    public function clear() {
        redirect(asset('').'categorie/'.$this->query);
    }
}
