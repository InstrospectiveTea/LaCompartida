<?
	require_once dirname(__FILE__).'/../conf.php';
	require_once Conf::ServerDir().'/../fw/classes/Sesion.php';
  require_once Conf::ServerDir().'/../fw/classes/Pagina.php';
	require_once Conf::ServerDir().'/../fw/classes/Utiles.php';
	require_once Conf::ServerDir().'/../fw/classes/Html.php';
	require_once Conf::ServerDir().'/../fw/classes/Buscador.php';
	require_once Conf::ServerDir().'/../app/classes/Debug.php';
	require_once Conf::ServerDir().'/classes/Carpeta.php';
	require_once Conf::ServerDir().'/classes/InputId.php';
	require_once Conf::ServerDir().'/classes/Funciones.php';
	require_once Conf::ServerDir().'/classes/Asunto.php';
	require_once Conf::ServerDir().'/classes/Cliente.php';
	require_once Conf::ServerDir().'/classes/Autocompletador.php';

	$sesion = new Sesion(array('EDI'));
	$pagina = new Pagina($sesion);
	$id_usuario = $sesion->usuario->fields['id_usuario'];

	$carpeta = new Carpeta($sesion);
	if($id_carpeta > 0)
	{
		$carpeta->Load($id_carpeta);
		if(!$codigo_asunto_secundario)
		{
			$asunto = new Asunto($sesion);
			$asunto->LoadByCodigo($carpeta->fields['codigo_asunto']);
			$codigo_asunto_secundario=$asunto->fields['codigo_asunto_secundario'];
		}
		else
		{
			$asunto = new Asunto($sesion);
			$asunto->LoadByCodigoSecundario($codigo_asunto_secundario);
			$codigo_asunto = $asunto->fields['codigo_asunto'];
		}
		if(!$codigo_cliente && !$codigo_cliente_secundario)
		{
			$codigo_cliente=$asunto->fields['codigo_cliente'];
			$cliente = new Cliente($sesion);
			$cliente->LoadByCodigo($codigo_cliente);
			$codigo_cliente_secundario=$cliente->fields['codigo_cliente_secundario'];
		}
	}
	else
	{
		$codigo_carpeta=$carpeta->AsignarCodigoCarpeta();
		if($codigo_asunto_secundario)
		{
			$asunto = new Asunto($sesion);
			$asunto->LoadByCodigoSecundario($codigo_asunto_secundario);
			$codigo_asunto=$asunto->fields['codigo_asunto'];
		}
		if($codigo_cliente_secundario)
		{
			$cliente = new Cliente($sesion);
			$cliente->LoadByCodigoSecundario($codigo_cliente_secundario);
			$codigo_cliente=$cliente->fields['codigo_cliente'];
		}
	}
		
	if($opcion == 'guardar')
	{
		
		$carpeta->Edit('codigo_carpeta', $codigo_carpeta);
		$carpeta->Edit('glosa_carpeta', $glosa_carpeta);
		$carpeta->Edit('nombre_carpeta',$nombre_carpeta);
		$carpeta->Edit('codigo_asunto', $codigo_asunto);
		$carpeta->Edit('id_tipo_carpeta',$id_tipo_carpeta);
		$carpeta->Edit('id_bodega',$id_bodega);
		

		if($carpeta->Write())
		{
			$pagina->AddInfo( __('Archivo guardado con exito') );
		}
		else
			$pagina->AddError($carpeta->error);
	}

	$txt_pagina = $id_carpeta ? __('Edici�n de Carpeta') : __('Ingreso de Carpeta');

	$pagina->titulo = $txt_pagina;
	$pagina->PrintTop($popup);
?>
<script>

function Validar()
{
	var form = $('form_agregar_carpeta');

	if(!form.glosa_carpeta.value)
	{
		alert("<?=__('Ud. debe ingresar el t�tulo del archivo')?>");
		form.glosa_carpeta.focus();
        return false;
	}
	<?
			if( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'CodigoSecundario') ) || ( method_exists('Conf','CodigoSecundario') && Conf::CodigoSecundario() ) )
			{
				echo "if(!form.codigo_asunto_secundario.value){";
			}
			else
			{
				echo "if(!form.codigo_asunto.value){";
			}
?>
			alert("<?=__('Debe seleccionar un').' '.__('asunto')?>");
