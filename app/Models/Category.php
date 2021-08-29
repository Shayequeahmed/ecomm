<?php

namespace App\Models;

use Fantom\Database\Model;

/**
 * Category Tabl
 */
class Category extends Model
{
	protected $table = "product_categories";
	protected $primary = "id";

	public static function make(array $data)
	{
		$category = new self;

		self::populateCategory($category, $data);
		$category->created_at = $category->updated_at = date("Y-m-d H:i:s");

		return $category;
	}

	public static function change(Category &$category, array $data)
	{
		self::populateCategory($category, $data);
		$category->updated_at = date("Y-m-d H:i:s");
	}

	private static function populateCategory(Category &$category, array $data)
	{
		$category->category = title_case($data['category']);
	}

	public  static function recent($page = 1, $item = 20)
	{
		$offset = calc_page_offset($page, $item);
		$sql = "
				SELECT * 
				FROM product_categories
				ORDER BY id DESC
				LIMIT {$item} OFFSET {$offset}
				";
			return static::raw($sql);
	}
	
}