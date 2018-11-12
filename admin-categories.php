<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

$app->get("/admin/categories", function() 
{
	User::verifyLogin();

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", array(

		'categories'=>$categories

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


$app->get("/categories/:idcategory", function($idcategory) 
{
	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();

	$page->setTpl("category", [

		'category'=>$category->getValues(),
		'products'=>[]

	]);	
	
});#ROUTE /categories/:idcategory GET

 ?>