<?
			if ( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'CodigoSecundario') ) || ( method_exists('Conf','CodigoSecundario') && Conf::CodigoSecundario() ) )
			{
				echo "form.codigo_asunto_secundario.focus();";
			}
			else
			{
				echo "form.codigo_asunto.focus();";
			}
?>
			return false;
    }
    <?
			if( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'CodigoSecundario') ) || ( method_exists('Conf','CodigoSecundario') && Conf::CodigoSecundario() ) )
			{
				echo "if(!form.codigo_cliente_secundario.value){";
			}
			else
			{
				echo "if(!form.codigo_cliente.value){";
			}
?>
			alert("<?=__('Debe seleccionar un').' '.__('cliente')?>");
<?
			if ( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'CodigoSecundario') ) || ( method_exists('Conf','CodigoSecundario') && Conf::CodigoSecundario() ) )
			{
				echo "form.codigo_cliente_secundario.focus();";
			}
			else
			{
				echo "form.codigo_cliente.focus();";
			}
?>
			return false;
    }
form.submit();
return true;
}
</script>
<? echo Autocompletador::CSS(); ?>
<form method="post" action="agregar_carpeta.php" name="form_agregar_carpeta" id="form_agregar_carpeta">
<input type=hidden name=opcion value="guardar" />
<input type=hidden name=popup value="1" />
<input type=hidden name=codigo_carpeta value="<?= $carpeta->fields['codigo_carpeta'] ?>" />
<input type=hidden name=id_carpeta value="<?= $carpeta->fields['id_carpeta'] ?>" />

<br>
<table width='90%'>
	<tr>
		<td align=left><b><?=$txt_pagina ?></b></td>
	</tr>
</table>
<br>

<table style="border: 1px solid black;" width='90%'>
	<tr>
		<td align=right>
			<?=__('N� Archivo')?>
		</td>
		<td align=left>
			<input name="codigo_carpeta" size="5" maxlength="5" readonly value="<?=$carpeta->fields['codigo_carpeta']?>" id="codigo_carpeta" />
	<? if( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'SistemaCarpetasEspecial') ) || ( method_exists('Conf','SistemaCarpetasEspecial') && Conf::SistemaCarpetasEspecial() ) ){?>
			<?=__('Nombre')?>
			<input name='nombre_carpeta' size='35' value="<?= $carpeta->fields['nombre_carpeta'] ?>" />
			<span style="color:#FF0000; font-size:10px">*</span>
		</td>
	</tr>
	<tr>
		<td align=right>
			<?=__('Contenido')?>
		</td>
		<td align=left>
	<? } else {?>
			<?=__('Contenido')?>
	<? } ?>
			<input name='glosa_carpeta' size='60' value="<?= $carpeta->fields['glosa_carpeta'] ?>" />
			<span style="color:#FF0000; font-size:10px">*</span>
		</td>
	</tr>
	<tr>
		<td align=right>
			<?=__('Cliente')?>
		</td>
		<td align=left nowrap>
			<?
				if( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'TipoSelectCliente')=='autocompletador' ) || ( method_exists('Conf','TipoSelectCliente') && Conf::TipoSelectCliente() ) )
					{
						if( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'CodigoSecundario') ) || ( method_exists('Conf','CodigoSecundario') && Conf::CodigoSecundario() ) )
							echo Autocompletador::ImprimirSelector($sesion,'',$codigo_cliente_secundario);
						else 
							echo Autocompletador::ImprimirSelector($sesion,$codigo_cliente);
					}
				else
					{
						if( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'CodigoSecundario') ) || ( method_exists('Conf','CodigoSecundario') && Conf::CodigoSecundario() ) )
						{
							echo InputId::Imprimir($sesion,"cliente","codigo_cliente_secundario","glosa_cliente", "codigo_cliente_secundario", $codigo_cliente_secundario,"","CargarSelect('codigo_cliente_secundario','codigo_asunto_secundario','cargar_asuntos',1);", 320, $codigo_asunto_secundario);
						}
						else
						{
							echo InputId::Imprimir($sesion,"cliente","codigo_cliente","glosa_cliente", "codigo_cliente", $codigo_cliente,"","CargarSelect('codigo_cliente','codigo_asunto','cargar_asuntos',1);", 320, $codigo_asunto);
						}
					}
