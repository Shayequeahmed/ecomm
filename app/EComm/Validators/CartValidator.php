<?php

namespace App\EComm\Validators;

use Fantom\Validation\Validator;
use App\EComm\Repositories\ProductAvailableColorRepository;

/**
 * CartValidator
 */
class CartValidator extends Validator
{
	public function validatAddItem()
	{
		$this->validate("POST",[
			"product_id" => "required|numeric|exist:products,id",
			"qty" 			=> "required|numeric|check_stock",
			"color_id"		=> "required|numeric|depends:product_id|color_available"
		]);
	}

	protected function color_available_rule($field, $data)
	{
		$product_id = (int) post_or_empty('product_id');
		$color_id   = (int) $data;

		$available = ProductAvailableColorRepository::byProductId($product_id)->get();
		foreach ($available as $a) {
			if ((int) $a->color_id === (int) $color_id) {
				return true;
			}
		}

		$this->setError($field, __FUNCTION__, "Color is not available for this product");

		return false;
	}

	public function validateQuantityUpdate()
	{
		$this->validate("POST", [
			"cart_item_id" 	=> "required|numeric",
			"qty" 			=> "required|numeric|depends:cart_item_id|check_stock",
		]);
	}

	protected function check_stock_rule($field, $data)
	{
		if ((int) $data < 1) {
			$this->setError($field, __FUNCTION__, "qty can not be zero and negative");
			return false;
		}

		// @TODO check stock availability

		return true;
	}
}