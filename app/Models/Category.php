<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory, HasUuids;
    protected $table = 't_categorie';
    protected $fillable = [
        'name',    'parent_id', 'image', 'status',    'deleted'
    ];
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id', 'id');
    }
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id')->where('deleted', 0);
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
    public function produits()
    {
        return $this->hasMany(Produits::class, 'id_category', 'id');
    }
    public static function tree()
    {
        $allCategories = Category::where('deleted', 0)->whereNull('parent_id')->get();
        $rootCategories = $allCategories->whereNull('parent_id');
        self::generateCategories($allCategories);
        return $allCategories;
    }

    private static function formatTree($categories, $allCategories)
    {
        foreach ($categories as $category) {
            if ($category->children->isNotEmpty()) {
                self::formatTree($category->children, $allCategories);
            }
        }
    }

    public static function generateCategories($categories)
    {
        foreach ($categories as $category) {
            if (count($category->children) > 0) {
                self::generateCategories($category->children);
            }
        }
    }

    public static function getProductTree($cat)
    {
        $array = [$cat->id];
        $allCategories = Category::with('children')->where('deleted', 0)->whereId($cat->id)->get();
        return array_merge($array, self::generateCate($allCategories[0]->children));
    }

    public static function generateCate($cate)
    {
        $arr = [];
        foreach ($cate as $category) {
            $allCategories = Category::with('children')->where('deleted', 0)->whereId($category->id)->get();
            self::generateCate($allCategories[0]->children);
            array_push($arr, $category->id);
        }

        return $arr;
    }
    public static function getAllTree($cat)
    {
        $allCategories = Category::where('deleted', 0)->get();
        return self::generateAllTree($allCategories);
        //return $cat;
    }

    public static function generateAllTree($cate)
    {
        $arr = [];
        foreach ($cate as $category) {
            $allCategories = Category::with('children')->where('deleted', 0)->whereId($category->id)->get();
            self::generateCate($allCategories[0]->children);
            array_push($arr, $category->id);
        }

        return $arr;
    }
}