?>
		</td>
	</tr>
	<tr>
		<td align=right>
			<?=__('Asunto')?>
		</td>
		<td align=left >
			<?
					if (( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'CodigoSecundario') ) || ( method_exists('Conf','CodigoSecundario') && Conf::CodigoSecundario() ) ))
					{
						echo InputId::Imprimir($sesion,"asunto","codigo_asunto_secundario","glosa_asunto", "codigo_asunto_secundario", $codigo_asunto_secundario,"","CargarSelectCliente(this.value);", 320,  $codigo_cliente_secundario);
					}
					else
					{
						echo InputId::Imprimir($sesion,"asunto","codigo_asunto","glosa_asunto", "codigo_asunto", $carpeta->fields['codigo_asunto'],"", "CargarSelectCliente(this.value);", 320,  $codigo_cliente);
					}
?>
			<br />
		</td>
	</tr>
	<tr>
		<td align=right>
			<?=__('Ubicaci�n')?>
		</td>
		<td align=left >
			<?= Html::SelectQuery($sesion, "SELECT * FROM bodega ORDER BY glosa_bodega","id_bodega", $id_bodega ? $id_bodega : $carpeta->fields['id_bodega'],"onchange='cambia_bodega(this.value)'","","120"); ?>
			<?=__('Tipo')?>
			<?= Html::SelectQuery($sesion, "SELECT * FROM prm_tipo_carpeta ORDER BY glosa_tipo_carpeta","id_tipo_carpeta", $id_tipo_carpeta ? $id_tipo_carpeta : $carpeta->fields['id_tipo_carpeta'],"onchange='cambia_bodega(this.value)'","","120"); ?>
		</td>
	</tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	<tr>
		<td colspan=2 align="center">
			<input type="button" class=btn value="<?=__('Guardar')?>" onclick="return Validar()" />
		</td>
	</tr>
	<? if($id_carpeta){ ?>
<tr>
        <td align=right colspan=2><img src='<?=Conf::ImgDir()?>/agregar.gif' border=0> 
        <a href='agregar_carpeta.php?popup=1'>Agregar nuevo archivo</a></td></tr>
        	<?}?>
        			<? if($carpeta->Loaded()){ ?>
	<tr><td colspan=2 align='center'>
		<?
			if($carpeta->fields['id_tipo_movimiento_carpeta'] > 0)
			{
				$query = "SELECT CONCAT_WS(' ',usuario.nombre,usuario.apellido1,usuario.apellido2) as nombre_abogado,
										CONCAT_WS(' ',usuario_modificacion.nombre,usuario_modificacion.apellido1,usuario_modificacion.apellido2) as nombre_modificador,
										carpeta.fecha_modificacion, prm_tipo_movimiento_carpeta.glosa_tipo_movimiento_carpeta
										FROM carpeta
										LEFT JOIN prm_tipo_movimiento_carpeta ON prm_tipo_movimiento_carpeta.id_tipo_movimiento_carpeta=carpeta.id_tipo_movimiento_carpeta
										LEFT JOIN usuario ON usuario.id_usuario=carpeta.id_usuario_ultimo_movimiento
										LEFT JOIN usuario AS usuario_modificacion ON usuario_modificacion.id_usuario=carpeta.id_usuario_modificacion
										WHERE id_carpeta=".$carpeta->fields['id_carpeta'];
				$resp = mysql_query($query, $sesion->dbh);
				$row = mysql_fetch_array($resp);
				echo "<span style='font-size:10pt;font-style:italic'>".$row['glosa_tipo_movimiento_carpeta']." por ".$row['nombre_abogado']." con fecha ".Utiles::sql2date($row['fecha_modificacion'])." (".$row['nombre_modificador'].").</span>";
			}
			else
			{
				echo "<span style='font-size:10pt;font-style:italic'>No existe movimiento.</span>";
			}
		?>
	</td>
</tr>
<?}?>
</table>
	
</form>
<br/><br/>
<?
	if( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'TipoSelectCliente')=='autocompletador' ) || ( method_exists('Conf','TipoSelectCliente') && Conf::TipoSelectCliente() ) )
	{
		echo(Autocompletador::Javascript($sesion));
	}
	echo(InputId::Javascript($sesion));
	$pagina->PrintBottom($popup);
?>