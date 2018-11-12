<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;

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

 ?>