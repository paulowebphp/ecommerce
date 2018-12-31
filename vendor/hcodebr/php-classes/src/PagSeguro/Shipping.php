<?php 

namespace Hcode\PagSeguro;

use DOMDocument;
use DOMElement;

class Shipping
{

	const PAC = 1;
	const SEDEX = 2;
	const OTHER = 3;

	private $address;
	private $type;
	private $cost;
	private $addressRequired;


	public function __construct(

		Address $address,
		float $cost,
		int $type, 
		bool $addressRequired = true

	)

	{

		# Validando type
		if( 

			$type < 1
			||
			$type > 3

		)

		{
			throw new \Exception("Informe um tipo de frete válido");
			
		}#end if


		$this->address = $address;
		$this->cost = $cost;
		$this->type = $type;
		$this->addressRequired = $addressRequired;

	}#END __construct	


	public function getDOMElement():DOMElement
	{

		$dom = new DOMDocument();

		$shipping = $dom->createElement("shipping");
		$shipping = $dom->appendChild($shipping);

		$address = $this->address->getDOMElement();
		$address = $dom->importNode($address, true);
		$address = $shipping->appendChild($address);

		$cost = $dom->createElement("cost", number_format($this->cost, 2, ".", ""));
		$cost = $shipping->appendChild($cost);

		$type = $dom->createElement("type", $this->type);
		$type = $shipping->appendChild($type);

		$addressRequired = $dom->createElement("addressRequired", ($this->addressRequired) ? "true" : "false");
		$addressRequired = $shipping->appendChild($addressRequired);
 	
		return $shipping;

	}#END getDOMElement



}#END class Shipping


 ?>