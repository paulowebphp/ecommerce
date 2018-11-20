<?php 

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
use \Hcode\Model\Cart;
use \Hcode\Model\Address;
use \Hcode\Model\User;
use \Hcode\Model\Order;
use \Hcode\Model\OrderStatus;


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

	$address = new Address();

	$cart = Cart::getFromSession();

	if( !isset($_GET['zipcode']) )
	{

		$_GET['zipcode'] = $cart->getdeszipcode();

	}#end if

	if( isset($_GET['zipcode']) )
	{

		$address->loadFromCEP($_GET['zipcode']);

		$cart->setdeszipcode($_GET['zipcode']);

		$cart->save();

		$cart->getCalculateTotal();

	}#end if

	if(!$address->getdesaddress()) $address->setdesaddress('');
	if(!$address->getdesnumber()) $address->setdesnumber('');
	if(!$address->getdescomplement()) $address->setdescomplement('');
	if(!$address->getdesdistrict()) $address->setdesdistrict('');
	if(!$address->getdescity()) $address->setdescity('');
	if(!$address->getdesstate()) $address->setdesstate('');
	if(!$address->getdescountry()) $address->setdescountry('');
	if(!$address->getdeszipcode()) $address->setdeszipcode('');
	
	$page = new Page();

	$page->setTpl("checkout", [

		'cart'=>$cart->getValues(),
		'address'=>$address->getValues(),
		'products'=>$cart->getProducts(),
		'error'=>Address::getMsgError()

	]); 

});#END route





$app->post("/checkout", function() 
{

	User::verifyLogin(false);

	# Validando se informou o CEP
	if( !isset($_POST['zipcode']) || $_POST['zipcode'] === '' )
	{

		Address::setMsgError("Informe o CEP");

		header('Location: /checkout');
		exit;

	}#end if


	 # Validando se informou o Logradouro
	if( !isset($_POST['desaddress']) || $_POST['desaddress'] === '' )
	{

		Address::setMsgError("Informe o Logradouro");

		header('Location: /checkout');
		exit;

	}#end if



	 # Validando se informou o Bairro
	if( !isset($_POST['desdistrict']) || $_POST['desdistrict'] === '' )
	{

		Address::setMsgError("Informe o Bairro");

		header('Location: /checkout');
		exit;

	}#end if


	 # Validando se informou a Cidade
	if( !isset($_POST['descity']) || $_POST['descity'] === '' )
	{

		Address::setMsgError("Informe a Cidade");

		header('Location: /checkout');
		exit;

	}#end if


	 # Validando se informou o Estado
	if( !isset($_POST['desstate']) || $_POST['desstate'] === '' )
	{

		Address::setMsgError("Informe o Estado");

		header('Location: /checkout');
		exit;

	}#end if


	 # Validando se informou o País
	if( !isset($_POST['descountry']) || $_POST['descountry'] === '' )
	{

		Address::setMsgError("Informe o País");

		header('Location: /checkout');
		exit;

	}#end if


	$user = User::getFromSession();

	$address = new Address();

	$_POST['deszipcode'] = $_POST['zipcode'];
	$_POST['idperson'] = $user->getidperson();

	$address->setData($_POST);

	$address->save();

	$cart = Cart::getFromSession();

	$totals = $cart->getCalculateTotal();

	$order = new Order();

	$order->setData([

		'idcart'=>$cart->getidcart(),
		'idaddress'=>$address->getidaddress(),
		'iduser'=>$user->getiduser(),
		'idstatus'=>OrderStatus::EM_ABERTO,
		'vltotal'=>$cart->getvltotal()

	]);

	$order->save();

	header("Location: /order/".$order->getidorder());
	exit;

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

	Cart::removeFromSession();

    session_regenerate_id();

	header("Location: /login");
	exit;

});#END route



$app->post("/register", function() 
{
	# Validando informar Nome
	$_SESSION['registerValues'] = $_POST;

	if( !isset($_POST['name']) || $_POST['name'] == '' )
	{

		User::setErrorRegister("Preencha o seu nome");

		header("Location: /login");
		exit;

	}#end if

	# Validando informar e-mail
	if( !isset($_POST['email']) || $_POST['email'] == '' )
	{

		User::setErrorRegister("Informe o seu e-mail");

		header("Location: /login");
		exit;

	}#end if


	# Validando informar senha
	if( !isset($_POST['password']) || $_POST['password'] == '' )
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

});#END route



