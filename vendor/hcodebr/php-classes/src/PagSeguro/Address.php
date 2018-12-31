<?php 

namespace Hcode\PagSeguro;

use DOMDocument;
use DOMElement;
# use Exception;


class Address
{

	private $street;
	private $number;
	private $complement;
	private $district;
	private $postalCode;
	private $city;
	private $state;
	private $country;


	public function __construct(

		string $street, 
		string $number, 
		string $complement, 
		string $district, 
		string $postalCode, 
		string $city, 
		string $state, 
		string $country

	)

	{

		# Validando street
		if( !$street )
		{
			throw new \Exception("Informe o logradouro");
			
		}#end if


		# Validando number
		if( !$number )
		{
			throw new \Exception("Informe o número");
			
		}#end if


		# Validando complement
		if( !$complement )
		{
			throw new \Exception("Informe o complemento");
			
		}#end if

		# Validando district
		if( !$district )
		{
			throw new \Exception("Informe o bairro");
			
		}#end if

		# Validando postalCode
		if( !$postalCode )
		{
			throw new \Exception("Informe o CEP");
			
		}#end if

		# Validando city
		if( !$city )
		{
			throw new \Exception("Informe a cidade");
			
		}#end if

		# Validando state
		if( !$state )
		{
			throw new \Exception("Informe o estado");
			
		}#end if

		# Validando country
		if( !$country )
		{
			throw new \Exception("Informe o país");
			
		}#end if

		$this->street = $street;
		$this->number = $number;
		$this->complement = $complement;
		$this->district = $district;
		$this->postalCode = $postalCode;
		$this->city = $city;
		$this->state = $state;
		$this->country = $country;

	}#END __construct	


	public function getDOMElement($node = "address"):DOMElement
	{

		$dom = new DOMDocument();

		$address = $dom->createElement($node);
		$address = $dom->appendChild($address);

		$street = $dom->createElement("street", $this->street);
		$street = $address->appendChild($street);

		$number = $dom->createElement("number", $this->number);
		$number = $address->appendChild($number);

		$complement = $dom->createElement("complement", $this->complement);
		$complement = $address->appendChild($complement);

		$district = $dom->createElement("district", $this->district);
		$district = $address->appendChild($district);

		$postalCode = $dom->createElement("postalCode", $this->postalCode);
		$postalCode = $address->appendChild($postalCode);

		$city = $dom->createElement("city", $this->city);
		$city = $address->appendChild($city);

		$state = $dom->createElement("state", $this->state);
		$state = $address->appendChild($state);

		$country = $dom->createElement("country", $this->country);
		$country = $address->appendChild($country);

		return $address;

	}#END getDOMElement


}#END class Address


 ?>