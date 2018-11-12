<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Product;

$app->get("/admin/products", function() 
{
	User::verifyLogin();

	$products = Product::listAll();

	$page = new PageAdmin();

	$page->setTpl("products", [

		'products'=>$products

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

	$product->setPhoto($_FILES["file"]);

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