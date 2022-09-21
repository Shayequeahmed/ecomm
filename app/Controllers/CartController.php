<?php 

namespace App\Controllers;

use Fantom\Log\Log;
use Fantom\Session;
use Fantom\Controller;
use App\Support\Authentication\Auth;
use App\EComm\Validators\CartValidator;
use App\Middlewares\UserAuthMiddleware;
use App\EComm\Repositories\CartRepository;
use App\EComm\Repositories\SizeRepository;
use App\EComm\Repositories\ColorRepository;
use App\EComm\Repositories\OrderRepository;
use App\EComm\Repositories\CouponRepository;
use App\EComm\Repositories\ProductRepository;
use App\EComm\Repositories\CartItemRepository;
use App\EComm\Repositories\OrderItemRepository;
use App\Support\OrderBooker\Coupons\PercentageCoupon;

/**
 * 
 */
class CartController extends Controller
{
	
	protected function addItem()
	{
		// @TODO Validate, duplicate assignment $color_id
		// cleanup $_POST variable
		// Out of stock product can not be added in the cart
		$v =  new CartValidator();
		$v->validatAddItem();
		if ($v->hasError()) {
			redirect('cart/checkout');
		}
		
		$product_id = (int) $_POST['product_id'];
		$size_id 	= (int) $_POST['size_id'];
		$color_id 	= (int) $_POST['color_id'];
		$user_id 	= Auth::userId();
		$qty 		= (int) $_POST['qty'];
		$cart 		= CartRepository::byUserId($user_id)->first();
		if (is_null($cart)) {
			$cart = CartRepository::make([
				"user_id" => $user_id,
			]);
			$cart->save();
		}
		$cart_item = $cart->addItem($qty, $product_id, $size_id, $color_id);
		$cart_item->save();
		Session::flash("success", "Your Product is added to cart successfully ");
		redirect('cart/checkout');
	}

	protected function checkout()
	{
		$cart = CartRepository::byUserId(Auth::userId())->first();
		$cart_items = $cart->items();
		$result = [];

		$order = new OrderRepository();

		foreach ($cart_items as $ci) {
			$product = ProductRepository::find($ci->product_id);
			$color = ColorRepository::find($ci->color_id);
			$size = SizeRepository::find($ci->size_id);
			$qty = $ci->qty;

			$result[] = (object) [
				'cart_item'	=> $ci,
				'product' 	=> $product,
				'color' 	=> $color,
				'size' 		=> $size,
				'qty' 		=> $qty,
			];

			$order_item = new OrderItemRepository();
			$order_item->addProduct($product);
			$order_item->setVariation('size', $size);
			$order_item->setVariation('color', $color);
			$order_item->quantity($qty);
			$order->addOrderItem($order_item);

		}

		// @TODO Check if coupon exist in this cart then apply it.
		if ($cart->hasCoupon()) {
			$coupon_value = CouponRepository::find($cart->coupon_id);
			$coupon = new PercentageCoupon($order, $coupon_value->value);
			$order->applyCoupon($coupon);
		}

		return $this->view->render("Cart/checkout.php", [
			'items' => $result,
			'order' => $order,
		]);
	}

	protected function removeProduct()
	{
		$cartItem_id = (int) $this->route_params['id'];
		$product_remove = CartItemRepository::find($cartItem_id);
		if (is_null($product_remove)) {
			Session::flash("error", "Item not present in the cart");
			redirect('cart/checkout');
		}
		$product_remove->delete();
		Session::flash('success','Product Removed Successfully');
		redirect('/cart/checkout');
	}

	protected function updateQuantity()
	{
		$v = new CartValidator();
		$v->validateQuantityUpdate();
		if ($v->hasError()) {
			redirect('cart/checkout');
		}

		$user_id		= Auth::userId();
		$cart 			= CartRepository::byUserId($user_id)->first();
		$cart_item_id 	= (int) post_or_empty('cart_item_id');
		$qty 			= (int) post_or_empty('qty');
		$cart_item 		= $cart->item($cart_item_id);
		// @TODO Use $cart->updateQuantity() method
		if (is_null($cart_item)) {
			Log::info("user {$user_id}: attempted to access others cart item {$cart_item_id}");
			Session::flash("error", "Cart item not found.");
			redirect("/cart/checkout");
		}

		$cart_item->qty = $qty;
		if ($cart_item->save() === false) {
			Session::flash("error", "Failed to update quantity");
			redirect("/cart/checkout");
		}

		redirect("/cart/checkout");
	}

	protected function applyCoupon()
	{
		// @TODO Validate request
		$coupon_code = post_or_empty('coupon_code');
		$coupon = CouponRepository::byCouponCode($coupon_code);
		$cart = CartRepository::byUserId(Auth::userId())->first();
		// @TODO $cart can by null
		if ($cart->applyCoupon($coupon)->save() === false) {
			Session::flash('error', 'Failed to apply coupon.');
			redirect('cart/checkout');
		}
		redirect('cart/checkout');
	}

	protected function before()
	{
		return (new UserAuthMiddleware)();
	}
}
