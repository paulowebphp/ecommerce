<?php 

namespace Hcode\PagSeguro;

use DOMDocument;
use DOMElement;
# use Exception;

class Phone
{

	private $areaCode;
	private $number;


	public function __construct(int $areaCode, int $number)
	{

		# Validando DDD
		if(
			!$areaCode
			||
			$areaCode < 11
			||
			$areaCode > 99
		)

		{
			throw new \Exception("Informe o DDD");
			
		}#end if


		# Validando ó número do telefone
		if(
			!$number
			||
			strlen($number) < 8
			||
			strlen($number) > 9
		)
			
		{
			throw new \Exception("Informe o número do telefone");
			
		}#end if


		$this->areaCode = $areaCode;
		$this->number = $number;

	}#END __construct	


	public function getDOMElement():DOMElement
	{

		$dom = new DOMDocument();

		$phone = $dom->createElement("phone");
		$phone = $dom->appendChild($phone);

		$areaCode = $dom->createElement("areaCode", $this->areaCode);
		$areaCode = $phone->appendChild($areaCode);

		$number = $dom->createElement("number", $this->number);
		$number = $phone->appendChild($number);

		return $phone;

	}#END getDOMElement


}#END class Phone


 ?>