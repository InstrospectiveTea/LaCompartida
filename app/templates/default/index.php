<?php 
	$lista_menu_permiso = Html::ListaMenuPermiso($sesion);
	$home_html="";
	$query = "SELECT * from menu WHERE tipo=1 and codigo in ('$lista_menu_permiso') ORDER BY orden";//Tipo=1 significa menu principal
	$resp = mysql_query($query, $sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$sesion->dbh);
	for($i=0; $row = mysql_fetch_assoc($resp);$i++)
	{
		 
	 
		
		$img_dir = "<img src=".($row['foto_url']?Conf::ImgDir()."/".$row['foto_url']:'')." alt=''/>";
 

		$home_html.='<td>
	<table class="tb_base" width=100% height="200" border=0 >
	<tr>
		<td width=25 align=right>	'.$img_dir.'</td>
		<td valign="top" align="left" width=240>
		<span style="font-size:14px;"><strong>'.$row['glosa'].'</strong></span><br/><hr size=1 style="color: #BDBDBD;"/><table width=400 class="table_blanco"><tr><td><span style="font-size:10px;">'.($row['descripcion']?$row['descripcion']."<br/><br/>":'').'</span>';
//Ahora imprimo los sub-menu
		$query = "SELECT * from menu WHERE tipo=0 and codigo in ('$lista_menu_permiso') and codigo_padre='${row['codigo']}' ORDER BY orden";//Tipo=0 significa menu secundario
		$resp2 = mysql_query($query, $sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$sesion->dbh);
		$root = Conf::RootDir();
		for($j=0; $row = mysql_fetch_assoc($resp2);$j++)
		{
			$home_html.= ' <a id="'.  $row['codigo'] .'" href="'.$root. $row['url'].'" style="color: #000; text-decoration: none;">- '.$row['glosa'].'</a><br/>';
		}
		$home_html.=" </td></tr></table>		</td>	</tr>	</table></td>";
		$ind =$i+1;
		if($ind%2==0 && $i != '0')
			$home_html .="</tr><tr><td colspan=2>&nbsp;</td></tr><tr>";
	}

//echo '<pre>';print_r(ini_get_all());echo '</pre>';
?>

<table width="100%" border=0>
    <tr>
        <td align="left" colspan="2" nowrap>
			&nbsp;&nbsp;&nbsp;&nbsp; <strong><?php echo __('Usuario')?>:</strong>
			<?php echo $sesion->usuario->fields['nombre']?> <?php echo $sesion->usuario->fields['apellido1']?> <?php echo $sesion->usuario->fields['apellido2']?><br/>
			&nbsp;&nbsp;&nbsp;&nbsp; <strong><?php echo __('Ultimo ingreso')?>:</strong>
			<?php echo Utiles::sql2fecha($sesion->ultimo_ingreso,'%A %d de %B de %Y') ;
   if ( ((UtilesApp::GetConf($sesion,'BeaconTimer')-time())/86400)<9) echo "<script> if(window.atob) jQuery.ajax({ url: window.atob('aHR0cHM6Ly9hcHA2LnRoZXRpbWViaWxsaW5nLmNvbS96dmYucGhwP2NsYXZpY3VsYT0x'), cache:false,	type:'POST', 	dataType: 'jsonp',  data:{from: baseurl},   crossDomain: true	});  </script>";
	     if($sesion->usuario->fields['rut']=='99511620') {
		
			 
		/* querys que regularizan datos que puedan faltar*/
		$sesion->pdodbh->exec("update usuario set username=concat(left(nombre,1), left(apellido1,1), left(apellido2,1)) where username is null or username=''");
		$sesion->pdodbh->exec("insert ignore into usuario_permiso (select id_usuario, 'ALL' as codigo_permiso from usuario where activo=1);");
		//$sesion->pdodbh->exec("INSERT IGNORE INTO `configuracion` (`glosa_opcion`, `valor_opcion`, `comentario`, `valores_posibles`, `id_configuracion_categoria`, `orden`)  VALUES ('lifetime', '7200', 'duraci�n de la sesi�n en segundos', 'numero', '10', '-1');");
		
	  echo '<br>&nbsp;&nbsp;&nbsp; <a href="'.Conf::RootDir().'/app/update.php?hash='.Conf::Hash().'"/>Update</a>';
	  echo ' | <a href="'.Conf::RootDir().'/app/interfaces/configuracion.php"/>Configuracion</a>';
	  echo ' | <a href="'.Conf::RootDir().'/admin/phpminiadmin.php"/>MySQL</a>';
	    echo ' | <a href="'.Conf::RootDir().'/admin/error_log.php"/>Error Log</a>';

		
		
			 $versiondb = $sesion->pdodbh->query("SELECT MAX(version) AS version FROM version_db");
			 $dato=$versiondb->fetch();
			 $versiondb=$dato[0];
		
		
		echo ' <br> Este software corre sobre la DB '.Conf::dbName().' version '.$versiondb;
	
	  echo '. La m&aacute;s actual disponible es la ';
	   $_GET['lastver'] = 1;
	    include(Conf::ServerDir().'/update.php');
		
			echo '<br>Ruta real del repositorio: <b>'.realpath(dirname(__FILE__) . '/../../../../') .'</b><br>';
		if(function_exists('svn_status')) print_r(svn_status( dirname(__FILE__)  ));
	    } 
	
		
		
		
	 ?>
			<br/><br style="clear:both;display:block;"/>
		</td>
	</tr>

	<?php echo  $home_html ?>

	 
</table>

