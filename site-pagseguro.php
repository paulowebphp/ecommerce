<?php 

use \Hcode\Page;
use \Hcode\Model\User;
use \GuzzleHttp\Client;
use \Hcode\PagSeguro\Config;

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


$app->get('/teste', function() 
{

	echo "ze";

});#END route


 ?>