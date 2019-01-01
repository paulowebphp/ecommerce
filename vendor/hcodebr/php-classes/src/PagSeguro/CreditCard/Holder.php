<?php 


namespace Hcode\PagSeguro\CreditCard;


use DOMDocument;
use DOMElement;
use DateTime;
use \Hcode\PagSeguro\Document;
use \Hcode\PagSeguro\Phone;

# use Exception;

class Holder
{
	
	private $name;
	private $cpf;
	private $birthDate;
	private $phone;


	public function __construct(

		string $name, 
		Document $cpf, 
		DateTime $birthDate, 
		Phone $phone
	)

	{

		# Validando name
		if( !$name )
		{
			throw new \Exception("Informe o nome");
			
		}#end if


		$this->name = $name;
		$this->cpf = $cpf;
		$this->birthDate = $birthDate;
		$this->phone = $phone;

	}#END __construct	


	public function getDOMElement():DOMElement
	{

		$dom = new DOMDocument();

		$holder = $dom->createElement("holder");
		$holder = $dom->appendChild($holder);

		$name = $dom->createElement("name", $this->name);
		$name = $holder->appendChild($name);

		$birthDate = $dom->createElement("birthDate", $this->birthDate->format("d/m/Y"));
		$birthDate = $holder->appendChild($birthDate);

		$documents = $dom->createElement("documents");
		$documents = $holder->appendChild($documents);

		$cpf = $this->cpf->getDOMElement();
		$cpf = $dom->importNode($cpf, true);
		$cpf = $documents->appendChild($cpf);

		$phone = $this->phone->getDOMElement();
		$phone = $dom->importNode($phone, true);
		$phone = $holder->appendChild($phone);

		return $holder;

	}#END getDOMElement


}#END class Holder


 ?>