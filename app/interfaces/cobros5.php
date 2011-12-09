<?
	require_once dirname(__FILE__).'/../conf.php';
	require_once Conf::ServerDir().'/../fw/classes/Sesion.php';
	require_once Conf::ServerDir().'/../app/classes/PaginaCobro.php';
	require_once Conf::ServerDir().'/../fw/classes/Buscador.php';
	require_once Conf::ServerDir().'/../app/classes/Asunto.php';
	require_once Conf::ServerDir().'/../app/classes/CobroAsunto.php';
	require_once Conf::ServerDir().'/../app/classes/Cobro.php';
	require_once Conf::ServerDir().'/../app/classes/Gasto.php';
	require_once Conf::ServerDir().'/../app/classes/InputId.php';
	require_once Conf::ServerDir().'/../app/classes/Trabajo.php';
	require_once Conf::ServerDir().'/../fw/classes/Utiles.php';
	require_once Conf::ServerDir().'/../fw/classes/Html.php';
	require_once Conf::ServerDir().'/../app/classes/Funciones.php';
	require_once Conf::ServerDir().'/../app/classes/Moneda.php';
	require_once Conf::ServerDir().'/../app/classes/Debug.php';
	require_once Conf::ServerDir().'/../app/classes/Contrato.php';
	require_once Conf::ServerDir().'/../app/classes/Observacion.php';
	require_once Conf::ServerDir().'/../app/classes/CobroMoneda.php';
	require_once Conf::ServerDir().'/../app/classes/UtilesApp.php';
	require_once Conf::ServerDir().'/../app/classes/Cliente.php';

	$sesion = new Sesion(array('COB'));
	$pagina = new PaginaCobro($sesion);

	$cobro = new Cobro($sesion);
	if(!$cobro->Load($id_cobro))
		$pagina->FatalError(__('Cobro inv�lido'));

	$enpdf = ( $opc == 'guardar_cobro_pdf' ? true : false );

	$cliente = new Cliente($sesion);
	$cliente->LoadByCodigo($cobro->fields['codigo_cliente']);
	$nombre_cliente = $cliente->fields['glosa_cliente'];
	$pagina->titulo = __('Emitir') . ' ' . __('Cobro') . __(' :: Detalle #').$id_cobro.__(' ').$nombre_cliente;

	//Contrato
	$contrato = new Contrato($sesion);
	$contrato->Load($cobro->fields['id_contrato']);

	// Idioma
	$idioma = new Objeto($sesion,'','','prm_idioma','codigo_idioma');
	if( method_exists('Conf','GetConf') )
		$idioma->Load(Conf::GetConf($sesion,'Idioma'));
	else
		$idioma->Load(Conf::Idioma());

	// Moneda
	$moneda_base = new Objeto($sesion,'','','prm_moneda','id_moneda');
	$moneda_base->Load($cobro->fields['id_moneda_base']);

	$retainer = "";

	if($cobro->fields['estado'] != 'CREADO' && $cobro->fields['estado'] != 'EN REVISION' && $opc != 'anular_emision')
		$pagina->Redirect("cobros6.php?id_cobro=".$id_cobro."&popup=1&contitulo=true");

	if($opc == 'anular_emision')
	{
		if($estado=='EN REVISION')
			$cobro->AnularEmision('EN REVISION');
		else
			$cobro->AnularEmision();
		#Se ingresa la anotaci�n en el historial
		$his = new Observacion($sesion);
		$his->Edit('fecha',date('Y-m-d H:i:s'));
		$his->Edit('comentario',__('COBRO ANULADO'));
		$his->Edit('id_usuario',$sesion->usuario->fields['id_usuario']);
		$his->Edit('id_cobro',$cobro->fields['id_cobro']);
		if($his->Write())
			$pagina->AddInfo(__('Historial ingresado'));
	}
	elseif($opc == 'guardar_cobro' || $opc == 'guardar_cobro_pdf') #Guardamos todos los datos del cobro
	{
		// Si se ajustar el valor del cobro por monto,
		// llamar a la function AjustarPorMonto de la clase Cobro

		if( !is_numeric($cobro_monto_honorarios) )
			$cobro_monto_honorarios = 0;

		if( $ajustar_monto_hide && $ajustar_monto_hide != "false" )
			{
			$cobro->Edit('monto_ajustado',$cobro_monto_honorarios);
			}
		else
			{
			$cobro->Edit('monto_ajustado',"0");
			}

		/* tarifa escalonada */
		if( isset( $_POST['esc_tiempo'] ) ) {
			for( $i = 1; $i <= sizeof($_POST['esc_tiempo']) ; $i++){		
				if( $_POST['esc_tiempo'][$i-1] != '' ){
					$cobro->Edit('esc'.$i.'_tiempo', $_POST['esc_tiempo'][$i-1] );
					if( $_POST['esc_selector'][$i-1] != 1 ){
						//caso monto
						$cobro->Edit('esc'.$i.'_id_tarifa', "NULL");
						$cobro->Edit('esc'.$i.'_monto', $_POST['esc_monto'][$i-1]);
					} else {
						//caso tarifa
						$cobro->Edit('esc'.$i.'_id_tarifa', $_POST['esc_id_tarifa_'.$i]);
						$cobro->Edit('esc'.$i.'_monto', "NULL");
					}
					$cobro->Edit('esc'.$i.'_id_moneda', $_POST['esc_id_moneda_'.$i]);
					$cobro->Edit('esc'.$i.'_descuento', $_POST['esc_descuento'][$i-1]);
				} else {
					$cobro->Edit('esc'.$i.'_tiempo', "NULL");
					$cobro->Edit('esc'.$i.'_id_tarifa', "NULL");
					$cobro->Edit('esc'.$i.'_monto', "NULL");
					$cobro->Edit('esc'.$i.'_id_moneda', "NULL");
					$cobro->Edit('esc'.$i.'_descuento', "NULL");
				}
			}		
		}
		
		$cobro->Edit("opc_ver_detalles_por_hora",$opc_ver_detalles_por_hora);
		$cobro->Edit('id_moneda',$cobro_id_moneda);
		$cobro->Edit('tipo_cambio_moneda',$cobro_tipo_cambio);
		//$cobro->Edit('forma_cobro',$cobro_forma_cobro);
		$cobro->Edit('id_moneda_monto', $id_moneda_monto);
		$cobro->Edit('monto_contrato',$cobro_monto_contrato);
		$cobro->Edit('retainer_horas',$cobro_retainer_horas);
		#################### OPCIONES #######################
		$cobro->Edit('opc_moneda_total',$opc_moneda_total);
		$cobro->Edit("opc_ver_modalidad",$opc_ver_modalidad);
		$cobro->Edit("opc_ver_profesional",$opc_ver_profesional);
		$cobro->Edit("opc_ver_gastos",$opc_ver_gastos);
		$cobro->Edit("opc_ver_concepto_gastos",$opc_ver_concepto_gastos);
		$cobro->Edit("opc_ver_morosidad",$opc_ver_morosidad);
		$cobro->Edit("opc_ver_resumen_cobro",$opc_ver_resumen_cobro);
		$cobro->Edit("opc_ver_profesional_iniciales",$opc_ver_profesional_iniciales);
		$cobro->Edit("opc_ver_profesional_categoria",$opc_ver_profesional_categoria);
 		$cobro->Edit("opc_ver_profesional_tarifa",$opc_ver_profesional_tarifa);
 		$cobro->Edit("opc_ver_profesional_importe",$opc_ver_profesional_importe);
		$cobro->Edit("opc_ver_detalles_por_hora_categoria",$opc_ver_detalles_por_hora_categoria);
		$cobro->Edit("opc_ver_detalles_por_hora_iniciales",$opc_ver_detalles_por_hora_iniciales);
		$cobro->Edit("opc_ver_detalles_por_hora_tarifa",$opc_ver_detalles_por_hora_tarifa);
		$cobro->Edit("opc_ver_detalles_por_hora_importe",$opc_ver_detalles_por_hora_importe);
		$cobro->Edit("opc_ver_tipo_cambio",$opc_ver_tipo_cambio);
		$cobro->Edit("opc_ver_descuento",$opc_ver_descuento);
		$cobro->Edit("opc_ver_numpag",$opc_ver_numpag);
		$cobro->Edit("opc_papel",$opc_papel);
		$cobro->Edit("opc_ver_solicitante",$opc_ver_solicitante);
		$cobro->Edit('opc_ver_carta',$opc_ver_carta);
		$cobro->Edit("opc_ver_asuntos_separados",$opc_ver_asuntos_separados);
		$cobro->Edit("opc_ver_horas_trabajadas",$opc_ver_horas_trabajadas);
		$cobro->Edit("opc_ver_cobrable",$opc_ver_cobrable);
		// opciones especificos para Vial Olivares
			$cobro->Edit("opc_restar_retainer",$opc_restar_retainer);
			$cobro->Edit("opc_ver_detalle_retainer",$opc_ver_detalle_retainer);
		$cobro->Edit("opc_ver_valor_hh_flat_fee",$opc_ver_valor_hh_flat_fee);
		$cobro->Edit('id_carta',$id_carta);
		$cobro->Edit('id_formato',$id_formato);
		$cobro->Edit('codigo_idioma',$lang);
		$cobro->Edit('se_esta_cobrando',$se_esta_cobrando);
		$cobro->Write();//Se guarda porque despues se necesita para recalcular los datos del cobro
		################### DESCUENTOS #####################
		if($tipo_descuento == 'PORCENTAJE')
		{
			$total_descuento = ($cobro->fields['monto_subtotal'] * $porcentaje_descuento)/100;
			$cobro->Edit('descuento',$total_descuento);
			$cobro->Edit('porcentaje_descuento',$porcentaje_descuento);
		}
		elseif($tipo_descuento == 'VALOR')
		{
			$cobro->Edit('descuento',$cobro_descuento);
			$cobro->Edit('porcentaje_descuento','0');
		}
		$cobro->Edit('tipo_descuento', $tipo_descuento);
		$cobro_moneda_cambio = new CobroMoneda($sesion);
		$cobro_moneda_cambio->UpdateTipoCambioCobro($cobro_id_moneda, $cobro_tipo_cambio, $id_cobro);

		$ret = $cobro->GuardarCobro();
		if($accion == 'emitir' && $ret == '') 				##################### EMISION ######################
		{
			/*Guardo el cobro generando los movimientos de cuenta corriente*/
			$cobro->GuardarCobro(true);
			$cobro->Edit('fecha_emision',date('Y-m-d H:i:s'));
			$cobro->Edit('estado','EMITIDO');
			$historial_comentario = __('COBRO EMITIDO');
			##Historial##
			$his = new Observacion($sesion);
			$his->Edit('fecha',date('Y-m-d H:i:s'));
			$his->Edit('comentario',$historial_comentario);
			$his->Edit('id_usuario',$sesion->usuario->fields['id_usuario']);
			$his->Edit('id_cobro',$cobro->fields['id_cobro']);
			$his->Write();
			if($cobro->Write())
			{
				if(!empty($usar_adelantos)){
					$documento = new Documento($sesion);
					$documento->LoadByCobro($id_cobro);
					$documento->GenerarPagosDesdeAdelantos($documento->fields['id_documento']);
				}
				$cobro->CambiarEstadoSegunFacturas();
				$refrescar = "<script language='javascript' type='text/javascript'>if(window.opener.Refrescar) window.opener.Refrescar(".$id_foco.");</script>";
				$pagina->Redirect("cobros6.php?id_cobro=".$id_cobro."&popup=1&contitulo=true&refrescar=1");
			}
		}
		elseif($accion == 'imprimir' && $ret == '' ) 	#################### IMPRESION #####################
		{
			include dirname(__FILE__).'/cobro_doc.php';
			exit;
		}
		elseif($accion == 'descargar_excel_especial')
		{
			if( UtilesApp::GetConf($sesion,'XLSFormatoEspecial') != '' )
				require_once Conf::ServerDir().'/../app/interfaces/'.UtilesApp::GetConf($sesion,'XLSFormatoEspecial');
			exit;
		}
		elseif($accion == 'descargar_excel')
		{
			if( UtilesApp::GetConf($sesion,'XLSFormatoEspecial') == 'cobros_xls_formato_especial.php' )
				require_once Conf::ServerDir().'/../app/interfaces/cobros_xls_formato_especial.php';
			else
				require_once Conf::ServerDir().'/../app/interfaces/cobros_xls.php';
			exit;
		}
		elseif($accion == 'anterior')									################## ANTERIOR PASO ###################
		{
			if(!empty($cobro->fields['incluye_gastos']))
				$pagina->Redirect("cobros4.php?id_cobro=".$id_cobro."&popup=1&contitulo=true");
			else
				$pagina->Redirect("cobros_tramites.php?id_cobro=".$id_cobro."&popup=1&contitulo=true");
		}

		if( $ret != '' )
			$pagina->AddInfo($ret);
		else
			$pagina->AddInfo(__('Informaci�n actualizada'));
	}
	elseif($opc == 'up_cambios')
	{
		$pagina->AddInfo(__('Los cambios han sido actualizados correctamente.'));
	}
	elseif($opc == 'en_revision')
	{
		$cobro->Edit('estado','EN REVISION');
		$cobro->Edit('fecha_en_revision',date('Y-m-d H:i:s'));
		if($cobro->Write())
			$pagina->AddInfo(__('El Cobro ha sido transferido') . " " . __('al estado: En Revisi�n'));
		$historial_comentario = __('COBRO EN REVISION');
		##Historial##
		$his = new Observacion($sesion);
		$his->Edit('fecha',date('Y-m-d H:i:s'));
		$his->Edit('comentario',$historial_comentario);
		$his->Edit('id_usuario',$sesion->usuario->fields['id_usuario']);
		$his->Edit('id_cobro',$cobro->fields['id_cobro']);
		$his->Write();
	}
	elseif($opc == 'volver_a_creado')
	{
		$cobro->Edit('estado','CREADO');
		if($cobro->Write())
			$pagina->AddInfo(__('El Cobro ha sido transferido') . " " . __('al estado: Creado'));
		$historial_comentario = __('REVISION ANULADO');
		##Historial##
		$his = new Observacion($sesion);
		$his->Edit('fecha',date('Y-m-d H:i:s'));
		$his->Edit('comentario',$historial_comentario);
		$his->Edit('id_usuario',$sesion->usuario->fields['id_usuario']);
		$his->Edit('id_cobro',$cobro->fields['id_cobro']);
		$his->Write();
	}

	$cobro->Edit('etapa_cobro','4');
	if($cobro->Write())
	{
		$refrescar = "<script language='javascript' type='text/javascript'>if( window.opener.Refrescar ) window.opener.Refrescar(".$id_foco.");</script>";
	}

	$moneda_cobro = new Objeto($sesion,'','','prm_moneda','id_moneda');
	$moneda_cobro->Load($cobro->fields['id_moneda']);

	if($popup)
	{
?>
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
			<tr>
				<td valign="top" align="left" class="titulo" bgcolor="<?=(method_exists('Conf','GetConf')?Conf::GetConf($sesion,'ColorTituloPagina'):Conf::ColorTituloPagina())?>">
					<?=__('Emitir') . " " . __('Cobro') . __(' :: Detalle #').$id_cobro.__(' ').$nombre_cliente;?>
				</td>
			</tr>
		</table>
		<br>
<?
	}
	$pagina->PrintTop($popup);

	$pagina->PrintPasos($sesion,4,'',$id_cobro, $cobro->fields['incluye_gastos'], $cobro->fields['incluye_honorarios']);

	#Tooltips para las modalidades de cobro.
	$tip_tasa				= __("En esta modalidad se cobra hora a hora. Cada profesional tiene asignada su propia tarifa para cada asunto.");
	$tip_suma				= __("Es un �nico monto de dinero para el asunto. Aqu� interesa llevar la cuenta de HH para conocer la rentabilidad del proyecto. Esta es la �nica modalida de ") . __("cobro") . __(" que no puede tener l�mites.");
	$tip_retainer			= __("El cliente compra un n�mero de HH. El l�mite puede ser por horas o por un monto.");
	$tip_proporcional		= __("El cliente compra un n�mero de horas, el exceso de horas trabajadas se cobra proporcional a la duraci�n de cada trabajo.");
	$tip_escalonada			= __("El cliemnte define una serie de escalas de tiempos durante las cuales podr� variar la tarifa, definir un monto espec�fico y un descuento individual.");
	$tip_flat				= __("El cliente acuerda cancelar un <strong>monto fijo mensual</strong> por atender todos los trabajos de este asunto. Puede tener l�mites por HH o monto total");
	$tip_cap				= __("Cap");
	$tip_honorarios 		= __("S�lamente lleva la cuenta de las HH profesionales. Al terminar el proyecto se puede cobrar eventualmente.");
	$tip_mensual			= __("El cobro se har� de forma mensual.");
	$tip_tarifa_especial	= __("Al ingresar una nueva tarifa, esta se actualizar� autom�ticamente.");
	$tip_subtotal			= __("El monto total") . " " . __("del cobro") . " " . __("hasta el momento sin gastos y sin incluir descuentos.");
	$tip_descuento			= __("El monto del descuento.");
	$tip_total				= __("El monto total") . " " . __("del cobro") . " " . __("hasta el momento incluidos descuentos.");
	$tip_actualizar			= __("Actualizar los montos");
	$tip_refresh			= __("Actualizar a cambio actual");
	function TTip($texto)
	{
		return "onmouseover=\"ddrivetip('$texto');\" onmouseout=\"hideddrivetip('$texto');\"";
	}
	echo $refrescar;
