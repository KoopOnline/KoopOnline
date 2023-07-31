<?php

namespace App\Http\Livewire\Components;

use App\Models\Category;
use Livewire\Component;

class NavCategories extends Component
{
    public function render()
    {

        $categories = Category::where(['parent' => 0])->get();

        $formatedCategories = (Object) array();

        foreach($categories as $category) {

            $subCategories = Category::where(['parent' => $category->id])->get();
            $formatedCategories->{$category->name} = $subCategories;

        }

        return view('livewire.components.nav-categories', ['categories' => $formatedCategories]);
    }
}
