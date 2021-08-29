<?php

namespace App\Models;

use Fantom\Database\Model;

/**
 * Size Tabl
 */
class Size extends Model
{
	protected $table = "sizes";
	protected $primary = "id";

	public static function make(array $data)
	{
		$size = new self;

		self::populateSize($size, $data);

		return $size;
	}

	public static function recent( $page=1, $items = 20)
	{
		$offset = calc_page_offset($page , $items);
		$sql = "
				SELECT *
				FROM sizes
				ORDER BY id DESC
				LIMIT {$items} OFFSET {$offset} 
		";
		return static::raw($sql);
	}

	public static function change(Size &$size, array $data)
	{
		self::populateSize($size, $data);
	}

	private static function populateSize(Size &$size, array $data)
	{
		$size->size = strtoupper($data['size']);
	}
	
}