<?php
require_once dirname(__FILE__) . '/../app/conf.php';

$sesion = new Sesion(array('ADM'));

$pagina = new Pagina($sesion);
$pagina->titulo = __("Carga masiva de $clase");
$pagina->PrintTop();

$CargaMasiva = new CargaMasiva($sesion);

if (isset($data) && isset($campos)) {
	$CargaMasiva->CargarData($data, $clase, $campos);
}

$listados = $CargaMasiva->ObtenerListados($clase);

$campos_clase = $CargaMasiva->ObtenerCampos($clase);
$titulos_campos = array_map(function($campo) {
		return $campo['titulo'];
	}, $campos_clase);

if (isset($raw_data)) {
	$data = $CargaMasiva->ParsearData($raw_data);
	$campos = array_keys($campos_clase);
}

if (empty($data)) {
	$data[] = array_fill(0, count($campos), '');
} else if (count($campos) > count($data[0])) {
	$campos = array_slice($campos, 0, count($data[0]));
} else {
	while (count($campos) < count($data[0])) {
		$campos[] = '';
	}
}
?>
<style type="text/css">
	#data thead tr{
		height: 40px;
		background-color: #42A62B;
	}
	#data tbody tr:nth-child(odd){
		background-color: #eee;
	}
	.warning{
		background-color: #ffc !important;
	}
	.error{
		background-color: #fcc !important;
	}
