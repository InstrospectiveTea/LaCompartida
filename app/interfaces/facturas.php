<?php
require_once dirname(__FILE__) . '/../conf.php';
require_once Conf::ServerDir() . '/classes/Reportes/SimpleReport.php';

$sesion = new Sesion(array('COB'));
$pagina = new Pagina($sesion);

$serienumero_documento = new DocumentoLegalNumero($sesion);

$factura = new Factura($sesion);

if ($id_factura != "") {
	$factura->Load($id_factura);
}

if ($opc == 'generar_factura') {
	// POR HACER
	// mejorar
	if ($id_factura_grabada) {
		include dirname(__FILE__) . '/factura_doc.php';
		die();
	} else {
		die("Error");
	}
	die('Listo');
} else if ($opc == 'generar_factura_pdf') {
	if ($id_factura_grabada) {
		$factura_pdf_datos = new FacturaPdfDatos($sesion);
		$factura_pdf_datos->generarFacturaPDF($id_factura_grabada);
		die();
	} else {
		die(__('Factura no existe!'));
	}
}

if ($exportar_excel) {
	// Es necesaria esta bestialidad para que no se caiga cuando es llamada desde otro lado.
//		$no_activo = !$activo;
//		$multiple = true;
//		require_once Conf::ServerDir().'/interfaces/facturas_listado_xls.php';
//		exit;

}



if ($archivo_contabilidad) {
	require_once Conf::ServerDir() . '/interfaces/facturas_contabilidad_txt.php';
	exit;
}


$idioma_default = new Objeto($sesion, '', '', 'prm_idioma', 'codigo_idioma');
$idioma_default->Load(strtolower(UtilesApp::GetConf($sesion, 'Idioma')));

global $factura;
($Slim=Slim::getInstance()) ?  $Slim->applyHook('hook_factura_inicio'):false;

if ($opc == 'buscar' || $opc == 'generar_factura') {

	$results = $factura->DatosReporte($orden, $where, $numero, $fecha1, $fecha2
		,$tipo_documento_legal_buscado
		, $codigo_cliente,$codigo_cliente_secundario
		, $codigo_asunto,$codigo_asunto_secundario
		, $id_contrato, $id_cia,
		$id_cobro, $id_estado, $id_moneda, $grupo_ventas, $razon_social, $descripcion_factura, $serie, $desde_asiento_contable );

	if ($exportar_excel) {

		$factura->DownloadExcel($results);
	die();
	}
}

$pagina->titulo = __('Revisar Documentos Tributarios');
$pagina->PrintTop();

?>
<script type="text/javascript">
	function CrearNuevoDocumentoLegal()
	{
		var dl_url = 'agregar_factura.php?popup=1&id_documento_legal='+$('tipo_documento_legal').value;
		if($('codigo_cliente')){
			dl_url += '&codigo_cliente='+$('codigo_cliente').value
		}
		if($('id_cobro')){
			dl_url += '&id_cobro='+$('id_cobro').value
			$('id_cobro').focus();
		}
		nuovaFinestra('Agregar_Factura',730,580,dl_url, 'top=100, left=155');')	';
	}




	function ImprimirDocumento( id_factura )
	{
		var fecha1=$('fecha1').value;
		var fecha2=$('fecha2').value;
		var vurl = 'facturas.php?opc=generar_factura&id_factura_grabada=' + id_factura + '&fecha1=' + fecha1 + '&fecha2=' + fecha2;

		self.location.href=vurl;
	}

	function ImprimirPDF( id_factura )
	{
		var vurl = 'facturas.php?opc=generar_factura_pdf&id_factura_grabada=' + id_factura;
		self.location.href=vurl;
	}

	function Refrescar()
	{
		BuscarFacturas('','buscar');
	}

	function BuscarFacturas( form, from )
	{
		if (!form) {
			var form = $('form_facturas');
		}
		switch (from) {
			case 'buscar':
				form.action = 'facturas.php?buscar=1';
				break;

			case 'exportar_excel':
				form.action = 'facturas.php?opc=buscar&exportar_excel=1';
				break;

<?php if (UtilesApp::GetConf($sesion, 'DescargarArchivoContabilidad')) { ?>
			case 'archivo_contabilidad':
				form.action = 'facturas.php?archivo_contabilidad=1';
				break;
<?php } ?>
			default:
				return false;
			}

			form.submit();
			return true;
		}

		function AgregarNuevo()
		{
			var urlo = "agregar_factura.php?popup=1";
			nuovaFinestra('Agregar_Factura',730,470,urlo,'top=100, left=125');
		}
</script>

 <form method='post' name="form_facturas" id="form_facturas">
	<input type='hidden' name='opc' id='opc' value='buscar'>

	<?php
		echo "<table width=\"90%\"><tr><td>";
		$class_diseno = 'class="tb_base" style="width: 100%; border: 1px solid #BDBDBD;"';

	?>
	<fieldset class="tb_base" style="width: 100%; border: 1px solid #BDBDBD;">
		<legend><?php echo __('Filtros') ?></legend>
		<table style="border: 0px solid black" width='720px'>
			<tr>
				<td align=right width="20%">
					<?php echo __('Cliente') ?>
				</td>
				<td colspan="3" align=left nowrap>