$app->post("/forgot", function() 
{
	$user = User::getForgot($_POST["email"], false);

	header("Location: /forgot/sent");
	exit;

});#END route



$app->get("/forgot/sent", function() 
{
	$page = new Page();
	
	$page->setTpl("forgot-sent");
	
});#END route



$app->get("/forgot/reset", function() 
{
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new Page();
	
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
	
});#END route



$app->get("/forgot/reset", function() 
{
	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new Page();
	
	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));
	
});#END route



$app->post("/forgot/reset", function() 
{
	$forgot = User::validForgotDecrypt($_POST["code"]);

	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	
	# Aula 120
	$password = User::getPasswordHash($_POST["password"]);
	
	/*
	# Aula 120
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, [

		"cost"=>12

	]);
	*/

	$user->setPassword($password);

	$page = new Page();
	
	$page->setTpl("forgot-reset-success");

});#END route



$app->get("/profile", function() 
{
	User::verifyLogin(false);

	$user = User::getFromSession();

	$page = new Page();
	
	$page->setTpl("profile", [

		"user"=>$user->getValues(),
		'profileMsg'=>User::getSuccess(),
		'profileError'=>User::getError()

	]);
	
});#END route



$app->post("/profile", function() 
{
	User::verifyLogin(false);


	# Valida preenchimento de Nome
	if( !isset($_POST['desperson']) || $_POST['desperson'] === '' )
	{

		User::setError("Insira o seu nome");

		header('Location: /profile');
		exit;

	}#end if


	# Valida preenchimento de e-mail
	if( !isset($_POST['desemail']) || $_POST['desemail'] === '' )
	{

		User::setError("Insira o seu e-mail");

		header('Location: /profile');
		exit;

		
	}#end if

	$user = User::getFromSession();

	if( $_POST['desemail'] != $user->getdesemail() )
	{

		if( User::checkLoginExist($_POST['desemail']) === true )
		{

			User::setError("Este endereço de e-mail já está cadastrado");

			header('Location: /profile');
			exit;

		}#end if 

	}#end if


	$_POST['iduser'] = $user->getiduser();
	$_POST['inadmin'] = $user->getinadmin();
	$_POST['despassword'] = $user->getdespassword();
	$_POST['deslogin'] = $_POST['desemail'];

	$user->setData($_POST);

	$user->update();

	$_SESSION[User::SESSION] = $user->getValues(); 

	User::setSuccess("Dados alterados com sucesso");

	header('Location: /profile');
	exit;
	
});#END route



$app->get("/order/:idorder", function($idorder) 
{
	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	$page = new Page();
	
	$page->setTpl("payment", [

		"order"=>$order->getValues()

	]);
	
});#END route


