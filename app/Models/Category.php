<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public $table = "pt_categories_hierarchy";

    public function finished()
    {
        return $this->hasMany(Product::class, 'category', '');
    }

}