?>

<script language="javascript" type="text/javascript">
<!-- //
function SubirExcel()
{
	nuevaVentana('SubirExcel',500,300,"subir_excel.php");
}

function Refrescar()
{
	var id_cobro = $('id_cobro').value;
	var vurl = "cobros5.php?popup=1&id_cobro="+id_cobro+"&id_foco=2";
	self.location.href = vurl;
}

function Anterior( form )
{
	if(!form)
		var form = $('form_cobro5');
	form.accion.value = 'anterior';
	form.submit();
	return true;
}

function ActualizarTarifas( form )
{
    var http = getXMLHTTP();
    http.open('get', 'ajax.php?accion=actualizar_tarifas&id_cobro='+document.getElementById('id_cobro').value);
    http.onreadystatechange = function()
    {
       if(http.readyState == 4)
       {
           response = http.responseText;
           if( response == "OK" ) {
               alert('Tarifas actualizados con �xito.');
               $('form_cobro5').submit();
               return true;
           }
           else {
               alert('No se pudieron actualizar las tarifas.')
               return false;
           }
       }
    }
    http.send(null);
}

function showOpcionDetalle( id, bloqueDetalle )
{
	if( $(id).checked )
		$(bloqueDetalle).style.display = "table-row";
	else
		$(bloqueDetalle).style.display = "none";
}

function AjustarMonto( accion )
{
	form = document.getElementById('form_cobro5');

	if( accion == 'ajustar' )
	{
		document.getElementById('cobro_monto_honorarios').value = "";
		document.getElementById('cobro_monto_honorarios').readOnly = false;
		document.getElementById('ajustar_monto_hide').value = true;
		document.getElementById('cancelar_ajustacion').style.display = 'inline';
		document.getElementById('tr_monto_original').style.display = 'table-row';
		document.getElementById('cobro_monto_honorarios').focus();
	}
	else if( accion == 'cancelar' )
	{
		document.getElementById('cobro_monto_honorarios').value = document.getElementById('monto_original').value;
		document.getElementById('cobro_monto_honorarios').readOnly = true;
		document.getElementById('ajustar_monto_hide').value = false;
		document.getElementById('cancelar_ajustacion').style.display = 'none';
		document.getElementById('tr_monto_original').style.display = 'none';

		form.submit();
		return true;
	}
}

function MontoValido( id_campo )
{
	var monto = document.getElementById( id_campo ).value.replace('\,','.');
	var arr_monto = monto.split('\.');
	var monto = arr_monto[0];
	for($i=1;$i<arr_monto.length-1;$i++)
		monto += arr_monto[$i];
	if( arr_monto.length > 1 )
		monto += '.' + arr_monto[arr_monto.length-1];

	document.getElementById( id_campo ).value = monto;
}

function AgregarParametros( form )
{
	if(!form)
		var form = $('form_cobro5');

	for(var i=0;i < form.cobro_id_moneda.length;i++)
	{
		if( form.cobro_id_moneda[i].checked )
			form.cobro_id_moneda.value = form.cobro_id_moneda[i].value;
	}

	for(var i=0;i < form.cobro_forma_cobro.length;i++)
	{
		if( form.cobro_forma_cobro[i].checked )
			form.cobro_forma_cobro.value = form.cobro_forma_cobro[i].value;
	}

  if(form.cobro_id_moneda.value)
  {
  	var valor_cobro_id_moneda=form.cobro_id_moneda.value;
  }
  else
  {
  	var i=0;
		while( form.cobro_id_moneda[i] )
		{
			if( form.cobro_id_moneda[i].checked == true )
				var valor_cobro_id_moneda = form.cobro_id_moneda[i].value;

			i++;
		}
	}
	var cobro_tipo_cambio = document.getElementById('cobro_tipo_cambio_'+parseInt(valor_cobro_id_moneda)).value;

	form.cobro_tipo_cambio.value = parseFloat(cobro_tipo_cambio);

	if( form.cobro_id_moneda.value == '' )
	{
		alert("<?=__('Tienes que ingresar el tipo de moneda.')?>");
		return false;
	}

	if( form.cobro_forma_cobro.value == '' )
	{
		alert("<?=__('Tienes que ingresar la forma de cobro.')?>");
		return false;
	}

	if( form.cobro_descuento.value == '' )
		form.cobro_descuento.value = 0;

	return true;

	alert("<?=__('Error al procesar los par�metros.')?>");
	return false;
}

function EnRevision( form )
{
	form.opc.value = 'en_revision';
	form.submit();
	return true;
}

function VolverACreado( form )
{
	if($('existe_factura').value == 1)
	{
			alert("<?=__('No se puede regresar a estado CREADO. Existen Documentos Tributarios creados para') . " " . __('este cobro')?>");
			return false;
	}

	form.opc.value = 'volver_a_creado';
	form.submit();
	return true;
}

function Emitir(form)
{
		var http = getXMLHTTP();
		http.open('get', 'ajax.php?accion=num_abogados_sin_tarifa&id_cobro='+document.getElementById('id_cobro').value);
		http.onreadystatechange = function()
		{
      if(http.readyState == 4)
      {
				var response = http.responseText;
				response = response.split('//');

				var text_window = "<img src='<?=Conf::ImgDir()?>/alerta_16.gif'>&nbsp;&nbsp;<span style='font-size:12px; color:#FF0000; text-align:center;font-weight:bold'><u><?=__("ALERTA")?></u><br><br>";
				if( response[0] != 0 )
					{
						if( response[0] < 2 )
							text_window += '<span style="text-align:center; font-size:11px; color:#000; "><?=__("La tarifa del abogado ")?></span>';
						else
							text_window += '<span style="text-align:center; font-size:11px; color:#000; "><?=__("Las tarifas de los abogados ")?></span><br><br>';
						for(i=1;i<response.length;i++)
							{
								var datos = response[i].split('~');
								if( response[0] < 2 )
									text_window += '<span style="text-align:center; font-size:11px; color:#000; ">'+datos[1]+'</span>';
								else
									text_window += '<span style="text-align:center; font-size:11px; color:#000; ">'+datos[1]+'</span><br>';
							}
						if( response[0] < 2 )
							text_window += '<span style="text-align:center; font-size:11px; color:#000; "><?=__(" no esta definido.")?></span><br>';
						else
							text_window += '<br><span style="text-align:center; font-size:11px; color:#000; "><?=__(" no estan definidos.")?></span><br>';
						text_window += '<a href="#" onclick="DefinirTarifas();" style="color:blue;">Definir tarifas</a><br><br>';
					}
				text_window += '<span style="text-align:center; font-size:11px; color:#000; "><?=__("Una vez efectuado") . " " . __("el cobro") . ", " . __("la informaci�n no podr� ser modificada sin reemitir") . " " . __("el cobro") . ", " . __("�Est� seguro que desea Emitir") . " " . __("el Cobro") . "?"?></span><br>';
				text_window += '<br><table><tr>';
				text_window += '</table>';
				Dialog.confirm(text_window,
				{
					top:150, left:290, width:400, okLabel: "<?=__('Continuar')?>", cancelLabel: "<?=__('Cancelar')?>", buttonClass: "btn", className: "alphacube",
					id: "myDialogId",
					cancel:function(win){ return false; },
					ok:function(win){
								if( !AgregarParametros( form ) )
									return false;
								else
								{
									var adelantos = $F('saldo_adelantos');
									var total = Number($F('total_honorarios'))+Number($F('total_gastos'));
									if(adelantos && confirm('Tiene disponibles '+adelantos+' en adelantos.\n�Desea utilizarlos autom�ticamente para '+
										(Number(adelantos.replace(/[^\d\.]/g,'')) < total ? 'abonar' : 'pagar')+' este cobro?')){
										$('usar_adelantos').value = '1';
									}
									form.accion.value = 'emitir';
									form.opc.value = 'guardar_cobro';
									form.submit();
									return true;
								}
					}
				});
      }
		};
	  http.send(null);
}

