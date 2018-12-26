<?php 

use \Hcode\Page;
use \Hcode\Model\User;
use \GuzzleHttp\Client;
use \Hcode\PagSeguro\Config;
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

	$page = new Page([
		"footer" => false
	]);

	$page->setTpl("payment", [

		"order" => $order->getValues(),
		"msgError" => Order::getError(),
		"years" => $years,
		"pagseguro" => [

			"urlJS" => Config::getUrlJS()

		]

	]);

});#END route



$app->get('/payment/pagseguro', function() 
{

	$client = new Client();

	$res = $client->request(

		'POST', 
		Config::getUrlSessions()."?".http_build_query(Config::getAuthentication()),
		['verify'=> false]#verify false desabilita a verificação de certificado SSL

	);

	echo $res->getBody()->getContents();
	// '{"id": 1420053, "name": "guzzle", ...}'


	/*
	$page = new Page();

	$page->setTpl("pagseguro11", [

		'products'=>Product::checkList($products)

	]);
	*/

});#END route





 ?>