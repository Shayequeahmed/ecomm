<?php

namespace App\Support;

use App\Config;
use Konekt\PdfInvoice\InvoicePrinter;
use App\EComm\Repositories\OrderRepository;

/**
 * BillGenerator
 */
class BillGenerator
{
	private $invoice;
	private $order;
	private $logo;
	private $brandColor;

	public function __construct(OrderRepository $order,$logo,$brandColor="#000000")
	{
		$this->order = $order;
		$this->logo = $logo;
		$this->brandColor = $brandColor; 
		$this->invoice = new InvoicePrinter('A4', 'INR', 'en');
	}

	public function toPDF()
	{
		$customer = $this->order->user();

		/* Header settings */
		$from_addr = [
			Config::get('company_name'),
			Config::get('company_address'),
			Config::get('company_state') . ' - ' . Config::get('company_pincode'),
		];
		$to_addr   = [
			$customer->fullName(),
		];

		$this->addHeader();
		
		$this->invoice->setFrom($from_addr);
		$this->invoice->setTo($to_addr);

		$order_items = $this->order->orderItems()->get();
		$this->addOrderItems($order_items);

		$this->invoice->addTotal("Total", $this->order->amount);

		return $this;
	}

	protected function addOrderItems(array $order_items)
	{
		// @TODO Handle coupon discount
		foreach ($order_items as $oi) {
			$product = $oi->product();
			$this->invoice->addItem(
				$product->title,
				$product->description,
				$oi->qty,
				false,
				$oi->price_mp,
				0,
				$oi->price_sp,
			);
		}
	}

	protected function addHeader()
	{
		$this->invoice->setLogo($this->logo);
		$this->invoice->setColor($this->brandColor);   
		$this->invoice->setType("Sale Invoice"); 
		$this->invoice->setReference("INV-{$this->order->id}");
		$this->invoice->setDate(date('M d, Y', strtotime($this->order->created_at)));
		$this->invoice->setTime(date('h:i:s A', strtotime($this->order->created_at)));
	}

	public function download()
	{
		$this->invoice->render('ecomm-invoice.pdf','D');
	}
}