function DefinirTarifas()
{
	var id_tarifa = document.getElementById('id_tarifa').value;
	nuevaVentana('Definir_Tarifas', 700, 550, 'agregar_tarifa.php?id_tarifa_edicion='+id_tarifa+'&popup=1','');
}

function ImprimirCobro(form)
{
	if(!form)
		var form = $('form_cobro5');
	if( !AgregarParametros( form ) )
		return false;
	form.accion.value = 'imprimir';
	form.opc.value = 'guardar_cobro';
	form.submit();
	return true;
}

function ImprimirCobroPDF(form)
{
	if(!form)
		var form = $('form_cobro5');
	if( !AgregarParametros( form ) )
		return false;
	form.accion.value = 'imprimir';
	form.opc.value = 'guardar_cobro_pdf';
	form.submit();
	return true;
}

function ImprimirExcel( form, formato_especial )
{
	if(!form){
		var form = $('form_cobro5');
	}
	if( !AgregarParametros( form ) )
		return false;
	if( formato_especial == 'especial' )
		form.accion.value = 'descargar_excel_especial';
	else
		form.accion.value = 'descargar_excel';
	form.opc.value = 'guardar_cobro';
	form.submit();
	return true;
}

function GuardaCobro( form )
{
	if(!form)
		var form = $('form_cobro5');

	if( !AgregarParametros( form ) )
		return false;

	form.accion.value = '';
	form.opc.value = 'guardar_cobro';
	form.submit();
	return true;
}

function ActualizarMontos( form )
{
	if(!form)
		var form = $('form_cobro5');

	if(form.cobro_descuento.value=='')
	{
		alert("<?=__('Ud. debe ingresar un descuento a realizar.')?>");
		form.cobro_descuento.focus();
		return false;
	}

	if( !AgregarParametros( form ) )
		return false;

	form.opc.value = 'guardar_cobro';
	form.submit();
	return true;
}

function ShowMonto( showHoras )
{
	var div = document.getElementById("div_monto");
	div.style.display = "block";

	if( showHoras )
	{
   		div = document.getElementById("div_horas");
   		div.style.display = "block";
	}
	else
	{
		div = document.getElementById("div_horas");
		div.style.display = "none";
	}
	document.getElementById('ajustar_monto').style.display = 'none';
}

function ShowCapMsg(valor)
{
	/*var form = $('form_resumen_cobro');
	var div = $('msg_cap');
	if(form.excedido.value)
		div.style.display = valor;
	else
		div.style.display = 'none';
	*/
}

function HideMonto()
{
    var div = document.getElementById("div_monto");
    div.style.display = "none";

    div = document.getElementById("div_horas");
    div.style.display = "none";

    document.getElementById('ajustar_monto').style.display = 'inline';
}

function DisplayEscalas(mostrar){
	var div = document.getElementById("div_escalonada");
	if( mostrar ){
		div.style.display = "block";
	} else {
		div.style.display = "none";
	}
}

function ActualizaRango(desde, cant){
		var aplicar = parseInt(desde.substr(-1,1));
		var ini = 0;
		num_escalas = (document.getElementsByName('esc_tiempo[]')).length;
		for( var i = aplicar; i< num_escalas; i++){
			
			if( i > 1){
				ini = 0;
				for( var j = i; j > 1; j-- ){
					ini += parseFloat(document.getElementById('esc_tiempo_'+(j-1)).value);
					if( ini.length == 0 || isNaN(ini)){
						ini = 0;
					}
				}				
			}
			
			valor_actual = document.getElementById('esc_tiempo_'+(i)).value;
			if( i == aplicar ){
				if( cant.length > 0 && !isNaN(cant)){
					tiempo_final = parseFloat(ini,10) + parseFloat(cant,10);
				} else {
					tiempo_final = parseFloat(ini, 10);
				}
			} else {				
				if( valor_actual.length > 0 && !isNaN(valor_actual)){
					tiempo_final = parseFloat(ini,10) + parseFloat(valor_actual,10);
				} else {
					tiempo_final = parseFloat(ini, 10);
				}
			}
			revisor = document.getElementById('esc_tiempo_'+(i)).value;
			if( valor_actual.length == 0 || isNaN(valor_actual)){
				ini = 0;
				tiempo_final = 0;
			}
			donde = document.getElementById('esc_rango_'+i);
			donde.innerHTML = ini + ' - ' + tiempo_final;
		}
		
	}
	
	function cambia_tipo_forma(valor, desde){
		var aplicar = parseInt(desde.substr(-1,1));
		var donde = 'tipo_forma_' + aplicar + '_';
		var selector = document.getElementById(desde);
		
		for( var i = 1; i <= selector.length; i++ ){
			if( i == valor ) {
				document.getElementById(donde+i).style.display = 'inline-block';
			} else {
				document.getElementById(donde+i).style.display = 'none';
			}
		}
	}
	
	function setear_valores_escalon( donde, desde, tiempo, tipo, id_tarifa, monto, id_moneda, descuento ){
		if( desde != '' ) {
			/* si le paso desde donde copiar, los utilizo */
			document.getElementById('esc_tiempo_' + donde).value = document.getElementById('esc_tiempo_' + desde).value;
			document.getElementById('esc_selector_' + donde).value = document.getElementById('esc_selector_' + desde).value;
			cambia_tipo_forma(document.getElementById('esc_selector_' + desde).value, 'esc_selector_' + donde);
			document.getElementById('esc_id_tarifa_' + donde).value = document.getElementById('esc_id_tarifa_' + desde).value;			
			document.getElementById('esc_monto_' + donde).value = document.getElementById('esc_monto_' + desde).value;
			document.getElementById('esc_id_moneda_' + donde).value = document.getElementById('esc_id_moneda_' + desde).value;
			document.getElementById('esc_descuento_' + donde).value = document.getElementById('esc_descuento_' + desde).value;
		} else {
			/* sino utilizo los valores entregados individualmente */
			document.getElementById('esc_tiempo_' + donde).value = tiempo;
			document.getElementById('esc_selector_' + donde).value = tipo;
			cambia_tipo_forma(1,'esc_selector_' + donde);
			document.getElementById('esc_id_tarifa_' + donde).value = id_tarifa;
			document.getElementById('esc_monto_' + donde).value = monto;
			document.getElementById('esc_id_moneda_' + donde).value = id_moneda;
			document.getElementById('esc_descuento_' + donde).value = descuento;
			
		}
	}
	
	function agregar_eliminar_escala(divID){
		var numescala = parseInt(divID.substr(-1,1));
		var divArea = document.getElementById(divID);
		var divAreaImg = document.getElementById(divID+"_img");
		var divAreaVisible = divArea.style['display'] != "none";
		var esconder = "";
		
		if( !divAreaVisible ){
			for( var i = numescala; i> 1; i--){
				var valor_anterior = document.getElementById('esc_tiempo_'+(i-1)).value;
				if( valor_anterior != '' && valor_anterior > 0 ){
					divArea.style['display'] = "inline-block";
					divAreaImg.innerHTML = "<img src='../templates/default/img/menos.gif' border='0' title='Ocultar'> Eliminar";
				} else {
					alert('No puede agregar un escal�n nuevo, si no ha llenado los datos del escalon actual');
					return 0;
				}
			}
		} else {
			num_escalas = (document.getElementsByName('esc_tiempo[]')).length;
			esconder = divID;
			for( var i = numescala; i <= (num_escalas-2) ; i++ ){
				var siguiente = document.getElementById('esc_tiempo_'+(parseInt(i)+1));
				if( siguiente.style.display != "none"){
					valor_siguiente = document.getElementById('esc_tiempo_'+(parseInt(i)+1)).value;
					if( valor_siguiente > 0 ){
						setear_valores_escalon(i, (i+1),0,1,1,0,1,0);
						ActualizaRango('esc_tiempo_'+i, document.getElementById('esc_tiempo_'+(i+1)).value);
						setear_valores_escalon((i+1), '','',1,1,'',1,'');
						ActualizaRango('esc_tiempo_'+(parseInt(i)+1), '');
						esconder = "escalon_" + (parseInt(numescala)+1);
						
					} else {
						id_sgte = "escalon_" +(parseInt(i)+1);
						document.getElementById(id_sgte).style.display = "none";
						document.getElementById(id_sgte+"_img").innerHTML = "<img src='../templates/default/img/mas.gif' border='0' title='Desplegar'> Agregar";
					}
				} else {
					setear_valores_escalon(i, '','',1,1,'',1,'');
					ActualizaRango('esc_tiempo_'+i, '');
					esconder = "escalon_" + i;
					/*i = num_escalas;*/
				}
			}
			setear_valores_escalon(parseInt(esconder.substr(-1,1)), '','',1,1,'',1,'');
			ActualizaRango('esc_tiempo_'+esconder.substr(-1,1), '');
			document.getElementById(esconder).style.display = 'none';
			divAreaImg = document.getElementById(esconder+"_img");
			divAreaImg.innerHTML = "<img src='../templates/default/img/mas.gif' border='0' title='Desplegar'> Agregar";
		}
	}


function RecalcularTotal(desc) //isCap -> pasa true si es forma_cobro CAP
{
	var subtotal = parseFloat(document.getElementById("cobro_subtotal").value);
	var descuento = parseFloat(desc); //document.getElementById("cobro_descuento").value);
	var totalObj = document.getElementById("cobro_total");
	var form = document.getElementById("form_cobro5");

	if(parseFloat(descuento)>parseFloat(subtotal))
	{
		descuento = 0;
		form.cobro_descuento.value = 0;
	}

	if( isNaN(descuento) ) descuento = 0;
	var impuesto=0;
<?
	if( ( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'UsarImpuestoSeparado') ) || ( method_exists('Conf','UsarImpuestoSeparado') ) ) && $contrato->fields['usa_impuesto_separado'] )
	{
?>
	var campoImpuesto = document.getElementById("cobro_impuesto");
	valorImpuesto = (subtotal - descuento)*(<?=$cobro->fields['porcentaje_impuesto']?$cobro->fields['porcentaje_impuesto']:0?>)/100;
	campoImpuesto.value = valorImpuesto.toFixed(2);
	impuesto = parseFloat(campoImpuesto.value);

<?
	}
?>
	valorTotal = subtotal - descuento + impuesto;
	totalObj.value = valorTotal.toFixed(2);
}

function ToggleDiv( divId )
{
	var divObj = document.getElementById( divId );

	if( divObj )
	{
		if( divObj.style.display == 'none' )
			divObj.style.display = 'inline';
		else
			divObj.style.display = 'none';
	}
}

function ActualizarTipoCambio( form, valor )
{
	form.cobro_tipo_cambio.value = valor;
}

function ActualizarPadre()
{
if( window.opener.Refrescar )
	window.opener.Refrescar(<?=$id_foco ?>);
}

/*Array tipo de cambios de prm_moneda JS*/
var tipo_cambio = new Array(false);
<?
$monedas = new ListaMonedas($sesion, '','SELECT * FROM prm_moneda');
for( $i=0; $i<$monedas->num; $i++ )
{
	$moneda = $monedas->Get($i);
?>
	tipo_cambio[<?=$moneda->fields['id_moneda']?>]= <?=$moneda->fields['tipo_cambio']?>;
<?
}
?>



/* Actualiza los tipos de cambio al cambio actual de cada moneda */
function UpdateTipoCambio( form )
{
	var form = document.getElementById('form_cobro5');
	var id_cobro = document.getElementById('id_cobro').value;

	if(confirm('<?=__("�Desea actualizar al tipo de cambio actual?")?>'))
	{
		var http = getXMLHTTP();
		http.open('get', 'ajax.php?accion=update_cobro_moneda&id_cobro='+id_cobro);
		http.onreadystatechange = function()
		{
      if(http.readyState == 4)
      {
				var response = http.responseText;
				if(response)
				{
					msg_div = $('msg_cambio');
					msg_div.style.display = 'inline';
					form.opc.value = 'up_cambios';
					form.submit();
				}
      }
		};
	    http.send(null);
	}
}

/* Ajax guarda tipo de cambio en cobro_moneda */
function GuardaTipoCambio( id_moneda, tipo_cambio )
{
	var form = $('form_cobro5');
	var msg_cambio = $('msg_cambio');
	if(!parseFloat(tipo_cambio) || parseFloat(tipo_cambio) == 0)
	{
		alert('<?=__("El monto ingresado del tipo de cambio es incorrecto")?>');
		var tipo_cambio = 'cobro_tipo_cambio_'+id_moneda;
		var tipo_cambio_id = $(tipo_cambio);
		tipo_cambio_id.value = 1;
		tipo_cambio_id.focus();
		return false;
	}
	else
	{
		var id_cobro = $('id_cobro').value;
		tipo_cambio = tipo_cambio.replace(',','.');

		var http = getXMLHTTP();
		http.open('get', 'ajax_grabar_campo.php?accion=guardar_tipo_cambio&id_cobro='+id_cobro+'&id_moneda='+id_moneda+'&tipo_cambio='+tipo_cambio);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4)
		  	{
				var response = http.responseText;
				if(response)
				{
					msg_cambio.style.display = 'inline';
					return true;
				}
				else
					return false;
			}
		};
		http.send(null);
		ActualizarSaldoAdelantos();
}

