<?php

namespace App\Models;

use Fantom\Database\Model;

/**
 * Color Tabl
 */
class Color extends Model
{
	protected $table = "colors";
	protected $primary = "id";

	public static function make(array $data)
	{
		$color = new self;

		self::populateColor($color, $data);

		return $color;
	}

	public static function change(Color &$color, array $data)
	{
		self::populateColor($color, $data);
	}

	private static function populateColor(Color &$color, array $data)
	{
		$color->color = title_case($data['color']);
		$color->code = $data['code'];
	}
	
}