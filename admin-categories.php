<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;
use \Hcode\Model\Product;

$app->get("/admin/categories", function() 
{
	User::verifyLogin();

	$search = (isset($_GET['search'])) ? $_GET['search'] : "";

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	if( $search != '' )
	{

		$pagination = Category::getPageSearch($search, $page, 2);

	}#end if
	else
	{
		# Aula 126
		// $users = User::listAll();

		# Aula 126
		$pagination = Category::getPage($page, 2);

	}#end else


	$pages = [];

	for ($x=0; $x < $pagination['pages']; $x++)
	{ 
		# code...
		array_push($pages, [

			'href'=>'/admin/categories?'.http_build_query([

				'page'=>$x+1,
				'search'=>$search

			]),

			'text'=>$x+1

		]);

	}#end for

	$page = new PageAdmin();

	$page->setTpl("categories", array(

		"categories"=>$pagination['data'],
		"search"=>$search,
		"pages"=>$pages

	));
	
});#ROUTE /admin/categories GET

$app->get("/admin/categories/create", function() 
{
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");
	
});#ROUTE /admin/categories/create GET


$app->post("/admin/categories/create", function() 
{
	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');
	exit;
	
});#ROUTE /admin/categories/create POST



$app->get("/admin/categories/:idcategory/delete", function($idcategory) 
{
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header('Location: /admin/categories');
	exit;
	
});#ROUTE /admin/categories/:idcategory/delete GET


$app->get("/admin/categories/:idcategory", function($idcategory) 
{
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", [

		'category'=>$category->getValues()

	]);	
	
});#ROUTE /admin/categories/:idcategory GET


$app->post("/admin/categories/:idcategory", function($idcategory) 
{
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');
	exit;
	
});#ROUTE /admin/categories/:idcategory POST


$app->get("/admin/categories/:idcategory/products", function($idcategory) 
{
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-products", [

		'category'=>$category->getValues(),
		'productsRelated'=>$category->getProducts(),
		'productsNotRelated'=>$category->getProducts(false)

	]);	
	
});#ROUTE /categories/:idcategory GET

$app->get("/admin/categories/:idcategory/products/:idproduct/add", function($idcategory, $idproduct) 
{
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->addProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
	
});#ROUTE /categories/:idcategory GET

$app->get("/admin/categories/:idcategory/products/:idproduct/remove", function($idcategory, $idproduct) 
{
	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$product = new Product();

	$product->get((int)$idproduct);

	$category->removeProduct($product);

	header("Location: /admin/categories/".$idcategory."/products");
	exit;
	
});#ROUTE /categories/:idcategory GET

 ?>