function ActualizarSaldoAdelantos(){
		var tipos_cambio = [];
		$$('[id^="cobro_tipo_cambio_"]').each(function(elem){
			tipos_cambio.push(elem.id.substr('cobro_tipo_cambio_'.length)+':'+elem.value);
		});
		var http = getXMLHTTP();
		http.open('get', 'ajax.php?accion=saldo_adelantos&codigo_cliente=<?=$cobro->fields['codigo_cliente']?>&id_contrato=<?=$cobro->fields['id_contrato']?>&pago_honorarios='+(Number($F('total_honorarios'))>0?1:0)+'&pago_gastos='+(Number($F('total_gastos'))>0?1:0)+'&id_moneda='+$F('opc_moneda_total')+'&tipocambio='+tipos_cambio.join(';'));
		http.onreadystatechange = function()
		{
			if(http.readyState == 4)
		  	{
				var response = http.responseText;
				if(response)
				{
					$('saldo_adelantos').value = response;
					return true;
				}
				else
					return false;
			}
		};
		http.send(null);
	}
}

/*CANCELA UPDATE CAP*/
function CancelaUpdateCap()
{
	var form = $('form_cobro5');
	form.cobro_monto_contrato.value = parseFloat(form.monto_contrato.value);
	return true;
}

/* UPDATE valor de cap para COBRO y CONTRATO asosiado */
function UpdateCap(monto_update, guardar)
{
	if(!guardar)
	{
		var text_window = "<img src='<?=Conf::ImgDir()?>/alerta_16.gif'>&nbsp;&nbsp;<span style='font-size:12px; color:#FF0000; text-align:center;font-weight:bold'><u><?=__("ALERTA")?></u><br><br>";
		text_window += '<span style="text-align:center; font-size:11px; color:#000; "><?=__('Ud. est� modificando el valor del CAP. Si Ud. modifica ese valor, tambi�n se modificar� el valor del CAP en el contrato asociado').', '.__('el valor del CAP seg�n contrato es de').': <u>'.$contrato->fields['monto'].' '.$moneda_cobro->fields['glosa_moneda'].'</u><br><br>'.__('�desea realizar esta operaci�n?')?></span><br>';
		Dialog.confirm(text_window,
		{
			top:250, left:290, width:400, okLabel: "<?=__('Aceptar')?>", cancelLabel: "<?=__('Cancelar')?>", buttonClass: "btn", className: "alphacube",
			id: "myDialogId",
			cancel:function(win){ CancelaUpdateCap() },
			ok:function(win){ UpdateCap(monto_update,true); }
		});
	}

	if(!parseFloat(monto_update))
		return false;
	var form = $('form_cobro5');
	var id_cobro = $('id_cobro').value;
	var id_contrato = $('id_contrato').value;
	var id_moneda_monto = $('id_moneda_monto').value;

	if(guardar == true)
	{
		var http = getXMLHTTP();
		http.open('get', 'ajax.php?accion=update_cap&id_cobro='+id_cobro+'&id_contrato='+id_contrato+'&monto_update='+monto_update+'&id_moneda_monto='+id_moneda_monto);
		http.onreadystatechange = function()
		{
      if(http.readyState == 4)
      {
				var response = http.responseText;
				if(response)
				{
					var form_montos = $('form_cobro5');
					form_montos.submit();
					return true;
				}
      }
		};
    http.send(null);
	}
	else
		return false;
}
// -->
</script>

<?
	$x_resultados = UtilesApp::ProcesaCobroIdMoneda($sesion, $cobro->fields['id_cobro'],array(),0,false);

	#Para revisar si existen facturas (No puede volver a creado).
	$query = "SELECT count(*) FROM factura_cobro WHERE id_cobro = '".$cobro->fields['id_cobro']."'";
	$resp = mysql_query($query, $sesion->dbh) or Utiles::errorSQL($query,__FILE__,__LINE__,$sesion->dbh);
	list($numero_facturas_asociados) = mysql_fetch_array($resp);
	if( $numero_facturas_asociados > 0 )
		$existe_factura = 1;
	else
		$existe_factura = 0;
?>

<form method="post" id="form_cobro5" name="form_cobro5" >
<input type="hidden" name="existe_factura" id="existe_factura" value="<?=$existe_factura?>" />
<input type="hidden" name="id_cobro" id="id_cobro" value="<?=$id_cobro?>">
<input type="hidden" name="ajustar_monto_hide" id="ajustar_monto_hide" value="<?=$cobro->fields['monto_ajustado'] > 0 ? true : false ?>" />
<input type="hidden" name="opc" value="guardar_cobro">
<input type="hidden" name="id_contrato" value="<?=$cobro->fields['id_contrato']?>" id="id_contrato">
<input type="hidden" name="excedido" value="<?=$excedido?>" />
<input type="hidden" name="monto_contrato" id="monto_contrato" value="<?=$cobro->fields['monto_contrato']?>">
<input type="hidden" name="cobro_tipo_cambio" value="<?=$cobro->fields['tipo_cambio_moneda']?>" size="8">
<input type="hidden" name="id_tarifa" id="id_tarifa" value="<?=$contrato->fields['id_tarifa']?>" />
<input type="hidden" name="accion" value="" id="accion">
<input type="hidden" name="saldo_adelantos" value="<?php
$documento = new Documento($sesion);
$pago_honorarios = (float)($cobro->fields['monto_subtotal']) ? 1 : 0;
$pago_gastos = (float)($cobro->fields['subtotal_gastos']) ? 1 : 0;
$cobro_moneda = new ListaMonedas($sesion, '','SELECT * FROM cobro_moneda WHERE id_cobro = '.$id_cobro);
$tipo_cambio_cobro = Array();
for( $i=0; $i<$monedas->num; $i++ )
{
	$cambio_moneda = $cobro_moneda->Get($i);
	if(empty($cambio_moneda->fields['tipo_cambio'])){
		$moneda = $monedas->Get($i);
		$tipo_cambio_cobro[$cambio_moneda->fields['id_moneda']] = $moneda->fields['tipo_cambio'];
	}
	else $tipo_cambio_cobro[$cambio_moneda->fields['id_moneda']] = $cambio_moneda->fields['tipo_cambio'];
}
echo $documento->SaldoAdelantosDisponibles($cobro->fields['codigo_cliente'], $cobro->fields['id_contrato'], $pago_honorarios, $pago_gastos, $cobro->fields['opc_moneda_total'], $tipo_cambio_cobro);
?>" id="saldo_adelantos" />
<input type="hidden" name="usar_adelantos" value="" id="usar_adelantos" />

<table width='720px'>
	<tr>
		<td align=left><input type="button" class=btn value="<?=__('<< Anterior')?>" onclick="Anterior(this.form);"></td>
		<td align=right>
<?
			if($cobro->fields['estado'] == 'CREADO')
				{ ?>
				<input type="button" class=btn value="<?=__('Revisar Cobro')?>" onclick="EnRevision(this.form);">
		<?  }
			else if($cobro->fields['estado'] == 'EN REVISION')
				{ ?>
					En revisi�n. &nbsp;&nbsp;
					<input type="button" class=btn value="<?=__('Volver al estado CREADO')?>" onclick="VolverACreado(this.form);">
		<?	}
?>
			<input type="button" class=btn value="<?=__('Emitir Cobro')?>" onclick="Emitir(this.form);">
		</td>
	</tr>
</table>
<br>
<table width=100% cellspacing="3" cellpadding="3">
  <tr>
    <td align="left" style="background-color: #A3D55C; color: #000000; font-size: 14px; font-weight: bold;">
        <?=__('Par�metros del Cobro')?>
    </td>
  </tr>
</table>

<?php if(!empty($cobro->fields['incluye_honorarios'])){ ?>
<fieldset id="periodo" style="width: 95%">
<legend><?=__('Periodo')?></legend>
<table width=100% cellspacing=3 cellpadding=3>
    <tr>
		<td align="center">
			<?=__('Periodo').': '?>
			<?=$cobro->fields['fecha_ini'] != '0000-00-00' ? __('Desde').': '.Utiles::sql2date($cobro->fields['fecha_ini']).' ' : '' ?>
			<?=$cobro->fields['fecha_fin'] != '0000-00-00' ? __('Hasta').': '.Utiles::sql2date($cobro->fields['fecha_fin']) : '' ?>
			&nbsp;&nbsp;&nbsp;<a href='cobros3.php?id_cobro=<?=$cobro->fields['id_cobro']?>&popup=1' title='<?=__('Editar periodo')?>'><span style='font-size:11px'>Editar</span></a>
		</td>
	</tr>
</table>
</fieldset>
<?php } ?>

<!-- Moneda -->
<fieldset id="moneda" style="width: 95%">
<legend><?=__('Moneda')?></legend>
<table width=100% cellspacing=3 cellpadding=3>
 	<tr>
    	<td align="center">
			<table width=95%>
				<tr>
					<td colspan='<?=$monedas->num ?>' align='left' style='padding-left:20px; padding-right:10px'>
						<table width=100%>
						<tr>
							<td align=left>
								<span style="font-size:9px; color:#FF7D7D; font-style:italic" >Ingresar decimales con punto. Ejemplo 23024.33</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							</td>
							<td align=right>
								<span style="font-size:9px; color:#FF7D7D; font-style:italic;" >Actualizar a los tipos de cambio actuales</span>&nbsp;&nbsp;<img <?= TTip($tip_refresh) ?> style="cursor:pointer" src="<?=Conf::ImgDir()?>/download_from_web.gif" onclick="UpdateTipoCambio(this.form)">
							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr>
			<?
				/* Lista de moneda del cobro */
				$cobro_moneda = new ListaMonedas($sesion, '','SELECT * FROM cobro_moneda WHERE id_cobro = '.$id_cobro);
				$tipo_cambio_cobro = Array();
				for( $i=0; $i<$monedas->num; $i++ )
				{
					$cambio_moneda = $cobro_moneda->Get($i);
					$tipo_cambio_cobro[$cambio_moneda->fields['id_moneda']] = $cambio_moneda->fields['tipo_cambio'];
				}

				#$monedas = new ListaMonedas($sesion, '','SELECT * FROM prm_moneda');
				for( $i=0; $i<$monedas->num; $i++ )
				{
					$moneda = $monedas->Get($i);
			?>
					<td align='center' style='padding-left:10px; padding-right:10px'>
						<input type="radio" id="cobro_id_moneda<?=$i?>" name="cobro_id_moneda"  value="<?=$moneda->fields['id_moneda']?>" <?=$moneda->fields['id_moneda']== $cobro->fields['id_moneda'] ? 'checked' : ''?> onclick="ActualizarTipoCambio(this.form, '<?=$moneda->fields['tipo_cambio']?>');" ><label for="cobro_id_moneda<?=$i?>"><?=$moneda->fields['glosa_moneda']?></label>
					</td>
			<?
				}
			?>
				</tr>
					<input type=hidden name=monedas_num value=<?=$monedas->num > 0 ? $monedas->num : 0 ?> id=monedas_num>
				<tr>
			<?
				for( $i=0; $i<$monedas->num; $i++ )
				{
					$moneda = $monedas->Get($i);
					$tipo = $tipo_cambio_cobro[$moneda->fields['id_moneda']];
			?>
					<td align='center' style='padding-left:10px; padding-right:10px'>
						<input type="text" size="8" name="cobro_tipo_cambio_<?=$moneda->fields['id_moneda']?>" id="cobro_tipo_cambio_<?=$moneda->fields['id_moneda']?>" onkeydown="MontoValido( this.id );" value="<?=$tipo > 0 ? $tipo : $moneda->fields['tipo_cambio']?>" onchange="GuardaTipoCambio(<?=$moneda->fields['id_moneda']?>,this.value)">
					</td>
			<?
				}
			?>
				</tr>
				<tr>
					<td colspan='<?=$monedas->num ?>' align='center' style='padding-left:20px; padding-right:10px'>
						<div id='msg_cambio' style='font-size:10px;display:none;color:#FF7D7D'><?=__('Los tipos de cambio han sido actualizados correctamente') ?></div>
          </td>
        </tr>
			</table>
    </td>
 	</tr>
</table>
</fieldset>
<!-- fin Moneda -->

