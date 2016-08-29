<?php
	require_once dirname(__FILE__).'/../conf.php';

	$sesion = new Sesion(array('REP'));
	$pagina = new Pagina($sesion);
	$Form = new Form($sesion);
	$Html = new \TTB\Html;

  if($id_usuario == "")
		$id_usuario = $sesion->usuario->fields['id_usuario'];

	if (!$fecha1) {
		$fecha1 = date("d-m-Y", strtotime("- 1 month"));
	}
	if (!$fecha2) {
		$fecha2 = date("d-m-Y");
	}

	$pagina->titulo = __('Reporte Gr�fico Usuarios');
	$pagina->PrintTop();
?>

<form method="post" action="<?php echo $_SERVER[PHP_SELF]; ?>">
<table class="border_plomo tb_base">
	<tr>
		<td align="right">
			<?php echo __('Fecha desde');?>
		</td>
		<td align="left">
			<?php echo $Html::PrintCalendar('fecha1', $fecha1); ?>
		</td>
	</tr>
	<tr>
		<td align="right">
			<?php echo __('Fecha hasta'); ?>
		</td>
		<td align="left">
			<?php echo $Html::PrintCalendar('fecha2', $fecha2); ?>
		</td>
	</tr>
	<tr>
		<td align="right">
			<?php echo __('Usuario'); ?>
		</td>
		<td align="left"><!-- Nuevo Select -->
			<?php echo $Form->select('id_usuario', $sesion->usuario->ListarActivos('', 'PRO'), $id_usuario, array('empty' => FALSE, 'style' => 'width: 200px')); ?>
		</td>
	</tr>
	<tr>
		<td align="right">
			<?php echo __('Tipo de reporte'); ?>
		</td>
		<td align="left">
			<select id="tipo_reporte" name="tipo_reporte">
				<option <?php echo $tipo_reporte == "proyectos_trabajados" ? "selected" : ""; ?> value="proyectos_trabajados"><?php echo __('Asuntos trabajados'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="4" align="right">
			<input type="button" class="btn" id="genera_reporte" value="<?php echo __('Generar reporte'); ?>">
		</td>
	</tr>
</table>
</form>

<div id="contenedor_graficos"></div>

<?= $Form->Html->script(Conf::RootDir() . '/app/layers/assets/js/graphic.js'); ?>
<script type="text/javascript">
jQuery(function() {
	var graficoBarraUsuarios;

	jQuery("#genera_reporte").on("click", function() {
		var id_usuario = jQuery("#id_usuario").val();
		var nombre_usuario = jQuery("#id_usuario option:selected").text();
		var fecha1 = jQuery("#fecha1").val();
		var fecha2 = jQuery("#fecha2").val();

		var url = 'graficos/grafico_' + jQuery("#tipo_reporte").val() + '.php';
		var charts_data = [{
			'url': url,
			'data': {
				'id_usuario': id_usuario,
				'nombre_usuario': nombre_usuario,
				'fecha1': fecha1,
				'fecha2': fecha2
			}
		}];
		graphic.render('#contenedor_graficos', charts_data);
	});
});

function Habilitar(form)
{
	if(form.tipo_reporte.selectedIndex > 0)
		form.codigo_asunto.disabled = true;
	else
		form.codigo_asunto.disabled = false;
}
</script>

<?php
	echo(InputId::Javascript($sesion));
	$pagina->PrintBottom();
?>
