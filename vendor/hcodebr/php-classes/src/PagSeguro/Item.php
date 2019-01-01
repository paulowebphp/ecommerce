<?php 

namespace Hcode\PagSeguro;

use DOMDocument;
use DOMElement;


class Item
{

	private $id;
	private $description;
	private $amount;
	private $quantity;


	public function __construct(

		int $id, 
		string $description,
		float $amount,
		int $quantity

	)

	{

		# Validando id
		if(

			!$id
			||
			!$id > 0

		)
		{
			throw new \Exception("Informe o ID do item");
			
		}#end if


		# Validando description
		if( !$description )
		{
			throw new \Exception("Informe a descrição do item");
			
		}#end if


		# Validando amount
		if(

			!$amount
			||
			!$amount > 0

		)
		{
			throw new \Exception("Informe o valor total do item");
			
		}#end if


		# Validando quantity
		if(

			!$quantity
			||
			!$quantity > 0

		)
		{
			throw new \Exception("Informe a quantidade do item");
			
		}#end if

		$this->id = $id;
		$this->description = $description;
		$this->amount = $amount;
		$this->quantity = $quantity;

	}#END __construct


	public function getDOMElement():DOMElement
	{

		$dom = new DOMDocument();

		$item = $dom->createElement("item");
		$item = $dom->appendChild($item);

		$amount = $dom->createElement("amount", number_format($this->amount, 2, ".", ""));
		$amount = $item->appendChild($amount);

		$id = $dom->createElement("id", $this->id);
		$id = $item->appendChild($id);

		$quantity = $dom->createElement("quantity", $this->quantity);
		$quantity = $item->appendChild($quantity);

		$description = $dom->createElement("description", $this->description);
		$description = $item->appendChild($description);
	
		return $item;

	}#END getDOMElement

}#END class Item


 ?>