$app->get("/boleto/:idorder", function($idorder) 
{
	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	// DADOS DO BOLETO PARA O SEU CLIENTE
	$dias_de_prazo_para_pagamento = 10;
	$taxa_boleto = 5.00;
	$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 

	# Aula 122
	$valor_cobrado = formatPrice($order->getvltotal()); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal

	# Aula 123
	$valor_cobrado = str_replace(".", "", $valor_cobrado);
	$valor_cobrado = str_replace(",", ".", $valor_cobrado);

	$valor_boleto = number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

	# Aula 122
	$dadosboleto["nosso_numero"] = $order->getidorder();  // Nosso numero - REGRA: Máximo de 8 caracteres!

	# Aula 122
	$dadosboleto["numero_documento"] = $order->getidorder();	// Num do pedido ou nosso numero

	$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
	$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
	$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
	$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

	// DADOS DO SEU CLIENTE

	# Aula 122
	$dadosboleto["sacado"] = $order->getdesperson();

	# Aula 122
	$dadosboleto["endereco1"] = $order->getdesaddress()." - ".$order->getdescomplement()." - ".$order->getdesdistrict().".";

	# Aula 122
	$dadosboleto["endereco2"] = $order->getdescity()." - ".$order->getdesstate()." - ".$order->getdescountry()." - "."CEP: ".$order->getzipcode().".";

	// INFORMACOES PARA O CLIENTE
	$dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja Hcode E-commerce";
	$dadosboleto["demonstrativo2"] = "Taxa bancária - R$ 0,00";
	$dadosboleto["demonstrativo3"] = "";
	$dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% após o vencimento";
	$dadosboleto["instrucoes2"] = "- Receber até 10 dias após o vencimento";
	$dadosboleto["instrucoes3"] = "- Em caso de dúvidas entre em contato conosco: suporte@hcode.com.br";
	$dadosboleto["instrucoes4"] = "&nbsp; Emitido pelo sistema Projeto Loja Hcode E-commerce - www.hcode.com.br";

	// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
	$dadosboleto["quantidade"] = "";
	$dadosboleto["valor_unitario"] = "";
	$dadosboleto["aceite"] = "";		
	$dadosboleto["especie"] = "R$";
	$dadosboleto["especie_doc"] = "";


	// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


	// DADOS DA SUA CONTA - ITAÚ
	$dadosboleto["agencia"] = "1690"; // Num da agencia, sem digito
	$dadosboleto["conta"] = "48781";	// Num da conta, sem digito
	$dadosboleto["conta_dv"] = "2"; 	// Digito do Num da conta

	// DADOS PERSONALIZADOS - ITAÚ
	$dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175, 174, 104, 109, 178, ou 157

	// SEUS DADOS
	$dadosboleto["identificacao"] = "Hcode Treinamentos";
	$dadosboleto["cpf_cnpj"] = "24.700.731/0001-08";
	$dadosboleto["endereco"] = "Rua Ademar Saraiva Leão, 234 - Alvarenga, 09853-120";
	$dadosboleto["cidade_uf"] = "São Bernardo do Campo - SP";
	$dadosboleto["cedente"] = "HCODE TREINAMENTOS LTDA - ME";

	# Aula 122
	$path = $_SERVER['DOCUMENT_ROOT'].
	DIRECTORY_SEPARATOR."res".
	DIRECTORY_SEPARATOR."boletophp".
	DIRECTORY_SEPARATOR."include".
	DIRECTORY_SEPARATOR;
	require_once($path . "funcoes_itau.php");
	require_once($path . "layout_itau.php");

});#END route



$app->get("/profile/orders", function() 
{
	User::verifyLogin(false);

	$user = User::getFromSession();

	$page = new Page();
	
	$page->setTpl("profile-orders", [

		"orders"=>$user->getOrders()

	]);
	
});#END route



$app->get("/profile/orders/:idorder", function($idorder) 
{
	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	$cart = new Cart();

	$cart->get((int)$order->getidcart());

	$cart->getCalculateTotal();

	$page = new Page();
	
	$page->setTpl("profile-orders-detail", [

		'order'=>$order->getValues(),
		'cart'=>$cart->getValues(),
		'products'=>$cart->getProducts()

	]);
	
});#END route




$app->get("/profile/change-password", function() 
{
	User::verifyLogin(false);

	$page = new Page();
	
	$page->setTpl("profile-change-password", [

		'changePassError'=>User::getError(),
		'changePassSuccess'=>User::getSuccess()

	]);
	
});#END route


$app->post("/profile/change-password", function() 
{

	User::verifyLogin(false);


	# Validando se informou a senha atual
	if( !isset($_POST['current_pass']) || $_POST['current_pass'] === '' )
	{

		User::setError("Digite a senha atual");

		header("Location: /profile/change-password");
		exit;

	}#end if


	# Validando se informou a nova senha
	if( !isset($_POST['new_pass']) || $_POST['new_pass'] === '' )
	{

		User::setError("Digite a nova senha");

		header("Location: /profile/change-password");
		exit;
		
	}#end if


	# Validando se confirmou a nova senha
	if( !isset($_POST['new_pass_confirm']) || $_POST['new_pass_confirm'] === '' )
	{

		User::setError("Confirme a nova senha");

		header("Location: /profile/change-password");
		exit;
		
	}#end if
	

	# Validando se informou uma senha diferente da atual
	if( $_POST['current_pass'] === $_POST['new_pass'] )
	{

		User::setError("Escolha uma senha diferente da atual");

		header("Location: /profile/change-password");
		exit;

	}#end if

	$user = User::getFromSession();

	# Verificando se a senha atual informada é válida
	if( !password_verify($_POST['current_pass'], $user->getdespassword()) )
	{

		User::setError("Senha inválida");

		header("Location: /profile/change-password");
		exit;

	}#end if

	$user->setdespassword($_POST['new_pass']);

	$user->update();

	User::setSuccess("Senha alterada com sucesso");

	header("Location: /profile/change-password");
	exit;

});#END route


 ?>