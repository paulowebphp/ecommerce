<?php 

use \Hcode\Page;
use \Hcode\Model\User;
use \Hcode\PagSeguro\Config;
use \Hcode\PagSeguro\Transporter;
use \Hcode\Model\Order;


$app->post('/payment/credit', function() 
{

	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$address = $order->getAddress();

	$cart = $order->getCart();

	echo "Order:";
	echo "<br><br>";
	var_dump($order->getValues());
	echo "<br><br>";
	echo "Address:";
	echo "<br><br>";
	var_dump($address->getValues());
	echo "<br><br>";
	echo "Cart:";
	echo "<br><br>";
	var_dump($cart->getValues());

});#END route






$app->get('/payment', function() 
{
	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$years = [];

	for ($y = date('Y'); $y < date('Y')+14; $y++)
	{ 
		# code...
		array_push($years, $y);
	}#end for

	$page = new Page();

	$page->setTpl("payment", [

		"order" => $order->getValues(),
		"msgError" => Order::getError(),
		"years" => $years,
		"pagseguro" => [

			"urlJS" => Config::getUrlJS(),
			"id" => Transporter::createSession(),
			"maxInstallmentNoInterest" => Config::MAX_INSTALLMENT_NO_INTEREST,
			"maxInstallment" => Config::MAX_INSTALLMENT

		]

	]);

});#END route







 ?>