<!-- Modalidad -->
<fieldset id="forma_cobro" style="width: 95%; display: <?php echo ( $cobro->fields['incluye_honorarios'] != 0 ? "block" : "none"); ?>">
<legend><?=__('Forma de cobro')?></legend>
<table width='100%' cellspacing='3' cellpadding='3'>
 <tr>
    <td align="center">
        <?=__('Forma de cobro')?>
    </td>
    <td align="center">
<?
						if($cobro->fields['forma_cobro']=='')
							$cobro_forma_cobro = 'TASA';
						else
							$cobro_forma_cobro = $cobro->fields['forma_cobro'];
?>
            <input <?= TTip($tip_tasa) ?> onclick="HideMonto();ShowCapMsg('none');DisplayEscalas(false);" id="fc1" type="radio" name="cobro_forma_cobro" value="TASA" <?= $cobro_forma_cobro == "TASA" ? "checked" : "" ?> />
            <label for="fc1">Tasas/HH</label>&nbsp; &nbsp;
            <input <?= TTip($tip_retainer) ?> onclick="ShowMonto(true);ShowCapMsg('none');DisplayEscalas(false);" id="fc3" type="radio" name="cobro_forma_cobro" value="RETAINER" <?= $cobro_forma_cobro == "RETAINER" ? "checked" : "" ?> />
            <label for="fc3">Retainer</label> &nbsp; &nbsp;
            <input <?= TTip($tip_flat) ?> onclick="ShowMonto(false);ShowCapMsg('none');DisplayEscalas(false);" id="fc4" type="radio" name="cobro_forma_cobro" value="FLAT FEE" <?= $cobro_forma_cobro == "FLAT FEE" ? "checked" : "" ?> />
            <label for="fc4">Flat fee</label>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            <? if($cobro->fields['id_contrato']){ ?>
            <input <?= TTip($tip_cap) ?> onclick="ShowMonto(false);ShowCapMsg('inline');DisplayEscalas(false);" id="fc5" type="radio" name="cobro_forma_cobro" value="CAP" <?= $cobro_forma_cobro == "CAP" ? "checked" : "" ?> />
						<label for="fc5"><?=__('Cap')?></label>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
						<? } ?>
			<input <?= TTip($tip_proporcional) ?> onclick="ShowMonto(true);ShowCapMsg('none');DisplayEscalas(false);" id="fc6" type=radio name="cobro_forma_cobro" value="PROPORCIONAL" <?= $cobro_forma_cobro == "PROPORCIONAL" ? "checked" : "" ?> />
            <label for="fc6">Proporcional</label> &nbsp; &nbsp;
			<input <?= TTip($tip_escalonada) ?> onclick="HideMonto();ShowCapMsg('none');DisplayEscalas(true);" id="fc7" type=radio name="cobro_forma_cobro" value="ESCALONADA" <?= $cobro_forma_cobro == "ESCALONADA" ? "checked" : "" ?> />
            <label for="fc7">Escalonada</label> &nbsp; &nbsp;
            <div id="div_monto" align="left" style="display:none; background-color:#F8FBBD; padding-left:20px">
            	<table>
            		<tr>
            			<td>
              			<?=__('Monto')?>
              		</td>
              		<td>
              			<input name="cobro_monto_contrato" size="7" value="<?=$cobro->fields['monto_contrato']?>" <?=$cobro->fields['id_contrato'] && $cobro->fields['forma_cobro'] == 'CAP' ? 'onchange="UpdateCap(this.value, false)"' : ''?>>
              		</td>
              		<td>&nbsp;&nbsp;&nbsp;&nbsp;
              			<?=__('Moneda')?>&nbsp;
										<?=Html::SelectQuery( $sesion, "SELECT id_moneda,glosa_moneda FROM prm_moneda ORDER BY id_moneda","id_moneda_monto", $cobro->fields['id_moneda_monto'] ? $cobro->fields['id_moneda_monto'] : $id_moneda_monto, '','',"80"); ?>
									</td>
								</tr>
							</table>
            </div>
            <div id="div_horas" align="left" style="display:none; background-color:#F8FBBD; padding-left:20px">
            	<table>
            		<tr>
            			<td align=left>
							<?=__('Horas')?>
						</td>
						<td align=left>
							<input name="cobro_retainer_horas" size="7" value="<?=$cobro->fields['retainer_horas']?>" />
						</td>
					</tr>
				</table>
            </div>
			<div id="div_escalonada" align="left" style="display:none; background-color:#F8FBBD; padding-left:20px">
				<div class="template_escalon" id="escalon_1">
					<table style='padding: 5px; border: 0px solid' bgcolor='#F8FBBD'>
						<tr>
							<td valign="bottom">
								<div style="display:inline-block; width: 75px;"><?php echo __('Las primeras'); ?> </div>
								<input type="text" name="esc_tiempo[]" id="esc_tiempo_1" size="4" value="<?php echo $cobro->fields['esc1_tiempo']; ?>" onkeyup="ActualizaRango(this.id , this.value);" /> 
								<span><?php echo __('horas trabajadas'); ?> (</span> <div id="esc_rango_1" style="display:inline-block; width: 60px; text-align: center;">0 - 0</div> <span>) <?php echo __('aplicar'); ?></span>
								<select name="esc_selector[]" id="esc_selector_1" onchange="cambia_tipo_forma(this.value, this.id);">
									<option value="1" <?php echo !isset($cobro->fields['esc1_monto']) || $cobro->fields['esc1_monto'] == 0 ? 'selected="selected"' : ''; ?>>tarifa</option>
									<option value="2" <?php echo $cobro->fields['esc1_monto'] > 0 ? 'selected="selected"' : ''; ?> >monto</option>
								</select>
								<span>
									<span id="tipo_forma_1_1" style="display: inline-block;">
										<?php echo Html::SelectQuery($sesion, "SELECT id_tarifa, glosa_tarifa FROM tarifa", "esc_id_tarifa_1" , $cobro->fields['esc1_id_tarifa'], 'style="font-size:9pt; width:130px;"'); ?>
									</span>
									<span id="tipo_forma_1_2" style="display: none;">
										<input type="text" size="8" style="font-size:9pt; width:130px;" id="esc_monto_1" value="<?php echo $cobro->fields['esc1_monto']; ?>" name="esc_monto[]" />
									</span>
								</span>
								<span><?php echo __('en'); ?></span> 
								<?php echo Html::SelectQuery($sesion, "SELECT id_moneda, glosa_moneda FROM prm_moneda ORDER BY id_moneda", 'esc_id_moneda_1', $cobro->fields['esc1_id_moneda'], 'style="font-size:9pt; width:70px;"'); ?> 
								<span><?php echo __('con'); ?> </span>
								<input type="text" name="esc_descuento[]" id="esc_descuento_1" value="<?php echo $cobro->fields['esc1_descuento']; ?>" size="4" /> 
								<span><?php echo __('% dcto.'); ?> </span>
							</td>
						</tr>
					</table>
					<div <?= !$div_show ? 'onClick="agregar_eliminar_escala(\'escalon_2\')" style="cursor:pointer"' : '' ?> >
						<?= !$div_show ? '<span id="escalon_2_img"><img src="' . Conf::ImgDir() . '/mas.gif" border="0" id="datos_cobranza_img"> ' . __('Agregar') . '</span>' : '' ?>											
					</div>
				</div>
				<div class="template_escalon" id="escalon_2" style="display: <?php echo isset($cobro->fields['esc2_tiempo']) && $cobro->fields['esc2_tiempo'] > 0 ? 'block' : 'none'; ?>;">
					<table style='padding: 5px; border: 0px solid' bgcolor='#F8FBBD'>
						<tr>
							<td valign="bottom">
								<div style="display:inline-block; width: 75px;"><?php echo __('Las siguientes'); ?> </div>
								<input type="text" name="esc_tiempo[]" id="esc_tiempo_2" size="4" value="<?php echo $cobro->fields['esc2_tiempo']; ?>" onkeyup="ActualizaRango(this.id , this.value);" /> 
								<span><?php echo __('horas trabajadas'); ?> (</span> <div id="esc_rango_2" style="display:inline-block; width: 60px; text-align: center;">0 - 0</div> <span>) <?php echo __('aplicar'); ?></span>
								<select name="esc_selector[]" id="esc_selector_2" onchange="cambia_tipo_forma(this.value, this.id);">
									<option value="1" <?php echo !isset($cobro->fields['esc2_monto']) || $cobro->fields['esc1_monto'] == 0 ? 'selected="selected"' : ''; ?>>tarifa</option>
									<option value="2" <?php echo $cobro->fields['esc2_monto'] > 0 ? 'selected="selected"' : ''; ?> >monto</option>
								</select>
								<span>
									<span id="tipo_forma_2_1" style="display: inline-block;">
										<?php echo Html::SelectQuery($sesion, "SELECT id_tarifa, glosa_tarifa FROM tarifa", "esc_id_tarifa_2" , $cobro->fields['esc2_id_tarifa'], 'style="font-size:9pt; width:130px;"'); ?>
									</span>
									<span id="tipo_forma_2_2" style="display: none;">
										<input type="text" size="8" style="font-size:9pt; width:130px;" id="esc_monto_2" name="esc_monto[]" value="<?php echo $cobro->fields['esc2_monto']; ?>" />
									</span>
								</span>
								<span><?php echo __('en'); ?></span> 
								<?php echo Html::SelectQuery($sesion, "SELECT id_moneda, glosa_moneda FROM prm_moneda ORDER BY id_moneda", 'esc_id_moneda_2', $cobro->fields['esc2_id_moneda'], 'style="font-size:9pt; width:70px;"'); ?> 
								<span><?php echo __('con'); ?> </span>
								<input type="text" name="esc_descuento[]" value="<?php echo $cobro->fields['esc2_descuento']; ?>" id="esc_descuento_2" size="4" /> 
								<span><?php echo __('% dcto.'); ?> </span>
							</td>
						</tr>
					</table>
					<div <?= !$div_show ? 'onClick="agregar_eliminar_escala(\'escalon_3\')" style="cursor:pointer"' : '' ?> >
						<?= !$div_show ? '<span id="escalon_3_img"><img src="' . Conf::ImgDir() . '/mas.gif" border="0" id="datos_cobranza_img"> ' . __('Agregar') . '</span>' : '' ?>
					</div>
				</div>
				<div class="template_escalon" id="escalon_3" style="display: <?php echo isset($cobro->fields['esc3_tiempo']) && $cobro->fields['esc3_tiempo'] > 0 ? 'block' : 'none'; ?>;">
					<table style='padding: 5px; border: 0px solid' bgcolor='#F8FBBD'>
						<tr>
							<td valign="bottom">
								<div style="display:inline-block; width: 75px;"><?php echo __('Las siguientes'); ?> </div>
								<input type="text" name="esc_tiempo[]" id="esc_tiempo_3" size="4" value="<?php echo $cobro->fields['esc3_tiempo']; ?>" onkeyup="ActualizaRango(this.id , this.value);" /> 
								<span><?php echo __('horas trabajadas'); ?> (</span> <div id="esc_rango_3" style="display:inline-block; width: 60px; text-align: center;">0 - 0</div> <span>) <?php echo __('aplicar'); ?></span>
								<select name="esc_selector[]" id="esc_selector_3" onchange="cambia_tipo_forma(this.value, this.id);">
									<option value="1" <?php echo !isset($cobro->fields['esc3_monto']) || $cobro->fields['esc1_monto'] == 0 ? 'selected="selected"' : ''; ?>>tarifa</option>
									<option value="2" <?php echo $cobro->fields['esc3_monto'] > 0 ? 'selected="selected"' : ''; ?> >monto</option>
								</select>
								<span>
									<span id="tipo_forma_3_1" style="display: inline-block;">
										<?php echo Html::SelectQuery($sesion, "SELECT id_tarifa, glosa_tarifa FROM tarifa", "esc_id_tarifa_3" , $cobro->fields['esc3_id_tarifa'], 'style="font-size:9pt; width:130px;"'); ?>
									</span>
									<span id="tipo_forma_3_2" style="display: none;">
										<input type="text" size="8" style="font-size:9pt; width:130px;" id="esc_monto_3" name="esc_monto[]" value="<?php echo $cobro->fields['esc3_monto']; ?>" />
									</span>
								</span>
								<span><?php echo __('en'); ?></span> 
								<?php echo Html::SelectQuery($sesion, "SELECT id_moneda, glosa_moneda FROM prm_moneda ORDER BY id_moneda", 'esc_id_moneda_3', $cobro->fields['esc3_id_moneda'], 'style="font-size:9pt; width:70px;"'); ?> 
								<span><?php echo __('con'); ?> </span>
								<input type="text" name="esc_descuento[]" id="esc_descuento_3" value="<?php echo $cobro->fields['esc3_descuento']; ?>" size="4" /> 
								<span><?php echo __('% dcto.'); ?> </span>
							</td>
						</tr>
					</table>
				</div>
				<div class="template_escalon" id="escalon_4">
					<table style='padding: 5px; border: 0px solid' bgcolor='#F8FBBD'>
						<tr>
							<td valign="bottom">
								<div style="display:inline-block; width: 170px;"><?php echo __('Para el resto de horas trabajadas'); ?> </div>													
								<?php echo __('aplicar'); ?>
								<input type="hidden" name="esc_tiempo[]" id="esc_tiempo_4" value="-1" size="4" onkeyup="ActualizaRango(this.id , this.value);" /> 
								<select name="esc_selector[]" id="esc_selector_4" onchange="cambia_tipo_forma(this.value, this.id);">
									<option value="1" <?php echo !isset($cobro->fields['esc4_monto']) || $cobro->fields['esc1_monto'] == 0 ? 'selected="selected"' : ''; ?>>tarifa</option>
									<option value="2" <?php echo $cobro->fields['esc4_monto'] > 0 ? 'selected="selected"' : ''; ?> >monto</option>
								</select>
								<span>
									<span id="tipo_forma_4_1" style="display: inline-block;">
										<!-- function SelectQuery( $sesion, $query, $name, $selected='', $opciones='',$titulo='',$width='150') -->
										<?php echo Html::SelectQuery($sesion, "SELECT id_tarifa, glosa_tarifa FROM tarifa", "esc_id_tarifa_4" , $cobro->fields['esc4_id_tarifa'], 'style="font-size:9pt; width:130px;"'); ?>
									</span>
									<span id="tipo_forma_4_2" style="display: none;">
										<input type="text" size="8" style="font-size:9pt; width:130px;" id="esc_monto_4" value="<?php echo $cobro->fields['esc4_monto']; ?>" name="esc_monto[]" />
									</span>
								</span>
								<span><?php echo __('en'); ?></span> 
								<?php echo Html::SelectQuery($sesion, "SELECT id_moneda, glosa_moneda FROM prm_moneda ORDER BY id_moneda", 'esc_id_moneda_4', $cobro->fields['esc4_id_moneda'], 'style="font-size:9pt; width:70px;"'); ?> 
								<span><?php echo __('con'); ?> </span>
								<input type="text" name="esc_descuento[]" id="esc_descuento_4" value="<?php echo $cobro->fields['esc4_descuento']; ?>" size="4" /> 
								<span><?php echo __('% dcto.'); ?> </span> 
							</td>
						</tr>
					</table>
				</div>
			</div>
    </td>
 </tr>
 <tr>
    <td align="center" colspan="2"><a href="#" onclick="ActualizarTarifas();" title="Actualizar las tarifas de todos los trabajos de este cobro">Actualizar tarifas</a></td>
 </tr>