<?php UtilesApp::CampoCliente($sesion, $codigo_cliente, $codigo_cliente_secundario, $codigo_asunto, $codigo_asunto_secundario); ?>
				</td>
			</tr>
			<tr>
				<td align='right' width="20%">
					<?php echo __('Asunto') ?>
				</td>
				<td colspan="3" align=left nowrap>
<?php UtilesApp::CampoAsunto($sesion, $codigo_cliente, $codigo_cliente_secundario, $codigo_asunto, $codigo_asunto_secundario); ?>
				</td>
			</tr>
			</tr>
				<?php ($Slim=Slim::getInstance()) ?  $Slim->applyHook('hook_filtros_facturas'):false; ?>

			<tr>
			<tr>
				<td align=right>
					<?php echo __('Raz�n Social') ?>
				</td>
				<td align=left colspan="3" >
					<input type="text" name="razon_social" id="razon_social" value="<?php echo $razon_social; ?>" size="72">
				</td>
			</tr>
			<tr>
				<td align=right>
					<?php echo __('Descripci�n') ?>
				</td>
				<td align=left colspan="3" >
					<input type="text" name="descripcion_factura" id="descripcion_factura" value="<?php echo $descripcion_factura; ?>" size="72">
				</td>
			</tr>
			<tr>
				<td align=right>
					<?php echo __('Tipo de Documento') ?>
				</td>
				<td align=left >
<?php echo Html::SelectQuery($sesion, "SELECT id_documento_legal, glosa FROM prm_documento_legal", 'tipo_documento_legal_buscado', $tipo_documento_legal_buscado, '', 'Cualquiera', 150); ?>
				</td>
				<td align=right>
<?php echo __('Grupo Ventas') ?>
					<input type=checkbox name=grupo_ventas id=grupo_ventas value=1 <?php echo $grupo_ventas ? 'checked' : '' ?>>
				</td>
			</tr>
			<tr>
				<td align=right>
					<?php echo __('Estado') ?>
				</td>
				<td align=left>
					<?php echo Html::SelectQuery($sesion, "SELECT id_estado, glosa FROM prm_estado_factura ORDER BY id_estado ASC", "id_estado", $id_estado, 'onchange="mostrarAccionesEstado(this.form)"', 'Cualquiera', "150"); ?>
				</td>
			</tr>
			<tr>
				<td align=right>
					<?php echo __('Moneda') ?>
				</td>
				<td align=left>
					<?php echo Html::SelectQuery($sesion, "SELECT id_moneda, glosa_moneda FROM prm_moneda ORDER BY glosa_moneda ASC", "id_moneda", $id_moneda, '', 'Cualquiera', "150"); ?>
				</td>
			</tr>
			<tr>
				<td align=right>
					<?php echo __('N� Factura') ?>
				</td>
				<td align=left width="18%" nowrap>
					<?php if (UtilesApp::GetConf($sesion, 'NumeroFacturaConSerie')) { ?>
						<?php echo Html::SelectQuery($sesion, $serienumero_documento->SeriesQuery(), "serie", $serie, 'onchange="NumeroDocumentoLegal()"', "Vacio", 60); ?>
						<span style="vertical-align: center;">-</span>
<?php } ?>
					<input onkeydown="if(event.keyCode==13)BuscarFacturas(this.form,'buscar');" type="text" id="numero" name="numero" size="15" value="<?php echo $numero ?>" onchange="this.value=this.value.toUpperCase();">
				</td>
				<td align=right width="18%">
<?php echo __('N� Cobro') ?>
				</td>
				<td align=left width="44%">
					<input onkeydown="if(event.keyCode==13)BuscarFacturas(this.form,'buscar');" type="text" id="id_cobro" name="id_cobro" size="15" value="<?php echo $id_cobro ?>">
				</td>
			</tr>
<?php
if (method_exists('Conf', 'dbUser') && Conf::dbUser() == "rebaza") {
	?>
				<tr>
					<td align=right>
	<?php echo __('Compan�a') ?>
					</td>
					<td align=left width="18%">
						<select name="id_cia" id="id_cia" value="<?php echo $id_cia ?>">
							<option value="">Todos</option>
							<option value="1" <?php echo $id_cia == 1 ? 'selected' : '' ?>>Rebaza Alcazar</option>
							<option value="2" <?php echo $id_cia == 2 ? 'selected' : '' ?>>Acerta</option>
						</select>
					</td>
				</tr>
	<?php
}
?>
			<tr>
				<td align=right>
<?php echo __('Fecha Inicio') ?>
				</td>
				<td nowrap align=left>
					<input type="text" id="fecha1" class="fechadiff"  name="fecha1" value="<?php echo $fecha1 ?>" id="fecha1" size="11" maxlength="10" />

				</td>
				<td align=right>
