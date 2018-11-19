<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Order;
use \Hcode\Model\OrderStatus;



$app->get("/admin/orders/:idorder/status", function($idorder) 
{
	User::verifyLogin();

	$order = new Order();

	$order->get((int)$idorder);

	//var_dump(Order::getSuccess());
	//exit;

	$page = new PageAdmin();
	
	$page->setTpl("order-status", [

		'order'=>$order->getValues(),
		'status'=>OrderStatus::listAll(),
		'msgSuccess'=>Order::getSuccess(),
		'msgError'=>Order::getError()

	]);
	
});#END route


$app->post("/admin/orders/:idorder/status", function($idorder) 
{
	User::verifyLogin();

	if( !isset($_POST['idstatus']) || !(int)$_POST['idstatus'] > 0 )
	{

		Order::setError("Informe o status atual");

		header("Location: /admin/orders/".$idorder."/status");
		exit;

	}#end if

	$order = new Order();

	$order->get((int)$idorder);

	$order->setidstatus((int)$_POST['idstatus']);

	$order->save();

	Order::setSuccess("Status atualizado");

	header("Location: /admin/orders/".$idorder."/status");
	exit;
	
});#END route



$app->get("/admin/orders/:idorder/delete", function($idorder) 
{
	User::verifyLogin();

	$order = new Order();

	$order->get((int)$idorder);

	$order->delete();

	header("Location: /admin/orders");
	exit;
	
});#END route


$app->get("/admin/orders/:idorder", function($idorder) 
{
	User::verifyLogin();

	$order = new Order();

	$order->get((int)$idorder);

	$cart = $order->getCart();

	$page = new PageAdmin();
	
	$page->setTpl("order", [

		'order'=>$order->getValues(),
		'cart'=>$cart->getValues(),
		'products'=>$cart->getProducts()

	]);
	
});#END route


$app->get("/admin/orders", function() 
{
	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if( $search != '' )
	{

		$pagination = Order::getPageSearch($search, $page, 5);

	}#end if
	else
	{
		# Aula 126
		// $users = User::listAll();

		# Aula 126
		$pagination = Order::getPage($page, 5);

	}#end else


	$pages = [];

	for ($x=0; $x < $pagination['pages']; $x++)
	{ 
		# code...
		array_push($pages, [

			'href'=>'/admin/orders?'.http_build_query([

				'page'=>$x+1,
				'search'=>$search

			]),

			'text'=>$x+1

		]);

	}#end for

	$page = new PageAdmin();
	
	$page->setTpl("orders", [

		"orders"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages

	]);
	
});#END route





 ?>