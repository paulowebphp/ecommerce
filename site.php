<?php 

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;
use \Hcode\Model\Address;
use \Hcode\Model\User;

$app->get('/', function() 
{
	$products = Product::listAll();

	$page = new Page();

	$page->setTpl("index", [

		'products'=>Product::checkList($products)

	]);

});#END route

$app->get("/categories/:idcategory", function($idcategory) 
{
	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	$category = new Category();

	$category->get((int)$idcategory);

	$pagination = $category->getProductsPage($page);

	$pages = [];

	for ($i=1; $i <= $pagination['pages']; $i++)
	{ 
		# code...
		array_push($pages, [

			'link'=>'/categories/'.
				$category->getidcategory().
				'?page='.
				$i,
			'page'=>$i

		]);

	}#end for

	$page = new Page();

	$page->setTpl("category", [

		'category'=>$category->getValues(),
		'products'=>$pagination["data"],
		'pages'=>$pages

	]);	
	
});#END route


$app->get("/products/:desurl", function($desurl) 
{
	$product = new Product();

	$product->getFromURL($desurl);

	$page = new Page();

	$page->setTpl("product-detail", [

		'product'=>$product->getValues(),
		'categories'=>$product->getCategories()

	]); 

});#END route


$app->get("/cart", function() 
{
	$cart = Cart::getFromSession();

	$page = new Page();

	$page->setTpl("cart", [

		'cart'=>$cart->getValues(),
		'products'=>$cart->getProducts(),
		'error'=>Cart::getMsgError()

	]); 

});#END route


$app->get("/cart/:idproduct/add", function($idproduct) 
{
	$product = new Product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$qtd = (isset($_GET['qtd'])) ? (int)$_GET['qtd'] : 1;

	for ($i=0; $i < $qtd; $i++)
	{ 
		# code...
		$cart->addProduct($product);

	}#end for

	header("Location: /cart");
	exit;

});#END route


$app->get("/cart/:idproduct/minus", function($idproduct) 
{
	$product = new Product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product);

	header("Location: /cart");
	exit;

});#END route


$app->get("/cart/:idproduct/remove", function($idproduct) 
{
	$product = new Product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product, true);

	header("Location: /cart");
	exit;

});#END route


$app->post("/cart/freight", function() 
{
	$cart = Cart::getFromSession();

	$cart->setFreight($_POST['zipcode']);

	header("Location: /cart");
	exit;

});#END route


$app->get("/checkout", function() 
{

	User::verifyLogin(false);

	$cart = Cart::getFromSession();

	$address = new Address();
	
	$page = new Page();

	$page->setTpl("checkout", [

		'cart'=>$cart->getValues(),
		'address'=>$address->getValues()

	]); 

});#END route


$app->get("/login", function() 
{
	
	$page = new Page();

	$page->setTpl("login", [

		'error'=>User::getError(),
		'errorRegister'=>User::getErrorRegister(),
		'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : [

			'name'=>'', 
			'email'=>'', 
			'phone'=>''

		]

	]); 

});#END route


$app->post("/login", function() 
{

	try
	{

		User::login($_POST['login'], $_POST['password']);

	}#end try
	catch(Exception $e)
	{
		User::setError($e->getMessage());
		
	}#end catch

	header("Location: /checkout");
	exit;

});#END route



$app->get("/logout", function() 
{
	
	User::logout();

	header("Location: /login");
	exit;

});#END route



$app->post("/register", function() 
{
	# Validando informar Nome
	$_SESSION['registerValues'] = $_POST;

	if(

		!isset($_POST['name'])
		||
		$_POST['name'] == ''

	)
	{

		User::setErrorRegister("Preencha o seu nome");

		header("Location: /login");
		exit;

	}#end if

	# Validando informar e-mail
	if(

		!isset($_POST['email'])
		||
		$_POST['email'] == ''

	)
	{

		User::setErrorRegister("Informe o seu e-mail");

		header("Location: /login");
		exit;

	}#end if

	# Validando informar senha
	if(

		!isset($_POST['password'])
		||
		$_POST['password'] == ''

	)
	{

		User::setErrorRegister("Escolha uma senha");

		header("Location: /login");
		exit;

	}#end if


	if( User::checkLoginExist($_POST['email']) === true )
	{

		User::setErrorRegister("Este endereço de e-mail já está em uso");

		header("Location: /login");
		exit;

	}#end if


	$user = new User();

	$user->setData([

		'inadmin'=>0,
		'deslogin'=>$_POST['email'],
		'desperson'=>$_POST['name'],
		'desemail'=>$_POST['email'],
		'despassword'=>$_POST['password'],
		'nrphone'=>$_POST['phone'],

	]);

	$user->save();

	User::login($_POST['email'], $_POST['password']);

	header("Location: /checkout");
	exit;

});#END route













$app->get("/forgot", function() 
{
	$page = new Page();

	$page->setTpl("forgot");

});#ROUTE /admin/forgot GET


$app->post("/forgot", function() 
{
	$user = User::getForgot($_POST["email"], false);

	header("Location: /forgot/sent");
	exit;

});#ROUTE /admin/forgot POST


$app->get("/forgot/sent", function() 
{
	$page = new Page();
	
	$page->setTpl("forgot-sent");
	
});#ROUTE /admin/forgot/sent GET


$app->get("/forgot/reset", function() 
{
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new Page();
	
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
	
});#ROUTE /admin/forgot/reset GET


$app->get("/forgot/reset", function() 
{
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new Page();
	
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
	
});#ROUTE /admin/forgot/reset GET

$app->post("/forgot/reset", function() 
{
	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [

		"cost"=>12

	]);

	$user->setPassword($password);

	$page = new Page();
	
	$page->setTpl("forgot-reset-success");

	//header("Location: /admin/forgot/sent/");
	//exit;

});#ROUTE /admin/forgot/reset POST

 ?>