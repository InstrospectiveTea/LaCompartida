<?
require_once dirname(__FILE__).'/../conf.php';
require_once Conf::ServerDir().'/../fw/classes/Lista.php';
require_once Conf::ServerDir().'/../fw/classes/Objeto.php';
require_once Conf::ServerDir().'/../app/classes/Debug.php';

class CobroAsunto extends Objeto
{

	function CobroAsunto($sesion, $fields = "", $params = "")
	{
		$this->tabla = "cobro_asunto";
		$this->campo_id = "id_cobro_asunto";
		$this->sesion = $sesion;
		$this->fields = $fields;
		$this->guardar_fecha = false;
	}
    function LoadByCodigoAsunto($codigo,$id_cobro)
    {
        $query = "SELECT id_cobro_asunto FROM cobro_asunto WHERE codigo_asunto='$codigo' AND id_cobro='$id_cobro'";
        $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$this->sesion->dbh);
        list($id) = mysql_fetch_array($resp);
        return $this->Load($id);
    }
	
}

function CheckAll($id_cobro, $codigo_cliente)
{
    
			$query="SELECT * FROM asunto WHERE codigo_cliente = '$codigo_cliente' AND activo='1' AND cobrable='1'";
        $resp = mysql_query($query);

        while($row = mysql_fetch_array($resp))
        {
         $id_moneda = $row['id_moneda'];
         $codigo_asunto =  $row['codigo_asunto'];
         $query_insert = "INSERT INTO cobro_asunto SET id_cobro='$id_cobro', codigo_asunto = '$codigo_asunto', id_moneda = '$id_moneda'
                          ON DUPLICATE KEY UPDATE id_cobro='$id_cobro', codigo_asunto = '$codigo_asunto', id_moneda = '$id_moneda'";

         $result = mysql_query($query_insert);
      }
}
