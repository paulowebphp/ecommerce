<?php 

use \Hcode\Page;
use \Hcode\Model\User;
use \Hcode\PagSeguro\Config;
use \Hcode\PagSeguro\Transporter;
use \Hcode\Model\Order;

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
			"id" => Transporter::createSession()

		]

	]);

});#END route







 ?>