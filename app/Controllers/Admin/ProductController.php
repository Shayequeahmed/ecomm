<?php

namespace App\Controllers\Admin;

use App\EComm\Validators\ProductValidtor;
use App\Middlewares\AdminAuthMiddleware;
use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use Fantom\Controller;
use Fantom\Session;


/**
 * ProductController
 */
class ProductController extends Controller
{
	protected function index()
	{
		$products = Product::recent(get_page())->get();

		// View
		// foreach ($products as $p) {
		// 	$available_sizes = $p->productSizes()->get();
		// 	$available_colors = $p->productColors()->get();
		// 	$product_stocks = $p->productStocks()->get();
		// }

		 $this->view->render("Admin/Product/index.php", [
		 	"products" => $products,
		 ]);
	}

	protected function show()
	{
		$product_id = (int) $this->route_params['id'];
		if (empty($product_id)) {
			Session::flash("error", "Product id not found");
			redirect("admin/product/index");
		}

		$product = Product::find($product_id)->first();
		$product_sizes 		= $product->productSizes()->get();
		$product_colors 	= $product->productColors()->get();
		$product_stocks 	= $product->productStocks()->get();

		$this->view->render("Admin/Product/show.php",[
			"product" 			=> $product,
			"product_sizes" 	=> $product_sizes,
			"product_colors" 	=> $product_colors,
			"product_stocks" 	=> $product_stocks,
		]);
	}

	protected function create()
	{
		$categories = Category::all()->get();
		$materials = Material::all()->get();

		$this->view->render("Admin/Product/create.php", [
			"categories" => $categories,
			"materials" => $materials,
		]);
	}

	protected function store()
	{
		// 1. Validate
		$v = new ProductValidtor();
		$v->validateCreate();
		if ($v->hasError()) {
			redirect("admin/product/create");
		}

		// 2. Make product model
		$product = Product::make($_POST);

		// 3. Save product
		if ($product->save() === false) {
			Session::flash("error", "Failed to create product");
			redirect("admin/product/index");
		}

		// 4. Success messsage and redirect to index
		Session::flash("success", "Product created successfully!");
		redirect("admin/product-color/create?product_id={$product->lastId()}");
	}

	protected function edit()
	{
		$product_id = (int) $this->route_params['id'];

		$product = Product::find($product_id)->first();
		if (is_null($product)) {
			Session::flash("error", "Product with id '{$product_id}' does not exist.");
			redirect("admin/product/index");
		}

		$categories = Category::all()->get();
		$materials = Material::all()->get();

		$this->view->render("Admin/Product/edit.php", [
			"product" => $product,
			"categories" => $categories,
			"materials" => $materials
		]);
	}


	protected function update()
	{
		$v = new ProductValidtor();
		$v->validateUpdate();
		$product_id = (int) post_or_empty("id");
		if ($v->hasError()) {
			redirect("admin/product/{$product_id}/edit");
		}

		$product = Product::find($product_id)->first();
		Product::change($product, $_POST);
		if ($product->save() === false) {
			Session::flash("error", "Failed to update product");
			redirect("admin/product/{$product_id}/edit");
		}

		Session::flash("success", "Product updated successfully!");
		redirect("admin/product/{$product_id}/show");
	}

	protected function before()
	{
		return (new AdminAuthMiddleware)();
	}
}