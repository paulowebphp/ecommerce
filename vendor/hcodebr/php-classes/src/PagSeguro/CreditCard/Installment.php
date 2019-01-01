<?php 


namespace Hcode\PagSeguro\CreditCard;

use DOMDocument;
use DOMElement;
use \Hcode\PagSeguro\Config;



class Installment
{

	private $quantity;
	private $value;


	public function __construct(

		int $quantity, 
		float $value

	)

	{

		# Validando quantity
		if( 

			$quantity < 1
			||
			$quantity > Config::MAX_INSTALLMENT
			
		)

		{
			throw new \Exception("Numero de parcelas invalido");
			
		}#end if


		# Validando value
		if( $value <= 0 )

		{
			throw new \Exception("Valor total inválido");
			
		}#end if


		$this->quantity = $quantity;
		$this->value = $value;

	}#END __construct	


	public function getDOMElement():DOMElement
	{

		$dom = new DOMDocument();

		$installment = $dom->createElement("installment");
		$installment = $dom->appendChild($installment);

		$quantity = $dom->createElement("quantity", $this->quantity);
		$quantity = $installment->appendChild($quantity);

		$value = $dom->createElement("value", number_format($this->value, 2, '.', ''));
		$value = $installment->appendChild($value);

		$noInterestInstallmentQuantity = $dom->createElement("noInterestInstallmentQuantity", Config::MAX_INSTALLMENT_NO_INTEREST);
		$noInterestInstallmentQuantity = $installment->appendChild($noInterestInstallmentQuantity);

		return $installment;

	}#END getDOMElement


}#END class Installment


 ?>