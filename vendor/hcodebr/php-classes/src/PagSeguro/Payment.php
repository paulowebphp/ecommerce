<?php 

namespace Hcode\PagSeguro;

use DOMDocument;
use DOMElement;
use \Hcode\PagSeguro\CreditCard;
use \Hcode\PagSeguro\Bank;
use \Hcode\PagSeguro\Payment\Method;

class Payment
{

	private $mode = "default";
	private $currency = "BRL";
	private $extraAmount = 0;
	private $reference = "";
	private $items = [];
	private $sender;
	private $shipping;
	private $method;
	private $creditCard;
	private $bank;


	public function __construct(

		string $reference, 
		Sender $sender,
		Shipping $shipping,
		float $extraAmount = 0

	)

	{

		$this->sender = $sender;
		$this->shipping = $shipping;
		$this->reference = $reference;
		$this->extraAmount = number_format($extraAmount, 2, ".", "");

	}#END __construct



	public function addItem(Item $item)
	{
		array_push($this->items, $item);

	}#END addItem




	public function setCreditCard(CreditCard $creditCard)
	{

		$this->creditCard = $creditCard;

		$this->method = Method::CREDIT_CARD;


	}#END setCreditCard



	public function setBank(Bank $bank)
	{

		$this->bank = $bank;

		$this->method = Method::DEBIT;


	}#END setCreditCard



	public function setBoleto()
	{

		$this->method = Method::BOLETO;


	}#END setCreditCard




	public function getDOMDocument():DOMDocument
	{

		$dom = new DOMDocument("1.0", "ISO-8859-1");

		
		


		

		return $dom;

	}#END getDOMDocument

}#END class Payment

 ?>