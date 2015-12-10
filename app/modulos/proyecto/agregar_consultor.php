<?php
	require_once dirname(__FILE__).'/../../../conf.php';

	require_once Conf::ServerDir().'/fw/classes/Sesion.php';
	require_once Conf::ServerDir().'/fw/classes/Pagina.php';
	require_once Conf::ServerDir().'/fw/classes/Usuario.php';
	require_once Conf::ServerDir().'/fw/classes/Utiles.php';
	require_once Conf::ServerDir().'/fw/classes/Html.php';
	require_once Conf::ServerDir().'/fw/classes/Lista.php';
// las classes proyecto y lista se incluye en usuario

	$Sesion = new Sesion( array('ADM') );
	$pagina = new Pagina($Sesion);
    $proyecto = new Proyecto($Sesion);

	#@TODO checkear permisos
	# Proyecto::CheckPermisosConsultor($id_proyecto,$Sesion->usuario->fields['rut']);

	if($id_proyecto != "")
	{
		if(!$proyecto->Load($id_proyecto))
		    $pagina->FatalError("Grupo Inv�lido");

		$proyecto->LoadEmpresa();
	}
	else
		$pagina->AddError("Proyecto indeterminado");

	if($arreglo_ids != "")
	{
		$proyecto->CargarConsultores($arreglo_ids);
		$pagina->Redirect("editar_proyecto.php?accion=editar&id_proyecto=$id_proyecto&msg=ok");
	}

	$pagina->titulo = "Consultores del Proyecto";

	$pagina->PrintHeaders();

	$pagina->PrintTop();

?>
<table width="96%" align="left">
	<tr>
		<td width="20">&nbsp;</td>
		<td valign="top">

<form id="form" name="form_usuarios" method="post">
<input type="hidden" name="opc" value="">
<input type="hidden" name="accion" value="<?=$accion?>">
<input type="hidden" name="id_proyecto" value="<?=$id_proyecto?>">
<input type="hidden" name="arreglo_ids" value="">

<table width="100%" align="left">
	<tr>
		<td><b>Listado de Consultores</b></td>
		<td></td>
		<td><b>Consultores de este proyecto</b></td>
	</tr>
	<tr>
		<td valign="top" class="subtitulo" align="left">
			<select name="sel_fuera" size="2" style="width: 220px; height: 100px;">
			</select>
		</td>
		<td style="vertical-align:middle;" class="texto" align="left">
			<input type=button onclick="AgregarUsuario(this.form);" value=">">
			<br>
			<input type=button onclick="EliminarUsuario(this.form);" value="<">
		</td>
		<td valign="top" class="texto" align="left">
			<select name="sel_dentro" size="2" style="width: 220px; height: 100px;">
			</select>
		</td>
	</tr>
	<tr>
		<td colspan=3 align=right>
			<br>
			<input type=button value="Actualizar consultores" onclick="EnviarDatos( this.form );">
		</td>
	</tr>
</table>

</form>
		</td>
	</tr>
</table>

<script>

function EnviarDatos()
{
	var valores = new Array();
	for(i = 0; i < form.sel_dentro.options.length; i++ )
	{
		valores[i] = form.sel_dentro.options[i].value;
	}
	arreglo = valores.join(',');
	form.arreglo_ids.value = arreglo;
	form.submit();
}
function Lista()
{
	this.data = new Array();
	this.num = 0;

	this.Add = function(obj)
	{
		this.data[ this.num++ ] = obj;
		return obj;
	}

	this.Get = function(idx)
	{
		return this.data[idx];
	}
}
function Usuario(id, nombre)
{
	this.id = id;
	this.nombre = nombre;
	this.agregado = false;
}

function InicializarUsuarios( form )
{
	var i, opt;

	for( i = 0; i < usuarios.num; i++ )
	{
		var user = usuarios.Get(i);

		opt = document.createElement('option');
		opt.value = user.id;

		opt.appendChild( document.createTextNode( '' + user.nombre + '' ) );
		form.sel_fuera.appendChild( opt );
	}

	ActualizarSeleccion( form );
}

function ActualizarSeleccion( form )
{
	while( form.sel_dentro.childNodes.length > 0 )
		form.sel_dentro.removeChild( form.sel_dentro.childNodes[0] );
	while( form.sel_fuera.childNodes.length > 0)
		form.sel_fuera.removeChild( form.sel_fuera.childNodes[0] );

	for(i = 0; i < usuarios.num; i++)
	{
		user = usuarios.Get(i);

		var opt = document.createElement('option');
		opt.value = user.id;

		opt.appendChild( document.createTextNode( user.nombre ) );

		if(user.agregado)
			form.sel_dentro.appendChild( opt );
		else
			form.sel_fuera.appendChild( opt );
	}

}

function AgregarUsuario( form )
{
	var i, pro;

	if(form.sel_fuera.selectedIndex >= 0)
	{
		var opt = form.sel_fuera.options[form.sel_fuera.selectedIndex];

		if(opt)
		{
			for(i = 0; i < usuarios.num; i++)
			{
				user = usuarios.Get(i);

				if( user.id == opt.value )
				{
					user.agregado = true;
				}
			}
			ActualizarSeleccion( form );
		}
	}
}

function EliminarUsuario( form )
{
	var i, pro;

	if(form.sel_dentro.selectedIndex >= 0)
	{
		var opt = form.sel_dentro.options[form.sel_dentro.selectedIndex];

		if(opt)
		{
			for(i = 0; i < usuarios.num; i++)
			{
				user = usuarios.Get(i);

				if( user.id == opt.value )
				{
					user.agregado = false;
				}
			}
			ActualizarSeleccion( form );
		}
	}
}
var usuarios = new Lista();

<?php
	$query = "SELECT usuario.rut AS ru, CONCAT( apellido1, ' ', apellido2, ', ', nombre ) AS nombre_usuario,
				(
					SELECT id_proyecto AS pro
						FROM proyecto_consultor
						WHERE id_proyecto = '$id_proyecto'
						AND proyecto_consultor.rut_consultor = ru
				) AS id_proy
				FROM usuario JOIN usuario_permiso ON usuario.rut=usuario_permiso.rut
					JOIN prm_permisos USING (codigo_permiso)
				WHERE prm_permisos.codigo_permiso = 'CON'
				ORDER BY apellido1, apellido2, nombre";
	$usuarios = new ListaObjetos($Sesion, "", $query);
	for($i = 0; $i < $usuarios->num; $i++)
	{
		$user = $usuarios->Get($i);
		echo("user =  new Usuario(".$user->fields['ru'].",'".$user->fields['nombre_usuario']."');\n");
		if($user->fields['id_proy'] == $id_proyecto)
			echo("user.agregado = true;\n");
		echo("usuarios.Add(user);\n");
	}

?>

var form = document.getElementById("form");
ActualizarSeleccion( form );
</script>

<?php
	$pagina->PrintBottom();
?>