<?php echo __('Fecha Fin') ?>
				</td>
				<td align=left width="44%">
					<input type="text" id="fecha2" class="fechadiff" name="fecha2" value="<?php echo $fecha2 ?>" id="fecha2" size="11" maxlength="10" />

				</td>
			</tr>
			<tr id="fila_botones">
				<td colspan="4" style="text-align:center;margin:auto;">
					<a name='boton_buscar' id='boton_buscar'  class="btn botonizame" icon="find"   onclick="BuscarFacturas(jQuery('#form_facturas').get(0),'buscar')" class=btn><?php echo __('Buscar') ?></a>

					<a    class="btn botonizame" id="boton_descarga" icon="xls" name="boton_excel" onclick="BuscarFacturas(jQuery('#form_facturas').get(0), 'exportar_excel')"><?php echo __('Descargar Excel'); ?></a>
				<?php ($Slim=Slim::getInstance()) ?  $Slim->applyHook('hook_factura_fin'):false;



if (UtilesApp::GetConf($sesion, 'DescargarArchivoContabilidad')) { ?>
						<input type="button" value="<?php echo __('Descargar Archivo Contabilidad'); ?>" class="btn" name="boton_contabilidad" onclick="BuscarFacturas(this.form, 'archivo_contabilidad')" />
						<br />
						<label>desde el asiento contable
							<input type="text" size="4" name="desde_asiento_contable" value="<?php echo $desde_asiento_contable; ?>" /></label>
<?php }  ?>
				</td>
			</tr>
		</table>
	</fieldset> </td></tr></table>

</form>


<?php

if ($opc == 'buscar' || $opc == 'generar_factura') {


	$monto_saldo_total = 0;
	$glosa_monto_saldo_total = '';
	$where_moneda = ' WHERE moneda_base = 1';
	if ($id_moneda > 0) {
		$where_moneda = 'WHERE id_moneda = ' . $id_moneda;
	}
	$query_moneda = 'SELECT id_moneda, simbolo, cifras_decimales, moneda_base, tipo_cambio FROM prm_moneda	' . $where_moneda . ' ORDER BY id_moneda ';
	$resp_moneda = mysql_query($query_moneda, $sesion->dbh) or Utiles::errorSQL($query_moneda, __FILE__, __LINE__, $sesion->dbh);
	$id_moneda_base = 0;

	while (list($id_moneda_tmp, $simbolo_moneda_tmp, $cifras_decimales_tmp, $moneda_base_tmp, $tipo_cambio_tmp) = mysql_fetch_array($resp_moneda)) {
		foreach ($results as $row) {
			$monto_saldo_total += UtilesApp::CambiarMoneda($row['saldo'], $row['tipo_cambio'], $row['cifras_decimales'], $tipo_cambio_tmp, $cifras_decimales_tmp);
		}
		$glosa_monto_saldo_total = '<b>' . __('Saldo') . ' ' . $simbolo_moneda_tmp . ' ' . number_format($monto_saldo_total, $cifras_decimales_tmp, $idioma_default->fields['separador_decimales'], $idioma_default->fields['separador_miles']) . "</b>";
	}
	// calcular el saldo en moneda base


 	$SimpleReport = new SimpleReport($sesion);
	$SimpleReport->SetRegionalFormat(UtilesApp::ObtenerFormatoIdioma($sesion));

	$config=$SimpleReport->LoadConfiguration('FACTURAS');


 $config->columns['numero']->title="Datos<br>Documento";
$config->columns['glosas_asunto']->title=__('Expedientes');
$config->columns['glosa_cliente']->title=__('Destinatario Documento');
$config->columns['factura_rsocial']->visible=false;
$config->columns['tipo']->visible=false;
$config->columns['serie_documento_legal']->visible=false;
$config->columns['codigo_cliente']->visible=false;
$config->columns['observaciones']->visible=false;
$config->columns['descripcion']->visible=false;
$config->columns['codigos_asunto']->visible=false;
$config->columns['estado']->class='cellcenter';
$config->columns['simbolo']->visible=false;
$config->columns['monto_real']->visible=false;
$config->columns['tipo_cambio']->visible=false;
$config->columns['subtotal_gastos']->visible=false;
$config->columns['subtotal_gastos_sin_impuesto']->visible=false;
$config->columns['honorarios']->visible=false;
$config->columns['iva']->visible=true;
$config->columns['codigo_contrato']->visible=false;
$config->columns['acciones']=(object) array('visible'=>true,"field"=>"acciones","title"=>"Acciones");


	$SimpleReport->LoadResults($results );

	$writer = SimpleReport_IOFactory::createWriter($SimpleReport, 'DataTable');
	echo '<div class="titulo_buscador">Documentos Tributarios <br>'.$glosa_monto_saldo_total .'</div>';


	echo $writer->save(null,null,$factura->FormatoDataTable());


}




$pagina->PrintBottom();
