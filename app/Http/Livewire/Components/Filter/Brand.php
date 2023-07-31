<?php

namespace App\Http\Livewire\Components\Filter;

use Livewire\Component;

class Brand extends Component
{

    public $filterCategory;
    public $category;
    public $price_to;
    public $price_from;
    public $query;
    public $url;

    public function render()
    {
        return view('livewire.components.filter.brand');
    }

    public function filter() {

        $parameters = array();

        if($this->category) {
            $parameters[] = 'categorie='.$this->category;
        }

        if($this->price_to) {
            $parameters[] = 'prijs_tot='.$this->price_to;
        }

        if($this->price_from) {
            $parameters[] = 'prijs_vanaf='.$this->price_from;
        }

        redirect(asset('').'merk/'.$this->query.'?'.implode('&', $parameters));

    }

    public function clear() {
        redirect(asset('').'merk/'.$this->query);
    }
}
