<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\Cart;



class Order extends Model
{

	const SUCCESS = "Order-Success";
	const ERROR = "Order-Error";


	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("

			CALL sp_orders_save(

			:idorder,
			:idcart,
			:iduser,
			:idstatus,
			:idaddress,
			:vltotal

			)", [

				':idorder'=>$this->getidorder(),
				':idcart'=>$this->getidcart(),
				':iduser'=>$this->getiduser(),
				':idstatus'=>$this->getidstatus(),
				':idaddress'=>$this->getidaddress(),
				':vltotal'=>$this->getvltotal()

			]);

		if( count($results) > 0 )
		{

			$this->setData($results[0]);

		}#end if


	}#END save




	public function get($idorder)
	{

		$sql = new Sql();

		$results = $sql->select("

			SELECT *
			FROM tb_orders a
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			WHERE a.idorder = :idorder

			", [

				':idorder'=>$idorder

			]);

		if( count($results) > 0 )
		{

			$this->setData($results[0]);
			
		}#end if

	}#END get



	public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("

			SELECT *
			FROM tb_orders a
			INNER JOIN tb_ordersstatus b USING(idstatus)
			INNER JOIN tb_carts c USING(idcart)
			INNER JOIN tb_users d ON d.iduser = a.iduser
			INNER JOIN tb_addresses e USING(idaddress)
			INNER JOIN tb_persons f ON f.idperson = d.idperson
			ORDER BY a.dtregister DESC

			");

	}#END listAll


	public function delete()
	{

		$sql = new Sql();

		$sql->query("

			DELETE FROM tb_orders
			WHERE idorder = :idorder

			", [

				'idorder'=>$this->getidorder()

			]);

	}#END delete



	public function getCart():Cart
	{

		$cart = new Cart();

		$cart->get((int)$this->getidcart());

		return $cart;
		
	}#END getCart



	public static function setError($msg)
	{

		$_SESSION[Order::ERROR] = $msg;

	}#END setError


	public static function getError()
	{

		$msg = (isset($_SESSION[Order::ERROR]) && $_SESSION[Order::ERROR]) ? $_SESSION[Order::ERROR] : '';

		Order::clearError();

		return $msg;

	}#END getError


	public static function clearError()
	{
		$_SESSION[Order::ERROR] = NULL;

	}#END clearError


	public static function setSuccess($msg)
	{

		$_SESSION[Order::SUCCESS] = $msg;

	}#END setSuccess


	public static function getSuccess()
	{

		$msg = (isset($_SESSION[Order::SUCCESS]) && $_SESSION[Order::SUCCESS]) ? $_SESSION[Order::SUCCESS] : '';

		Order::clearSuccess();

		return $msg;

	}#END getSuccess


	public static function clearSuccess()
	{
		$_SESSION[Order::SUCCESS] = NULL;

	}#END clearSuccess 


}#END class Order

 ?>