<?php 


namespace Hcode\PagSeguro\CreditCard;

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
			throw new \Exception("A quantidade máxima de parcelas é" . Config::MAX_INSTALLMENT);
			
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

		$installment = $dom->createElement($installment);
		$installment = $dom->appendChild($installment);

		$value = $dom->createElement("value", number_format($this->value, 2, ".", ""));
		$value = $shipping->appendChild($value);

		$quantity = $dom->createElement("quantity", $this->quantity);
		$quantity = $shipping->appendChild($quantity);

		$noInterestInstallmentQuantity = $dom->createElement("noInterestInstallmentQuantity", Config::MAX_INSTALLMENT_NO_INTEREST);
		$noInterestInstallmentQuantity = $shipping->appendChild($noInterestInstallmentQuantity);
 	
		return $installment;

	}#END getDOMElement


}#END class Installment


 ?>