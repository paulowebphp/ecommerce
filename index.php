<?php 

session_start();

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

$app = new Slim();


$app->config('debug', true);

$app->get('/', function() 
{  
	$page = new Page();

	$page->setTpl("index");

});#ROUTE / GET


$app->get('/admin/', function() 
{  
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("index");

});#ROUTE admin GET


$app->get('/admin/login/', function() 
{  
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("login");

});#ROUTE /admin/login GET


$app->post('/admin/login/', function() 
{  
	User::login($_POST["login"], $_POST["password"]);

	header("Location: /admin");
	exit;

});#ROUTE /admin/login POST


$app->get('/admin/logout/', function() 
{  
	User::logout();

	header("Location: /admin/login");
	exit;

});#ROUTE /admin/logout GET


$app->get("/admin/users/", function() 
{
	User::verifyLogin();

	$users = User::listAll();

	$page = new PageAdmin();

	$page->setTpl("users", array(

		"users"=>$users

	));

});#ROUTE /admin/users GET


$app->get("/admin/users/create/", function() 
{
	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});#ROUTE /admin/users/create GET


$app->get("/admin/users/:iduser/delete", function($iduser) 
{
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});#ROUTE /admin/users/:iduser/delete GET


$app->get("/admin/users/:iduser", function($iduser) 
{
	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

});#ROUTE /admin/users/:iduser GET


$app->post("/admin/users/create/", function() 
{
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	# LINHA ACRESCENTADA PARA CRIPTOGRAFAR A SENHA NA CRIAÇÃO
	$_POST['despassword'] = password_hash($_POST["despassword"], PASSWORD_DEFAULT, [

		"cost"=>12
	]);

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users/");
	exit;

});#ROUTE /admin/users/create POST


$app->post("/admin/users/:iduser", function($iduser) 
{
	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users");
	exit;

});#ROUTE /admin/users/:iduser POST


$app->get("/admin/forgot/", function() 
{
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("forgot");

});#ROUTE /admin/forgot GET


$app->post("/admin/forgot/", function() 
{
	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent/");
	exit;

});#ROUTE /admin/forgot POST


$app->get("/admin/forgot/sent/", function() 
{
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("forgot-sent");
	
});#ROUTE /admin/forgot/sent GET


$app->get("/admin/forgot/reset", function() 
{
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
	
});#ROUTE /admin/forgot/reset GET


$app->get("/admin/forgot/reset", function() 
{
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
	
});#ROUTE /admin/forgot/reset GET

$app->post("/admin/forgot/reset", function() 
{
	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [

		"cost"=>12

	]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false

	]);

	$page->setTpl("forgot-reset-success");

	//header("Location: /admin/forgot/sent/");
	//exit;

});#ROUTE /admin/forgot/reset POST

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

$app->run();

 ?>