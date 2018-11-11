<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Category extends Model
{
	public static function listAll()
	{
		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
		
	}#END listAll


	public function save()
	{
		$sql = new Sql();

		$results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(

			":idcategory"=>$this->getidcategory(),
			":descategory"=>$this->getdescategory()

		));

		$this->setData($results[0]);

		Category::updateFile();

	}#END save


	public function get($idcategory)
	{
		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_categories
			WHERE idcategory = :idcategory", [

				':idcategory'=>$idcategory

			]);

		$this->setData($results[0]);

	}#END get


	# DELETE não recebe parâmetro porque espera-se que o objeto
	# já esteja carregado
	public function delete()
	{
		$sql = new Sql();

		$sql->query("DELETE FROM tb_categories
			WHERE idcategory = :idcategory",[

				':idcategory'=>$this->getidcategory()

			]);

		Category::updateFile();

	}#END delete


	public static function updateFile()
	{
		$categories = Category::listAll();

		$html = [];

		foreach ($categories as $row)
		{
			# code...
			array_push($html, '<li><a href="/categories/'.$row['idcategory'].'">'.$row['descategory'].'</a></li>');

		}#end foreach

		file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));

	}#END updateFile

}#END class user_error()

 ?>