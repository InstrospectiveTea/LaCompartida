<?
require_once dirname(__FILE__).'/../conf.php';
require_once Conf::ServerDir().'/../fw/classes/Lista.php';
require_once Conf::ServerDir().'/../fw/classes/Objeto.php';
require_once Conf::ServerDir().'/../app/classes/NeteoDocumento.php';
require_once Conf::ServerDir().'/../app/classes/Debug.php';
require_once Conf::ServerDir().'/../app/classes/Cobro.php';
require_once Conf::ServerDir().'/../app/classes/CobroMoneda.php';
require_once Conf::ServerDir().'/../app/classes/Moneda.php';

class Documento extends Objeto
{
	function Documento($sesion, $fields = "", $params = "")
	{
		$this->tabla = "documento";
		$this->campo_id = "id_documento";
		#$this->guardar_fecha = false;
		$this->sesion = $sesion;
		$this->fields = $fields;
	}

	function LoadByCobro($id_cobro)
	{
		$query = "SELECT id_documento FROM documento WHERE id_cobro = '$id_cobro' AND tipo_doc='N';";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$this->sesion->dbh);
		list($id) = mysql_fetch_array($resp);

		if($id)
			return $this->Load($id);
		return false;
	}
	
	function AnularMontos()
	{
		$this->EliminarNeteos();
		$anular = array(
			'subtotal_honorarios',
			'subtotal_gastos',
			'descuento_honorarios',
			'subtotal_sin_descuento',
			'honorarios',
			'saldo_honorarios',
			'monto',
			'gastos',
			'saldo_gastos',
			'monto_base',
			'impuesto',
			'saldo_pago'
		);
		foreach($anular as $a)
			$this->Edit($a,'0');
		$this->Edit('honorarios_pagados','NO');
		$this->Edit('gastos_pagados','NO');
		$this->Write();
	}
	
	function BorrarDocumentoMoneda()
	{
			$query = "DELETE FROM documento_moneda WHERE id_documento = '".$this->fields['id_documento']."'";
			$resp = mysql_query($query,$this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__, $this->sesion->dbh);
	}
	
	function ActualizarDocumentoMoneda($tipo_cambio = array())
	{
			$query = "DELETE FROM documento_moneda WHERE id_documento = '".$this->fields['id_documento']."'";
			$resp = mysql_query($query,$this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__, $this->sesion->dbh);
			
			if(empty($tipo_cambio))
			{
				$query = "INSERT INTO documento_moneda (id_documento, id_moneda, tipo_cambio)
					SELECT '".$this->fields['id_documento']."', id_moneda, tipo_cambio
					FROM prm_moneda WHERE 1";
				$resp =mysql_query($query,$this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__, $this->sesion->dbh);
			}
			else foreach($tipo_cambio as $id_moneda => $tc)
			{
				$query = "INSERT INTO documento_moneda (id_documento, id_moneda, tipo_cambio)
					VALUES (".$this->fields['id_documento'].",".$id_moneda.",".$tc.");";
				$resp =mysql_query($query,$this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__, $this->sesion->dbh);
			}
	}
	
	function TipoCambioDocumento(& $sesion, $id_documento, $id_moneda)
	{
		$query = "SELECT tipo_cambio FROM documento_moneda WHERE id_documento = '$id_documento' AND id_moneda = '$id_moneda' ";
		$resp  = mysql_query($query,$sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$sesion->dbh);
		list($tc) = mysql_fetch_array($resp);
		
		return $tc;
	}
	
	function IngresoDocumentoPago(& $pagina, $id_cobro, $codigo_cliente, $monto, $id_moneda, $tipo_doc, $numero_doc="", $fecha, $glosa_documento="", $id_banco="", $id_cuenta="", $numero_operacion="", $numero_cheque="", $ids_monedas_documento, $tipo_cambios_documento, $arreglo_pagos_detalle=array(), $id_factura_pago = null)
	{
		if($id_cobro)
				{
				$query="UPDATE cobro SET fecha_cobro='".Utiles::fecha2sql($fecha)." 00:00:00' WHERE id_cobro=".$id_cobro;
				$resp=mysql_query($query,$this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$this->sesion->dbh);
				}
				
		$query = "SELECT activo FROM cliente WHERE codigo_cliente=".$codigo_cliente;
		$resp=mysql_query($query,$this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$this->sesion->dbh);
		list($activo)=mysql_fetch_array($resp);
		
#		if($activo==1) 
#			{
			$monto=str_replace(',','.',$monto);
			
			/*Es pago, asi que monto es negativo*/
			$multiplicador = -1.0;
			$moneda = new Moneda($this->sesion);
			$moneda->Load($id_moneda);
			$moneda_base = Utiles::MonedaBase($this->sesion);
			$monto_base = $monto * $moneda->fields['tipo_cambio'] / $moneda_base['tipo_cambio'];
	
			$this->Edit("monto",number_format($monto*$multiplicador,$moneda->fields['cifras_decimales'],".",""));
			$this->Edit("monto_base",number_format($monto_base*$multiplicador,$moneda_base['cifras_decimales'],".",""));
			$this->Edit("saldo_pago",number_format($monto*$multiplicador,$moneda->fields['cifras_decimales'],".",""));
			if($id_cobro) $this->Edit("id_cobro",$id_cobro);
			$this->Edit('tipo_doc',$tipo_doc);
			$this->Edit("numero_doc",$numero_doc);
			$this->Edit("id_moneda",$id_moneda);
			$this->Edit("fecha",Utiles::fecha2sql($fecha));
			$this->Edit("glosa_documento",$glosa_documento);
			$this->Edit("codigo_cliente",$codigo_cliente);
			$this->Edit("id_banco",$id_banco);
			$this->Edit("id_cuenta",$id_cuenta);
			$this->Edit("numero_operacion",$numero_operacion);
			$this->Edit("numero_cheque",$numero_cheque);
			$this->Edit("id_factura_pago",$id_factura_pago ? $id_factura_pago : "NULL" );
			if( $pago_retencion ) $this->Edit("pago_retencion","1");
			
			$out_neteos = "";
			
			if($this->Write())
				{
					$id_documento = $this->fields['id_documento'];
					$ids_monedas = explode(',',$ids_monedas_documento);
					$tipo_cambios = explode(',',$tipo_cambios_documento);
					$tipo_cambio = array();
					foreach($tipo_cambios as $key => $tc)
					{
						$tipo_cambio[$ids_monedas[$key]] = $tc;
					}
					$this->ActualizarDocumentoMoneda($tipo_cambio);
					$pagina->addInfo(__('Pago ingresado con �xito'));
					
							//Si se ingresa el documento, se ingresan los pagos
							foreach($arreglo_pagos_detalle as $key => $data)
							{
									$moneda_documento_cobro = new Moneda($this->sesion);
									$moneda_documento_cobro->Load($data['id_moneda']);
									
									// Guardo los saldos, para indicar cuales fueron actualizados
									$id_cobro_neteado   = $data['id_cobro'];
									$documento_cobro_aux = new Documento($this->sesion);
									if($documento_cobro_aux->LoadByCobro($id_cobro_neteado))
									{
										$saldo_honorarios_anterior = $documento_cobro_aux->fields['saldo_honorarios'];
										$saldo_gastos_anterior = $documento_cobro_aux->fields['saldo_gastos'];
									}
									
									$id_documento_cobro = $documento_cobro_aux->fields['id_documento'];
									$pago_honorarios    = $data['monto_honorarios'];
									$pago_gastos        = $data['monto_gastos'];
									$cambio_cobro       = $this->TipoCambioDocumento($this->sesion, $id_documento_cobro, $documento_cobro_aux->fields['id_moneda']);
									$cambio_pago        = $this->TipoCambioDocumento($this->sesion, $id_documento_cobro,$id_moneda);
									$decimales_cobro    = $moneda_documento_cobro->fields['cifras_decimales'];
									$decimales_pago     = $moneda->fields['cifras_decimales'];
									
									if(!$pago_gastos) 		$pago_gastos = 0;
									if(!$pago_honorarios) $pago_honorarios = 0;
									
									$neteo_documento = new NeteoDocumento($this->sesion);
									//Si el neteo exist�a, est� siendo modificado y se debe partir de 0:
									if( $neteo_documento->Ids($id_documento,$id_documento_cobro)) 
										$out_neteos .= $neteo_documento->Reestablecer($decimales_cobro);
									else
										$out_neteos .= "<tr><td>No</td><td>0</td><td>0</td>";
									
									//Luego se modifica
									if($pago_honorarios != 0 || $pago_gastos != 0)
										$out_neteos .= $neteo_documento->Escribir($pago_honorarios,$pago_gastos,$cambio_pago,$cambio_cobro,$decimales_pago,$decimales_cobro,$id_cobro_neteado);
									
									/*Compruebo cambios en saldos para mostrar mensajes de actualizacion*/
									$documento_cobro_aux = new Documento($this->sesion);
									if($documento_cobro_aux->Load($id_documento_cobro))
									{
										if($saldo_honorarios_anterior != $documento_cobro_aux->fields['saldo_honorarios'])
											$cambios_en_saldo_honorarios[] = $id_documento_cobro;
										if($saldo_gastos_anterior != $documento_cobro_aux->fields['saldo_gastos'])
											$cambios_en_saldo_gastos[] = $id_documento_cobro;

										$neteo_documento->CambiarEstadoCobro($id_cobro_neteado,$documento_cobro_aux->fields['saldo_honorarios'],$documento_cobro_aux->fields['saldo_gastos']);
									}
								}
						
						?>
						<script type="text/javascript">
						window.opener.Refrescar();
						</script> 
						<?
				}
				else
					$pagina->AddError($documento->error);
/*			}
		else
		{ ?>
			<script type="text/javascript">alert('�No se puede modificar un pago de un cliente inactivo!');</script>
<?	}
*/
		
		$out_neteos = "<table border=1><tr> <td>Id Cobro</td><td>Faltaba</td> <td>Aportaba y Devolv�</td> <td>Pas� a Faltar</td> <td>Ahora aporto</td> <td>Ahora falta </td> </tr>".$out_neteos."</table>";
		//echo $out_neteos;
		
		return $id_documento;
	}
	
	function EliminarNeteos()
	{
		$neteo_documento = new NeteoDocumento($this->sesion);
		$query = "
						SELECT neteo_documento.id_neteo_documento AS id
							FROM neteo_documento
							WHERE neteo_documento.id_documento_pago = '".$this->fields['id_documento']."'
							OR neteo_documento.id_documento_cobro = '".$this->fields['id_documento']."';
				 ";

		$resp = mysql_query ($query, $this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$this->sesion->dbh);

		while( list($id) = mysql_fetch_array($resp) )
		{
			if($neteo_documento->Load($id))
			{
				//No importan los decimales
				$neteo_documento->Reestablecer(2);
				$neteo_documento->Delete();
			}
		}
	}
	
	function EliminarDocumentoMoneda()
	{
		$query = "
						DELETE FROM documento_moneda
							WHERE id_documento = '".$this->fields['id_documento']."';
				 ";
		$resp = mysql_query ($query, $this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$this->sesion->dbh);
	}

	function mayor_fecha($fecha1,$fecha2)
	{
		$f1 = 	explode('-',$fecha1);
		$f1 = mktime(0,0,0,$f1[1],$f1[2],$f1[0]);

		$f2 = 	explode('-',$fecha2);
		$f2 = mktime(0,0,0,$f2[1],$f2[2],$f2[0]);

		if($f1 > $f2)
			return $fecha1;
		return $fecha2;
	}

	function FechaPagos()
	{
		$max_fecha = '';
		$query = "
						SELECT documento.fecha
							FROM neteo_documento
							JOIN documento ON (neteo_documento.id_documento_pago = documento.id_documento)
							WHERE neteo_documento.id_documento_cobro = '".$this->fields['id_documento']."';
				 ";

		$resp = mysql_query ($query, $this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$this->sesion->dbh);

		while( list($fecha) = mysql_fetch_array($resp) )
		{
			if($fecha)
			{
				if($max_fecha == '')
					$max_fecha = $fecha;
				else
					$max_fecha = $this->mayor_fecha($fecha,$max_fecha);
			}
		}
		return $max_fecha;
	}

	function ListaPagos()
	{
		if( method_exists('Conf','GetConf') && Conf::GetConf($this->sesion,'NuevoModuloFactura') )
			$nuevo_modulo = true;
		else
			$nuevo_modulo = false;
			
		$out = '';
		$query = "
						SELECT neteo_documento.id_documento_pago AS id, valor_cobro_honorarios as honorarios, valor_cobro_gastos as gastos, pago_retencion 
							FROM neteo_documento
							JOIN documento ON documento.id_documento=neteo_documento.id_documento_pago 
							WHERE neteo_documento.id_documento_cobro = '".$this->fields['id_documento']."';
				 ";

		$resp = mysql_query ($query, $this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$this->sesion->dbh);

		while( list($id, $honorarios, $gastos, $pago_retencion) = mysql_fetch_array($resp) )
		{
			if($id)
			{
				if($honorarios != 0)
					$honorarios = 'Honorarios: '.$honorarios;
				else
					$honorarios = '';
				if($gastos != 0)
					$gastos = 'Gastos: '.$gastos;
				else
					$gastos = '';
				
				if( $nuevo_modulo )
					$out .= "<tr><td align=left>".__('Documento #').$id."</td><td align = right style=\"color: #333333; font-size: 10px;\"> ".$honorarios.' '.$gastos." </td><td>&nbsp;</td></tr>";
				else
					$out .= "<tr><td align=left><a href='javascript:void(0)' style=\"color: blue; font-size: 11px;\" onclick=\"EditarPago(".$id.")\" title=\"Editar Pago\">".__('Documento #').$id."</a></td><td align = right style=\"color: #333333; font-size: 10px;\"> ".$honorarios.' '.$gastos." </td> <td><a target=_parent href='javascript:void(0)' onclick=\"EliminaDocumento($id)\" ><img src='".Conf::ImgDir()."/cruz_roja.gif' border=0 title=Eliminar></a></td></tr>";
				if( $pago_retencion )
					$out .= "<tr><td align=left colspan=2> ( Pago retenci�n impuestos ) </td></tr>";
			}
		}
		return $out;
	}


	function tabla($filas)
	{
		echo "<table border=1> <tr>";
		echo "<th>ID</th>";
		echo "<th>Cobro</th>";
		echo "<th>Glosa</th>";
		echo "<th>Moneda</th>";
		echo "<th>Monto</th>";
		echo "<th>Honorarios</th>";
		echo "<th>Gastos</th>";
		echo "<th>Saldo H</th>";
		echo "<th>Saldo G</th>";
		echo "<th>Saldo P</th>";
		echo "<th>H P</th>";
		echo "<th>G P</th>";
		echo "</tr>";
		echo $filas;
		echo "</table>";
	}

	function tabla_neteos($filas)
	{
		echo "<table border=1> <tr>";
		echo "<th>ID</th>";
		echo "<th>ID doc cobro</th>";
		echo "<th>ID doc pago</th>";
		echo "<th>moneda_cobro</th>";
		echo "<th>cobro honorarios</th>";
		echo "<th>cobro gastos</th>";
		echo "<th>moneda_pago</th>";
		echo "<th>pago honorarios</th>";
		echo "<th>pago gastos</th>";
		echo "</tr>";
		echo $filas;
		echo "</table>";
	}

	function fakeWrite()
	{
		$out = "<tr>";
		$out .= "<td>".$this->fields['id_documento']."</td>";
		$out .= "<td>".$this->fields['id_cobro']."</td>";
		$out .= "<td>".$this->fields['glosa_documento']."</td>";
		$out .= "<td>".$this->fields['id_moneda']."</td>";
		$out .= "<td>".$this->fields['monto']."</td>";
		$out .= "<td>".$this->fields['honorarios']."</td>";
		$out .= "<td>".$this->fields['gastos']."</td>";
		$out .= "<td>".$this->fields['saldo_honorarios']."</td>";
		$out .= "<td>".$this->fields['saldo_gastos']."</td>";
		$out .= "<td>".$this->fields['saldo_pago']."</td>";
		$out .= "<td>".$this->fields['honorarios_pagados']."</td>";
		$out .= "<td>".$this->fields['gastos_pagados']."</td>";
		$out .= "</tr>";
		return $out;
	}



	//Actualiza la informaci�n de TODOS los Documentos de TODOS los Cobros Emitidos [Advertencia: Deja todos los pagos en 0]
	function ReiniciarDocumentos($sesion, $write = 0)
	{
		$out = '';
		$out_cobros = '';
		$out_neteos = '';


		$query = "
					SELECT cobro.id_cobro FROM cobro WHERE cobro.estado <> 'CREADO' AND cobro.estado <> 'EN REVISION' AND cobro.estado IS NOT NULL
				";
		$resp = mysql_query ($query, $sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$sesion->dbh);

		$out_cobros .= "<table border=1>";

		$out_cobros .= "<tr> <th> ID </th> <th> Estado </th> <th> Doc </th> <th> Moneda </th> <th> Honorarios </th> <th> Gastos </th> <th> Moneda Total </th> <th> Honorarios MT </th> <th> Gastos MT</th> <th>Pagado Hon</th> <th> Pagado Gas</th> <th> Doc Pago Hon </th> <th> Doc Pago Gas </th></tr> ";


		while( list($id_cobro) = mysql_fetch_array($resp) )
		{
			$cobro = new Cobro($sesion);
			$cobro_moneda = new CobroMoneda($sesion);
			$cobro_moneda->Load($id_cobro);


			$out_cobros .= "<tr> <td> $id_cobro </td>";

			if($cobro->Load($id_cobro))
			{

				$out_cobros .= "<td>".$cobro->fields['estado']."</td>";
				$documento = new Documento($sesion);
				if(1)
				{
					if($documento->LoadByCobro($id_cobro))
						$out_cobros .= "<td>".$documento->fields['id_documento']."</td>";
					else
						$out_cobros .= "<td>"."NULL"."</td>";

					#GASTOS del Cobro
					$cobro_total_gastos = 0;

					$query = "SELECT SQL_CALC_FOUND_ROWS cta_corriente.descripcion, cta_corriente.fecha,cta_corriente.id_moneda,cta_corriente.egreso,cta_corriente.ingreso,cta_corriente.id_movimiento,cta_corriente.codigo_asunto
					FROM cta_corriente
					LEFT JOIN asunto USING(codigo_asunto)
					WHERE cta_corriente.id_cobro='". $id_cobro . "' AND (egreso > 0 OR ingreso > 0) AND cta_corriente.incluir_en_cobro = 'SI'
					ORDER BY cta_corriente.fecha ASC";
					$lista_gastos = new ListaGastos($sesion,'',$query);

					for( $v=0; $v<$lista_gastos->num; $v++ )
					{
						$gasto = $lista_gastos->Get($v);

						//cobro_total_gastos en moneda cobro
						if($gasto->fields['egreso'] > 0)
						{
							$cobro_total_gastos += $gasto->fields['monto_cobrable'] * $cobro_moneda->moneda[$gasto->fields['id_moneda']]['tipo_cambio'] /
							$cobro_moneda->moneda[$cobro->fields['opc_moneda_total']]['tipo_cambio'];
						}
						elseif($gasto->fields['ingreso'] > 0)
						{
							$cobro_total_gastos -= $gasto->fields['monto_cobrable'] * $cobro_moneda->moneda[$gasto->fields['id_moneda']]['tipo_cambio'] / $cobro_moneda->moneda[$cobro->fields['opc_moneda_total']]['tipo_cambio'];
						}
					}

					if( ( ( method_exists('Conf','UsarImpuestoSeparado') && Conf::UsarImpuestoSeparado() ) || ( method_exists('Conf','GetConf') && Conf::GetConf($this->sesion,'UsarImpuestoSeparado') ) ) && $cobro->fields['porcentaje_impuesto'])
					{
						$cobro_total_gastos *= (1+$cobro->fields['porcentaje_impuesto']/100);
					}

					#HONORARIOS del cobro
					if($cobro_moneda->moneda[$cobro->fields['opc_moneda_total']]['tipo_cambio']!=0)
					{
						$aproximacion_monto = number_format($cobro->fields['monto'],$cobro_moneda->moneda[$cobro->fields['id_moneda']]['cifras_decimales'],'.','');
						$cobro_total_honorarios = $aproximacion_monto * $cobro_moneda->moneda[$cobro->fields['id_moneda']]['tipo_cambio'] /
				$cobro_moneda->moneda[$cobro->fields['opc_moneda_total']]['tipo_cambio'];
					}


					$out_cobros .= "<td>".$cobro->fields['id_moneda']."</td>";
					$out_cobros .= "<td>".$cobro->fields['monto']."</td>";
					$out_cobros .= "<td>".$cobro->fields['monto_gastos']."</td>";
					$out_cobros .= "<td>".$cobro->fields['opc_moneda_total']."</td>";

					#Documento de Cobro

					$documento->Edit('id_moneda',$cobro->fields['opc_moneda_total']);
					$documento->Edit('codigo_cliente',$cobro->fields['codigo_cliente']);
					$documento->Edit('id_cobro',$cobro->fields['id_cobro']);
					$documento->Edit('glosa_documento',"Documento de Cobro #".$cobro->fields['id_cobro']);

					$moneda_total = new Objeto($sesion,'','','prm_moneda','id_moneda');
					$moneda_total->Load($cobro->fields['opc_moneda_total'] > 0 ? $cobro->fields['opc_moneda_total'] : 1);
					$decimales = $moneda_total->fields['cifras_decimales'];

					$moneda_base = new Objeto($sesion,'','','prm_moneda','id_moneda');
					$moneda_base->Load($cobro->fields['id_moneda_base'] > 0 ? $cobro->fields['id_moneda_base'] : 1);
					$decimales_base = $moneda_base->fields['cifras_decimales'];

					$documento->Edit('monto',number_format(($cobro_total_honorarios+$cobro_total_gastos),$decimales,".",""));
					$documento->Edit('honorarios',number_format($cobro_total_honorarios,$decimales,".",""));
					$documento->Edit('gastos',number_format($cobro_total_gastos,$decimales,".",""));

					$cambio_cobro = $cobro_moneda->moneda[$cobro->fields['opc_moneda_total']]['tipo_cambio'];
					$cambio_base = $cobro_moneda->moneda[$cobro->fields['id_moneda_base']]['tipo_cambio'];

					$monto_base = ($cobro_total_honorarios+$cobro_total_gastos)* $cambio_cobro /
					$cambio_base;

					$documento->Edit('monto_base',number_format($monto_base,$decimales_base,".",""));

					$out_cobros .= "<td>".$documento->fields['honorarios']."</td>";
					$out_cobros .= "<td>".$documento->fields['gastos']."</td>";

					if($cobro->fields['honorarios_pagados'] == 'SI' ||  $documento->fields['honorarios'] <= 0)
					{
						$documento->Edit('saldo_honorarios','0');
						$documento->Edit('honorarios_pagados','SI');
					}
					else
					{
						$documento->Edit('saldo_honorarios',number_format($cobro_total_honorarios,$decimales,".",""));
						$documento->Edit('honorarios_pagados','NO');
					}

					if($cobro->fields['gastos_pagados'] == 'SI' ||  $documento->fields['gastos'] <= 0)
					{
						$documento->Edit('saldo_gastos','0');
						$documento->Edit('gastos_pagados','SI');
					}
					else
					{
						$documento->Edit('saldo_gastos',number_format($cobro_total_gastos,$decimales,".",""));
						$documento->Edit('gastos_pagados','NO');
					}

					$out_cobros .= "<td>".$documento->fields['honorarios_pagados']."</td>";
					$out_cobros .= "<td>".$cobro->fields['gastos_pagados']."</td>";

					# PAGOS
					$pago_honorarios = false;
					$pago_gastos = false;
					$monto_pago = 0;
					$monto_pago_base = 0;

					if($cobro->fields['id_doc_pago_honorarios'])
					{
						$out_cobros .= "<td>".$cobro->fields['id_doc_pago_honorarios']."</td>";
					}
					else if($documento->fields['honorarios_pagados']=='SI' && $documento->fields['honorarios'] > 0)
					{
						$out_cobros .= "<td>"."NUEVO"."</td>";
						$pago_honorarios = true;
						$moneda_pago = $documento->fields['id_moneda'];
						$monto_pago += $documento->fields['honorarios'];

						$monto_pago_base += $documento->fields['honorarios'] * $cambio_cobro /
				$cambio_base;
					}
					else
						$out_cobros .= "<td>"."Null"."</td>";

					if($cobro->fields['id_doc_pago_gastos'])
					{
						$out_cobros .= "<td>".$cobro->fields['id_doc_pago_gastos']."</td>";
					}
					else if($documento->fields['gastos_pagados']=='SI' && $documento->fields['gastos'] > 0)
					{
						$out_cobros .= "<td>"."NUEVO"."</td>";
						$pago_gastos = true;
						$moneda_pago = $documento->fields['id_moneda'];
						$monto_pago += $documento->fields['gastos'];

						$monto_pago_base += $documento->fields['gastos']* $cambio_cobro /
				$cambio_base;;
					}
					else
						$out_cobros .= "<td>"."Null"."</td>";

					if($pago_honorarios || $pago_gastos)
					{
						$documento_pago = new Documento($sesion);
						$documento_pago->Edit('glosa_documento',"Documento de Pago para Cobro #".$cobro->fields['id_cobro']);
						$documento_pago->Edit('id_moneda',$cobro->fields['opc_moneda_total']);
						$documento_pago->Edit('codigo_cliente',$cobro->fields['codigo_cliente']);
						$documento_pago->Edit('tipo_doc','P');
						$documento_pago->Edit('monto',number_format($monto_pago*-1.0, $decimales,".",""));
						$documento_pago->Edit('monto_base',number_format($monto_pago_base*-1.0, $decimales,".",""));

						if($write)
						{
							$documento_pago->Write();
							if($pago_honorarios)
								$cobro->Edit('id_doc_pago_honorarios',$documento_pago->fields['id_documento']);
							if($pago_gastos)
								$cobro->Edit('id_doc_pago_gastos',$documento_pago->fields['id_documento']);
							$cobro->Write();
						}
						$out .= $documento_pago->fakeWrite();
					}

					if($write)
						$documento->Write();

					//NETEOS
					$neteo = new NeteoDocumento($sesion);
					if($cobro->fields['id_doc_pago_honorarios'])
					{
						$doc_pago_honorarios = new Documento($sesion);
						$doc_pago_honorarios->Load($cobro->fields['id_doc_pago_honorarios']);

						$cambio_pago = $cobro_moneda->moneda[$doc_pago_honorarios->fields['id_moneda']]['tipo_cambio'];
						$out_neteos .= $neteo->NeteoCompleto($documento,$doc_pago_honorarios,1, $cambio_cobro, $cambio_pago, $write);
					}

					if($cobro->fields['id_doc_pago_gastos'])
					{
						$doc_pago_gastos = new Documento($sesion);
						$doc_pago_gastos->Load($cobro->fields['id_doc_pago_gastos']);

						$cambio_pago = $cobro_moneda->moneda[$doc_pago_gastos->fields['id_moneda']]['tipo_cambio'];
						$out_neteos .=  $neteo->NeteoCompleto($documento,$doc_pago_gastos,0, $cambio_cobro, $cambio_pago, $write);
					}

					$out_cobros .= "</tr>";

					$out .= $documento->fakeWrite();
				}
			}
		}
		//echo $this->tabla($out);
		//echo $this->tabla_neteos($out_neteos);
	}

	function EliminarDesdeFacturaPago($id_factura_pago){
		
		$query = "SELECT id_documento FROM documento WHERE id_factura_pago = '$id_factura_pago'";
		$resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$this->sesion->dbh);
		list($id) = mysql_fetch_array($resp);

		if(!$id) return false;
		$this->Load($id);

		$this->EliminarNeteos();
		$query_p = "DELETE from cta_corriente WHERE cta_corriente.documento_pago = '".$id."' ";
		mysql_query($query_p, $this->sesion->dbh) or Utiles::errorSQL($query_p,__FILE__,__LINE__,$this->sesion->dbh);

		return $this->Delete();
	}
}



class ListaDocumentos extends Lista
{
    function ListaDocumentos($sesion, $params, $query)
    {
        $this->Lista($sesion, 'Documento', $params, $query);
    }
}
