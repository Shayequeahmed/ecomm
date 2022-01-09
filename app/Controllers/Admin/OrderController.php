<?php

namespace App\Controllers\Admin;

use App\Config;
use Fantom\Controller;
use App\Support\BillGenerator;
use App\Middlewares\AdminAuthMiddleware;
use App\EComm\Repositories\OrderRepository;
use App\EComm\Repositories\OrderItemRepository;

/**
 * 
 */
class OrderController extends Controller
{
	
	protected function index()
	{
		$orders = OrderRepository::recent(get_page())->get();
		$this->view->render('Admin/Order/index.php',[
			"orders" =>$orders,
		]);
	}

	protected function show()
	{
		$order_id = (int) $this->route_params['id'];
		$order = OrderRepository::find($order_id);
		if (is_null($order)) {
			Session::flash("error", "Order id not found");
			redirect("admin/order/index");
		}

		$items = $order->orderItems()->get();

		$this->view->render("Admin/Order/show.php",[
			"order" => $order,
			"order_items" => $items,
		]);
	}

	protected function downloadBill()
	{
		if (!isset($this->route_params['id'])) {
			Session::flash('error', 'Order id required');
			redirect('admin/order/index');
		}

		$order_id = (int) $this->route_params['id'];
		if (is_null($order = OrderRepository::find($order_id))) {
			Session::flash('error', "Order does not exist with id '{$order->id}'");
			redirect('admin/order/index');
		}

		$brandColor = Config::get('brand_color');
		$logo = ROOT . '/public/assets/img/logo.jpeg';

		$invoice = new BillGenerator($order,$logo, $brandColor);
		$invoice->toPDF()->download();
	}

	protected function before()
	{
		return (new AdminAuthMiddleware)();
	}
}