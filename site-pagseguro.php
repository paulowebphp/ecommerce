<?php 

use \Hcode\Page;
use \Hcode\Model\User;
use \Hcode\Model\Order;
use \Hcode\PagSeguro\Config;
use \Hcode\PagSeguro\Transporter;
use \Hcode\PagSeguro\Document;
use \Hcode\PagSeguro\Phone;
use \Hcode\PagSeguro\Address;
use \Hcode\PagSeguro\Sender;
use \Hcode\PagSeguro\Shipping;
use \Hcode\PagSeguro\CreditCard;
use \Hcode\PagSeguro\Item;
use \Hcode\PagSeguro\Payment;
use \Hcode\PagSeguro\CreditCard\Holder;
use \Hcode\PagSeguro\CreditCard\Installment;



$app->post('/payment/credit', function() 
{

	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$order->get((int)$order->getidorder());

	$address = $order->getAddress();

	$cart = $order->getCart();

	$cpf = new Document(Document::CPF, $_POST['cpf']);


	$phone = new Phone($_POST['ddd'], $_POST['phone']);

	$shippingAddress = new Address(

		$address->getdesaddress(),
		$address->getdesnumber(),
		$address->getdescomplement(),
		$address->getdesdistrict(),
		$address->getdeszipcode(),
		$address->getdescity(),
		$address->getdesstate(),
		$address->getdescountry()

	);

	$birthDate = new DateTime($_POST['birth']);

	$sender = new Sender(

		$order->getdesperson(),
		$cpf,
		$birthDate,
		$phone,
		$order->getdesemail(),
		$_POST['hash']

	);

	$holder = new Holder(

		$order->getdesperson(),
		$cpf,
		$birthDate,
		$phone

	);

	$shipping = new Shipping(

		$shippingAddress,
		(float)$cart->vlfreight(),
		Shipping::PAC

	);

	$installment = new Installment(

		(int)$_POST["installments_qtd"],
		(float)$_POST["installments_value"]

	);

	$billingAddress = new Address(

		$address->getdesaddress(),
		$address->getdesnumber(),
		$address->getdescomplement(),
		$address->getdesdistrict(),
		$address->getdeszipcode(),
		$address->getdescity(),
		$address->getdesstate(),
		$address->getdescountry()

	);

	$creditCard = new CreditCard(

		$_POST['token'],
		$installment,
		$holder,
		$billingAddress

	);

	$payment = new Payment(

		$order->getidorder(),
		$sender,
		$shipping

	);




	foreach ($cart->getProducts() as $product)
	{
		# code...
		$item = new Item(

		(int)$product['idproduct'],
		$product['desproduct'],
		(float)$product['vlprice'],
		(int)$product['nrqtd'],

		);

		$payment->addItem($item);

	}#end foreach



	$payment->setCreditCard($creditCard);

	Transporter::sendTransaction($payment);



});#END route






$app->get('/payment', function() 
{
	User::verifyLogin(false);

	$order = new Order();

	$order->getFromSession();

	$years = [];

	for ($y = date('Y'); $y < date('Y')+14; $y++)
	{ 
		# code...
		array_push($years, $y);
	}#end for

	$page = new Page();

	$page->setTpl("payment", [

		"order" => $order->getValues(),
		"msgError" => Order::getError(),
		"years" => $years,
		"pagseguro" => [

			"urlJS" => Config::getUrlJS(),
			"id" => Transporter::createSession(),
			"maxInstallmentNoInterest" => Config::MAX_INSTALLMENT_NO_INTEREST,
			"maxInstallment" => Config::MAX_INSTALLMENT

		]

	]);

});#END route







 ?>