</table>
</fieldset>
<!-- fin Modalidad -->

<!--<form id="form_resumen_cobro" method="post" onsubmit="return ActualizarMontos(this);">-->
<table width=100% cellspacing="3" cellpadding="3">
  <tr>
    <td valign="middle" align="left" style="background-color: #A3D55C; color: #000000; font-size: 14px; font-weight: bold;">
        <?=__('Resumen final del Cobro')?>
    </td>
  </tr>
</table>

<?
	if( $cobro->fields['forma_cobro'] == 'TASA' )
	{
		if( $cobro->fields['monto_ajustado'] > 0 )
		{
			$display_ajustar = 'style="display: table-row;"';
			$deshabilitar = '';
			$display_buton_cancelar = 'style="display: inline;"';
			$display_buton_ajuste = 'style="display: none;"';
		}
		else
		{
			$display_ajustar = 'style="display: none;"';
			$deshabilitar = 'readonly="readonly"';
			$display_buton_cancelar = 'style="display: none;"';
			$display_buton_ajuste = 'style="display: inline;"';
		}
	}
	else
	{
		$display_ajustar = 'style="display: none;"';
		$deshabilitar = 'readonly="readonly"';
		$display_buton_cancelar = 'style="display: none;"';
		$display_buton_ajuste = 'style="display: none;"';
	}
?>

<table width=100% cellspacing="3" cellpadding="3">
  <tr>
    <td align="left">
			<table cellspacing="1" cellpadding="2" style='border:1px dotted #bfbfcf'>
				<tr>
					<td colspan="2" bgcolor="#dfdfdf">
						<span style="font-weight: bold; font-size: 11px;"><?=__('Honorarios')?></span>
					</td>
				</tr>
			  <tr>
			    <td align="right" width="45%" nowrap>
			    		<?=__('Trabajos')?> (<span id="divCobroUnidadHonorarios" style='font-size:10px'><?=$moneda_cobro->fields['simbolo']?></span>):
			    </td>
			    <td align="left" width="55%" nowrap>
			    	<input type="text" name="cobro_monto_honorarios" id="cobro_monto_honorarios" onkeydown="MontoValido( this.id );" value="<?=number_format($cobro->fields['monto_subtotal']-$cobro->CalculaMontoTramites( $cobro ),$moneda_cobro->fields['cifras_decimales'],'.','')?>" size="12" <?=$deshabilitar ?> style="text-align: right;" onkeydown="MontoValido( this.id );">
			    	&nbsp;&nbsp;<img src="<?=Conf::ImgDir()?>/reload_16.png" onclick='GuardaCobro(this.form)' style='cursor:pointer' <?=TTip($tip_actualizar)?>>
			    	<img id="ajustar_monto" <?=$display_buton_ajuste ?> src="<?=Conf::ImgDir().'/editar_on.gif'?>" title="<?=__('Ajustar Monto')?>" border=0 style="cursor:pointer" onclick="AjustarMonto('ajustar');">
			    	<img id="cancelar_ajustacion" <?=$display_buton_cancelar ?> src="<?=Conf::ImgDir().'/cruz_roja_nuevo.gif'?>" title="<?=__('Usar Monto Original')?>" border=0 style='cursor:pointer' onclick="AjustarMonto('cancelar')">
			 </td>
			  </tr>
			  <tr id="tr_monto_original" <?=$display_ajustar ?>>
			  	<td>
			  		<?=__('Monto Original')?> (<span id="divCobroUnidadHonorarios" style='font-size:10px'><?=$moneda_cobro->fields['simbolo']?></span>):
			  	</td>
			  	<td align="left">
			  		<input type="text" id="monto_original" name="monto_original" value="<?=number_format($cobro->fields['monto_original'],$moneda_cobro->fields['cifras_decimales'],'.','')?>" size="12" disabled style="text-align: right;">
			   	</td>
			  </tr>
			  <tr>
			    <td align="right" width="45%" nowrap>
			    		<?=__('Tr�mites')?> (<span id="divCobroUnidadTramites" style='font-size:10px'><?=$moneda_cobro->fields['simbolo']?></span>):
			    </td>
			    <td align="left" width="55%" nowrap>
			    	<input type="text" id="cobro_monto_tramites" value="<?=number_format($cobro->CalculaMontoTramites( $cobro ),$moneda_cobro->fields['cifras_decimales'],'.','')?>" size="12" readonly="readonly" style="text-align: right;">
			    	</td>
			  </tr>
			  <tr>
			    <td align="right" width="45%" nowrap>
			    		<?=__('Subtotal')?> (<span id="divCobroUnidadSubtotal" style='font-size:10px'><?=$moneda_cobro->fields['simbolo']?></span>):
			    </td>
			    <td align="left" width="55%" nowrap>
			    	<input type="text" id="cobro_subtotal" value="<?=number_format($cobro->fields['monto_subtotal'],$moneda_cobro->fields['cifras_decimales'],'.','')?>" size="12" readonly="readonly" style="text-align: right;" <?=TTip($tip_subtotal)?>>
			    	</td>
			  </tr>
			  <tr bgcolor='#F3F3F3'>
					<td align=right nowrap>
						<?=__('Descuento')?> (<span id="divCobroUnidadDescuento" style='font-size:10px'><?=$moneda_cobro->fields['simbolo']?></span>):
					</td>
					<td align=left nowrap>
<?
						if($cobro->fields['tipo_descuento']=='')
							$chk = 'VALOR';
						else
							$chk = $cobro->fields['tipo_descuento'];
						#$moneda_cobro->fields['cifras_decimales']
?>
						<input type="text" name="cobro_descuento" style="text-align: right;" id="cobro_descuento" onkeydown="MontoValido( this.id );" size=12 value=<?=number_format($cobro->fields['descuento'],$moneda_cobro->fields['cifras_decimales'],'.','')?> onchange="RecalcularTotal(this.value);" <?=TTip($tip_descuento)?>>
						<input type="radio" name="tipo_descuento" id="tipo_descuento" value='VALOR' <?=$chk == 'VALOR' ? 'checked' : '' ?> ><?=__('Valor')?>
					</td>
				</tr>
				<tr bgcolor='#F3F3F3'>
					<td align=right>&nbsp;</td>
					<td align=left>
						<input type="text" name="porcentaje_descuento" style="text-align: right;" id="porcentaje_descuento" onkeydown="MontoValido( this.id );" size=12 value=<?=number_format($cobro->fields['porcentaje_descuento'],$moneda_cobro->fields['cifras_decimales'],'.','') ?>>
						<input type="radio" name="tipo_descuento" id="tipo_descuento" value='PORCENTAJE' <?=$chk == 'PORCENTAJE' ? 'checked' : '' ?>><?=__('%')?>
					</td>
				</tr>
<?
			if( ( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'UsarImpuestoSeparado') ) || ( method_exists('Conf','UsarImpuestoSeparado') ) ) && $contrato->fields['usa_impuesto_separado'])
			{
?>
			  <tr>
			    <td align="right"><?=__('Impuesto')?> (<span id="divCobroImpuestoUnidad" style='font-size:10px'><?=$cobro->fields['porcentaje_impuesto'].'%'?></span>):</td>
			    <td align="left"><input type="text" id="cobro_impuesto" value="<?=number_format(($cobro->fields['monto_subtotal']-$cobro->fields['descuento'])*$cobro->fields['porcentaje_impuesto']/100,$moneda_cobro->fields['cifras_decimales'],'.','')?>" size="12" readonly="readonly" style="text-align: right;" ></td>
			  </tr>
<?
			}
