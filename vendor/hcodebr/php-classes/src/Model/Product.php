<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Product extends Model
{
	public static function listAll()
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");
		
	}#END listAll


	public static function checkList($list)
	{
		foreach ($list as &$row)
		{
			# code...
			$p = new Product();

			$p->setData($row);

			$row = $p->getValues();

		}#end foreach

		return $list;

	}#END checkList


	public function save()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(

			":idproduct"=>$this->getidproduct(),
			":desproduct"=>$this->getdesproduct(),
			":vlprice"=>$this->getvlprice(),
			":vlwidth"=>$this->getvlwidth(),
			":vlheight"=>$this->getvlheight(),
			":vllength"=>$this->getvllength(),
			":vlweight"=>$this->getvlweight(),
			":desurl"=>$this->getdesurl()

		));

		$this->setData($results[0]);

	}#END save


	public function get($idproduct)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products
			WHERE idproduct = :idproduct", [

				':idproduct'=>$idproduct

			]);

		$this->setData($results[0]);

	}#END get


	# DELETE não recebe parâmetro porque espera-se que o objeto
	# já esteja carregado
	public function delete()
	{
		$sql = new Sql();

		$sql->query("DELETE FROM tb_products
			WHERE idproduct = :idproduct",[

				':idproduct'=>$this->getidproduct()

			]);

	}#END delete


	public function checkPhoto()
	{
		if(file_exists(
			$_SERVER['DOCUMENT_ROOT']. 
			DIRECTORY_SEPARATOR. "res" . 
			DIRECTORY_SEPARATOR. "site" . 
			DIRECTORY_SEPARATOR. "img" . 
			DIRECTORY_SEPARATOR. "products" .
			DIRECTORY_SEPARATOR. $this->getidproduct() . ".jpg"
		))

		{
			$url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";


		}#end if

		else
		{
			$url = "/res/site/img/product.jpg"; 

		}#end else

		return $this->setdesphoto($url);

	}#END getPhoto


	public function getValues()
	{
		$this->checkPhoto();

		$values = parent::getValues();

		return $values;

	}#END getValues


	public function setPhoto($file)
	{
		/*
		$extension = explode('.', $file['name']);

		$extension = end($extension);

		switch($extension)
		{
			case "jpg":
			case "jpg":

				$image = imagecreatefromjpeg($file["tmp_name"]);
				break;

			case "gif":

				$image = imagecreatefromgif($file["tmp_name"]);
				break;

			case "png":

				$image = imagecreatefrompng($file["tmp_name"]);
				break;

		}#end switch

		$dist = $_SERVER['DOCUMENT_ROOT']. 
			DIRECTORY_SEPARATOR. "res" . 
			DIRECTORY_SEPARATOR. "site" . 
			DIRECTORY_SEPARATOR. "img" . 
			DIRECTORY_SEPARATOR. "products" .
			DIRECTORY_SEPARATOR. $this->getidproduct() . ".jpg";

		imagejpeg($image, $dist);

		imagedestroy($image);

		$this->checkPhoto();
		*/
		if(empty($file['name']))
		{
			$this->checkPhoto();
			
		}#end if
		else
		{
			$extension = explode('.', $file['name']);

			$extension = end($extension);

			switch($extension)
			{
				case "jpg":
				case "jpg":

					$image = imagecreatefromjpeg($file["tmp_name"]);
					break;

				case "gif":

					$image = imagecreatefromgif($file["tmp_name"]);
					break;

				case "png":

					$image = imagecreatefrompng($file["tmp_name"]);
					break;

			}#end switch

			$dist = $_SERVER['DOCUMENT_ROOT']. 
				DIRECTORY_SEPARATOR. "res" . 
				DIRECTORY_SEPARATOR. "site" . 
				DIRECTORY_SEPARATOR. "img" . 
				DIRECTORY_SEPARATOR. "products" .
				DIRECTORY_SEPARATOR. $this->getidproduct() . ".jpg";

			imagejpeg($image, $dist);

			imagedestroy($image);

			$this->checkPhoto();

		}#end else

	}#END setPhoto

	public function getFromURL($desurl)
	{
		$sql = new Sql();

		$rows = $sql->select("

			SELECT * FROM tb_products 
			WHERE desurl = :desurl
			LIMIT 1;

			", [

				':desurl'=>$desurl

			]);

		$this->setData($rows[0]);

	}#END getFromURL


	public function getCategories()
	{
		$sql = new Sql();

		return $sql->select("

			SELECT * FROM tb_categories a
			INNER JOIN tb_productscategories b
			ON a.idcategory = b.idcategory
			WHERE b.idproduct = :idproduct

			", [

				':idproduct'=>$this->getidproduct()

			]);

	}#END getCategories


	public static function getPage($page = 1, $itensPerPage = 10)
	{
		$start = ($page - 1) * $itensPerPage;

		$sql = new Sql();

		$results = $sql->select("

			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products 
			ORDER BY desproduct
			LIMIT $start, $itensPerPage;

			");

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [

			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itensPerPage)

		];


	}#END getPage


	public static function getPageSearch($search, $page = 1, $itensPerPage = 10)
	{
		$start = ($page - 1) * $itensPerPage;

		$sql = new Sql();

		$results = $sql->select("

			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products
			WHERE desproduct LIKE :search
			ORDER BY desproduct
			LIMIT $start, $itensPerPage;

			", [

				':search'=>'%'.$search.'%'

			]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [

			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itensPerPage)

		];


	}#END getPageSearch

}#END class user_error()

 ?>