</style>
<form method="POST" action="datos_carga_masiva.php">
	<table id="data">
		<thead>
			<tr>
				<?php foreach ($campos as $c => $campo) { ?>
					<th><?php echo Html::SelectArrayDecente($titulos_campos, "campos[$c]", $campo, '', '(ignorar)'); ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($data as $idx => $fila) { ?>
				<tr>
					<?php foreach ($fila as $c => $col) { ?>
						<td>
							<input name="<?php echo "data[$idx][$c]"; ?>" value="<?php echo $col; ?>"/>
							<span class="extra"></span>
						</td>
					<?php } ?>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="clase" value="<?php echo $clase; ?>"/>
	<input type="submit" value="Enviar"/>
</form>

<script type="text/javascript">
	var clase = '<?php echo $clase; ?>';
	var llave = '<?php echo $CargaMasiva->LlaveUnica($clase); ?>';
	var listados = <?php echo json_encode(UtilesApp::utf8izar($listados)); ?>;
	var campos_clase = <?php echo json_encode(UtilesApp::utf8izar($campos_clase)); ?>;

	function cambioRelacion() {
		jQuery(this).closest('td').find('[name^=data]')
				.val(jQuery(this).find(':selected').text()).change();
	}

	function cambioCheck() {
		jQuery(this).closest('td').find('[name^=data]')
				.val(jQuery(this).is(':checked') ? 'SI' : 'NO');
	}

	function limpiar(s) {
		return s.replace(/\s/g, '').toUpperCase();
	}

	//valida que el valor ingresado este dentro de las opciones existentes
	function validarRelacion(td) {
		var input = td.find('[name^=data]');
		var val = limpiar(input.val());
		var op = td.find('.extra option').filter(function() {
			//compara sin considerar mayusculas ni espacios
			return limpiar(jQuery(this).text()) === val;
		});

		if (op.length) {
			//todo ok: actualizo el selector y corrijo el valor del input para hacerlo identico al original
			op.closest('select').val(op.val());
			input.val(op.text()).removeAttr('title');
			input.removeClass('warning');
			input.removeClass('error');
		}
		else {
			//fail: no existe el dato, si es creable solo es warning, si no es un error
			var idx = td.index();
			var campo = jQuery('[name="campos[' + idx + ']"]').val();
			var creable = campos_clase[campo].creable;
			input.addClass(creable ? 'warning' : 'error')
					.attr('title', 'No existe este valor en el listado actual' +
					(creable ? '. Se crear� al cargar los datos' : ''));
			td.find('.extra select').val('');

			if (creable) {
				//homogeneizar espacios/mayusculas con otras filas existentes
				jQuery('[name^=data][name$="[' + idx + ']"]').filter(function() {
					return limpiar(jQuery(this).val()) === val;
				}).val(input.val());
			}
		}
	}

	//valida que no haya valores repetidos en una columna
	function validarRepetidos(idx) {
		var inputs = jQuery('[name^=data][name$="[' + idx + ']"]');
		inputs.filter('.error').removeClass('error').removeAttr('title');

		var valores = inputs.map(function() {
			return limpiar(jQuery(this).val());
		});
		var repetidos = inputs.filter(function(idx, input) {
			var val = valores.get(idx);
			return valores.is(function(idx2, val2) {
				return idx2 !== idx && val2 === val;
			});
		});

		repetidos.addClass('error').attr('title', 'Este valor debe ser �nico, pero est� repetido en esta carga');
	}

	function validarUnicidad(td) {
		var idx = td.index();
		var input = td.find('[name^=data]').removeAttr('title');
		var val = limpiar(input.val());

		validarRepetidos(idx);

		var existe = false;
		jQuery.each(listados[clase], function(id, valor) {
			if (limpiar(valor) === val) {
				existe = valor;
				return false;
			}
		});
		if (existe) {
			//ya existe en los datos anteriores: se esta editando
			input.addClass('warning').val(existe);
			td.closest('tr').addClass('warning')
					.attr('title', campos_clase[llave].titulo + ' debe ser �nico, pero ya existe el valor ' + existe + ' entre los datos actuales. Se editar� el dato existente con los valores ingresados');
		}
		else {
			input.removeClass('warning');
			td.closest('tr').removeClass('warning').removeAttr('title');
		}
	}

	function actualizarCheckbox(input, defval) {
		var val = limpiar(input.val());
		var checked = defval;
		if (val !== '') {
			checked = val.charAt(0) !== 'N' && val !== '0';
		}

		var check = input.closest('td').find(':checkbox');
		if (checked) {
			check.attr('checked', 'checked');
		}
		else {
			check.removeAttr('checked');
		}
	}

	jQuery(function() {
		jQuery('[name^=data]').change(function() {
			var input = jQuery(this);
			var idx = input.closest('td').index();
			var campo = jQuery('[name="campos[' + idx + ']"]').val();
			var info = campos_clase[campo];

			input.removeClass('error').removeClass('warning').removeAttr('title');

			if (!info) {
				return;
			}

			if (info.requerido && limpiar(input.val()) === '') {
				input.addClass('error').attr('title', 'Este campo es obligatorio');
			}
			else if (campo === llave) {
				validarUnicidad(input.closest('td'));
			}
			else if (info.relacion) {
				validarRelacion(input.closest('td'));
			}
			else if (info.tipo === 'bool') {
				actualizarCheckbox(input, info.defval);
			}
		});

		jQuery('[name^=campos]').change(function() {
			var info = campos_clase[jQuery(this).val()];
			var idx = jQuery(this).closest('th').index();
			var inputs = jQuery('[name^=data][name$="[' + idx + ']"]');
			var extras = inputs.siblings('.extra');
			extras.html('');

			if (info) {
				//mostrar listado para relaciones o checkbox para bools
				if (info.relacion) {
					var sel = jQuery('<select/>', {html: '<option value=""/>'});
					jQuery.each(listados[info.relacion], function(id, valor) {
						sel.append(jQuery('<option/>', {value: id, text: valor}));
					});
					sel.change(cambioRelacion);
					extras.append(sel);
				}
				else if (info.tipo === 'bool') {
					//agregar checkbox
					var check = jQuery('<input/>', {type: 'checkbox'}).change(cambioCheck);
					var br = jQuery('<br/>');
					extras.append(br);
					extras.append(check);
				}
			}

			inputs.change();
		}).change();

		jQuery('form').submit(function() {
			var errores = jQuery('.error');
			if (errores.length) {
				alert('Hay errores!');
				errores.focus();
				return false;
			}
			return true;
		});
	});

</script>