?>
			  <tr>
			    <td align="right"><?=__('Total')?> (<span id="divCobroUnidadTotal" style='font-size:10px'><?=$moneda_cobro->fields['simbolo']?></span>):</td>
			    <td align="left"><input type="text" id="cobro_total" value="<?=number_format(round($cobro->fields['monto'],2),$moneda_cobro->fields['cifras_decimales'],'.','')?>" size="12" readonly="readonly" style="text-align: right;" <?=TTip($tip_total)?>></td>
			  </tr>
			  </table>
			</td>
			<td align=center>

				<?
				$moneda_total = new Moneda($sesion);
				$moneda_total->Load($cobro->fields['opc_moneda_total']);
				$cobro_moneda_tipo_cambio = new CobroMoneda($sesion);
				$cobro_moneda_tipo_cambio->Load($id_cobro);
				$tipo_cambio_moneda_cobro = $cobro_moneda_tipo_cambio->moneda[$cobro->fields['id_moneda']]['tipo_cambio'];
				$tipo_cambio_moneda_total = $cobro_moneda_tipo_cambio->moneda[$cobro->fields['opc_moneda_total']]['tipo_cambio'];
				$cifras_decimales_moneda_cobro = $cobro_moneda_tipo_cambio->moneda[$cobro->fields['id_moneda']]['cifras_decimales'];
				$cifras_decimales_moneda_total = $cobro_moneda_tipo_cambio->moneda[$cobro->fields['opc_moneda_total']]['cifras_decimales'];
				?>

			<? if( UtilesApp::GetConf($sesion,'UsarImpuestoPorGastos') && !empty($cobro->fields['incluye_gastos']))
			{ ?>
			<table cellspacing="1" cellpadding="2" style='border:1px dotted #bfbfcf'>
				<tr>
					<td colspan="2" bgcolor="#dfdfdf">
						<span style="font-weight: bold; font-size: 11px;"><?=__('Gastos')?></span>
					</td>
				</tr>
				<tr>
					<td align="right" width="45%" nowrap>
							<?=__('Subtotal Gastos c/IVA')?> (<span id="divCobroUnidadGastos" style='font-size:10px'><?=$moneda_cobro->fields['simbolo']?></span>):
					</td>
					<td align="left" width="55%" nowrap>
						<input type="text" id="subtotal_gastos_con" value="<?=$x_resultados['gastos']['subtotal_gastos_con_impuestos'][$moneda_cobro->fields['id_moneda']]?>" size="12" readonly="readonly" style="text-align: right;" />
					</td>
				</tr>
				<tr>
					<td align="right" width="45%" nowrap>
							<?=__('Subtotal Gastos s/IVA')?> (<span id="divCobroUnidadGastos" style='font-size:10px'><?=$moneda_cobro->fields['simbolo']?></span>):
					</td>
					<td align="left" width="55%" nowrap>
						<input type="text" id="subtotal_gastos_sin" value="<?=$x_resultados['gastos']['subtotal_gastos_sin_impuestos'][$moneda_cobro->fields['id_moneda']]?>" size="12" readonly="readonly" style="text-align: right;" />
					</td>
				</tr>
				<tr>
					<td align="right" width="45%" nowrap>
							<?=__('Impuestos Gastos')?> (<span id="divCobroUnidadGastos" style='font-size:10px'><?=$moneda_cobro->fields['simbolo']?></span>):
					</td>
					<td align="left" width="55%" nowrap>
						<input type="text" id="impuestos_gastos" value="<?=$x_resultados['gastos']['gasto_impuesto'][$moneda_cobro->fields['id_moneda']]?>" size="12" readonly="readonly" style="text-align: right;" />
					</td>
				</tr>
				<tr>
					<td align="right" width="45%" nowrap>
							<?=__('Total Gastos')?> (<span id="divCobroUnidadGastos" style='font-size:10px'><?=$moneda_cobro->fields['simbolo']?></span>):
					</td>
					<td align="left" width="55%" nowrap>
						<input type="text" id="total_gastos" value="<?=$x_resultados['gastos']['gasto_total_con_impuesto'][$moneda_cobro->fields['id_moneda']]?>" size="12" readonly="readonly" style="text-align: right;" />
					</td>
				</tr>
			</table>
			<br />
			<? } ?>

			<!--Agregar un resumen en moneda total para mejor indicacion de esta,
					Versiones anteriores quedan comentado por sia caso que volvemos a estas mas tarde-->
			<table cellspacing="0" cellpadding="3" style='border:1px dotted #bfbfcf'>
				<tr>
					<td colspan="2" bgcolor="#dfdfdf">
						<span style="font-weight: bold; font-size: 11px;"><?=__('Resumen total')?></span>
					</td>
				</tr>
				<tr>
					<td align="right" width="45%" nowrap>
						 <span style='font-size:10px;float:left'><?=__('Total Honorarios ').(UtilesApp::GetConf($sesion,'UsarImpuestoSeparado')?'<br/>('.__('con impuestos').')':'')?></span> (<span id="divCobroUnidadHonorariosTotal" style='font-size:10px'><?=$moneda_total->fields['simbolo']?></span>):
					</td>
					<td align="left" width="55%" nowrap>
						<input type="text" id="total_honorarios" value="<?=$x_resultados['monto'][$cobro->fields['opc_moneda_total']]-$x_resultados['descuento'][$cobro->fields['opc_moneda_total']]?>" size="12" readonly="readonly" style="text-align: right;">
					</td>
				</tr>
				<tr>
					<td align="right" width="45%" nowrap>
						<span style='font-size:10px;float:left'><?=__('Total Gastos ').(UtilesApp::GetConf($sesion,'UsarImpuestoPorGastos')?'<br/>('.__('con impuestos').')':'')?></span> (<span id="divCobroUnidadGastosTotal" style='font-size:10px'><?=$moneda_total->fields['simbolo']?></span>):
					</td>
					<td align="left" width="55%" nowrap>
						<input type="text" id="total_gastos" value="<?=$x_resultados['monto_gastos'][$cobro->fields['opc_moneda_total']]?>" size="12" readonly="readonly" style="text-align: right;">
					</td>
				</tr>
				<tr>
					<td align="right" width="45%" nowrap>
						<span style='font-size:10px'><?=__('Total')?></span> (<span id="divCobroUnidadGastosTotal" style='font-size:10px'><?=$moneda_total->fields['simbolo']?></span>):
					</td>
					<td align="left" width="55%" nowrap>
						<input type="text" id="total" value="<?= number_format($x_resultados['monto_gastos'][$cobro->fields['opc_moneda_total']]+$x_resultados['monto'][$cobro->fields['opc_moneda_total']]-$x_resultados['descuento'][$cobro->fields['opc_moneda_total']],$moneda_cobro->fields['cifras_decimales'],'.','')?>" size="12" readonly="readonly" style="text-align: right;">
					</td>
				</tr>
			</table>
			<br>
			<table cellspacing="0" cellpadding="3" style='border:1px dotted #bfbfcf'>
				<tr>
					<td bgcolor="#dfdfdf">
						<span style="font-weight: bold; font-size: 11px;"><?=__('Se esta cobrando:')?></span>
					</td>
				</tr>
				<tr>
					<td>
						<?php
						$se_esta_cobrando = __('Periodo');
						$se_esta_cobrando .=': ';
						if($cobro->fields['fecha_ini'] != '0000-00-00')
						{
								$se_esta_cobrando_fecha_ini = Utiles::sql2date($cobro->fields['fecha_ini']);
								$se_esta_cobrando .=__('Desde').': '.$se_esta_cobrando_fecha_ini;
						}
						if($cobro->fields['fecha_fin'] != '0000-00-00')
						{
								$se_esta_cobrando_fecha_fin = Utiles::sql2date($cobro->fields['fecha_fin']);
								$se_esta_cobrando .=__('Hasta').': '.$se_esta_cobrando_fecha_fin;
						}

						if($cobro->fields['se_esta_cobrando'])
							$se_esta_cobrando = $cobro->fields['se_esta_cobrando'];
						?>
						<textarea name="se_esta_cobrando" id="se_esta_cobrando"><?php echo $se_esta_cobrando;?></textarea>
					</td>
				</tr>
			</table>
    </td>
 <? /*
    <td align="center">
    	<table width="270" border="0" cellspacing="0" cellpadding="3" style="border: 1px dotted #bfbfcf;" align=center>
				<?
		$moneda_total = new Moneda($sesion);
		$moneda_total->Load($cobro->fields['opc_moneda_total']);
		$cobro_moneda_tipo_cambio = new CobroMoneda($sesion);
		$cobro_moneda_tipo_cambio->Load($id_cobro);

		$monto_honorario=(round($cobro->CalculaMontoTrabajos( $cobro->fields['id_cobro'] ),2)*round($cobro_moneda_tipo_cambio->moneda[$moneda_cobro->fields['id_moneda']]['tipo_cambio'],2))/round($cobro_moneda_tipo_cambio->moneda[$moneda_total->fields['id_moneda']]['tipo_cambio'],2);

			if( $cobro->fields['solo_gastos'] == 0 )
				{ ?>
				<tr>
					<td>
						Resumen Trabajos:
					</td>
				</tr>

				<tr>
					<td align="right">
						<?=$moneda_total->fields['simbolo']?>&nbsp;<?=/*$documento_cobro->fields['honorarios']* /round($monto_honorario,$moneda_total->fields['cifras_decimales']) ?>
					</td>
				</tr>
				<tr>
    			<td align="left">
    				Resumen Tr�mites:
    			</td>
    		</tr>
    		<tr>
    			<td align="right">
    				<?= $moneda_total->fields['simbolo']?>&nbsp;<?=round(round($cobro->CalculaMontoTramites( $cobro->fields['id_cobro'] ))/round($cobro_moneda_tipo_cambio->moneda[$moneda_total->fields['id_moneda']]['tipo_cambio'],2),2) ?>
    			</td>
    		</tr>
				<?
			}
			?>
    		<tr>
    			<td align="left">
    				Resumen Gastos:
    			</td>
    		</tr>
    		<tr>
    			<td align="right">
    				<?= $moneda_total->fields['simbolo']?>&nbsp;<?=round(round($cobro->CalculaMontoGastos( $cobro->fields['id_cobro'] ))/round($cobro_moneda_tipo_cambio->moneda[$moneda_total->fields['id_moneda']]['tipo_cambio'],2),2) ?>
    			</td>
    		</tr>
    	</table>
    </td> */ ?>
    <td align="center">
			<!-- OPCIONES IMPRESION -->
			<table width="270" border="0" cellspacing="0" cellpadding="3" style="border: 1px dotted #bfbfcf;" align=right>
			    <tr>
						<td align="left" bgcolor="#dfdfdf" style="font-size: 11px; font-weight: bold; vertical-align: middle;">
							<img src="<?=Conf::ImgDir()?>/imprimir_16.gif" border="0" alt="Imprimir"/> <?=__('Versi&oacute;n para imprimir')?>
						</td>
						<td align="right" bgcolor="#dfdfdf" style="vertical-align: middle;">
							<a href="javascript:void(0);" style="color: #990000; font-size: 9px; font-weight: normal;" onclick="ToggleDiv('doc_opciones');"><?=__('opciones')?></a>
						</td>
			    </tr>
			    <tr>
						<td align="center" colspan="2">
							<div id="doc_opciones" style="display: none; position: relative;">
							<table border="0" cellspacing="0" cellpadding="2" style="font-size: 10px;">
								<tr>
									<td colspan="3">&nbsp;</td>
								</tr>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_asuntos_separados" id="opc_ver_asuntos_separados" value="1" <?=$cobro->fields['opc_ver_asuntos_separados']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_asuntos_separados"><?=__('Ver asuntos por separado')?></label></td>
								</tr>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_resumen_cobro" id="opc_ver_resumen_cobro" value="1" <?=$cobro->fields['opc_ver_resumen_cobro']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_resumen_cobro"><?=__('Mostrar resumen del cobro')?></label></td>
								</tr>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_modalidad" id="opc_ver_modalidad" value="1" <?=$cobro->fields['opc_ver_modalidad']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_modalidad"><?=__('Mostrar modalidad del cobro')?></label></td>
								</tr>
								<?
									if( $cobro->fields['opc_ver_profesional'] )
										$display_detalle_profesional = "style='display: table-row;'";
									else
										$display_detalle_profesional = "style='display: none;'";

									if( $cobro->fields['opc_ver_detalles_por_hora'] )
										$display_detalle_por_hora = "style='display: table-row;'";
									else
										$display_detalle_por_hora = "style='display: none;'";
								?>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_profesional" id="opc_ver_profesional" value="1" <?=$cobro->fields['opc_ver_profesional']=='1'?'checked':''?> onchange="showOpcionDetalle( this.id, 'tr_detalle_profesional');"></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_profesional"><?=__('Mostrar detalle por profesional')?></label></td>
								</tr>
								<tr id="tr_detalle_profesional" <?=$display_detalle_profesional ?> >
									<td/>
									<td align="left" colspan="2" style="font-size: 10px;">
										<table width="100%">
											<tr>
												<td width="40%" align="left">
													<input type="checkbox" name="opc_ver_profesional_iniciales" id="opc_ver_profesional_iniciales" value="1" <?=$cobro->fields['opc_ver_profesional_iniciales']=='1'?'checked':''?>>
													<label for="opc_ver_profesional_iniciales"><?=__('Iniciales')?></label>
												</td>
												<td width="60%" align="left">
													<input type="checkbox" name="opc_ver_profesional_categoria" id="opc_ver_profesional_categoria" value="1" <?=$cobro->fields['opc_ver_profesional_categoria']=='1'?'checked':''?>>
													<label for="opc_ver_profesional_categoria"><?=__('Categor�a')?></label>
												</td>
											</tr>
											<tr>
												<td width="40%" align="left">
													<input type="checkbox" name="opc_ver_profesional_tarifa" id="opc_ver_profesional_tarifa" value="1" <?=$cobro->fields['opc_ver_profesional_tarifa']=='1'?'checked':''?>>
													<label for="opc_ver_profesional_tarifa"><?=__('Tarifa')?></label>
												</td>
												<td width="60%" align="left">
													<input type="checkbox" name="opc_ver_profesional_importe" id="opc_ver_profesional_importe" value="1" <?=$cobro->fields['opc_ver_profesional_importe']=='1'?'checked':''?>>
													<label for="opc_ver_profesional_importe"><?=__('Importe')?></label>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td align="right">
										<input type="checkbox" name="opc_ver_detalles_por_hora" id="opc_ver_detalles_por_hora" value="1" <?=$cobro->fields['opc_ver_detalles_por_hora']=='1'?'checked':''?> onchange="showOpcionDetalle( this.id, 'tr_detalle_por_hora');">
									</td>
									<td align="left" colspan="2" style="font-size: 10px;">
										<label for="opc_ver_detalles_por_hora"><?=__('Mostrar detalle por hora')?></label>
									</td>
								</tr>
								<tr id="tr_detalle_por_hora" <?=$display_detalle_por_hora ?> >
									<td/>
									<td align="left" colspan="2" style="font-size: 10px;">
										<table width="100%">
											<tr>
												<td width="40%" align="left">
													<input type="checkbox" name="opc_ver_detalles_por_hora_iniciales" id="opc_ver_detalles_por_hora_iniciales" value="1" <?=$cobro->fields['opc_ver_detalles_por_hora_iniciales']=='1'?'checked':''?>>
													<label for="opc_ver_detalles_por_hora_iniciales"><?=__('Iniciales')?></label>
												</td>
												<td width="60%" align="left">
													<input type="checkbox" name="opc_ver_detalles_por_hora_categoria" id="opc_ver_detalles_por_hora_categoria" value="1" <?=$cobro->fields['opc_ver_detalles_por_hora_categoria']=='1'?'checked':''?>>
													<label for="opc_ver_detalles_por_hora_categoria"><?=__('Categor�a')?></label>
												</td>
											</tr>
											<tr>
												<td width="40%" align="left">
													<input type="checkbox" name="opc_ver_detalles_por_hora_tarifa" id="opc_ver_detalles_por_hora_tarifa" value="1" <?=$cobro->fields['opc_ver_detalles_por_hora_tarifa']=='1'?'checked':''?>>
													<label for="opc_ver_detalles_por_hora_tarifa"><?=__('Tarifa')?></label>
												</td>
												<td width="60%" align="left">
													<input type="checkbox" name="opc_ver_detalles_por_hora_importe" id="opc_ver_detalles_por_hora_importe" value="1" <?=$cobro->fields['opc_ver_detalles_por_hora_importe']=='1'?'checked':''?>>
													<label for="opc_ver_detalles_por_hora_importe"><?=__('Importe')?></label>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_gastos" id="opc_ver_gastos" value="1" <?=$cobro->fields['opc_ver_gastos']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_gastos"><?=__('Mostrar gastos del cobro')?></label></td>
								</tr>
                                                                <?php if( UtilesApp::GetConf($sesion,'PrmGastos') ) { ?>
                                                                <tr>
									<td align="right"><input type="checkbox" name="opc_ver_concepto_gastos" id="opc_ver_concepto_gastos" value="1" <?=$cobro->fields['opc_ver_concepto_gastos']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_concepto_gastos"><?=__('Mostrar concepto de gastos')?></label></td>
								</tr>
                                                                <?php } ?>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_morosidad" id="opc_ver_morosidad" value="1" <?=$cobro->fields['opc_ver_morosidad']=='1'?'checked':''?>></td>
									<td align="left" colspan="2"style="font-size: 10px;"><label for="opc_ver_morosidad"><?=__('Mostrar saldo adeudado')?></label></td>
								</tr>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_tipo_cambio" id="opc_ver_tipo_cambio" value="1" <?=$cobro->fields['opc_ver_tipo_cambio']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_tipo_cambio"><?=__('Mostrar tipos de cambio')?></label></td>
								</tr>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_descuento" value="1" <?=$cobro->fields['opc_ver_descuento']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><?=__('Mostrar el descuento del cobro')?></td>
								</tr>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_numpag" id="opc_ver_numpag" value="1" <?=$cobro->fields['opc_ver_numpag']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_numpag"><?=__('Mostrar n�meros de p�gina')?></label></td>
								</tr>
