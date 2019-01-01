<?php 

namespace Hcode\PagSeguro;

use DOMDocument;
use DOMElement;
use \Hcode\PagSeguro\Config;
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

		$payment = $dom->createElement("payment");
		$payment = $dom->appendChild($payment);
		
		$mode = $dom->createElement("mode", $this->mode);
		$mode = $payment->appendChild($mode);
		
		$currency = $dom->createElement("currency", $this->currency);
		$currency = $payment->appendChild($currency);
		
		$notificationUrl = $dom->createElement("notificationURL", Config::NOTIFICATION_URL);
		$notificationUrl = $payment->appendChild($notificationUrl);
		
		$mode = $dom->createElement("mode", $this->mode);
		$mode = $payment->appendChild($mode);

		$receiverEmail = $dom->createElement("receiverEmail", Config::PRODUCTION_EMAIL);
		$receiverEmail = $payment->appendChild($receiverEmail);

		$sender = $this->sender->getDOMElement();
		$sender = $dom->importNode($sender, true);
		$sender = $payment->appendChild($sender);

		$items = $dom->createElement("items");
		$items = $payment->appendChild($items);

		foreach ($this->items as $_item)
		{
			# code...
			$item = $_item->getDOMElement();
			$item = $dom->importNode($item, true);

			$item = $items->appendChild($item);

		}#end foreach

		$reference = $dom->createElement("reference", $this->reference);
		$reference = $payment->appendChild($reference);

		$shipping = $this->shipping->getDOMElement();
		$shipping = $dom->importNode($shipping, true);
		$shipping = $payment->appendChild($shipping);

		$extraAmount = $dom->createElement("extraAmount", $this->extraAmount);
		$extraAmount = $payment->appendChild($extraAmount);

		$method = $dom->createElement("method", $this->method);
		$method = $payment->appendChild($method);

		switch ($this->method) {
			case Method::CREDIT_CARD:
				# code...
				$creditCard = $this->creditCard->getDOMElement();
				$creditCard = $dom->importNode($creditCard, true);
				$creditCard = $payment->appendChild($creditCard);
				break;

			case Method::DEBIT:
				# code...
				$bank = $this->bank->getDOMElement();
				$bank = $dom->importNode($bank, true);
				$bank = $payment->appendChild($bank);
				break;

		}#end switch

	

		return $dom;

	}#END getDOMDocument

}#END class Payment

 ?>