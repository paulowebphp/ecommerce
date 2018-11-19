<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app->get("/admin/products", function() 
{
	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if( $search != '' )
	{

		$pagination = Product::getPageSearch($search, $page, 5);

	}#end if
	else
	{
		# Aula 126
		// $users = User::listAll();

		# Aula 126
		$pagination = Product::getPage($page, 5);

	}#end else


	$pages = [];

	for ($x=0; $x < $pagination['pages']; $x++)
	{ 
		# code...
		array_push($pages, [

			'href'=>'/admin/products?'.http_build_query([

				'page'=>$x+1,
				'search'=>$search

			]),

			'text'=>$x+1

		]);

	}#end for

	$products = Product::listAll();

	$page = new PageAdmin();

	$page->setTpl("products", [

		"products"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages

	]);	
	
});#ROUTE /admin/products GET


$app->get("/admin/products/create", function() 
{
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("products-create");	
	
});#ROUTE /admin/products/create GET


$app->post("/admin/products/create", function() 
{
	User::verifyLogin();

	$product = new Product();

	$product->setData($_POST);

	$product->save();

	header("Location: /admin/products");
	exit;
	
});#ROUTE /admin/products/create POST


$app->get("/admin/products/:idproduct", function($idproduct) 
{
	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$page = new PageAdmin();

	$page->setTpl("products-update", [

		'product'=>$product->getValues()

	]);	
	
});#ROUTE /admin/products/:idproduct GET


$app->post("/admin/products/:idproduct", function($idproduct) 
{
	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->setData($_POST);

	$product->save();

	if($_FILES["file"]["name"] !== "") $product->setPhoto($_FILES["file"]);

	header('Location: /admin/products');
	exit;
	
});#ROUTE /admin/products/:idproduct POST


$app->get("/admin/products/:idproduct/delete", function($idproduct) 
{
	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->delete();

	header('Location: /admin/products');
	exit;
	
});#ROUTE /admin/products/:idproduct GET

 ?>