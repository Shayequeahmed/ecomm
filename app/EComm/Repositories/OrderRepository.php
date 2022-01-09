<?php

namespace App\EComm\Repositories;

use App\Models\Order;
use App\EComm\Traits\ModelOperationTrait;
use App\EComm\Repositories\UserRepository;
use App\Support\OrderBooker\Traits\OrderTrait;
use App\EComm\Repositories\OrderItemRepository;
use App\Support\OrderBooker\Coupons\NullCoupon;
use App\Support\OrderBooker\Interfaces\OrderInterface;

/**
 * Order repository
 */
class OrderRepository extends Order implements OrderInterface
{
	const STATUS_CREATED   = 0;
	const STATUS_COMPLETED = 1;
	const STATUS_CANCELLED = 2;

	protected static $_table = "orders";

	use ModelOperationTrait, OrderTrait;

	private $user;

	public function __construct()
	{
		$this->applyCoupon(new NullCoupon());
		//parent::__construct();
	}

	public static function make(array $data)
	{
		$order = new self;

		$order->amount = $data['amount'];
		$order->tax = $data['tax'];
		$order->user_id = $data['user_id'];
		$order->status = self::STATUS_CREATED;
		$order->created_at = $order->updated_at = date("Y-m-d H:i:s");

		return $order;
	}

	public function forUser($user)
	{
		$this->user = $user;

		return $this;
	}

	public function create()
	{
		$order = self::make([
			"amount" 	=> $this->orderTotal(),
			"tax" 		=> $this->tax(),
			"user_id" 	=> $this->user->id,
		]);

		if ($order->save() === false) {
			return false;
		}

		$order_items = $this->getOrderItems();

		foreach ($order_items as $oi) {
			$order_item = OrderItemRepository::make([
				"order_id" 		=> $order->thisId(),
				"product_id" 	=> $oi->getProduct()->thisId(),
				"user_id" 		=> $this->user->id,
				"color_id" 		=> $oi->getVariation('color')->id,
				"size_id" 		=> $oi->getVariation('size')->id,
				"price_mp" 		=> $oi->markedPrice(),
				"price_sp" 		=> $oi->sellingPrice(),
				"qty" 		=> $oi->quantity()
			]);
			if ($order_item->save() === false) {
				return false;
			}
		}

		return $order;
	}

	public function user()
	{
		return UserRepository::find($this->user_id);
	}

	public static function byRazorypayOrderId($rzp_order_id)
	{
		return static::where('rzp_order_id', $rzp_order_id)->first();
	}

	// @TODO Implement a method that will take cart object and
	// create order using that cart. This will allow developer
	// to not handle building order object using cart in the
	// controller.
	public function orderItems()
	{
		return OrderItemRepository::byOrderId($this->thisId());
	}
}