<?
				if(method_exists('Conf','GetConf'))
					$solicitante = Conf::GetConf($sesion, 'OrdenadoPor');
				elseif(method_exists('Conf','Ordenado_por'))
					$solicitante = Conf::Ordenado_por();
				else
					$solicitante = 2;

				if($solicitante == 0)		// no mostrar
				{
?>
					<input type="hidden" name="opc_ver_solicitante" id="opc_ver_solicitante" value="0" />
<?
				}
				elseif($solicitante == 1)	// obligatorio
				{
?>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_solicitante" id="opc_ver_solicitante" value="1" <?=$cobro->fields['opc_ver_solicitante']=='1'?'checked="checked"':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_solicitante"><?=__('Mostrar solicitante')?></label></td>
								</tr>
								<tr>
<?
				}
				elseif ($solicitante == 2)	// opcional
				{
?>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_solicitante" id="opc_ver_solicitante" value="1" <?=$cobro->fields['opc_ver_solicitante']=='1'?'checked="checked"':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_solicitante"><?=__('Mostrar solicitante')?></label></td>
								</tr>
								<tr>
<?
				}
?>
									<td align="right"><input type="checkbox" name="opc_ver_horas_trabajadas" id="opc_ver_horas_trabajadas" value="1" <?=$cobro->fields['opc_ver_horas_trabajadas']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_horas_trabajadas"><?=__('Mostrar horas trabajadas')?></label></td>
								</tr>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_cobrable" id="opc_ver_cobrable" value="1" <?=$cobro->fields['opc_ver_cobrable']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_cobrable"><?=__('Mostrar trabajos no visibles')?></label></td>
								</tr>
						<? if( ( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'ResumenProfesionalVial') ) || ( method_exists('Conf','ResumenProfesionalVial') && Conf::ResumenProfesionalVial() ) )
								{ ?>
								<tr>
									<td align="right"><input type="checkbox" name="opc_restar_retainer" id="opc_restar_retainer" value="1" <?=$cobro->fields['opc_restar_retainer']=='1'?'checked="checked"':''?> ></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_restar_retainer"><?=__('Restar valor retainer')?></td>
								</tr>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_detalle_retainer" id="opc_ver_detalle_retainer" value="1" <?=$cobro->fields['opc_ver_detalle_retainer']=='1'?'checked="checked"':''?> ></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_detalle_retainer"><?=__('Mostrar detalle retainer')?></td>
								</tr>
					<?		}  ?>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_valor_hh_flat_fee" id="opc_ver_valor_hh_flat_fee" value="1" <?=$cobro->fields['opc_ver_valor_hh_flat_fee']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_valor_hh_flat_fee"><?=__('Mostrar tarifa proporcional en base a HH')?></label></td>
								</tr>
								<tr>
									<td align="right"><input type="checkbox" name="opc_ver_carta" id="opc_ver_carta" value="1" onclick="ActivaCarta(this.checked)" <?=$cobro->fields['opc_ver_carta']=='1'?'checked':''?>></td>
									<td align="left" colspan="2" style="font-size: 10px;"><label for="opc_ver_carta"><?=__('Mostrar Carta')?></label></td>
								</tr>
								<tr>
									<td style="font-size: 10px;" colspan="3">
										<?=__('Formato de carta')?>:
									</td>
								</tr>
								<tr>
									<td align="left" colspan="3">
										<?= Html::SelectQuery($sesion, "SELECT carta.id_carta, carta.descripcion
																				FROM carta ORDER BY id_carta","id_carta",
																$cobro->fields['id_carta'] ? $cobro->fields['id_carta'] : $contrato->fields['id_carta'], ($cobro->fields['opc_ver_carta']=='1'?'':'disabled') . ' class="wide"','',150); ?>
									</td>
								</tr>
								<tr>
									<td style="font-size: 10px;"  colspan="3">
										<?=__('Formato Detalle Carta Cobro')?>:
									</td>
								</tr>
								<tr>
									<td align="left" colspan="3">
										<?= Html::SelectQuery($sesion, "SELECT cobro_rtf.id_formato, cobro_rtf.descripcion
																FROM cobro_rtf ORDER BY cobro_rtf.id_formato","id_formato",
												$cobro->fields['id_formato'] ? $cobro->fields['id_formato'] : $contrato->fields['id_formato'], 'class="wide"','Seleccione',150); ?>
									</td>
								</tr>
								<tr>
									<td align="left" style="font-size: 10px;" colspan="3">
										<?=__('Tama�o del papel')?>:
									</td>
								</tr>
								<tr>
									<td align="left" colspan="3">
<?php
if ($cobro->fields['opc_papel'] == '' && UtilesApp::GetConf($sesion, 'PapelPorDefecto')) {
	$cobro->fields['opc_papel'] = UtilesApp::GetConf($sesion, 'PapelPorDefecto');
}
?>
										<select name="opc_papel">
											<option value="LETTER" <?php echo $cobro->fields['opc_papel'] == 'LETTER' ? 'selected="selected"' : '' ?>><?php echo __('Carta'); ?></option>
											<option value="LEGAL" <?php echo $cobro->fields['opc_papel'] == 'LEGAL' ? 'selected="selected"' : '' ?>><?php echo __('Oficio'); ?></option>
											<option value="A4" <?php echo $cobro->fields['opc_papel'] == 'A4' ? 'selected="selected"' : '' ?>><?php echo __('A4'); ?></option>
											<option value="A5" <?php echo $cobro->fields['opc_papel'] == 'A5' ? 'selected="selected"' : '' ?>><?php echo __('A5'); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2">&nbsp;</td>
								</tr>
							</table>
							</div>
						</td>
			    </tr>
				<tr>
					<td align="center" colspan="2">
						<table width="180">
							<tr>
								<td width="50%" style="font-size: 10px;" nowrap>
									<?=__('Mostrar total en')?>:
								</td>
								<td width="50%">
									<?=Html::SelectQuery($sesion,"SELECT id_moneda, glosa_moneda FROM prm_moneda ORDER BY id_moneda", 'opc_moneda_total',$cobro->fields['opc_moneda_total'],'onchange="ActualizarSaldoAdelantos();"','','70');?>
								</td>
							</tr>
							<tr>
								<td style="font-size: 10px;" nowrap>
									<?=__('Idioma')?>:
								</td>
								<td>
									<?=Html::SelectQuery($sesion,"SELECT codigo_idioma,glosa_idioma FROM prm_idioma ORDER BY glosa_idioma","lang",$cobro->fields['codigo_idioma'] != '' ? $cobro->fields['codigo_idioma'] : $contrato->fields['codigo_idioma'] ,'','',70);?>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<a href="" style="font-size: 10px;" onclick="SubirExcel();">Subir excel modificado</a>
								</td>
							</tr>
							<tr>
								<td colspan="2">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="2" align="center">
									<input type="button" class="btn" value="<?=__('Descargar Archivo')?>" onclick="ImprimirCobro(this.form);" />
								</td>
							</tr>
							<?php
								if( method_exists('Conf','GetConf') && Conf::GetConf($sesion,'MostrarBotonCobroPDF') )
								{
							?>
							<tr>
								<td colspan="2" align="center">
									<input type="button" class="btn" value="<?=__('Descargar Archivo')?> PDF" onclick="return ImprimirCobroPDF(this.form);" />
								</td>
							</tr>
							<?php
								}
							?>
							<tr>
								<td colspan="2" align="center">
									<input type="button" class="btn" value="<?=__('descargar_excel_modificable')?>" onclick="ImprimirExcel(this.form);" />
								</td>
							</tr>
							<?
								if( UtilesApp::GetConf($sesion, 'XLSFormatoEspecial' ) != '' && UtilesApp::GetConf($sesion, 'XLSFormatoEspecial' ) != 'cobros_xls.php' )
								{ ?>
							<tr>
								<td colspan="2" align="center">
									<input type="button" class="btn" value="<?=__('Descargar Excel Cobro')?>" onclick="ImprimirExcel(this.form, 'especial');" />
								</td>
							</tr>
						<? } ?>
						</table>
					</td>
				</tr>
			</table>
			<!-- FIN   OPCIONES IMPRESION -->
		</td>
	</tr>
</table>

<table width="100%">
	<tr>
		<td align='right'>
			<hr size='1px'>
		</td>
	</tr>
	<tr>
		<td align='center'>
			<input type='button' name='btno' value='<?=__('Guardar cobro')?>' onclick='GuardaCobro(this.form)' class='btn'>
		</td>
	</tr>
</table>
</form>
<br>
<iframe src="historial_cobro.php?id_cobro=<?=$id_cobro?>" width=600px height=450px style="border: none;" frameborder=0></iframe>
<script language="javascript" type="text/javascript">
window.onunload = ActualizarPadre;

var form = document.getElementById('form_cobro5');

if( form )
{
	if( form.cobro_forma_cobro[0].checked ) {
		HideMonto();
	} else if( form.cobro_forma_cobro[1].checked ) {
		ShowMonto(true);
	} else if( form.cobro_forma_cobro[2].checked ) {
		ShowMonto(false);
	} else if( form.cobro_forma_cobro[3].checked ) {
		ShowMonto(false);
	} else if( form.cobro_forma_cobro[4].checked ) {
		ShowMonto(true);
	/*else if( form.cobro_forma_cobro[5].checked )
		HideMonto();*/
	} else if( form.cobro_forma_cobro[5].checked ) {
		HideMonto();
		DisplayEscalas(true);
	}
}

function ActivaCarta(check)
{
	if( !form )
		var form = $('form_cobro5');

	if(check)
		form.id_carta.disabled = false;
	else
		form.id_carta.disabled = true;
}
</script>
<?
	$pagina->PrintBottom($popup);
?>
