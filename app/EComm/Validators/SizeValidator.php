<?php 
namespace App\EComm\Validators;
use Fantom\Validation\Validator;

class SizeValidator extends Validator
{
	
	public function validateCreate()
	{
		$this->validate("POST",[
			"size" 	=> "required|alpha_num|max:10|unique:sizes,size",
		]);
	}
}