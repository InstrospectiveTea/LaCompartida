<?php

require_once dirname(__FILE__) . '/../conf.php';

class CartaCobro extends NotaCobro {

    var $carta_tabla = 'carta';
    var $carta_id = 'id_carta';
    var $carta_formato = 'formato';
    public $secciones = array(
        'CARTA' => array(
            'FECHA' => 'Secci�n FECHA',
            'ENVIO_DIRECCION' => 'Secci�n ENVIO_DIRECCION',
            'DETALLE' => 'Secci�n DETALLE',
            'ADJ' => 'Secci�n ADJ',
            'PIE' => 'Secci�n PIE',
            'DATOS_CLIENTE' => 'Secci�n DATOS_CLIENTE',
            'SALTO_PAGINA' => 'Secci�n SALTO_PAGINA'
        ),
        'DETALLE' => array(
            'FILAS_ASUNTOS_RESUMEN' => 'FILAS_ASUNTOS_RESUMEN',
            'FILAS_FACTURAS_DEL_COBRO' => 'FILAS_FACTURAS_DEL_COBRO',
            'FILA_FACTURAS_PENDIENTES' => 'FILA_FACTURAS_PENDIENTES'
        )
    );
    public $diccionario = array(
        'CARTA' => array(
            '%cuenta_banco%' => 'Cuenta bancaria',
            '%logo_carta%' => 'Imagen logo',
            '%direccion%' => 'Direcci�n',
            '%titulo%' => 'T�tulo',
            '%subtitulo%' => 'Subt�tulo',
            '%numero_cobro%' => 'N�mero cobro',
            '%xfecha_mes_dos_digitos%' => 'Mes emision (mm)',
            '%xfecha_ano_dos_digitos%' => 'A�o emision (yy)',
            '%xnro_factura%' => 'N� del cobro',
            '%glosa_cliente%' => 'Raz�n social Factura',
            '%xdireccion%' => 'Direcci�n Factura',
            '%xrut%' => 'RUT contrato'
        ),
        'FECHA' => array(
            '%fecha_especial%' => 'Ciudad (pa�s), d�a de mes de a�o',
            '%fecha_especial2%' => 'Santiago, dia de Mes de a�o',
            '%fecha_espanol%' => 'dia De Mes De a�o',
            '%fecha_espanol_del%' => 'dia De Mes Del a�o',
            '%fecha_slash%' => 'Ciudad, month dayth, year',
            '%fecha%' => 'mes dia, a�o',
            '%fecha_con_de%' => 'mes dia de a�o',
            '%fecha_ingles%' => 'month day, year',
            '%fecha_ingles_ordinal%' => 'month dayth, year',
            '%ciudad_fecha_ingles%' => 'Ciudad month dayth, year',
            '%ANO%' => 'a�o fecha fin',
            '%numero_cobro%' => 'n�mero cobro',
            '%inciales_encargado%' => 'iniciales encargado comercial',
            '%encargado_comercial%' => 'nombre completo enargado comercial',
            '%xrut%' => 'obtiene rut definido en el contrato',
            '%ciudad_estudio%' => 'Definido en Conf PaisEstudio'
        ),
        'ENVIO_DIRECCION' => array(
            '%SR%' => 'Por defecto (SR.) o el titulo_contacto definido en el contrato',
            '%glosa_codigo_postal%' => 'Texto segun lenguaje (C�digo Postal/Postal Code)',
            '%codigo_postal%' => 'campo factura_codigopostal definido en el contrato',
            '%titulo_contacto%' => 'El titulo_contacto definido en el contrato',
            '%NombreContacto%' => 'Nombre y Apellido del contrato ',
            '%NombreContacto_mayuscula%' => 'El nombre del contacto en mayuscula',
            '%xrut%' => 'El rut definido en el contrato',
            '%contrato_solo_nombre_contacto%' => 'El nombre del contacto',
            '%contrato_solo_apellido_contacto%' => 'el apellido del contacto',
            '%factura_razon_social_ucfirst%' => 'la factura_razon_social del contrato',
            '%glosa_cliente%' => 'la factura_razon_social del contrato',
            '%glosa_cliente_mayuscula%' => 'la factura_razon_social del contrato (Mayusculas)',
            '%factura_giro%' => 'Giro factura_giro definido en el contrato',
            '%valor_direccion%' => 'Direccion del contacto',
            '%fecha_especial%' => '(Santiago) dia de mes de a�o, (Santiago (Chile)) month dayth, year',
            '%fecha_especial_minusculas%' => '(santiago) dia de mes de a�o, (santiago (chile)) month dayth, year',
            '%NumeroCliente%' => 'campo id_cliente',
            '%Asunto%' => 'Glosas de los asuntos relacionados al cobro',
            '%asunto_salto_linea%' => 'Glosa de los asuntos relacionados con saldo de linea',
            '%CodigoAsunto%' => 'Codigos de asuntos relacionados',
            '%pais%' => 'Chile',
            '%num_letter%' => 'campo id_cobro',
            '%num_letter_documento%' => 'campo documento',
            '%presente%' => 'Texto segun lenguaje (Presente/)',
            '%nombre_pais%' => 'Nombre del pais definido en el contrato',
            '%nombre_pais_mayuscula%' => 'Nombre del pais definido en el contrato mayuscula',
            '%fecha_con_de%' => 'Correspondiente al mes de (fecha_fin)',
            '%factura_desc_mta%' => 'cuenta de cobro o factura',
            '%doc_tributario%' => 'Documentos tributarios relacionados',
            '%num_factura%' => 'campo documento de la tabla cobro',
            '%ciudad_cliente%' => 'campo factura_ciudad del contrato',
            '%comuna_cliente%' => 'campo factura_comuna del contrato',
            '%comuna_ciudad_cliente%' => 'Muestra comuna, ciudad si estas estan definidas',
            '%codigo_postal_cliente%' => 'campo factura_codigopostal del contrato',
            '%cliente_fax%' => 'campo fono_contacto del contrato',
            '%cliente_correo%' => 'campo email_contacto del contrato',
            '%factura_giro%' => 'campo factura_giro',
            '%asuntos_relacionados%' => 'Imprime segmento de parrafo con los asuntos relaionados'
        ),
        'DETALLE' => array(
            '%ApellidoContacto%' => 'Apellido del contacto',
            '%Asunto%' => 'Lista de asuntos',
            '%Asunto_ucwords%' => 'Lista de asuntos con primeros letras en mayuscula',
            '%asuntos_relacionados%' => 'Lista asuntos en relacion a los trabajos del cobro',
            '%NombrePilaContacto%' => 'Nombre del contacto',
            '%SoloNombreContacto%' => 'SoloNombreContacto',
            '%boleta_gastos%' => 'boleta_gastos (utilizado por mb define CH antes del simbolo de la moneda)',
            '%boleta_honorarios%' => 'boleta_honorarios (utilizado por mb en su formato antiguo de chile con boleta)',
            '%categoria_encargado_comercial%' => 'categoria_encargado_comercial',
            '%categoria_encargado_comercial_mayusculas%' => 'categoria_encargado_comercial_mayusculas',
            '%codigo_cci%' => 'codigo_cci definido en el contrato',
            '%codigo_cci2%' => 'codigo_cci2 definido en el contrato en relacion a la cuenta secundaria',
            '%codigo_swift%' => 'codigo_swift definido en el contrato',
            '%codigo_swift2%' => 'codigo_swift2 definido en el contrato en relacion a la cuenta secundaria',
            '%codigo_clabe%' => 'codigo_clabe segun la cuenta bancaria definida en el contrato',
            '%codigo_aba%' => 'codigo_aba segun la cuenta bancaria definida en el contrato',
            '%CodigoAsuntoGlosaAsunto%' => 'CodigoAsuntoGlosaAsunto en base a los asuntos relacionados con el cobro',
            '%codigopropuesta%' => 'codigopropuesta (no existe campo)',
            '%concepto_gastos_cuando_hay%' => 'concepto_gastos_cuando_hay',
            '%concepto_honorarios_cuando_hay%' => 'concepto_honorarios_cuando_hay',
            '%cta_cte_gbp_segun_moneda%' => 'Numero de cuenta que va a cambiar segun moneda (si es dolar una cuenta  en caso contrario otra  para gbplegal',
            '%datos_bancarios%' => 'datos bancarios segun inteligencia en una columna)',
            '%cuenta_banco%' => 'cuenta_banco segun la id_cuenta definida en el contrato',
            '%cuenta_mb%' => 'cuenta_mb',
            '%cuenta_mb_boleta%' => 'cuenta_mb_boleta',
            '%cuenta_mb_ny%' => 'direccion de cuenta de MB en Nueva York',
            '%mb_detalle_chile_boleta%' => 'Segmento especial generado por archivo lang es.php considera GASTOS HONORARIOS y MIXTOS',
            '%despedida_mb%' => 'Frase de despedida_mb',
            '%detalle_careyallende%' => 'Letra de detalle completo del estudio Carey Allende',
            '%detalle_cuenta_gastos%' => 'detalle_cuenta_gastos',
            '%detalle_cuenta_gastos2%' => 'detalle_cuenta_gastos2',
            '%detalle_cuenta_honorarios%' => 'detalle_cuenta_honorarios Segmento construido segun condiciones del cobro',
            '%detalle_cuenta_honorarios_primer_dia_mes%' => 'detalle_cuenta_honorarios_primer_dia_mes',
            '%detalle_cuenta_gastos_cl_boleta%' => 'detalle_cuenta_gastos_cl_boleta (utilizado por mb formato 2014',
            '%detalle_ebmo%' => 'Letra de detalle completo del estudio ebmo (manejo de honorarios y gastos',
            '%detalle_mb%' => 'Frase especial Morales y Bezas',
            '%detalle_mb_boleta%' => 'Frase descripcion detalle MB',
            '%detalle_mb_ny%' => 'Frase especial MB New York',
            '%duracion_trabajos%' => 'total duracion cobrable de las horas inluido en el cobro',
            '%documentos_relacionados%' => 'Obtiene documentos (Facturas) relacionadas con el cobro"',
            '%doc_tributario%' => 'Obtiene documentos (Facturas) relacionadas con el cobro"',
            '%encargado_comercial%' => 'encargado_comercial',
            '%encargado_comercial_uc%' => 'encargado_comercial_ucwords',
            '%equivalente_a_baz%' => 'extensi�n frase de carte en el caso de que se hace una transfer�a',
            '%equivalente_dolm%' => 'agrega segmento (que ascienden a %monto%) solo si existe diferencia entre id_moneda & opc_moneda_total',
            '%estimado%' => 'Estimada/Estimado',
            '%factura_razon_social_ucfirst%' => 'Campo factura_razon_social del contrato',
            '%fecha%' => 'Frase que indica el periodo de la fecha',
            '%fecha_al%' => 'En frase del periodo reemplazar la palabra "hasta" con la palabra "al"',
            '%fecha_al_minuscula%' => 'fecha_al_minuscula',
            '%fecha_con_de%' => 'En frase del periodo reemplazar la palabra "hasta" con la palabra "de"',
            '%fecha_con_prestada%' => 'fecha_con_prestada',
            '%fecha_con_prestada_mayuscula%' => 'fecha_con_prestada_mayuscula',
            '%fecha_con_prestada_minusculas%' => 'fecha_con_prestada_minusculas',
            '%fecha_dia_carta%' => 'D�a actual al momento de imprimir la carta',
            '%fecha_diff_prestada_durante%' => 'fecha_diff_prestada_durante',
            '%fecha_diff_prestada_durante_mayuscula%' => 'fecha_diff_prestada_durante_mayuscula',
            '%fecha_diff_prestada_durante_minusculas%' => 'fecha_diff_prestada_durante_minusculas',
            '%fecha_emision%' => 'Fecha de emisi�n del cobro',
            '%fecha_especial%' => 'fecha_especial Conf CiudadEstudio + Conf PaisEstudio + DD de MM de YYYY',
            '%fecha_especial_mta%' => 'fecha_especial_mta = Bogot�  D.C.  + DD de MM de YYYY ',
            '%fecha_especial_mta_en%' => 'fecha_especial_mta_en = Bogot� MM DD  YYYY',
            '%fecha_inicial_periodo_exacto%' => 'Considera fecha del primer gasto cuando no hay honorarios  de no ser asi utiliza campo fecha_ini del cobro',
            '%fecha_fin_periodo_exacto%' => 'Considera fecha del ultimo gasto cuando no hay honorarios  de no ser asi utiliza el campo fecha_fin del cobro',
            '%fecha_facturacion%' => 'Obtiene (DD de MM de YYYY) segun el campo fecha_facturacion del cobro',
            '%fecha_facturacion_mes%' => 'Obtiene (MM) segun el campo fecha_facturacion del cobro',
            '%fecha_hasta%' => 'fecha corte del cobro en Formato DIA de MES ( sin a�o )',
            '%fecha_hasta_dmy%' => 'fecha corte del cobro en formato DIA MES A�O',
            '%fecha_mes%' => 'Agrega (realizados el mes de MM). Seg�n campo fecha_fin de la tabla cobro',
            '%fecha_mta%' => 'Obtiene la fecha del campo fecha_facturacion si esta no existe considera la fecha de emisi�n del cobro',
            '%fecha_mta_agno%' => 'Obtiene el a�o desde el tag %fecha_mta%',
            '%fecha_mta_dia%' => 'Obtiene el d�a desde el tag %fecha_mta%',
            '%fecha_mta_mes%' => 'Obtiene el mes desde el tag %fecha_mta%',
            '%fecha_periodo_exacto%' => 'Periodo del cobro con fechas exactas (desde el dia DD-MM-YYY hasta el mes de MES de YYYY',
            '%fecha_primer_trabajo%' => 'Fecha del primer trabajo del cobro',
            '%fecha_primer_trabajo_de%' => 'Agrega segmento (durante el mes mes de MM de YYYY)',
            '%fecha_primer_trabajo_durante%' => 'Agrega segmento (prestados durante el mes de MM de YYYY',
            '%frase_gastos_egreso%' => 'Frase especial para baz',
            '%frase_gastos_ingreso%' => 'Frase especial para baz',
            '%frase_moneda%' => 'frase_moneda glosa_moneda_plural de opc_moneda_total "',
            '%glosa_banco_contrato%' => 'glosa_banco_contrato definido en contrato relacionado con la cuenta definida en el contrato',
            '%glosa_banco_contrato2%' => 'glosa_banco_contrato2 definido en contrato relacionado con la cuenta secundaria definida en el contrato',
            '%glosa_cliente%' => 'campo "factura_razon_social" de la tabla contrato',
            '%nombre_del_cliente%' => 'campo "glosa_cliente" de la tabla Cliente',
            '%glosa_cliente_mayuscula%' => 'glosa_cliente_mayuscula',
            '%glosa_contrato%' => 'obtiene campo glosa_contrato tabla contrato',
            '%glosa_cuenta_contrato%' => 'glosa_cuenta_contrato',
            '%glosa_cuenta_contrato2%' => 'glosa_cuenta_contrato2',
            '%glosa_moneda%' => 'glosa moneda relacionada con la cuenta corriente definida en el contrato',
            '%lista_asuntos%' => 'listado de asuntos en base a la relacion tabla cobro_asunto',
            '%lista_asuntos_guion%' => 'lista_asuntos_guion',
            '%logo_carta%' => 'Obtiene logo carta Conf->Server() + Conf->ImgDir() (deprecado)',
            '%simbolo_moneda_cobro%' => 'Simbolo de moneda segun la id_moneda del cobro"',
            '%monto%' => 'Monto total del cobro (segun opc_moneda_total)',
            '%monto_con_gasto%' => 'Monto sin gastos segun var $monto_moneda_con_gasto (Flat Fee considera monto_contrato definido en el cobro) (malo)',
            '%monto_en_palabras%' => 'monto_en_palabras',
            '%monto_en_palabras_en%' => 'monto_en_palabras_en',
            '%monto_en_pesos%' => 'monto total del cobro (equivalentes a la fecha $ + monto)',
            '%monto_gasto%' => 'total de los gastos definido por campo monto_gastos del cobro',
            '%monto_gasto_separado%' => 'Frase que indica valor de gastos',
            '%monto_gasto_separado_baz%' => 'monto_gasto_separado_baz',
            '%monto_gastos_con_iva%' => 'monto_gastos_con_iva',
            '%monto_gastos_cuando_hay%' => 'monto_gastos_cuando_hay',
            '%monto_gastos_sin_iva%' => 'monto_gastos_sin_iva',
            '%monto_honorarios_cuando_hay%' => 'monto_honorarios_cuando_hay',
            '%monto_honorarios_moneda_cobro%' => 'moneda->fields[simbolo] + this->fields[monto]"',
            '%mb_monto_honorarios_moneda_cobro%' => 'Equivalentes a moneda->fields[simbolo] + this->fields[monto]"',
            '%monto_iva%' => 'Definido por la operacion la diferencia entre monto_total_cobro- monto_cobro_original',
            '%monto_original%' => 'moneda->fields[simbolo] + this->fields[monto] (hace referencia a la moneda del cobro)"',
            '%monto_honorarios%' => 'Monto honorarios en la moneda del tarifa',
            '%monto_impuesto%' => 'monto equivalente a impuestos considerando espacio y simbolo de moneda',
            '%monto_solo_gastos%' => 'Monto solo gastos definido en la variable $gasto_en_pesos (variable no definida)',
            '%monto_subtotal%' => 'Monto definido por x_resultados[monto_subtotal_completo][this->fields[opc_moneda_total]]"',
            '%monto_total_demo%' => 'Monto total segun x_resultados[monto_total_cobro][this->fields[opc_moneda_total]]"',
            '%monto_total_espacio%' => 'Monto total demo con espacio entre simbolo y monto',
            '%monto_total_glosa_moneda%' => 'Monto total demo con glosa de la moneda en vez de simbolo',
            '%monto_total_demo_jdf%' => 'Obtiene un monto total segun x_resultados[monto_total_cobro][this->fields[opc_moneda_total]]"',
            '%monto_total_demo_uf%' => 'monto_total_demo_uf (monto moneda demo sin simbolo) (no funciona) variable no definida',
            '%monto_total_sin_iva%' => 'Monto subtotal honorarios y gastos sin iva segun la moneda original del cobro',
            '%n_num_factura%' => 'n_num_factura (N� + campo documento tabla cobro)',
            '%num_factura%' => 'campo "documento" de la tabla "cobro"',
            '%num_letter%' => 'num_letter',
            '%num_letter_baz%' => 'num_letter_baz',
            '%num_letter_documento%' => 'num_letter_documento',
            '%num_letter_rebaza%' => 'Generado por (la factura N� + this->fields[documento])"',
            '%num_letter_rebaza_especial%' => 'num_letter_rebaza_especial',
            '%numero_cuenta_contrato%' => 'numero_cuenta_contrato segun lo definido en el contrato',
            '%numero_cuenta_contrato2%' => 'numero_cuenta_contrato2 segun lo definido en el contrato referente a la segunda cuenta bancaria',
            '%NombreContacto%' => 'Obtiene Nombre del contrato + apellido contacto',
            '%NombreContacto_mayuscula%' => 'Obtiene Nombre del contrato + apellido contacto en mayuscula',
            '%porcentaje_impuesto%' => 'Numero de Porcentaje (incluye simbolo %)',
            '%porcentaje_impuesto_sin_simbolo%' => 'porcentaje_impuesto_sin_simbolo',
            '%porcentaje_iva_con_simbolo%' => 'porcentaje_iva_con_simbolo definido por campo porcentaje_impuesto del cobro',
            '%rut_cliente%' => 'rut_cliente obtenido desde el contrato',
            '%saldo_egreso_moneda_total%' => 'obtiene la suma del campo monto_cobrable de la tabla gastos cuando el campo egreso > 0',
            '%saldo_ingreso_moneda_total%' => 'obtiene la suma del campo monto_cobrable de la tabla gastos cuando el campo ingreso > 0',
            '%saldo_gastos_balance%' => 'saldo_balance_gastos_moneda_total',
            '%subtotal_gastos_solo_provision%' => 'x_cobro_gastos[subtotal_gastos_solo_provision]"',
            '%subtotal_gastos_sin_provision%' => 'x_cobro_gastos[subtotal_gastos_sin_provision]"',
            '%subtotal_gastos_diff_con_sin_provision%' => 'x_cobro_gastos[gasto_total]"',
            '%saldo_gastos_balance%' => 'Balance de gastos en relacion a ingresos y egresos del cobro',
            '%saldo_gasto_facturado%' => 'Saldo gasto facturado segun ArrayTotalesDelContrato()',
            '%saldo_gasto_facturado_moneda_base%' => 'Saldo gastos en moneda base facturado segun ArrayTotalesDelContrato()',
            '%solo_num_factura%' => 'Obtiene solo los valores numericos del campo documento tabla cobro',
            '%saludo_mb%' => 'De mi consideraci�n:',
            '%si_gastos%' => 'agrega segmento de texto (y gastos) solo cuando el monto gastos es > 0',
            '%simbolo_opc_moneda_totall%' => 'simbolo_opc_moneda_totall',
            '%sr%' => 'Titulo del contacto definido en el contrato  por defecto "Se�or"',
            '%subtotal_gastos_diff_con_sin_provision%' => 'balance cuenta de gastos',
            '%subtotal_gastos_sin_provision%' => 'monto gastos sin las provisiones',
            '%subtotal_gastos_solo_provision%' => 'monto gastos solo contando las provisiones',
            '%tipo_cuenta%' => 'tipo_cuenta considera Cuenta Corriente o Cuenta de ahorro segun lo definido en el contrato',
            '%tipo_gbp_segun_moneda%' => 'Tipo de moneda (Nacional/Extranjera) que va a cambiar segun moneda (gbplegal)',
        ),
        'ADJ' => array(
            '%cliente_correo%' => 'Obtiene campo email_contacto tabla cobro',
            '%cliente_fax%' => 'Obtiene campo fono_contacto tabla contrato',
            '%firma_careyallende%' => 'firma_careyallende elemento generado por lang',
            '%iniciales_encargado_comercial%' => 'iniciales_encargado_comercial',
            '%nombre_encargado_comercial%' => 'Obtiene nombre del encargado comercial',
            '%num_letter%' => 'Obtiene campo id_cobro tabla cobro',
            '%num_letter_baz%' => 'Obtiene campo documento tabla cobro',
            '%num_letter_documento%' => 'Obtiene campo documento tabla cobro',
            '%nro_factura%' => 'Obtiene campo documento tabla cobro',
        ),
        'PIE' => array(
            '%direccion%' => 'Generado por (Conf PdfLinea2 + Conf PdfLinea3 + Conf SitioWeb + Conf::Email)',
            '%logo_carta%' => 'Obtiene logo carta desde el Conf::Server(). Conf::ImgDir() (no utilizar deprecado)',
            '%num_letter%' => 'Obtiene campo id_cobro desde tabla cobro',
            '%num_letter_documento%' => 'Obtiene campo documento desde tabla cobro',
            '%salto_pagina%' => 'Inserta salto de pagina',
        ),
        'DATOS_CLIENTE' => array(
            '%ApellidoContacto%' => 'Obtiene campo apellido_contacto desde tabla contrato',
            '%NombrePilaContacto%' => 'obtiene campo contacto desde tabla contrato',
            '%encargado_comercial_mayusculas%' => 'encargado_comercial_mayusculas',
            '%estimado%' => 'estimado(a)',
            '%glosa_cliente%' => 'Obtiene campo factura_razon_social desde tabla contrato',
            '%sr%' => 'Se�or',
            '%SR%' => 'Obtiene campo titulo_contacto desde contrato',
        ),
        'FILAS_FACTURAS_DEL_COBRO' => array(
            '%factura_impuesto%' => 'factura_impuesto',
            '%factura_moneda%' => 'factura_moneda',
            '%factura_numero%' => 'factura_numero',
            '%factura_periodo%' => 'factura_periodo',
            '%factura_total%' => 'factura_total',
            '%factura_total_sin_impuesto%' => 'factura_total_sin_impuesto',
        ),
        'FILAS_FACTURAS_DEL_COBRO' => array(
            '%factura_pendiente%' => 'factura_pendiente',
        ),
        'FILAS_ASUNTOS_RESUMEN' => array(
            '%fecha_mta%' => 'fecha de facturacion',
            '%gastos_asunto%' => 'monto de gastos segun la id_moneda',
            '%gastos_asunto_mi%' => 'monto gastos del asunto opc_ver_moneda',
            '%glosa_asunto%' => 'glosa_asunto',
            '%honorarios_asunto%' => 'monto honorarios de asuntos segun id_moneda',
            '%honorarios_asunto_mi%' => 'monto honorarios segun opc_ver_moneda',
            '%num_factura%' => 'num_factura',
            '%num_letter%' => 'num_letter',
            '%simbolo%' => 'simbolo segun id_moneda',
            '%simbolo_mi%' => 'simbolo segun opc_ver_moneda',
            '%total_asunto%' => 'total_asunto',
            '%total_asunto_mi%' => 'total del asunto segun opc_ver_moneda',
        ),
        'FILA_FACTURAS_PENDIENTES' => array(
            '%facturas_pendientes%' => 'facturas_pendientes',
        ),
        'SALTO_PAGINA' => array()
    );

    function __construct($sesion, $fields, $ArrayFacturasDelContrato, $ArrayTotalesDelContrato) {
        $this->sesion = $sesion;
        $this->fields = $fields;
        $this->ArrayFacturasDelContrato = $ArrayFacturasDelContrato;
        $this->ArrayTotalesDelContrato = $ArrayTotalesDelContrato;
        $valorsinespacio = '&nbsp;';
        if (Conf::GetConf($this->sesion, 'ValorSinEspacio')) {
            $valorsinespacio = '';
        }
        $this->espacio = $valorsinespacio;
        $this->monedas = Moneda::GetMonedas($sesion, '', true);
    }

    function NuevoRegistro() {
        return array(
            'descripcion' => 'Nueva Carta',
            'margen_superior' => 1.5,
            'margen_inferior' => 2,
            'margen_izquierdo' => 2,
            'margen_derecho' => 2,
            'margen_encabezado' => 0.88,
            'margen_pie_de_pagina' => 0.88
        );
    }

    function GenerarEjemplo($parser) {
        extract($this->ParametrosGeneracion());
        return $this->GenerarDocumentoCarta2($parser, 'CARTA', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta);
    }

    function GenerarDocumentoCarta($parser_carta, $theTag = '', $lang, $moneda_cliente_cambio, $moneda_cli, & $idioma, $moneda, $moneda_base, $trabajo, & $profesionales, $gasto, & $totales, $tipo_cambio_moneda_total, $cliente, $id_carta) {
        global $id_carta;
        global $contrato;
        global $cobro_moneda;
        global $moneda_total;
        global $x_cobro_gastos;

        if (!isset($parser_carta->tags[$theTag])) {
            return;
        }

        $html2 = $parser_carta->tags[$theTag];

        switch ($theTag) {
            case 'CARTA':

                if (method_exists('Conf', 'GetConf')) {
                    $PdfLinea1 = Conf::GetConf($this->sesion, 'PdfLinea1');
                    $PdfLinea2 = Conf::GetConf($this->sesion, 'PdfLinea2');
                    $PdfLinea3 = Conf::GetConf($this->sesion, 'PdfLinea3');
                } else {
                    $PdfLinea1 = Conf::PdfLinea1();
                    $PdfLinea2 = Conf::PdfLinea2();
                    $PdfLinea3 = Conf::PdfLinea3();
                }

                if (strpos($html2, '%cuenta_banco%')) {
                    if ($contrato->fields['id_cuenta']) {
                        $query_banco = "SELECT glosa FROM cuenta_banco WHERE id_cuenta = '" . $contrato->fields['id_cuenta'] . "'";
                        $resp = mysql_query($query_banco, $this->sesion->dbh) or Utiles::errorSQL($query_banco, __FILE__, __LINE__, $this->sesion->dbh);
                        list($glosa_cuenta) = mysql_fetch_array($resp);
                    } else
                        $glosa_cuenta = '';
                    $html2 = str_replace('%cuenta_banco%', $glosa_cuenta, $html2);
                }

                $html2 = str_replace('%logo_carta%', Conf::Server() . Conf::ImgDir(), $html2);
                $html2 = str_replace('%direccion%', $PdfLinea1, $html2);
                $html2 = str_replace('%titulo%', $PdfLinea1, $html2);
                $html2 = str_replace('%subtitulo%', $PdfLinea2, $html2);
                $html2 = str_replace('%numero_cobro%', $this->fields['id_cobro'], $html2);
                $html2 = str_replace('%encargado_comercial%', $nombre_encargado, $html2);

                $html2 = str_replace('%FECHA%', $this->GenerarDocumentoCartaComun($parser_carta, 'FECHA', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%ENVIO_DIRECCION%', $this->GenerarDocumentoCartaComun($parser_carta, 'ENVIO_DIRECCION', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%DETALLE%', $this->GenerarDocumentoCarta($parser_carta, 'DETALLE', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%ADJ%', $this->GenerarDocumentoCartaComun($parser_carta, 'ADJ', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%PIE%', $this->GenerarDocumentoCartaComun($parser_carta, 'PIE', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%DATOS_CLIENTE%', $this->GenerarDocumentoCartaComun($parser_carta, 'DATOS_CLIENTE', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%SALTO_PAGINA%', $this->GenerarDocumentoCartaComun($parser_carta, 'SALTO_PAGINA', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);

                break;

            case 'DETALLE': //GenerarDocumentoCarta

                $queryasuntosrel = "SELECT asunto.glosa_asunto
										FROM trabajo
									LEFT JOIN asunto ON ( asunto.codigo_asunto = trabajo.codigo_asunto) WHERE id_cobro='" . $this->fields['id_cobro'] . "' GROUP BY asunto.glosa_asunto ";
                $resultado = mysql_query($queryasuntosrel, $this->sesion->dbh) or Utiles::errorSQL($queryasuntosrel, __FILE__, __LINE__, $this->sesion->dbh);

                while ($data = mysql_fetch_assoc($resultado)) {
                    $asuntos_rel[] = $data;
                }

                $asuntosrelacionados = '';

                for ($k = 0; $k < count($asuntos_rel); $k++) {
                    $espace_rel = $k < count($asuntos_rel) - 1 ? ', ' : '';
                    $asuntos_relacionados .= $asuntos_rel[$k]['glosa_asunto'] . '' . $espace_rel;
                }

                $html2 = str_replace('%asuntos_relacionados%', $asuntos_relacionados, $html2);

                $html2 = str_replace('%saludo_mb%', __('%saludo_mb%'), $html2);
                $html2 = str_replace('%logo_carta%', Conf::Server() . Conf::ImgDir(), $html2);

                if (count($this->asuntos) > 1) {
                    $html2 = str_replace('%detalle_mb%', __('%detalle_mb_asuntos%'), $html2);
                    $html2 = str_replace('%detalle_mb_ny%', __('%detalle_mb_ny_asuntos%'), $html2);
                    $html2 = str_replace('%detalle_mb_boleta%', __('%detalle_mb_boleta_asuntos%'), $html2);
                } else {
                    $html2 = str_replace('%detalle_mb_ny%', __('%detalle_mb_ny%'), $html2);
                    $html2 = str_replace('%detalle_mb_boleta%', __('%detalle_mb_boleta%'), $html2);
                    if ($this->fields['monto_gastos'] > 0 && $this->fields['monto'] == 0) {
                        $html2 = str_replace('%detalle_mb%', __('%detalle_mb_gastos%'), $html2);
                        $html2 = str_replace('%cuenta_mb%', __('%cuenta_mb%'), $html2);
                    } else {
                        $html2 = str_replace('%detalle_mb%', __('%detalle_mb%'), $html2);
                        $html2 = str_replace('%cuenta_mb%', '', $html2);
                    }
                }

                $this->LoadGlosaAsuntos();
                $lista_asuntos = "<ul>";
                foreach ($this->glosa_asuntos as $asunto) {
                    $lista_asuntos .= "<li>" . $asunto . "</li>";
                }
                $lista_asuntos .= "</ul>";
                $html2 = str_replace('%lista_asuntos%', $lista_asuntos, $html2);

                $html2 = str_replace('%FILAS_ASUNTOS_RESUMEN%', $this->GenerarDocumentoCartaComun($parser_carta, 'FILAS_ASUNTOS_RESUMEN', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);

                $html2 = str_replace('%despedida_mb%', __('%despedida_mb%'), $html2);
                $html2 = str_replace('%cuenta_mb_ny%', __('%cuenta_mb_ny%'), $html2);
                $html2 = str_replace('%cuenta_mb_boleta%', __('%cuenta_mb_boleta%'), $html2);
                $html2 = str_replace('%detalle_careyallende%', __('%detalle_careyallende%'), $html2);

                if ($this->fields['monto_gastos'] > 0 && $this->fields['monto_subtotal'] == 0) {
                    $html2 = str_replace('%detalle_ebmo%', __('%detalle_ebmo_solo_gastos%'), $html2);
                } else if ($this->fields['monto_gastos'] == 0 && $this->fields['monto_subtotal'] > 0) {
                    $html2 = str_replace('%detalle_ebmo%', __('%detalle_ebmo_solo_honorarios%'), $html2);
                } else {
                    $html2 = str_replace('%detalle_ebmo%', __('%detalle_ebmo%'), $html2);
                }

                if ($cobro_moneda->moneda[$this->fields['opc_moneda_total']]['codigo'] == 'USD') {
                    $html2 = str_replace('%cta_cte_gbp_segun_moneda%', __('194-1861108179'), $html2);
                    $html2 = str_replace('%tipo_gbp_segun_moneda%', __('Extranjera'), $html2);
                } else {
                    $html2 = str_replace('%cta_cte_gbp_segun_moneda%', __('194-1847085-0-23'), $html2);
                    $html2 = str_replace('%tipo_gbp_segun_moneda%', __('Nacional'), $html2);
                }

                $html2 = str_replace('%porcentaje_impuesto%', (int) ($this->fields['porcentaje_impuesto']) . '%', $html2);
                $html2 = str_replace('%porcentaje_impuesto_sin_simbolo%', (int) ($this->fields['porcentaje_impuesto']), $html2);

                if (Conf::GetConf($this->sesion, 'TituloContacto')) {
                    $html2 = str_replace('%sr%', __($contrato->fields['titulo_contacto']), $html2);
                    $html2 = str_replace('%NombrePilaContacto%', $contrato->fields['contacto'], $html2);
                    $html2 = str_replace('%ApellidoContacto%', $contrato->fields['apellido_contacto'], $html2);
                } else {
                    $html2 = str_replace('%sr%', __('Se�or'), $html2);
                    $NombreContacto = explode(' ', $contrato->fields['contacto']);
                    $html2 = str_replace('%NombrePilaContacto%', $NombreContacto[0], $html2);
                    $html2 = str_replace('%ApellidoContacto%', $NombreContacto[1], $html2);
                }

                $html2 = str_replace('%glosa_cliente%', $contrato->fields['factura_razon_social'], $html2);
                $html2 = str_replace('%nombre_del_cliente%', $cliente->fields['glosa_cliente'], $html2);

                if (strtolower($contrato->fields['titulo_contacto']) == 'sra.' || strtolower($contrato->fields['titulo_contacto']) == 'srta.') {
                    $html2 = str_replace('%estimado%', __('Estimada'), $html2);
                } else {
                    $html2 = str_replace('%estimado%', __('Estimado'), $html2);
                }

                /*
                  Total Gastos
                  se suma cuando idioma es ingl�s
                  se presenta separadamente cuando es en espa�ol
                 */

                $total_gastos = 0;
                $total_gastos_balance = 0;
                $saldo_egreso_moneda_total = 0;
                $saldo_ingreso_moneda_total = 0;

                $query = "SELECT SQL_CALC_FOUND_ROWS * FROM cta_corriente WHERE id_cobro='" . $this->fields['id_cobro'] . "' AND (egreso > 0 OR ingreso > 0) ORDER BY fecha ASC";
                $lista_gastos = new ListaGastos($this->sesion, '', $query);

                $sum_egreso = 0;
                $sum_ingreso = 0;
                for ($i = 0; $i < $lista_gastos->num; $i++) {
                    $gasto = $lista_gastos->Get($i);

                    //Cargar cobro_moneda

                    if ($gasto->fields['egreso'] > 0) {
                        $saldo = $gasto->fields['monto_cobrable'];
                        if ($gasto->fields['id_movimiento'] != $this->fields['id_gasto_generado']) {
                            $egreso_moneda_total = $gasto->fields['monto_cobrable'] * ($cobro_moneda->moneda[$gasto->fields['id_moneda']]['tipo_cambio'] / $cobro_moneda->moneda[$moneda_total->fields['id_moneda']]['tipo_cambio']);
                        } else {
                            $egreso_moneda_total = 0;
                        }
                        $ingreso_moneda_total = 0;
                        if ($gasto->fields['cobrable_actual'] == 1) {
                            $sum_egreso += $gasto->fields['monto_cobrable'];
                        }
                    } elseif ($gasto->fields['ingreso'] > 0) {
                        $saldo = -$gasto->fields['monto_cobrable'];
                        $ingreso_moneda_total = $gasto->fields['monto_cobrable'] * ($cobro_moneda->moneda[$gasto->fields['id_moneda']]['tipo_cambio'] / $cobro_moneda->moneda[$moneda_total->fields['id_moneda']]['tipo_cambio']);
                        $egreso_moneda_total = 0;
                        if ($gasto->fields['cobrable_actual'] == 1) {
                            $sum_ingreso += $gasto->fields['monto_cobrable'];
                        }
                    }

                    if (substr($gasto->fields['descripcion'], 0, 19) == "Saldo aprovisionado") {
                        $saldo_balance = $saldo;
                    } else {
                        $saldo_balance = 0;
                    }

                    $saldo_balance_moneda_total = $saldo_balance * ($cobro_moneda->moneda[$gasto->fields['id_moneda']]['tipo_cambio'] / $cobro_moneda->moneda[$moneda_total->fields['id_moneda']]['tipo_cambio']);
                    $saldo_egreso_moneda_total += $egreso_moneda_total;
                    $saldo_ingreso_moneda_total += $ingreso_moneda_total;
                    $total_gastos_balance += $saldo_balance_moneda_total;
                    $total_gastos = $this->fields['monto_gastos'];
                }
                $total_gastos_subtotal = $this->fields['subtotal_gastos'];

                // Si utiliza el nuevo m�dulo, agrego el saldo de adelantos para gastos a
                if (Conf::GetConf($this->sesion, 'NuevoModuloGastos')) {
                    $detalle_pagos_contrato = Cobro::DetallePagoContrato($this->sesion, $this->fields['id_cobro']);
                    $saldo_ingreso_moneda_total += -1 * $detalle_pagos_contrato['saldo_adelantos'];
                    $saldo_ingreso_moneda_total += $detalle_pagos_contrato['monto_adelantos_sin_asignar_gastos'];
                }

                $saldo_balance_gastos_moneda_total = max(0, $saldo_ingreso_moneda_total - $saldo_egreso_moneda_total);

                $mb_monto_honorarios = $this->fields['monto'];
                $mb_monto_gastos = $this->fields['monto_gastos'];

                // Utilizado por Morales y Besa Solicitado por @Gtigre
                if ($mb_monto_honorarios > 0 && $mb_monto_gastos > 0) {
                    $mb_detalle_chile_boleta = "lang_mb_detalle_chile_boleta_hyg";
                } elseif ($mb_monto_honorarios == 0 && $mb_monto_gastos > 0) {
                    $mb_detalle_chile_boleta = 'lang_mb_detalle_chile_boleta_g';
                } else {
                    $mb_detalle_chile_boleta = 'lang_mb_detalle_chile_boleta_h';
                }

                $html2 = str_replace('%mb_detalle_chile_boleta%', __($mb_detalle_chile_boleta), $html2);
                $html2 = str_replace('%saldo_egreso_moneda_total%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($saldo_egreso_moneda_total, $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2); // suma ingresos cobrables
                $html2 = str_replace('%saldo_ingreso_moneda_total%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($saldo_ingreso_moneda_total, $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2); // suma ingresos cobrables
                $html2 = str_replace('%saldo_gastos_balance%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($saldo_balance_gastos_moneda_total, $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2);
                $html2 = str_replace('%subtotal_gastos_solo_provision%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($saldo_balance_gastos_moneda_total, $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2); // en la carta se especifica que el monto debe aparecer como positivo
                $html2 = str_replace('%subtotal_gastos_sin_provision%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($x_cobro_gastos['subtotal_gastos_sin_provision'], $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2); // en la carta se especifica que el monto debe aparecer como positivo
                $html2 = str_replace('%subtotal_gastos_diff_con_sin_provision%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($x_cobro_gastos['gasto_total'], $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2); // en la carta se especifica que el monto debe aparecer como positivo
                // Monto honorario moneda cobro
                $html2 = str_replace('%simbolo_moneda_cobro%', $moneda->fields['simbolo'], $html2);
                $html2 = str_replace('%monto_honorarios_moneda_cobro%', $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                if ($this->fields['id_moneda'] != $this->fields['opc_moneda_total']) {
                    $html2 = str_replace('%mb_monto_honorarios_moneda_cobro%', ' equivalentes a ' . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' conforme a su equivalencia al %fecha_hasta_dmy%.', $html2);
                } else {
                    $html2 = str_replace('%mb_monto_honorarios_moneda_cobro%', '.', $html2);
                }

                /* MONTOS SEGUN MONEDA TOTAL IMPRESION */
                $aproximacion_monto = number_format($this->fields['monto'], $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], '.', '');
                $aproximacion_monto_subtotal = number_format($this->fields['monto_subtotal'], $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], '.', '');
                $aproximacion_monto_demo = $aproximacion_monto;
                $monto_moneda_demo = number_format($aproximacion_monto_demo * $cobro_moneda->moneda[$this->fields['id_moneda']]['tipo_cambio'] / $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['tipo_cambio'], $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], '.', '');
                $monto_moneda = ((double) $aproximacion_monto * (double) $this->fields['tipo_cambio_moneda']) / ($tipo_cambio_moneda_total > 0 ? $tipo_cambio_moneda_total : $moneda_total->fields['tipo_cambio']);
                $monto_moneda_subtotal = number_format($aproximacion_monto_subtotal * $cobro_moneda->moneda[$this->fields['id_moneda']]['tipo_cambio'] / $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['tipo_cambio'], $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], '.', '');
                $monto_moneda_sin_gasto = ((double) $aproximacion_monto * (double) $this->fields['tipo_cambio_moneda']) / ($tipo_cambio_moneda_total > 0 ? $tipo_cambio_moneda_total : $moneda_total->fields['tipo_cambio']);
                $monto_moneda_con_gasto = ((double) $aproximacion_monto * (double) $this->fields['tipo_cambio_moneda']) / ($tipo_cambio_moneda_total > 0 ? $tipo_cambio_moneda_total : $moneda_total->fields['tipo_cambio']);

                //Caso retainer menor de un valor y distinta tarifa (diferencia por decimales)
                if ((($this->fields['total_minutos'] / 60) < $this->fields['retainer_horas']) && ($this->fields['forma_cobro'] == 'RETAINER' || $this->fields['forma_cobro'] == 'PROPORCIONAL') && $this->fields['id_moneda'] != $this->fields['id_moneda_monto']) {
                    $monto_moneda_con_gasto = ((double) $this->fields['monto'] * (double) $this->fields['tipo_cambio_moneda']) / ($tipo_cambio_moneda_total > 0 ? $tipo_cambio_moneda_total : $moneda_total->fields['tipo_cambio']);
                }

                $query = "SELECT SUM( TIME_TO_SEC( duracion_cobrada )/3600 ) FROM trabajo WHERE id_cobro = '" . $this->fields['id_cobro'] . "' ";
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                list($duracion_trabajos) = mysql_fetch_array($resp);

                $html2 = str_replace('%duracion_trabajos%', number_format($duracion_trabajos, 2, ',', ''), $html2);

                //	Caso flat fee
                if ($this->fields['forma_cobro'] == 'FLAT FEE' && $this->fields['id_moneda'] != $this->fields['id_moneda_monto'] && $this->fields['id_moneda_monto'] == $this->fields['opc_moneda_total'] && empty($this->fields['descuento'])) {
                    $monto_moneda = $this->fields['monto_contrato'];
                    $monto_moneda_con_gasto = $this->fields['monto_contrato'];
                    $monto_moneda_sin_gasto = $this->fields['monto_contrato'];
                    $monto_moneda_subtotal = $this->fields['monto_contrato'];
                }

                $monto_moneda_demo += number_format($total_gastos, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], '.', '');
                $monto_moneda_subtotal += number_format($total_gastos_subtotal, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], '.', '');
                $monto_moneda_con_gasto += $total_gastos;

                if ($lang != 'es')
                    $monto_moneda += $total_gastos;
                if ($total_gastos > 0) {
                    $html2 = str_replace('%monto_gasto%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                }

                //	Fechas periodo
                $datefrom = strtotime($this->fields['fecha_ini'], 0);
                $dateto = strtotime($this->fields['fecha_fin'], 0);
                $difference = $dateto - $datefrom; //Dif segundos
                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }

                $datediff = $months_difference;

                //	 Mostrando fecha seg�n idioma

                if ($this->fields['fecha_ini'] != '' && $this->fields['fecha_ini'] != '0000-00-00') {
                    $texto_fecha_es = __('entre los meses de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_ini'], '%B %Y')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y'));
                } else {
                    $texto_fecha_es = __('hasta el mes de') . ' ' . ucfirst(ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y')));
                }

                if ($this->fields['fecha_ini'] != '' && $this->fields['fecha_ini'] != '0000-00-00') {
                    $texto_fecha_en = __('between') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_ini']))) . ' ' . __('and') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                } else {
                    $texto_fecha_en = __('until') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                }

                if ($lang == 'es') {
                    $fecha_diff = $datediff > 0 && $datediff < 12 ? $texto_fecha_es : __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y'));
                    $fecha_al = $datediff > 0 && $datediff < 12 ? $texto_fecha_es : __('al mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y'));
                    $fecha_diff_con_de = $datediff > 0 && $datediff < 12 ? $texto_fecha_es : __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B de %Y'));
                    $fecha_diff_prestada = $datediff > 0 && $datediff < 12 ? __('prestada ') . $texto_fecha_es : __('prestada en el mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y'));
                } else {
                    $fecha_diff = $datediff > 0 && $datediff < 12 ? $texto_fecha_en : __('during') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                    $fecha_al = $datediff > 0 && $datediff < 12 ? $texto_fecha_en : __('to') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                    $fecha_diff_prestada = $datediff > 0 && $datediff < 12 ? $texto_fecha_en : __('during') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                }

                if (( $fecha_diff == 'durante el mes de No existe fecha' || $fecha_diff == 'hasta el mes de No existe fecha' ) && $lang == 'es') {
                    $fecha_diff = __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %Y'));
                    $fecha_al = __('al mes de') . ' ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B de %Y'));
                    $fecha_diff_con_de = __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B de %Y'));
                    $fecha_diff_prestada = __('prestada en el mes de') . ' ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B de %Y'));
                }

                /* FECHA PERIODO EXACTO PARA COBROS SOLO GASTOS */

                $query_fecha_ini_periodo_gastos = "SELECT MIN(fecha) FROM cta_corriente WHERE id_cobro='" . $this->fields['id_cobro'] . "' ORDER BY fecha LIMIT 1 ";
                $resp_fecha_ini_gastos = mysql_query($query_fecha_ini_periodo_gastos, $this->sesion->dbh) or Utiles::errorSQL($query_fecha_ini_periodo_gastos, __FILE__, __LINE__, $this->sesion->dbh);

                list($fecha_primer_gasto) = mysql_fetch_array($resp_fecha_ini_gastos);

                $query_fecha_fin_periodo_gastos = "SELECT max(fecha) FROM cta_corriente WHERE id_cobro='" . $this->fields['id_cobro'] . "' ORDER BY fecha LIMIT 1";
                $resp_fecha_fin_gastos = mysql_query($query_fecha_fin_periodo_gastos, $this->sesion->dbh) or Utiles::errorSQL($query_fecha_fin_periodo_gastos, __FILE__, __LINE__, $this->sesion->dbh);

                list($fecha_ultimo_gasto) = mysql_fetch_array($resp_fecha_fin_gastos);

                $fecha_diff_primer_gasto = ucfirst(Utiles::sql3fecha($fecha_primer_gasto, '%d-%m-%Y'));
                $fecha_diff_ultimo_gasto = ucfirst(Utiles::sql3fecha($fecha_ultimo_gasto, '%d-%m-%Y'));

                //Se saca la fecha inicial seg�n el primer trabajo
                $query = "SELECT fecha FROM trabajo WHERE id_cobro='" . $this->fields['id_cobro'] . "' AND visible='1' ORDER BY fecha LIMIT 1";
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                //ac� se calcula si hay trabajos o no (porque si no sale como fecha 1969)
                if (mysql_num_rows($resp) > 0) {
                    list($fecha_primer_trabajo) = mysql_fetch_array($resp);
                } else {
                    $fecha_primer_trabajo = $this->fields['fecha_fin'];
                }

                //Tambi�n se saca la fecha final seg�n el �ltimo trabajo
                $query = "SELECT LAST_DAY(fecha) FROM trabajo WHERE id_cobro='" . $this->fields['id_cobro'] . "' AND visible='1' ORDER BY fecha DESC LIMIT 1";
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);

                //ac� se calcula si hay trabajos o no (porque si no sale como fecha 1969)
                if (mysql_num_rows($resp) > 0) {
                    list($fecha_ultimo_trabajo) = mysql_fetch_array($resp);
                } else {
                    $fecha_ultimo_trabajo = $this->fields['fecha_fin'];
                }

                $fecha_inicial_primer_trabajo = date('Y-m-01', strtotime($fecha_primer_trabajo));
                $fecha_final_ultimo_trabajo = date('Y-m-d', strtotime($fecha_ultimo_trabajo));

                $datefrom = strtotime($fecha_inicial_primer_trabajo, 0);
                $dateto = strtotime($fecha_final_ultimo_trabajo, 0);
                $difference = $dateto - $datefrom; //Dif segundos
                $months_difference = floor($difference / 2678400);

                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }

                $datediff = $months_difference;

                $asuntos_doc = '';

                for ($k = 0; $k < count($this->asuntos); $k++) {
                    $asunto = new Asunto($this->sesion);
                    $asunto->LoadByCodigo($this->asuntos[$k]);
                    $espace = $k < count($this->asuntos) - 1 ? ', ' : '';
                    $asuntos_doc .= $asunto->fields['glosa_asunto'] . '' . $espace;
                    $codigo_asunto .= $asunto->fields['codigo_asunto'] . '' . $espace;
                }

                $html2 = str_replace('%Asunto%', $asuntos_doc, $html2);
                $asunto_ucwords = ucwords(strtolower($asuntos_doc));
                $html2 = str_replace('%Asunto_ucwords%', $asunto_ucwords, $html2);

                //	Mostrando fecha seg�n idioma

                if ($fecha_inicial_primer_trabajo != '' && $fecha_inicial_primer_trabajo != '0000-00-00') {

                    if ($lang == 'es') {
                        $fecha_diff_periodo_exacto = __('desde el d�a') . ' ' . date("d-m-Y", strtotime($fecha_primer_trabajo)) . ' ';
                    } else {
                        $fecha_diff_periodo_exacto = __('from') . ' ' . date("m-d-Y", strtotime($fecha_primer_trabajo)) . ' ';
                    }

                    if (Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%Y') == Utiles::sql3fecha($this->fields['fecha_fin'], '%Y')) {
                        $texto_fecha_es = __('entre los meses de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%B')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B %Y'));
                        $texto_fecha_es_de = __('entre los meses de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%B')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y'));
                    } else {
                        $texto_fecha_es = __('entre los meses de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%B %Y')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B %Y'));
                        $texto_fecha_es_de = __('entre los meses de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%B %Y')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y'));
                    }
                } else {
                    $texto_fecha_es = __('hasta el mes de') . ' ' . ucfirst(ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B %Y')));
                    $texto_fecha_es_de = __('hasta el mes de') . ' ' . ucfirst(ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y')));
                }

                if ($lang == 'es') {
                    $fecha_diff_periodo_exacto .= __('hasta el d�a') . ' ' . Utiles::sql3fecha($this->fields['fecha_fin'], '%d-%m-%Y');
                } else {
                    $fecha_diff_periodo_exacto .= __('until') . ' ' . Utiles::sql3fecha($this->fields['fecha_fin'], '%m-%d-%Y');
                }

                if ($fecha_inicial_primer_trabajo != '' && $fecha_inicial_primer_trabajo != '0000-00-00') {

                    if (Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%Y') == Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%Y')) {
                        $texto_fecha_en = __('between') . ' ' . ucfirst(date('F', strtotime($fecha_inicial_primer_trabajo))) . ' ' . __('and') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                    } else {
                        $texto_fecha_en = __('between') . ' ' . ucfirst(date('F Y', strtotime($fecha_inicial_primer_trabajo))) . ' ' . __('and') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                    }
                } else {
                    $texto_fecha_en = __('until') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                }

                if ($lang == 'es') {
                    $fecha_primer_trabajo = $datediff > 0 && $datediff < 48 ? $texto_fecha_es : __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B %Y'));
                } else {
                    $fecha_primer_trabajo = $datediff > 0 && $datediff < 48 ? $texto_fecha_en : __('during') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                }

                if ($fecha_primer_trabajo == 'No existe fecha' && $lang == es) {
                    $fecha_primer_trabajo = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %Y'));
                }

                if ($lang == 'es') {
                    $fecha_primer_trabajo_de = $datediff > 0 && $datediff < 48 ? $texto_fecha_es_de : __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y'));
                } else {
                    $fecha_primer_trabajo_de = $datediff > 0 && $datediff < 48 ? $texto_fecha_en : __('during') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                }

                if ($fecha_primer_trabajo_de == 'No existe fecha' && $lang == es) {
                    $fecha_primer_trabajo_de = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %Y'));
                }

                if ($this->fields['opc_moneda_total'] != $this->fields['id_moneda']) {
                    $html2 = str_replace('%equivalente_dolm%', ' que ascienden a %monto%', $html2);
                } else {
                    $html2 = str_replace('%equivalente_dolm%', '', $html2);
                }

                $fecha_diff_primer_gasto = ucfirst(Utiles::sql3fecha($fecha_primer_gasto, '%d-%m-%Y'));
                $fecha_diff_ultimo_gasto = ucfirst(Utiles::sql3fecha($fecha_ultimo_gasto, '%d-%m-%Y'));

                $fecha_diff_primer_trabajo = Utiles::sql3fecha($this->fields['fecha_ini'], '%d-%m-%Y');
                $fecha_diff_ultimo_trabajo = Utiles::sql3fecha($this->fields['fecha_fin'], '%d-%m-%Y');

                if (($this->fields['incluye_honorarios'] == '0') && $this->fields['fecha_ini'] == '0000-00-00') {
                    $html2 = str_replace('%fecha_inicial_periodo_exacto%', $fecha_diff_primer_gasto, $html2);
                    $html2 = str_replace('%fecha_fin_periodo_exacto%', $fecha_diff_ultimo_gasto, $html2);
                } else {
                    $html2 = str_replace('%fecha_inicial_periodo_exacto%', $fecha_diff_primer_trabajo, $html2);
                    $html2 = str_replace('%fecha_fin_periodo_exacto%', $fecha_diff_ultimo_trabajo, $html2);
                }

                $query = "SELECT CONCAT_WS (' ',prm_documento_legal.codigo,CONCAT_WS('-',CONCAT('00',factura.serie_documento_legal),factura.numero)) as documentos
							FROM factura
							LEFT JOIN prm_documento_legal ON factura.id_documento_legal = prm_documento_legal.id_documento_legal
								WHERE id_cobro = '" . $this->fields['id_cobro'] . "' AND anulado != 1";

                $result = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);

                while ($data = mysql_fetch_assoc($result)) {
                    $documentos_relacionados[] = $data;
                }

                $documentos_rel = '';

                for ($k = 0; $k < count($documentos_relacionados); $k++) {
                    $espace = $k < count($documentos_relacionados) - 1 ? ', ' : '';
                    $documentos_rel .= $documentos_relacionados[$k]['documentos'] . '' . $espace;
                }

                $html2 = str_replace('%documentos_relacionados%', $documentos_rel, $html2);

                $html2 = str_replace('%factura_razon_social_ucfirst%', ucfirst($contrato->fields['factura_razon_social']), $html2);
                $html2 = str_replace('%num_factura%', $this->fields['documento'], $html2);
                $html2 = str_replace('%n_num_factura%', 'N�' . $this->fields['documento'], $html2);
                $html2 = str_replace('%fecha_primer_trabajo%', $fecha_primer_trabajo, $html2);
                $html2 = str_replace('%fecha_primer_trabajo_de%', $fecha_primer_trabajo_de, $html2);
                $html2 = str_replace('%fecha%', $fecha_diff, $html2);
                $html2 = str_replace('%fecha_al%', $fecha_al, $html2);
                $html2 = str_replace('%fecha_al_minuscula%', strtolower($fecha_al), $html2);
                $html2 = str_replace('%fecha_con_de%', $fecha_diff_con_de, $html2);
                $html2 = str_replace('%fecha_con_prestada%', $fecha_diff_prestada, $html2);
                $html2 = str_replace('%fecha_con_prestada_mayuscula%', mb_strtoupper($fecha_diff_prestada), $html2);
                $html2 = str_replace('%fecha_con_prestada_minusculas%', strtolower($fecha_diff_prestada), $html2);
                $html2 = str_replace('%fecha_emision%', $this->fields['fecha_emision'] ? Utiles::sql2fecha($this->fields['fecha_emision'], '%d de %B') : Utiles::sql2fecha($this->fields['fecha_fin'], '%d de %B'), $html2);
                $html2 = str_replace('%monto_total_demo_uf%', number_format($monto_moneda_demo, $cobro_moneda->moneda[3]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . $cobro_moneda->moneda[3]['simbolo'], $html2);
                $html2 = str_replace('%fecha_periodo_exacto%', $fecha_diff_periodo_exacto, $html2);
                $html2 = str_replace('%monto_total_demo_jdf%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . number_format($monto_moneda_demo, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);

                if ($this->fields['monto_gastos'] > 0 && $this->fields['monto_subtotal'] == 0) {
                    $html2 = str_replace('%detalle_ebmo%', __('%detalle_ebmo_solo_gastos%'), $html2);
                    $html2 = str_replace('%monto_honorarios_cuando_hay%', '', $html2);
                    $html2 = str_replace('%concepto_honorarios_cuando_hay%', '', $html2);
                    $html2 = str_replace('%monto_gastos_cuando_hay%', '%monto_gasto%', $html2);
                    $html2 = str_replace('%concepto_gastos_cuando_hay%', __('por_concepto_de_gastos'), $html2);
                } else if ($this->fields['monto_gastos'] == 0 && $this->fields['monto_subtotal'] > 0) {

                    $html2 = str_replace('%detalle_ebmo%', __('%detalle_ebmo_solo_honorarios%'), $html2);
                    $html2 = str_replace('%monto_gastos_cuando_hay%', '', $html2);
                    $html2 = str_replace('%concepto_gastos_cuando_hay%', '', $html2);
                    $html2 = str_replace('%monto_honorarios_cuando_hay%', '%monto_sin_gasto%', $html2);
                    $html2 = str_replace('%concepto_honorarios_cuando_hay%', __('por_concepto_de_honorarios'), $html2);
                } else {
                    $html2 = str_replace('%detalle_ebmo%', __('%detalle_ebmo%'), $html2);
                    $html2 = str_replace('%monto_honorarios_cuando_hay%', '%monto_sin_gasto%', $html2);
                    $html2 = str_replace('%concepto_honorarios_cuando_hay%', __('por_concepto_de_honorarios') . ' ' . __('y') . ' ', $html2);
                    $html2 = str_replace('%monto_gastos_cuando_hay%', '%monto_gasto%', $html2);
                    $html2 = str_replace('%concepto_gastos_cuando_hay%', __('por_concepto_de_gastos'), $html2);
                }

                $fecha_dia_carta = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%d de %B de %Y'));
                $html2 = str_replace('%fecha_dia_carta%', $fecha_dia_carta, $html2);

                $monto_honorarios = UtilesApp::CambiarMoneda(
                    $this->fields['monto'],
                    $cobro_moneda->moneda[$this->fields['id_moneda']]['tipo_cambio'],
                    $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'],
                    $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['tipo_cambio'],
                    $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales']
                );

                $html2 = str_replace('%monto%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_solo_gastos%', '$ ' . number_format($gasto_en_pesos, 0, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_sin_gasto%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda_sin_gasto, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_total_demo%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda_demo, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_con_gasto%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda_con_gasto, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_original%', $moneda->fields['simbolo'] . ' ' . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_total_sin_iva%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda_subtotal, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_honorarios%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_honorarios, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_total_espacio%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda_demo, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_total_glosa_moneda%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['glosa_moneda_plural'] . $this->espacio . $this->espacio . number_format($monto_moneda_demo, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);

                if ($this->fields['opc_moneda_total'] != $this->fields['id_moneda']) {
                    $html2 = str_replace('%equivalente_a_baz%', __(', equivalentes a ') . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                } else {
                    $html2 = str_replace('%equivalente_a_baz%', '', $html2);
                }

                $html2 = str_replace('%simbolo_moneda%', $cobro_moneda->moneda[$this->fields['id_moneda']]['simbolo'], $html2);
                $html2 = str_replace('%simbolo_moneda_total%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'], $html2);

                if ($this->fields['tipo_cambio_moneda_base'] <= 0) {
                    $tipo_cambio_moneda_base_cobro = 1;
                } else {
                    $tipo_cambio_moneda_base_cobro = $this->fields['tipo_cambio_moneda_base'];
                }

                $fecha_hasta_cobro = strftime(Utiles::FormatoStrfTime('%e de %B'), mktime(0, 0, 0, date("m", strtotime($this->fields['fecha_fin'])), date("d", strtotime($this->fields['fecha_fin'])), date("Y", strtotime($this->fields['fecha_fin']))));
                $html2 = str_replace('%fecha_hasta%', $fecha_hasta_cobro, $html2);

                $fecha_hasta_dmy = strftime(Utiles::FormatoStrfTime('%e de %B del %Y'), strtotime($this->fields['fecha_fin']));
                $html2 = str_replace('%fecha_hasta_dmy%', $fecha_hasta_dmy, $html2);

                if ($this->fields['id_moneda'] > 1 && $moneda_total->fields['id_moneda'] > 1) { #!= $moneda_cli->fields['id_moneda']
                    $en_pesos = (double) $this->fields['monto'] * ($this->fields['tipo_cambio_moneda'] / $tipo_cambio_moneda_base_cobro);
                    $html2 = str_replace('%monto_en_pesos%', __(', equivalentes a esta fecha a $ ') . number_format($en_pesos, 0, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2);
                } else {
                    $html2 = str_replace('%monto_en_pesos%', '', $html2);
                }

                //	Si hay gastos se muestran
                if ($total_gastos > 0) {
                    $gasto_en_pesos = $total_gastos;
                    $txt_gasto = "Asimismo, se agregan los gastos por la suma total de";
                    $html2 = str_replace('%monto_gasto_separado%', $txt_gasto . ' $' . number_format($gasto_en_pesos, 0, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                } else {
                    $html2 = str_replace('%monto_gasto_separado%', '', $html2);
                }

                $query = "SELECT count(*) FROM cta_corriente WHERE id_cobro = '" . $this->fields['id_cobro'] . "'";
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                list($cantidad_de_gastos) = mysql_fetch_array($resp);

                if (( $this->fields['monto_gastos'] > 0 || $cantidad_de_gastos > 0 ) && $this->fields['opc_ver_gastos']) {
                    // Calculo especial para BAZ, en ves de mostrar el total de gastos, se muestra la cuenta corriente al d�a
                    $where_gastos = " 1 ";
                    $lista_asuntos = implode(',', $this->asuntos);

                    if (!empty($lista_asuntos)) {
                        $where_gastos .= " AND cta_corriente.codigo_asunto IN ('$lista_asuntos') ";
                    }

                    $where_gastos .= " AND cta_corriente.codigo_cliente = '" . $this->fields['codigo_cliente'] . "' ";
                    $where_gastos .= " AND cta_corriente.fecha <= '" . $this->fields['fecha_fin'] . "' ";
                    $cuenta_corriente_actual = number_format(UtilesApp::TotalCuentaCorriente($this->sesion, $where_gastos), $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']);

                    $html2 = str_replace('%frase_gastos_ingreso%', '<tr>
												    					<td width="5%">&nbsp;</td>
																		<td align="left" class="detalle"><p>Adjunto a la presente encontrar�s comprobantes de gastos realizados por cuenta de ustedes por la suma de ' . $cuenta_corriente_actual . '</p></td>
																		<td width="5%">&nbsp;</td>
												  					</tr>
												  					<tr>
												    					<td>&nbsp;</td>
												    					<td valign="top" align="left" class="detalle"><p>&nbsp;</p></td>
												  					</tr>', $html2);
                    $html2 = str_replace('%frase_gastos_egreso%', '<tr>
																		<td width="5%">&nbsp;</td>
																		<td valign="top" align="left" class="detalle"><p>A mayor abundamiento, les recordamos que a esta fecha <u>existen cobros de notar�a por la suma de $xxxxxx.-</u>, la que les agradecer� enviar en cheque nominativo a la orden de don Eduardo Avello Concha.</p></td>
																		<td width="5%">&nbsp;</td>
																	</tr>
																	<tr>
												    					<td>&nbsp;</td>
												    					<td valign="top" align="left" class="vacio"><p>&nbsp;</p></td>
																		<td>&nbsp;</td>
												  					</tr>', $html2);
                } else {
                    $html2 = str_replace('%frase_gastos_ingreso%', '', $html2);
                    $html2 = str_replace('%frase_gastos_egreso%', '', $html2);
                }

                if ($total_gastos > 0) {
                    $html2 = str_replace('%monto_gasto%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                } else {
                    $html2 = str_replace('%monto_gasto%', $moneda_total->fields['simbolo'] . $this->espacio . number_format(0, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                }

                $html2 = str_replace('%monto_gasto_separado_baz%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($this->fields['saldo_final_gastos'], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%num_letter%', $this->fields['id_cobro'], $html2);
                $html2 = str_replace('%num_letter_documento%', $this->fields['documento'], $html2);
                $html2 = str_replace('%num_letter_baz%', $this->fields['documento'], $html2);

                $query = "SELECT factura.numero as documentos
							FROM factura
							LEFT JOIN prm_documento_legal ON factura.id_documento_legal = prm_documento_legal.id_documento_legal
								WHERE id_cobro = '" . $this->fields['id_cobro'] . "' AND anulado != 1";

                $result = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);

                while ($data = mysql_fetch_assoc($result)) {
                    $documentos_relacionados[] = $data;
                }

                $documentos_rel = '';

                for ($k = 0; $k < count($documentos_relacionados); $k++) {
                    $espace = $k < count($documentos_relacionados) - 1 ? ', ' : '';
                    $documentos_rel .= $documentos_relacionados[$k]['documentos'] . '' . $espace;
                }

                $html2 = str_replace('%doc_tributario%', $documentos_rel, $html2);

                if (($this->fields['documento'] != '')) {
                    $html2 = str_replace('%num_letter_rebaza%', __('la factura N�') . ' ' . $this->fields['documento'], $html2);
                } else {
                    $html2 = str_replace('%num_letter_rebaza%', __('el cobro N�') . ' ' . $this->fields['id_cobro'], $html2);
                }

                $html2 = str_replace('%si_gastos%', $total_gastos > 0 ? __('y gastos') : '', $html2);

                $detalle_cuenta_honorarios = '(i) ' . $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda_sin_gasto, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');

                if ($this->fields['id_moneda'] == 2 && $moneda_total->fields['id_moneda'] == 1) {

                    $detalle_cuenta_honorarios .= ' (';

                    if ($this->fields['forma_cobro'] == 'FLAT FEE') {
                        $detalle_cuenta_honorarios .= __('retainer ');
                    }

                    $detalle_cuenta_honorarios .= __('equivalente en pesos a ') . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']);

                    if ($this->fields['id_moneda'] == 2) {
                        $detalle_cuenta_honorarios .= __(', conforme al tipo de cambio observado') . ')';
                    } else {
                        $detalle_cuenta_honorarios .= __(', conforme al tipo de cambio observado del d�a de hoy') . ')';
                    }

                    $detalle_cuenta_honorarios_primer_dia_mes = '';

                    if ($this->fields['monto_subtotal'] > 0) {

                        if ($this->fields['monto_gastos'] > 0) {

                            if ($this->fields['monto'] == round($this->fields['monto'])) {
                                $detalle_cuenta_honorarios_primer_dia_mes .= __('. Esta cantidad corresponde a') . __(' (i) ') . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], 0, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');
                            } else {
                                $detalle_cuenta_honorarios_primer_dia_mes .= __('. Esta cantidad corresponde a') . __(' (i) ') . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');
                            }
                        } else {
                            $detalle_cuenta_honorarios_primer_dia_mes .= ' ' . __('correspondiente a') . ' ' . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');
                        }

                        $detalle_cuenta_honorarios_primer_dia_mes .= ' ( ' . __('conforme a su equivalencia en peso seg�n el D�lar Observado publicado por el Banco Central de Chile, el primer d�a h�bil del presente mes') . ' )';
                    }
                }

                if ($this->fields['id_moneda'] == 3 && $moneda_total->fields['id_moneda'] == 1) {

                    $detalle_cuenta_honorarios .= ' (';

                    if ($this->fields['forma_cobro'] == 'FLAT FEE') {
                        $detalle_cuenta_honorarios .= __('retainer ');
                    }

                    $detalle_cuenta_honorarios .= $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']);
                    $detalle_cuenta_honorarios .= __(', conforme a su equivalencia al ');
                    $detalle_cuenta_honorarios .= $lang == 'es' ? Utiles::sql3fecha($this->fields['fecha_fin'], '%d de %B de %Y') : Utiles::sql3fecha($this->fields['fecha_fin'], '%m-%d-%Y');
                    $detalle_cuenta_honorarios .= ')';
                    $detalle_cuenta_honorarios_primer_dia_mes = '';

                    if ($this->fields['monto_subtotal'] > 0) {

                        if ($this->fields['monto_gastos'] > 0) {

                            if ($this->fields['monto'] == round($this->fields['monto'])) {
                                $detalle_cuenta_honorarios_primer_dia_mes = __('. Esta cantidad corresponde a') . __(' (i) ') . $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . number_format($monto_moneda_sin_gasto, 0, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');
                            } else {
                                $detalle_cuenta_honorarios_primer_dia_mes = __('. Esta cantidad corresponde a') . __(' (i) ') . $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . number_format($monto_moneda_sin_gasto, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');
                            }
                        }

                        $detalle_cuenta_honorarios_primer_dia_mes .= ' (' . __('equivalente a') . ' ' . $moneda->fields['simbolo'] . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']);
                        $detalle_cuenta_honorarios_primer_dia_mes .= __(', conforme a su equivalencia en pesos al primer d�a h�bil del presente mes') . ')';
                    }
                }

                $boleta_honorarios = __('seg�n Boleta de Honorarios adjunta');

                if ($total_gastos != 0) {

                    if ($this->fields['monto_subtotal'] > 0) {
                        $detalle_cuenta_gastos = __('; m�s') . ' (ii) ' . $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de gastos incurridos por nuestro Estudio en dicho per�odo');
                        $detalle_cuenta_gastos_cl_boleta = __('; m�s') . ' (ii) Boleta de Recuperaci�n de Gastos adjunta por ' . $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.';
                    } else {
                        $detalle_cuenta_gastos = __(' por concepto de gastos incurridos por nuestro Estudio en dicho per�odo');
                        $detalle_cuenta_gastos_cl_boleta = ".";
                    }

                    $boleta_gastos = __('; m�s') . ' (ii) ' . $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por gastos a reembolsar') . __(', seg�n Boleta de Recuperaci�n de Gastos adjunta');
                    $detalle_cuenta_gastos2 = __('; m�s') . ' (ii) CH' . $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de gastos incurridos por nuestro Estudio');
                }

                $html2 = str_replace('%boleta_honorarios%', $boleta_honorarios, $html2);
                $html2 = str_replace('%boleta_gastos%', $boleta_gastos, $html2);
                $html2 = str_replace('%detalle_cuenta_honorarios%', $detalle_cuenta_honorarios, $html2);
                $html2 = str_replace('%detalle_cuenta_honorarios_primer_dia_mes%', $detalle_cuenta_honorarios_primer_dia_mes, $html2);
                $html2 = str_replace('%detalle_cuenta_gastos%', $detalle_cuenta_gastos, $html2);
                $html2 = str_replace('%detalle_cuenta_gastos2%', $detalle_cuenta_gastos2, $html2);
                $html2 = str_replace('%detalle_cuenta_gastos_cl_boleta%', $detalle_cuenta_gastos_cl_boleta, $html2);

                $query = "SELECT CONCAT_WS(' ',usuario.nombre,usuario.apellido1,usuario.apellido2) as nombre_encargado
							FROM usuario
								JOIN contrato ON usuario.id_usuario=contrato.id_usuario_responsable
							 	JOIN cobro ON contrato.id_contrato=cobro.id_contrato
									 WHERE cobro.id_cobro=" . $this->fields['id_cobro'];

                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                list($nombre_encargado) = mysql_fetch_array($resp);

                $html2 = str_replace('%encargado_comercial%', $nombre_encargado, $html2);
                $html2 = str_replace('%encargado_comercial_uc%', ucwords(strtolower($nombre_encargado)), $html2);

                if ($contrato->fields['id_cuenta'] > 0) {
                    $query = "	SELECT b.nombre, cb.numero, cb.cod_swift, cb.CCI, cb.glosa, m.glosa_moneda, cb.aba, cb.clabe
								FROM cuenta_banco cb
								LEFT JOIN prm_banco b ON b.id_banco = cb.id_banco
								LEFT JOIN prm_moneda m ON cb.id_moneda = m.id_moneda
								WHERE cb.id_cuenta = '" . $contrato->fields['id_cuenta'] . "'";
                    $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                    list($glosa_banco, $numero_cuenta, $codigo_swift, $codigo_cci, $glosa_cuenta, $glosa_moneda, $codigo_aba, $codigo_clabe) = mysql_fetch_array($resp);

                    if (strpos($glosa_cuenta, 'Ah') !== false) {
                        $tipo_cuenta = 'Cuenta Ahorros';
                    } else if (strpos($glosa_cuenta, 'Cte') !== false) {
                        $tipo_cuenta = 'Cuenta Corriente';
                    }

                    if (!empty($codigo_swift)) {
                        $html2 = str_replace('%glosa_swift%', 'SWIFT', $html2);
                    } else {
                        $html2 = str_replace('%glosa_swift%', '', $html2);
                    }

                    $html2 = str_replace('%numero_cuenta_contrato%', $numero_cuenta, $html2);
                    $html2 = str_replace('%glosa_banco_contrato%', $glosa_banco, $html2);
                    $html2 = str_replace('%glosa_cuenta_contrato%', $glosa_cuenta, $html2);
                    $html2 = str_replace('%codigo_swift%', $codigo_swift, $html2);
                    $html2 = str_replace('%codigo_cci%', $codigo_cci, $html2);
                    $html2 = str_replace('%codigo_aba%', $codigo_aba, $html2);
                    $html2 = str_replace('%codigo_clabe%', $codigo_clabe, $html2);
                    $html2 = str_replace('%tipo_cuenta%', $tipo_cuenta, $html2);
                    $html2 = str_replace('%glosa_moneda%', $glosa_moneda, $html2);

                    $datos_bancarios = '';

                    if (!empty($codigo_swift)) {
                        $datos_bancarios .= '<tr><td class="detalle"></td><td class="detalle">SWIFT</td><td class="detalle">' . $codigo_swift . '</td></tr>';
                    }
                    if (!empty($codigo_aba)) {
                        $datos_bancarios .= '<tr><td class="detalle"></td><td class="detalle">ABA</td><td class="detalle">' . $codigo_aba . '</td></tr>';
                    }
                    if (!empty($codigo_cci)) {
                        $datos_bancarios .= '<tr><td class="detalle"></td><td class="detalle">CCI</td><td class="detalle">' . $codigo_cci . '</td></tr>';
                    }
                    if (!empty($codigo_clabe)) {
                        $datos_bancarios .= '<tr><td class="detalle"></td><td class="detalle">CLABE</td><td class="detalle">' . $codigo_clabe . '</td></tr>';
                    }

                    $html2 = str_replace('%datos_bancarios%', $datos_bancarios, $html2);
                } else {
                    $html2 = str_replace('%numero_cuenta_contrato%', '', $html2);
                    $html2 = str_replace('%glosa_banco_contrato%', '', $html2);
                    $html2 = str_replace('%glosa_cuenta_contrato%', '', $html2);
                    $html2 = str_replace('%codigo_swift%', '', $html2);
                    $html2 = str_replace('%codigo_cci%', '', $html2);
                    $html2 = str_replace('%codigo_aba%', '', $html2);
                    $html2 = str_replace('%codigo_clabe%', '', $html2);
                    $html2 = str_replace('%tipo_cuenta%', '', $html2);
                    $html2 = str_replace('%glosa_moneda%', '', $html2);
                }

                // FIN cuenta segun contrato

                break;
        }

        return $html2;
    }

    function GenerarDocumentoCarta2($parser_carta, $theTag = '', $lang, $moneda_cliente_cambio, $moneda_cli, & $idioma, $moneda, $moneda_base, $trabajo, & $profesionales, $gasto, & $totales, $tipo_cambio_moneda_total, $cliente, $id_carta) {
        global $id_carta;
        global $contrato;
        global $cobro_moneda;
        global $moneda_total;
        global $x_resultados;
        global $x_cobro_gastos;
        global $moneda_cobro;

        if (!isset($parser_carta->tags[$theTag])) {
            return;
        }

        $html2 = $parser_carta->tags[$theTag];

        $_codigo_asunto_secundario = Conf::GetConf($this->sesion, 'CodigoSecundario');

        switch ($theTag) {
            case 'CARTA': //GenerarDocumentoCarta2

                if (method_exists('Conf', 'GetConf')) {
                    $PdfLinea1 = Conf::GetConf($this->sesion, 'PdfLinea1');
                    $PdfLinea2 = Conf::GetConf($this->sesion, 'PdfLinea2');
                    $PdfLinea3 = Conf::GetConf($this->sesion, 'PdfLinea3');
                } else {
                    $PdfLinea1 = Conf::PdfLinea1();
                    $PdfLinea2 = Conf::PdfLinea2();
                    $PdfLinea3 = Conf::PdfLinea3();
                }

                if (strpos($html2, '%cuenta_banco%')) {
                    if ($contrato->fields['id_cuenta']) {
                        $query_banco = "SELECT glosa FROM cuenta_banco WHERE id_cuenta = '" . $contrato->fields['id_cuenta'] . "'";
                        $resp = mysql_query($query_banco, $this->sesion->dbh) or Utiles::errorSQL($query_banco, __FILE__, __LINE__, $this->sesion->dbh);
                        list($glosa_cuenta) = mysql_fetch_array($resp);
                    } else {
                        $glosa_cuenta = '';
                    }
                    $html2 = str_replace('%cuenta_banco%', $glosa_cuenta, $html2);
                }

                $html2 = str_replace('%logo_carta%', Conf::Server() . Conf::ImgDir(), $html2);
                $html2 = str_replace('%direccion%', $PdfLinea1, $html2);
                $html2 = str_replace('%titulo%', $PdfLinea1, $html2);
                $html2 = str_replace('%subtitulo%', $PdfLinea2, $html2);
                $html2 = str_replace('%numero_cobro%', $this->fields['id_cobro'], $html2);
                $html2 = str_replace('%xfecha_mes_dos_digitos%', date("m", strtotime($this->fields['fecha_emision'])), $html2);
                $html2 = str_replace('%xfecha_ano_dos_digitos%', date("y", strtotime($this->fields['fecha_emision'])), $html2);
                $html2 = str_replace('%xnro_factura%', $this->fields['id_cobro'], $html2);

                $html2 = str_replace(array('%xnombre_cliente%', '%glosa_cliente%'), $contrato->fields['factura_razon_social'], $html2); #glosa cliente de factura

                $html2 = str_replace('%xdireccion%', nl2br($contrato->fields['factura_direccion']), $html2);
                $html2 = str_replace('%xrut%', $contrato->fields['rut'], $html2);
                $html2 = str_replace('%FECHA%', $this->GenerarDocumentoCartaComun($parser_carta, 'FECHA', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%ENVIO_DIRECCION%', $this->GenerarDocumentoCartaComun($parser_carta, 'ENVIO_DIRECCION', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%DETALLE%', $this->GenerarDocumentoCarta2($parser_carta, 'DETALLE', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%ADJ%', $this->GenerarDocumentoCartaComun($parser_carta, 'ADJ', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%PIE%', $this->GenerarDocumentoCartaComun($parser_carta, 'PIE', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%DATOS_CLIENTE%', $this->GenerarDocumentoCartaComun($parser_carta, 'DATOS_CLIENTE', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%SALTO_PAGINA%', $this->GenerarDocumentoCartaComun($parser_carta, 'SALTO_PAGINA', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);

                break;

            case 'DETALLE': //GenerarDocumentoCarta2

                if (strpos($html2, '%cuenta_banco%')) {

                    if ($contrato->fields['id_cuenta']) {
                        $query_banco = "SELECT glosa FROM cuenta_banco WHERE id_cuenta = '" . $contrato->fields['id_cuenta'] . "'";
                        $resp = mysql_query($query_banco, $this->sesion->dbh) or Utiles::errorSQL($query_banco, __FILE__, __LINE__, $this->sesion->dbh);
                        list($glosa_cuenta) = mysql_fetch_array($resp);
                    } else {
                        $glosa_cuenta = '';
                    }

                    $html2 = str_replace('%cuenta_banco%', $glosa_cuenta, $html2);
                }

                if (isset($contrato->fields['glosa_contrato'])) {
                    $html2 = str_replace('%glosa_contrato%', $contrato->fields['glosa_contrato'], $html2);
                } else {
                    $html2 = str_replace('%glosa_contrato%', '', $html2);
                }

                if (isset($contrato->fields['codigopropuesta'])) {
                    $html2 = str_replace('%codigopropuesta%', $contrato->fields['codigopropuesta'], $html2);
                } else {
                    $html2 = str_replace('%codigopropuesta%', '', $html2);
                }

                if (UtilesApp::GetConf($this->sesion, 'TituloContacto')) {
                    $html2 = str_replace('%NombreContacto%', $contrato->fields['contacto'] . ' ' . $contrato->fields['apellido_contacto'], $html2);

                    $html2 = str_replace('%NombreContacto_mayuscula%', mb_strtoupper($contrato->fields['contacto'] . ' ' . $contrato->fields['apellido_contacto']), $html2);
                } else {
                    $html2 = str_replace('%NombreContacto%', $contrato->fields['contacto'], $html2);
                    $html2 = str_replace('%NombreContacto_mayuscula%', mb_strtoupper($contrato->fields['contacto']), $html2);
                }

                $html2 = str_replace('%logo_carta%', Conf::Server() . Conf::ImgDir(), $html2);
                $html2 = str_replace('%glosa_cliente%', $contrato->fields['factura_razon_social'], $html2);
                $html2 = str_replace('%factura_razon_social_ucfirst%', ucfirst($contrato->fields['factura_razon_social']), $html2);
                $html2 = str_replace('%nombre_del_cliente%', $cliente->fields['glosa_cliente'], $html2);
                $html2 = str_replace('%rut_cliente%', $contrato->fields['rut'], $html2);
                $html2 = str_replace('%glosa_cliente_mayuscula%', strtoupper($contrato->fields['factura_razon_social']), $html2);
                $html2 = str_replace('%num_letter%', $this->fields['id_cobro'], $html2);
                $html2 = str_replace('%num_factura%', $this->fields['documento'], $html2);
                $html2 = str_replace('%solo_num_factura%', ereg_replace("[^0-9]", "", $this->fields['documento']), $html2);
                $html2 = str_replace('%saludo_mb%', __('%saludo_mb%'), $html2);

                $query = "SELECT factura.numero as documentos
							FROM factura
							LEFT JOIN prm_documento_legal ON factura.id_documento_legal = prm_documento_legal.id_documento_legal
								WHERE id_cobro = '" . $this->fields['id_cobro'] . "' AND anulado != 1";

                $result = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);

                while ($data = mysql_fetch_assoc($result)) {
                    $documentos_relacionados[] = $data;
                }

                $documentos_rel = '';

                for ($k = 0; $k < count($documentos_relacionados); $k++) {
                    $espace = $k < count($documentos_relacionados) - 1 ? ', ' : '';
                    $documentos_rel .= $documentos_relacionados[$k]['documentos'] . '' . $espace;
                }

                $html2 = str_replace('%doc_tributario%', $documentos_rel, $html2);

                if (count($this->asuntos) > 1) {
                    $html2 = str_replace('%detalle_mb%', __('%detalle_mb_asuntos%'), $html2);
                    $html2 = str_replace('%detalle_mb_ny%', __('%detalle_mb_ny_asuntos%'), $html2);
                    $html2 = str_replace('%detalle_mb_boleta%', __('%detalle_mb_boleta_asuntos%'), $html2);
                } else {
                    $html2 = str_replace('%detalle_mb_ny%', __('%detalle_mb_ny%'), $html2);
                    $html2 = str_replace('%detalle_mb_boleta%', __('%detalle_mb_boleta%'), $html2);
                    if ($this->fields['monto_gastos'] > 0 && $this->fields['monto'] == 0) {
                        $html2 = str_replace('%detalle_mb%', __('%detalle_mb_gastos%'), $html2);
                        $html2 = str_replace('%cuenta_mb%', __('%cuenta_mb%'), $html2);
                    } else {
                        $html2 = str_replace('%detalle_mb%', __('%detalle_mb%'), $html2);
                        $html2 = str_replace('%cuenta_mb%', '', $html2);
                    }
                }

                $this->LoadGlosaAsuntos();
                $lista_asuntos = "<ul>";

                foreach ($this->glosa_asuntos as $key => $asunto) {
                    $lista_asuntos .= "<li>" . $asunto . "</li>";
                }

                $lista_asuntos .= "</ul>";
                $html2 = str_replace('%lista_asuntos%', $lista_asuntos, $html2);

                $lista_asuntos_guion = implode(" - ", $this->glosa_asuntos);

                $html2 = str_replace('%lista_asuntos_guion%', $lista_asuntos_guion, $html2);
                $html2 = str_replace('%FILAS_ASUNTOS_RESUMEN%', $this->GenerarDocumentoCartaComun($parser_carta, 'FILAS_ASUNTOS_RESUMEN', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%FILAS_FACTURAS_DEL_COBRO%', $this->GenerarDocumentoCartaComun($parser_carta, 'FILAS_FACTURAS_DEL_COBRO', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                $html2 = str_replace('%FILA_FACTURAS_PENDIENTES%', $this->GenerarDocumentoCartaComun($parser_carta, 'FILA_FACTURAS_PENDIENTES', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);


                $html2 = str_replace('%despedida_mb%', __('%despedida_mb%'), $html2);
                $html2 = str_replace('%cuenta_mb_ny%', __('%cuenta_mb_ny%'), $html2);
                $html2 = str_replace('%cuenta_mb_boleta%', __('%cuenta_mb_boleta%'), $html2);
                $html2 = str_replace('%detalle_careyallende%', __('%detalle_careyallende%'), $html2);

                if ($this->fields['monto_gastos'] > 0 && $this->fields['monto_subtotal'] == 0) {
                    $html2 = str_replace('%detalle_ebmo%', __('%detalle_ebmo_solo_gastos%'), $html2);
                    $html2 = str_replace('%monto_honorarios_cuando_hay%', '', $html2);
                    $html2 = str_replace('%concepto_honorarios_cuando_hay%', '', $html2);
                    $html2 = str_replace('%monto_gastos_cuando_hay%', '%monto_gasto%', $html2);
                    $html2 = str_replace('%concepto_gastos_cuando_hay%', __('por_concepto_de_gastos'), $html2);
                } else if ($this->fields['monto_gastos'] == 0 && $this->fields['monto_subtotal'] > 0) {
                    $html2 = str_replace('%detalle_ebmo%', __('%detalle_ebmo_solo_honorarios%'), $html2);
                    $html2 = str_replace('%monto_gastos_cuando_hay%', '', $html2);
                    $html2 = str_replace('%concepto_gastos_cuando_hay%', '', $html2);
                    $html2 = str_replace('%monto_honorarios_cuando_hay%', '%monto_sin_gasto%', $html2);
                    $html2 = str_replace('%concepto_honorarios_cuando_hay%', __('por_concepto_de_honorarios'), $html2);
                } else {
                    $html2 = str_replace('%detalle_ebmo%', __('%detalle_ebmo%'), $html2);
                    $html2 = str_replace('%monto_honorarios_cuando_hay%', '%monto_sin_gasto%', $html2);
                    $html2 = str_replace('%concepto_honorarios_cuando_hay%', __('por_concepto_de_honorarios') . ' ' . __('y') . ' ', $html2);
                    $html2 = str_replace('%monto_gastos_cuando_hay%', '%monto_gasto%', $html2);
                    $html2 = str_replace('%concepto_gastos_cuando_hay%', __('por_concepto_de_gastos'), $html2);
                }

                if (Conf::GetConf($this->sesion, 'TituloContacto')) {
                    $html2 = str_replace('%sr%', __($contrato->fields['titulo_contacto']), $html2);
                    $html2 = str_replace('%NombrePilaContacto%', $contrato->fields['contacto'], $html2);
                    $html2 = str_replace('%ApellidoContacto%', $contrato->fields['apellido_contacto'], $html2);
                } else {
                    $html2 = str_replace('%sr%', __('Se�or'), $html2);
                    $NombreContacto = explode(' ', $contrato->fields['contacto']);
                    $html2 = str_replace('%NombrePilaContacto%', $NombreContacto[0], $html2);
                    $html2 = str_replace('%ApellidoContacto%', $NombreContacto[1], $html2);
                }

                $html2 = str_replace('%glosa_cliente%', $contrato->fields['factura_razon_social'], $html2);

                if (strtolower($contrato->fields['titulo_contacto']) == 'sra.' || strtolower($contrato->fields['titulo_contacto']) == 'srta.') {
                    $html2 = str_replace('%estimado%', __('Estimada'), $html2);
                } else {
                    $html2 = str_replace('%estimado%', __('Estimado'), $html2);
                }

                if ($cobro_moneda->moneda[$this->fields['opc_moneda_total']]['codigo'] == 'USD') {
                    $html2 = str_replace('%cta_cte_gbp_segun_moneda%', __('194-1861108179'), $html2);
                    $html2 = str_replace('%tipo_gbp_segun_moneda%', __('Extranjera'), $html2);
                } else {
                    $html2 = str_replace('%cta_cte_gbp_segun_moneda%', __('194-1847085-0-23'), $html2);
                    $html2 = str_replace('%tipo_gbp_segun_moneda%', __('Nacional'), $html2);
                }

                /* VALOR DEL PORCENTAGE DE IMPUESTOS */
                $html2 = str_replace('%porcentaje_impuesto%', (int) ($this->fields['porcentaje_impuesto']) . '%', $html2);
                $html2 = str_replace('%porcentaje_impuesto_sin_simbolo%', (int) ($this->fields['porcentaje_impuesto']), $html2);

                /* TOTAL GASTOS
                 * 	EL TOTAL DE GASTOS SE SUMA CUANDO EL IDIOMA ES INGLES
                 * 	EL TOTAL DE GASTOS SE PRESENTA SEPARADAMENTE CUANDO EL IDIOMA ES INGLES
                 */

                $total_gastos = 0;
                $total_gastos_balance = 0;

                $query = "SELECT SQL_CALC_FOUND_ROWS * FROM cta_corriente WHERE id_cobro='" . $this->fields['id_cobro'] . "' AND (egreso > 0 OR ingreso > 0) ORDER BY fecha ASC";
                $lista_gastos = new ListaGastos($this->sesion, '', $query);

                for ($i = 0; $i < $lista_gastos->num; $i++) {
                    $gasto = $lista_gastos->Get($i);

                    if ($gasto->fields['egreso'] > 0) {
                        $saldo = $gasto->fields['monto_cobrable'];
                    } elseif ($gasto->fields['ingreso'] > 0) {
                        $saldo = -$gasto->fields['monto_cobrable'];
                    }

                    if (substr($gasto->fields['descripcion'], 0, 19) != "Saldo aprovisionado") {
                        $saldo_balance = $saldo;
                    } else {
                        $saldo_balance = 0;
                    }

                    $monto_gasto = $saldo;
                    $saldo_moneda_total = $saldo * ($cobro_moneda->moneda[$gasto->fields['id_moneda']]['tipo_cambio'] / $cobro_moneda->moneda[$moneda_total->fields['id_moneda']]['tipo_cambio']);
                    $saldo_balance_moneda_total = $saldo_balance * ($cobro_moneda->moneda[$gasto->fields['id_moneda']]['tipo_cambio'] / $cobro_moneda->moneda[$moneda_total->fields['id_moneda']]['tipo_cambio']);
                    $total_gastos_balance += $saldo_balance_moneda_total;
                    $total_gastos = $this->fields['monto_gastos'];
                }

                $mb_monto_honorarios = $this->fields['monto'];
                $mb_monto_gastos = $this->fields['monto_gastos'];

                // Utilizado por Morales y Besa Solicitado por @Gtigre
                if ($mb_monto_honorarios > 0 && $mb_monto_gastos > 0) {
                    $mb_detalle_chile_boleta = "lang_mb_detalle_chile_boleta_hyg";
                } elseif ($mb_monto_honorarios == 0 && $mb_monto_gastos > 0) {
                    $mb_detalle_chile_boleta = 'lang_mb_detalle_chile_boleta_g';
                } else {
                    $mb_detalle_chile_boleta = 'lang_mb_detalle_chile_boleta_h';
                }

                $html2 = str_replace('%mb_detalle_chile_boleta%', __($mb_detalle_chile_boleta), $html2);

                $html2 = str_replace('%subtotal_gastos_solo_provision%', $moneda_total->fields['simbolo'] . $this->espacio . number_format(abs($x_cobro_gastos['subtotal_gastos_solo_provision']), $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2); // en la carta se especifica que el monto debe aparecer como positivo
                $html2 = str_replace('%subtotal_gastos_sin_provision%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($x_cobro_gastos['subtotal_gastos_sin_provision'], $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2); // en la carta se especifica que el monto debe aparecer como positivo
                $html2 = str_replace('%subtotal_gastos_diff_con_sin_provision%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($x_cobro_gastos['gasto_total'], $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2); // en la carta se especifica que el monto debe aparecer como positivo
                $html2 = str_replace('%saldo_gastos_balance%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos_balance, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ',-', $html2);
                $html2 = str_replace('%monto_gastos_con_iva%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($x_cobro_gastos['subtotal_gastos_con_impuestos'], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ',-', $html2);
                $html2 = str_replace('%monto_gastos_sin_iva%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($x_cobro_gastos['subtotal_gastos_sin_impuestos'], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ',-', $html2);
                $html2 = str_replace('%monto_impuesto%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($x_cobro_gastos['subtotal_gastos_sin_impuestos'], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ',-', $html2);
                $html2 = str_replace('%monto_honorarios%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($x_resultados['monto_honorarios'][$this->fields['opc_moneda_total']], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%simbolo_moneda_cobro%', $moneda->fields['simbolo'], $html2);

                // monto honorario moneda
                $html2 = str_replace('%monto_honorarios_moneda_cobro%', $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);

                if ($this->fields['id_moneda'] != $this->fields['opc_moneda_total']) {
                    $html2 = str_replace('%mb_monto_honorarios_moneda_cobro%', ' equivalentes a ' . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' conforme a su equivalencia al %fecha_hasta_dmy%.', $html2);
                } else {
                    $html2 = str_replace('%mb_monto_honorarios_moneda_cobro%', '.', $html2);
                }

                $aproximacion_monto = number_format($this->fields['monto'], $cobro_moneda->moneda[$this->fields['id_moneda']]['cifras_decimales'], '.', '');
                $monto_moneda = ((double) $aproximacion_monto * (double) $this->fields['tipo_cambio_moneda']) / ($tipo_cambio_moneda_total > 0 ? $tipo_cambio_moneda_total : $moneda_total->fields['tipo_cambio']);
                $monto_moneda_sin_gasto = ((double) $aproximacion_monto * (double) $this->fields['tipo_cambio_moneda']) / ($tipo_cambio_moneda_total > 0 ? $tipo_cambio_moneda_total : $moneda_total->fields['tipo_cambio']);
                $monto_moneda_con_gasto = ((double) $aproximacion_monto * (double) $this->fields['tipo_cambio_moneda']) / ($tipo_cambio_moneda_total > 0 ? $tipo_cambio_moneda_total : $moneda_total->fields['tipo_cambio']);

                $monto_moneda_con_gasto = $x_resultados['monto'][$this->fields['opc_moneda_total']];
                $monto_moneda_sin_gasto = $x_resultados['monto'][$this->fields['opc_moneda_total']];

                /* CASO RETAINER MENOR DE UN VALOR Y DISTINTA TARIFA ( DIFERENCIA POR DECIMALES ) */

                if ((($this->fields['total_minutos'] / 60) < $this->fields['retainer_horas']) && ($this->fields['forma_cobro'] == 'RETAINER' || $this->fields['forma_cobro'] == 'PROPORCIONAL') && $this->fields['id_moneda'] != $this->fields['id_moneda_monto']) {
                    //$monto_moneda_con_gasto = ((double)$this->fields['monto']*(double)$this->fields['tipo_cambio_moneda'])/($tipo_cambio_moneda_total > 0 ? $tipo_cambio_moneda_total : $moneda_total->fields['tipo_cambio']);
                    $monto_moneda_con_gasto = $x_resultados['monto'][$this->fields['opc_moneda_total']];
                }

                $query = "SELECT SUM( TIME_TO_SEC( duracion_cobrada )/3600 ) FROM trabajo WHERE id_cobro = '" . $this->fields['id_cobro'] . "' ";
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);

                list($duracion_trabajos) = mysql_fetch_array($resp);

                $html2 = str_replace('%duracion_trabajos%', number_format($duracion_trabajos, 2, ',', ''), $html2);

                /* FORMA COBRO FLAT FEE */
                if ($this->fields['forma_cobro'] == 'FLAT FEE' && $this->fields['id_moneda'] != $this->fields['id_moneda_monto'] && $this->fields['id_moneda_monto'] == $this->fields['opc_moneda_total']) {
                    $monto_moneda = $this->fields['monto_contrato'];
                    $monto_moneda_con_gasto = $this->fields['monto_contrato'];
                    $monto_moneda_sin_gasto = $this->fields['monto_contrato'];
                }

                $monto_moneda_con_gasto += $total_gastos;

                if ($lang != 'es') {
                    $monto_moneda += $total_gastos;
                }

                if ($total_gastos > 0) {
                    $html2 = str_replace('%monto_gasto%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                } else {
                    $html2 = str_replace('%monto_gasto%', $moneda_total->fields['simbolo'] . $this->espacio . number_format(0, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                }

                $html2 = str_replace('%saldo_gasto_facturado%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($this->ArrayTotalesDelContrato[$this->fields['id_cobro']]['saldo_gastos'], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%saldo_gasto_facturado_moneda_base%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($this->ArrayTotalesDelContrato[$this->fields['id_cobro']]['saldo_gastos_moneda'], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);

                #Fechas periodo
                $datefrom = strtotime($this->fields['fecha_ini'], 0);
                $dateto = strtotime($this->fields['fecha_fin'], 0);
                $difference = $dateto - $datefrom; //Dif segundos
                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }

                $datediff = $months_difference;

                /* MOSTRANDO FECHAS SEGUN IDIOMA */

                if ($this->fields['fecha_ini'] != '' && $this->fields['fecha_ini'] != '0000-00-00') {
                    $texto_fecha_es = __('entre los meses de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_ini'], '%B %Y')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y'));
                    $texto_fecha_es_durante = __('durante los meses de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_ini'], '%B %Y')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y'));
                    $texto_fecha_en = __('between') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_ini']))) . ' ' . __('and') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                } else {
                    $texto_fecha_es = __('hasta el mes de') . ' ' . ucfirst(ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y')));
                    $texto_fecha_es_durante = __('hasta el mes de') . ' ' . ucfirst(ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y')));
                    $texto_fecha_en = __('until') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                }

                if ($lang == 'es') {
                    $fecha_mes = $datediff > 0 && $datediff < 12 ? $texto_fecha_es : __('realizados el mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B'));
                    $fecha_diff = $datediff > 0 && $datediff < 12 ? $texto_fecha_es : __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y'));
                    $fecha_al = $datediff > 0 && $datediff < 12 ? $texto_fecha_es : __('al mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y'));
                    $fecha_diff_con_de = $datediff > 0 && $datediff < 12 ? $texto_fecha_es : __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B de %Y'));
                    $fecha_diff_prestada = $datediff > 0 && $datediff < 12 ? __('prestada ') . $texto_fecha_es : __('prestada en el mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y'));
                    $fecha_diff_prestada_durante = $datediff > 0 && $datediff < 12 ? $texto_fecha_es_durante : __('prestados durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B %Y'));
                } else {
                    $fecha_diff = $datediff > 0 && $datediff < 12 ? $texto_fecha_en : __('during') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                    $fecha_al = $datediff > 0 && $datediff < 12 ? $texto_fecha_en : __('to') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                    $fecha_diff_prestada = $datediff > 0 && $datediff < 12 ? $texto_fecha_en : __('during') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                    $fecha_diff_prestada_durante = $datediff > 0 && $datediff < 12 ? $texto_fecha_en : __('during') . ' ' . ucfirst(date('F Y', strtotime($this->fields['fecha_fin'])));
                    $fecha_diff_con_de = $datediff > 0 && $datediff < 12 ? $texto_fecha_es : __('during the month of') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B de %Y'));
                }

                if (( $fecha_diff == 'durante el mes de No existe fecha' || $fecha_diff == 'hasta el mes de No existe fecha' ) && $lang == 'es') {
                    $fecha_diff = __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %Y'));
                    $fecha_al = __('al mes de') . ' ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %Y'));
                    $fecha_diff_con_de = __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B de %Y'));
                    $fecha_diff_prestada = __('prestada en el mes de') . ' ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B de %Y'));
                    $fecha_diff_prestada_durante = __('prestados durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %Y'));
                }

                /* OBTENEMOS LA FECHA INICIAL DEL PRIMER TRABAJO (PARA EVITAR FECHAS 1969) */
                $query = "SELECT fecha FROM trabajo WHERE id_cobro='" . $this->fields['id_cobro'] . "' AND visible='1' ORDER BY fecha LIMIT 1";
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);

                if (mysql_num_rows($resp) > 0) {
                    list($fecha_primer_trabajo) = mysql_fetch_array($resp);
                } else {
                    $fecha_primer_trabajo = $this->fields['fecha_ini'];
                }

                /*  OBTENEMOS LA FECHA FINAL DEL ULTIMO TRABAJO (PARA EVITAR FECHAS 1969) */
                $query = "SELECT LAST_DAY(fecha) FROM trabajo WHERE id_cobro='" . $this->fields['id_cobro'] . "' AND visible='1' ORDER BY fecha DESC LIMIT 1";
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);

                if (mysql_num_rows($resp) > 0) {
                    list($fecha_ultimo_trabajo) = mysql_fetch_array($resp);
                } else {
                    $fecha_ultimo_trabajo = $this->fields['fecha_fin'];
                }

                $fecha_inicial_primer_trabajo = date('Y-m-01', strtotime($fecha_primer_trabajo));
                $fecha_final_ultimo_trabajo = date('Y-m-d', strtotime($fecha_ultimo_trabajo));
                $datefrom = strtotime($fecha_inicial_primer_trabajo, 0);
                $dateto = strtotime($fecha_final_ultimo_trabajo, 0);
                $difference = $dateto - $datefrom; //Dif segundos
                $months_difference = floor($difference / 2678400);

                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }

                $datediff = $months_difference;

                $asuntos_doc = '';

                for ($k = 0; $k < count($this->asuntos); $k++) {
                    $asunto = new Asunto($this->sesion);
                    $asunto->LoadByCodigo($this->asuntos[$k]);
                    $espace = $k < count($this->asuntos) - 1 ? ', ' : '';
                    $asuntos_doc .= $asunto->fields['glosa_asunto'] . '' . $espace;
                    $_codigo_asunto = $_codigo_asunto_secundario != '1' ? $asunto->fields['codigo_asunto'] : $asunto->fields['codigo_asunto_secundario'];
                    $codigo_asunto .= $_codigo_asunto . '' . $espace;
                    $_codigo_asunto = explode('-', $_codigo_asunto);
                    $codigo_asunto_glosa_asunto .= "{$_codigo_asunto[1]}-{$asunto->fields['glosa_asunto']}{$espace}";
                }

                $html2 = str_replace('%CodigoAsuntoGlosaAsunto%', $codigo_asunto_glosa_asunto, $html2);
                $html2 = str_replace('%Asunto%', $asuntos_doc, $html2);
                $asunto_ucwords = ucwords(strtolower($asuntos_doc));
                $html2 = str_replace('%Asunto_ucwords%', $asunto_ucwords, $html2);

                /*  MOSTRANDO FECHA SEGUN IDIOMA */
                if ($fecha_inicial_primer_trabajo != '' && $fecha_inicial_primer_trabajo != '0000-00-00') {
                    if ($lang == 'es') {
                        $fecha_diff_periodo_exacto = __('desde el d�a') . ' ' . date("d-m-Y", strtotime($fecha_primer_trabajo)) . ' ';
                    } else {
                        $fecha_diff_periodo_exacto = __('from') . ' ' . date("m-d-Y", strtotime($fecha_primer_trabajo)) . ' ';
                    }

                    if (Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%Y') == Utiles::sql3fecha($this->fields['fecha_fin'], '%Y')) {
                        $texto_fecha_es = __('entre los meses de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%B')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B %Y'));
                        $texto_fecha_es_de = __('entre los meses de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%B')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y'));
                        $texto_fecha_es_durante = __('prestados durante los meses de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%B')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y'));
                    } else {
                        $texto_fecha_es = __('entre los meses de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%B %Y')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B %Y'));
                        $texto_fecha_es_de = __('entre los meses de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%B %Y')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y'));
                        $texto_fecha_es_durante = __('prestados durante los meses de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%B %Y')) . ' ' . __('y') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y'));
                    }
                } else {
                    $texto_fecha_es = __('hasta el mes de') . ' ' . ucfirst(ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B %Y')));
                    $texto_fecha_es_de = __('hasta el mes de') . ' ' . ucfirst(ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y')));
                    $texto_fecha_es_durante = __('hasta el mes de') . ' ' . ucfirst(ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y')));
                }

                if ($lang == 'es') {
                    $fecha_diff_periodo_exacto .= __('hasta el d�a') . ' ' . Utiles::sql3fecha($this->fields['fecha_fin'], '%d-%m-%Y');
                } else {
                    $fecha_diff_periodo_exacto .= __('until') . ' ' . Utiles::sql3fecha($this->fields['fecha_fin'], '%m-%d-%Y');
                }

                if ($fecha_inicial_primer_trabajo != '' && $fecha_inicial_primer_trabajo != '0000-00-00') {
                    if (Utiles::sql3fecha($fecha_inicial_primer_trabajo, '%Y') == Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%Y')) {
                        $texto_fecha_en = __('between') . ' ' . ucfirst(date('F', strtotime($fecha_inicial_primer_trabajo))) . ' ' . __('and') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                    } else {
                        $texto_fecha_en = __('between') . ' ' . ucfirst(date('F Y', strtotime($fecha_inicial_primer_trabajo))) . ' ' . __('and') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                    }
                } else {
                    $texto_fecha_en = __('until') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                }

                if ($lang == 'es') {
                    $fecha_primer_trabajo = $datediff > 0 && $datediff < 48 ? $texto_fecha_es : __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B %Y'));
                    $fecha_primer_trabajo_de = $datediff > 0 && $datediff < 48 ? $texto_fecha_es_de : __('durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y'));
                    $fecha_primer_trabajo_durante = $datediff > 0 && $datediff < 48 ? $texto_fecha_es_de : __(' prestados durante el mes de') . ' ' . ucfirst(Utiles::sql3fecha($fecha_final_ultimo_trabajo, '%B de %Y'));
                } else {
                    $fecha_primer_trabajo = $datediff > 0 && $datediff < 48 ? $texto_fecha_en : __('during') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                    $fecha_primer_trabajo_de = $datediff > 0 && $datediff < 48 ? $texto_fecha_en : __('during') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                    $fecha_primer_trabajo_durante = $datediff > 0 && $datediff < 48 ? $texto_fecha_en : __('during') . ' ' . ucfirst(date('F Y', strtotime($fecha_final_ultimo_trabajo)));
                }

                if ($fecha_primer_trabajo == 'No existe fecha' && $lang == es) {
                    $fecha_primer_trabajo = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %Y'));
                    $fecha_primer_trabajo_de = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %Y'));
                    $fecha_primer_trabajo_durante = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %Y'));
                }

                if ($this->fields['id_moneda'] != $this->fields['opc_moneda_total']) {
                    $html2 = str_replace('%equivalente_dolm%', ' que ascienden a %monto%', $html2);
                } else {
                    $html2 = str_replace('%equivalente_dolm%', '', $html2);
                }

                /* FECHA PERIODO EXACTO PARA COBROS SOLO GASTOS */

                $query_fecha_ini_periodo_gastos = "SELECT MIN(fecha) FROM cta_corriente WHERE id_cobro='" . $this->fields['id_cobro'] . "' ORDER BY fecha LIMIT 1 ";
                $resp_fecha_ini_gastos = mysql_query($query_fecha_ini_periodo_gastos, $this->sesion->dbh) or Utiles::errorSQL($query_fecha_ini_periodo_gastos, __FILE__, __LINE__, $this->sesion->dbh);

                list($fecha_primer_gasto) = mysql_fetch_array($resp_fecha_ini_gastos);

                $query_fecha_fin_periodo_gastos = "SELECT max(fecha) FROM cta_corriente WHERE id_cobro='" . $this->fields['id_cobro'] . "' ORDER BY fecha LIMIT 1";
                $resp_fecha_fin_gastos = mysql_query($query_fecha_fin_periodo_gastos, $this->sesion->dbh) or Utiles::errorSQL($query_fecha_fin_periodo_gastos, __FILE__, __LINE__, $this->sesion->dbh);

                list($fecha_ultimo_gasto) = mysql_fetch_array($resp_fecha_fin_gastos);

                $fecha_diff_primer_gasto = ucfirst(Utiles::sql3fecha($fecha_primer_gasto, '%d-%m-%Y'));
                $fecha_diff_ultimo_gasto = ucfirst(Utiles::sql3fecha($fecha_ultimo_gasto, '%d-%m-%Y'));

                $fecha_diff_primer_trabajo = Utiles::sql3fecha($this->fields['fecha_ini'], '%d-%m-%Y');
                $fecha_diff_ultimo_trabajo = Utiles::sql3fecha($this->fields['fecha_fin'], '%d-%m-%Y');

                if (($this->fields['incluye_honorarios'] == '0') && $this->fields['fecha_ini'] == '0000-00-00') {
                    $html2 = str_replace('%fecha_inicial_periodo_exacto%', $fecha_diff_primer_gasto, $html2);
                    $html2 = str_replace('%fecha_fin_periodo_exacto%', $fecha_diff_ultimo_gasto, $html2);
                } else {
                    $html2 = str_replace('%fecha_inicial_periodo_exacto%', $fecha_diff_primer_trabajo, $html2);
                    $html2 = str_replace('%fecha_fin_periodo_exacto%', $fecha_diff_ultimo_trabajo, $html2);
                }

                $fecha_lang = Conf::GetConf($this->sesion, 'CiudadEstudio') . ' (' . Conf::GetConf($this->sesion, 'PaisEstudio') . '), ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%e de %B de %Y'));

                $fecha_espanol = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%e de %B de %Y'));

                $html2 = str_replace('%fecha_especial%', $fecha_lang, $html2);
                $fecha_lang_mta = 'Bogot�, D.C.,' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%e de %B de %Y'));
                $actual_locale = setlocale(LC_ALL, 0);
                $fecha_lang_mta_en = (setlocale(LC_ALL, 'en_US.UTF-8')) ? "Bogot�, " . strftime(Utiles::FormatoStrfTime("%B %e, %Y")) : $fecha_lang_mta;

                setlocale(LC_ALL, "$actual_locale");

                $query = "SELECT factura.numero as documentos
							FROM factura
							LEFT JOIN prm_documento_legal ON factura.id_documento_legal = prm_documento_legal.id_documento_legal
								WHERE id_cobro = '" . $this->fields['id_cobro'] . "' AND anulado != 1";

                $result = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);

                while ($data = mysql_fetch_assoc($result)) {
                    $documentos_relacionados[] = $data;
                }

                $documentos_rel = '';

                for ($k = 0; $k < count($documentos_relacionados); $k++) {
                    $espace = $k < count($documentos_relacionados) - 1 ? ', ' : '';
                    $documentos_rel .= $documentos_relacionados[$k]['documentos'] . '' . $espace;
                }

                $html2 = str_replace('%doc_tributario%', $documentos_rel, $html2);

                $html2 = str_replace('%num_factura%', $this->fields['documento'], $html2);
                $html2 = str_replace('%n_num_factura%', 'N�' . $this->fields['documento'], $html2);
                $html2 = str_replace('%fecha_primer_trabajo%', $fecha_primer_trabajo, $html2);
                $html2 = str_replace('%fecha_primer_trabajo_de%', $fecha_primer_trabajo_de, $html2);
                $html2 = str_replace('%fecha_primer_trabajo_durante%', $fecha_primer_trabajo_durante, $html2);
                $html2 = str_replace('%fecha%', $fecha_diff, $html2);
                $html2 = str_replace('%fecha_mes%', $fecha_mes, $html2);
                $html2 = str_replace('%fecha_especial_mta%', $fecha_lang_mta, $html2);
                $html2 = str_replace('%fecha_especial_mta_en%', $fecha_lang_mta_en, $html2);
                $html2 = str_replace('%fecha_al%', $fecha_al, $html2);
                $html2 = str_replace('%fecha_al_minuscula%', strtolower($fecha_al), $html2);
                $html2 = str_replace('%fecha_con_de%', $fecha_diff_con_de, $html2);
                $html2 = str_replace('%fecha_con_prestada%', $fecha_diff_prestada, $html2);
                $html2 = str_replace('%fecha_con_prestada_mayuscula%', mb_strtoupper($fecha_diff_prestada), $html2);
                $html2 = str_replace('%fecha_con_prestada_minusculas%', strtolower($fecha_diff_prestada), $html2);
                $html2 = str_replace('%fecha_diff_prestada_durante%', $fecha_diff_prestada_durante, $html2);
                $html2 = str_replace('%fecha_diff_prestada_durante_mayuscula%', mb_strtoupper($fecha_diff_prestada_durante), $html2);
                $html2 = str_replace('%fecha_diff_prestada_durante_minusculas%', strtolower($fecha_diff_prestada_durante), $html2);
                $html2 = str_replace('%fecha_emision%', $this->fields['fecha_emision'] ? Utiles::sql2fecha($this->fields['fecha_emision'], '%d de %B') : '', $html2);

                $fecha_creacion = $this->fields['fecha_creacion'] ? Utiles::sql2fecha($this->fields['fecha_creacion'], '%d/%m/%Y') : '';
                $fecha_mta_emision = $this->fields['fecha_emision'] ? Utiles::sql2fecha($this->fields['fecha_emision'], '%d/%m/%Y') : '';
                $fecha_mta_facturacion = $this->fields['fecha_facturacion'] ? Utiles::sql2fecha($this->fields['fecha_facturacion'], '%d/%m/%Y') : $fecha_mta_emision;
                list($fecha_mta_dia, $fecha_mta_mes, $fecha_mta_agno) = explode("/", $fecha_mta_facturacion);
                list($fecha_dia_creacion, $fecha_mes_creacion, $fecha_agno_creacion) = explode("/", $fecha_creacion);

                $html2 = str_replace('%fecha_mta%', $fecha_mta_facturacion, $html2);
                $html2 = str_replace('%fecha_mta_dia%', $fecha_mta_dia, $html2);
                $html2 = str_replace('%fecha_mta_mes%', $fecha_mta_mes, $html2);
                $html2 = str_replace('%fecha_mta_agno%', $fecha_mta_agno, $html2);
                $html2 = str_replace('%fecha_agno%', $fecha_agno_creacion, $html2);

                $fecha_facturacion_carta = ucfirst(Utiles::sql3fecha($this->fields['fecha_facturacion'], '%d de %B de %Y'));
                $fecha_facturacion_mes_carta = ucfirst(Utiles::sql3fecha($this->fields['fecha_facturacion'], '%B'));

                $html2 = str_replace('%monto_total_demo_uf%', number_format($monto_moneda_demo, $cobro_moneda->moneda[3]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . $cobro_moneda->moneda[3]['simbolo'], $html2);
                $html2 = str_replace('%fecha_facturacion%', $fecha_facturacion_carta, $html2);
                $html2 = str_replace('%fecha_facturacion_mes%', $fecha_facturacion_mes_carta, $html2);
                $html2 = str_replace('%monto_total_demo_jdf%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . number_format($x_resultados['monto_total_cobro'][$this->fields['opc_moneda_total']], $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%fecha_periodo_exacto%', $fecha_diff_periodo_exacto, $html2);

                $fecha_dia_carta = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%d de %B de %Y'));

                $html2 = str_replace('%fecha_dia_carta%', $fecha_dia_carta, $html2);
                $html2 = str_replace('%monto%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_solo_gastos%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($gasto_en_pesos, 0, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_sin_gasto%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda_sin_gasto, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_total_demo%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($x_resultados['monto_total_cobro'][$this->fields['opc_moneda_total']], $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_subtotal%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($x_resultados['monto_subtotal_completo'][$this->fields['opc_moneda_total']], $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_con_gasto%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda_con_gasto, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_original%', $moneda->fields['simbolo'] . ' ' . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_subtotal%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($x_resultados['monto_subtotal_completo'][$this->fields['opc_moneda_total']], $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_total_sin_iva%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($x_resultados['monto_cobro_original'][$this->fields['opc_moneda_total']], $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_iva%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format(( $x_resultados['monto_total_cobro'][$this->fields['opc_moneda_total']] - $x_resultados['monto_cobro_original'][$this->fields['opc_moneda_total']]), $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_total_espacio%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($x_resultados['monto_total_cobro'][$this->fields['opc_moneda_total']], $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%monto_total_glosa_moneda%', $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['glosa_moneda_plural'] . $this->espacio . number_format($x_resultados['monto_total_cobro'][$this->fields['opc_moneda_total']], $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);

                $html2 = str_replace('%porcentaje_iva_con_simbolo%', $this->fields['porcentaje_impuesto'] . "%", $html2);

                $monto_palabra = new MontoEnPalabra($this->sesion);

                $glosa_moneda_lang = __($cobro_moneda->moneda[$this->fields['opc_moneda_total']]['glosa_moneda']);
                $glosa_moneda_plural_lang = __($cobro_moneda->moneda[$this->fields['opc_moneda_total']]['glosa_moneda_plural']);
                $cobro_id_moneda = $this->fields['opc_moneda_total'];

                $total_mta = number_format($x_resultados['monto_total_cobro'][$this->fields['opc_moneda_total']], $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], '.', '');
                $decimales_monto = number_format(($total_mta - (int) $total_mta), 2);
                $decimales_monto = number_format(($decimales_monto * 100), 0);
                $monto_total_palabra = Numbers_Words::toWords((int) $total_mta, "es") . ' ' . ( ( $total_mta > 1 ) ? __("$glosa_moneda_plural_lang") : __("$glosa_moneda_lang") ) . ( ($decimales_monto > 0 ) ? " con $decimales_monto/100" : '' );
                $monto_total_palabra_en = Numbers_Words::toWords((int) $total_mta, "en_US") . ' ' . ( ( $total_mta > 1 ) ? __("$glosa_moneda_plural_lang") : __("$glosa_moneda_lang") ) . ( ($decimales_monto > 0 ) ? " and $decimales_monto/100" : '' );

                $cambio_monedas_texto_en = array(
                    'd�lar' => 'dollar', 'D�lar' => 'Dolar', 'D�LAR' => 'DOLLAR',
                    'd�lares' => 'dollars', 'D�lares' => 'Dollars', 'D�LARES' => 'DOLLARS',
                    'libra' => 'pound', 'Libra' => 'Pound', 'LIBRA' => 'POUND',
                    'libras' => 'pounds', 'Libras' => 'Pounds', 'LIBRAS' => 'POUNDS'
                );

                $monto_total_palabra_en = strtr($monto_total_palabra_en, $cambio_monedas_texto_en);
                $html2 = str_replace('%monto_en_palabras%', __(strtoupper($monto_total_palabra)), $html2);
                $html2 = str_replace('%monto_en_palabras_en%', __(strtoupper($monto_total_palabra_en)), $html2);

                $moneda_opc_total = new Moneda($this->sesion);
                $moneda_opc_total->Load($this->fields['opc_moneda_total']);

                if ($x_resultados['monto_total_cobro'][$this->fields['opc_moneda_total']] > 0) {
                    $html2 = str_replace('%frase_moneda%', __(strtolower($moneda_opc_total->fields['glosa_moneda_plural'])), $html2);
                } else {
                    $html2 = str_replace('%frase_moneda%', __(strtolower($moneda_opc_total->fields['glosa_moneda'])), $html2);
                }

                if ($this->fields['opc_moneda_total'] != $this->fields['id_moneda']) {
                    $html2 = str_replace('%equivalente_a_baz%', __(', equivalentes a ') . $moneda->fields['simbolo'] . ' ' . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                } else {
                    $html2 = str_replace('%equivalente_a_baz%', '', $html2);
                }

                if ($this->fields['tipo_cambio_moneda_base'] <= 0) {
                    $tipo_cambio_moneda_base_cobro = 1;
                } else {
                    $tipo_cambio_moneda_base_cobro = $this->fields['tipo_cambio_moneda_base'];
                }

                $fecha_hasta_cobro = strftime(Utiles::FormatoStrfTime('%e de %B'), mktime(0, 0, 0, date("m", strtotime($this->fields['fecha_fin'])), date("d", strtotime($this->fields['fecha_fin'])), date("Y", strtotime($this->fields['fecha_fin']))));
                $html2 = str_replace('%fecha_hasta%', $fecha_hasta_cobro, $html2);

                $fecha_hasta_dmy = strftime(Utiles::FormatoStrfTime('%e de %B del %Y'), strtotime($this->fields['fecha_fin']));
                $html2 = str_replace('%fecha_hasta_dmy%', $fecha_hasta_dmy, $html2);

                if ($this->fields['id_moneda'] > 1 && $moneda_total->fields['id_moneda'] > 1) { #!= $moneda_cli->fields['id_moneda']
                    $en_pesos = (double) $this->fields['monto'] * ($this->fields['tipo_cambio_moneda'] / $tipo_cambio_moneda_base_cobro);
                    $html2 = str_replace('%monto_en_pesos%', __(', equivalentes a esta fecha a $ ') . number_format($en_pesos, 0, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.-', $html2);
                } else {
                    $html2 = str_replace('%monto_en_pesos%', '', $html2);
                }

                #si hay gastos se muestran
                if ($total_gastos > 0) {
                    $gasto_en_pesos = $total_gastos;
                    $txt_gasto = __("Asimismo, se agregan los gastos por la suma total de");
                    $html2 = str_replace('%monto_gasto_separado%', $txt_gasto . ' $' . number_format($gasto_en_pesos, 0, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                } else {
                    $html2 = str_replace('%monto_gasto_separado%', '', $html2);
                }

                $query = "SELECT count(*) FROM cta_corriente WHERE id_cobro = '" . $this->fields['id_cobro'] . "'";
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                list($cantidad_de_gastos) = mysql_fetch_array($resp);

                if (( $this->fields['monto_gastos'] > 0 || $cantidad_de_gastos > 0 ) && $this->fields['opc_ver_gastos']) {
                    // Calculo especial para BAZ, en ves de mostrar el total de gastos, se muestra la cuenta corriente al d�a
                    $where_gastos = " 1 ";
                    $lista_asuntos = implode("','", $this->asuntos);

                    if (!empty($lista_asuntos)) {
                        $where_gastos .= " AND cta_corriente.codigo_asunto IN ('$lista_asuntos') ";
                    }

                    $where_gastos .= " AND cta_corriente.codigo_cliente = '" . $this->fields['codigo_cliente'] . "' ";
                    $where_gastos .= " AND cta_corriente.fecha <= '" . $this->fields['fecha_fin'] . "' ";
                    $cuenta_corriente_actual = number_format(UtilesApp::TotalCuentaCorriente($this->sesion, $where_gastos), $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']);

                    $html2 = str_replace('%frase_gastos_ingreso%', '<tr>
																	    <td width="5%">&nbsp;</td>
																	    <td align="left" class="detalle"><p>Adjunto a la presente encontrar�s comprobantes de gastos realizados por cuenta de ustedes por la suma de ' . $cuenta_corriente_actual . '</p></td>
																	    <td width="5%">&nbsp;</td>
																	</tr>
																	<tr>
																	    <td>&nbsp;</td>
																	    <td valign="top" align="left" class="detalle"><p>&nbsp;</p></td>
																	</tr>', $html2);
                    $html2 = str_replace('%frase_gastos_egreso%', '<tr>
																	    <td width="5%">&nbsp;</td>
																	    <td valign="top" align="left" class="detalle"><p>A mayor abundamiento, les recordamos que a esta fecha <u>existen cobros de notar�a por la suma de $xxxxxx.-</u>, la que les agradecer� enviar en cheque nominativo a la orden de don Eduardo Avello Concha.</p></td>
																	    <td width="5%">&nbsp;</td>
																	</tr>
																	<tr>
																	    <td>&nbsp;</td>
																	    <td valign="top" align="left" class="vacio"><p>&nbsp;</p></td>
																	    <td>&nbsp;</td>
																	</tr>', $html2);
                } else {
                    $html2 = str_replace('%frase_gastos_ingreso%', '', $html2);
                    $html2 = str_replace('%frase_gastos_egreso%', '', $html2);
                }

                if ($total_gastos > 0) {
                    $html2 = str_replace('%monto_gasto%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                } else {
                    $html2 = str_replace('%monto_gasto%', $moneda_total->fields['simbolo'] . $this->espacio . number_format(0, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                }

                $html2 = str_replace('%monto_gasto_separado_baz%', $moneda_total->fields['simbolo'] . $this->espacio . number_format($this->fields['saldo_final_gastos'], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $html2);
                $html2 = str_replace('%num_letter%', $this->fields['id_cobro'], $html2);
                $html2 = str_replace('%num_letter_documento%', $this->fields['documento'], $html2);
                $html2 = str_replace('%num_letter_baz%', $this->fields['documento'], $html2);

                if (($this->fields['documento'] != '')) {
                    $html2 = str_replace('%num_letter_rebaza%', __('la factura N�') . ' ' . $this->fields['documento'], $html2);

                    $documentos_asociados = explode(",", $this->fields['documento']);
                    if (sizeof($documentos_asociados) == 1) {
                        if (substr(trim($documentos_asociados[0]), 0, 2) == 'FA') {
                            $_doc_tmp = str_replace('FA', '', trim($documentos_asociados[0]));
                            $html2 = str_replace('%num_letter_rebaza_especial%', __('la factura N�') . ' ' . $_doc_tmp, $html2);
                        } else {
                            $html2 = str_replace('%num_letter_rebaza_especial%', __('el cobro N�') . ' ' . $this->fields['id_cobro'], $html2);
                        }
                    } else if (sizeof($documentos_asociados) > 1) {
                        $_documentos = array();

                        foreach ($documentos_asociados as $key => $doc_tmp) {
                            if (substr(trim($doc_tmp), 0, 2) == 'FA') {
                                $_doc_tmp = str_replace('FA', '', trim($doc_tmp));

                                $pos_anulada = stripos($_doc_tmp, "anula");
                                if (!$pos_anulada) {
                                    $_documentos[] = $_doc_tmp;
                                }
                            }
                        }

                        if (sizeof($_documentos) > 0) {
                            $html2 = str_replace('%num_letter_rebaza_especial%', __('las facturas N�') . ' ' . implode(", ", $_documentos), $html2);
                        } else {
                            $html2 = str_replace('%num_letter_rebaza_especial%', __('el cobro N�') . ' ' . $this->fields['id_cobro'], $html2);
                        }
                    } else {
                        $html2 = str_replace('%num_letter_rebaza_especial%', __('el cobro N�') . ' ' . $this->fields['id_cobro'], $html2);
                    }
                } else {
                    $html2 = str_replace('%num_letter_rebaza%', __('el cobro N�') . ' ' . $this->fields['id_cobro'], $html2);
                    $html2 = str_replace('%num_letter_rebaza_especial%', __('el cobro N�') . ' ' . $this->fields['id_cobro'], $html2);
                }

                # datos detalle carta mb y ebmo
                $html2 = str_replace('%si_gastos%', $total_gastos > 0 ? __('y gastos') : '', $html2);

                $detalle_cuenta_honorarios = '(i) ' . $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . $this->espacio . number_format($monto_moneda_sin_gasto, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');

                if ($this->fields['id_moneda'] == 2 && $moneda_total->fields['id_moneda'] == 1) {

                    $detalle_cuenta_honorarios .= ' (';

                    if ($this->fields['forma_cobro'] == 'FLAT FEE') {
                        $detalle_cuenta_honorarios .= __('retainer ');
                    }

                    $detalle_cuenta_honorarios .= __('equivalente en pesos a ') . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']);

                    if ($this->fields['id_moneda'] == 2) {
                        $detalle_cuenta_honorarios .= __(', conforme al tipo de cambio observado') . ')';
                    } else {
                        $detalle_cuenta_honorarios .= __(', conforme al tipo de cambio observado del d�a de hoy') . ')';
                    }

                    $detalle_cuenta_honorarios_primer_dia_mes = '';

                    if ($this->fields['monto_subtotal'] > 0) {

                        if ($this->fields['monto_gastos'] > 0) {

                            if ($this->fields['monto'] == round($this->fields['monto'])) {
                                $detalle_cuenta_honorarios_primer_dia_mes .= __('. Esta cantidad corresponde a') . __(' (i) ') . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], 0, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');
                            } else {
                                $detalle_cuenta_honorarios_primer_dia_mes .= __('. Esta cantidad corresponde a') . __(' (i) ') . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');
                            }
                        } else {
                            $detalle_cuenta_honorarios_primer_dia_mes .= ' ' . __('correspondiente a') . ' ' . $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');
                        }

                        $detalle_cuenta_honorarios_primer_dia_mes .= ' ( ' . __('conforme a su equivalencia en peso seg�n el D�lar Observado publicado por el Banco Central de Chile, el primer d�a h�bil del presente mes') . ' )';
                    }
                }

                if ($this->fields['id_moneda'] == 3 && $moneda_total->fields['id_moneda'] == 1) {

                    $detalle_cuenta_honorarios .= ' (';

                    if ($this->fields['forma_cobro'] == 'FLAT FEE') {
                        $detalle_cuenta_honorarios .= __('retainer ');
                    }

                    $detalle_cuenta_honorarios .= $moneda->fields['simbolo'] . $this->espacio . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']);
                    $detalle_cuenta_honorarios .= __(', conforme a su equivalencia al ');
                    $detalle_cuenta_honorarios .= $lang == 'es' ? Utiles::sql3fecha($this->fields['fecha_fin'], '%d de %B de %Y') : Utiles::sql3fecha($this->fields['fecha_fin'], '%m-%d-%Y');
                    $detalle_cuenta_honorarios .= ')';
                    $detalle_cuenta_honorarios_primer_dia_mes = '';

                    if ($this->fields['monto_subtotal'] > 0) {

                        if ($this->fields['monto_gastos'] > 0) {

                            if ($this->fields['monto'] == round($this->fields['monto'])) {
                                $detalle_cuenta_honorarios_primer_dia_mes = __('. Esta cantidad corresponde a') . __(' (i) ') . $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . number_format($monto_moneda_sin_gasto, 0, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');
                            } else {
                                $detalle_cuenta_honorarios_primer_dia_mes = __('. Esta cantidad corresponde a') . __(' (i) ') . $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'] . number_format($monto_moneda_sin_gasto, $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de honorarios');
                            }
                        }

                        $detalle_cuenta_honorarios_primer_dia_mes .= ' (' . __('equivalente a') . ' ' . $moneda->fields['simbolo'] . number_format($this->fields['monto'], $moneda->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']);
                        $detalle_cuenta_honorarios_primer_dia_mes .= __(', conforme a su equivalencia en pesos al primer d�a h�bil del presente mes') . ')';
                    }
                }

                $boleta_honorarios = __('seg�n Boleta de Honorarios adjunta');

                if ($total_gastos != 0) {

                    if ($this->fields['monto_subtotal'] > 0) {
                        $detalle_cuenta_gastos = __('; m�s') . ' (ii) ' . $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de gastos incurridos por nuestro Estudio en dicho per�odo');
                        $detalle_cuenta_gastos_cl_boleta = __('; m�s') . ' (ii) Boleta de Recuperaci�n de Gastos adjunta por ' . $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . '.';
                    } else {
                        $detalle_cuenta_gastos = __(' por concepto de gastos incurridos por nuestro Estudio en dicho per�odo');
                        $detalle_cuenta_gastos_cl_boleta = ".";
                    }

                    $boleta_gastos = __('; m�s') . ' (ii) ' . $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por gastos a reembolsar') . __(', seg�n Boleta de Recuperaci�n de Gastos adjunta');
                    $detalle_cuenta_gastos2 = __('; m�s') . ' (ii) CH' . $moneda_total->fields['simbolo'] . $this->espacio . number_format($total_gastos, $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']) . ' ' . __('por concepto de gastos incurridos por nuestro Estudio');
                }

                $html2 = str_replace('%boleta_honorarios%', $boleta_honorarios, $html2);
                $html2 = str_replace('%boleta_gastos%', $boleta_gastos, $html2);
                $html2 = str_replace('%detalle_cuenta_honorarios%', $detalle_cuenta_honorarios, $html2);
                $html2 = str_replace('%detalle_cuenta_honorarios_primer_dia_mes%', $detalle_cuenta_honorarios_primer_dia_mes, $html2);
                $html2 = str_replace('%detalle_cuenta_gastos%', $detalle_cuenta_gastos, $html2);
                $html2 = str_replace('%detalle_cuenta_gastos2%', $detalle_cuenta_gastos2, $html2);
                $html2 = str_replace('%detalle_cuenta_gastos_cl_boleta%', $detalle_cuenta_gastos_cl_boleta, $html2);

                $query = "SELECT CONCAT_WS(' ',usuario.nombre,usuario.apellido1,usuario.apellido2) as nombre_encargado, IFNULL( prm_categoria_usuario.glosa_categoria, ' ' ) as categoria_usuario
										FROM usuario
										JOIN contrato ON usuario.id_usuario=contrato.id_usuario_responsable
									 	JOIN cobro ON contrato.id_contrato=cobro.id_contrato
										LEFT JOIN prm_categoria_usuario ON ( usuario.id_categoria_usuario = prm_categoria_usuario.id_categoria_usuario AND usuario.id_categoria_usuario != 0 )
									 WHERE cobro.id_cobro=" . $this->fields['id_cobro'];

                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                list($nombre_encargado, $categoria_usuario) = mysql_fetch_array($resp);
                $html2 = str_replace('%encargado_comercial%', $nombre_encargado, $html2);
                $html2 = str_replace('%encargado_comercial_ucwords%', ucwords(strtolower($nombre_encargado)), $html2);
                $simbolo_opc_moneda_total = $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'];
                $html2 = str_replace('%simbolo_opc_moneda_totall%', $simbolo_opc_moneda_total, $html2);
                $html2 = str_replace('%categoria_encargado_comercial%', __($categoria_usuario), $html2);
                $html2 = str_replace('%categoria_encargado_comercial_mayusculas%', mb_strtoupper(__($categoria_usuario)), $html2);

                $nombre_contacto_partes = explode(' ', $contrato->fields['contacto']);
                $html2 = str_replace('%SoloNombreContacto%', $nombre_contacto_partes[0], $html2);

                if ($contrato->fields['id_cuenta'] > 0) {
                    $query = "	SELECT b.nombre, cb.numero, cb.cod_swift, cb.CCI, cb.glosa, m.glosa_moneda, cb.aba, cb.clabe
								FROM cuenta_banco cb
								LEFT JOIN prm_banco b ON b.id_banco = cb.id_banco
								LEFT JOIN prm_moneda m ON cb.id_moneda = m.id_moneda
								WHERE cb.id_cuenta = '" . $contrato->fields['id_cuenta'] . "'";
                    $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                    list($glosa_banco, $numero_cuenta, $codigo_swift, $codigo_cci, $glosa_cuenta, $glosa_moneda, $codigo_aba, $codigo_clabe) = mysql_fetch_array($resp);

                    if (strpos($glosa_cuenta, 'Ah') !== false) {
                        $tipo_cuenta = 'Cuenta Ahorros';
                    } else if (strpos($glosa_cuenta, 'Cte') !== false) {
                        $tipo_cuenta = 'Cuenta Corriente';
                    }

                    if (!empty($codigo_swift)) {
                        $html2 = str_replace('%glosa_swift%', 'SWIFT', $html2);
                    } else {
                        $html2 = str_replace('%glosa_swift%', '', $html2);
                    }

                    $html2 = str_replace('%numero_cuenta_contrato%', $numero_cuenta, $html2);
                    $html2 = str_replace('%glosa_banco_contrato%', $glosa_banco, $html2);
                    $html2 = str_replace('%glosa_cuenta_contrato%', $glosa_cuenta, $html2);
                    $html2 = str_replace('%codigo_swift%', $codigo_swift, $html2);
                    $html2 = str_replace('%codigo_cci%', $codigo_cci, $html2);
                    $html2 = str_replace('%codigo_aba%', $codigo_aba, $html2);
                    $html2 = str_replace('%codigo_clabe%', $codigo_clabe, $html2);
                    $html2 = str_replace('%tipo_cuenta%', $tipo_cuenta, $html2);
                    $html2 = str_replace('%glosa_moneda%', $glosa_moneda, $html2);

                    $datos_bancarios = '';

                    if (!empty($codigo_swift)) {
                        $datos_bancarios .= '<tr><td class="detalle"></td><td class="detalle">SWIFT</td><td class="detalle">' . $codigo_swift . '</td></tr>';
                    }
                    if (!empty($codigo_aba)) {
                        $datos_bancarios .= '<tr><td class="detalle"></td><td class="detalle">ABA</td><td class="detalle">' . $codigo_aba . '</td></tr>';
                    }
                    if (!empty($codigo_cci)) {
                        $datos_bancarios .= '<tr><td class="detalle"></td><td class="detalle">CCI</td><td class="detalle">' . $codigo_cci . '</td></tr>';
                    }
                    if (!empty($codigo_clabe)) {
                        $datos_bancarios .= '<tr><td class="detalle"></td><td class="detalle">CLABE</td><td class="detalle">' . $codigo_clabe . '</td></tr>';
                    }

                    $html2 = str_replace('%datos_bancarios%', $datos_bancarios, $html2);
                } else {
                    $html2 = str_replace('%numero_cuenta_contrato%', '', $html2);
                    $html2 = str_replace('%glosa_banco_contrato%', '', $html2);
                    $html2 = str_replace('%glosa_cuenta_contrato%', '', $html2);
                    $html2 = str_replace('%codigo_swift%', '', $html2);
                    $html2 = str_replace('%codigo_cci%', '', $html2);
                    $html2 = str_replace('%codigo_aba%', '', $html2);
                    $html2 = str_replace('%codigo_clabe%', '', $html2);
                    $html2 = str_replace('%tipo_cuenta%', '', $html2);
                    $html2 = str_replace('%glosa_moneda%', '', $html2);
                }

                if (UtilesApp::GetConf($this->sesion, 'SegundaCuentaBancaria')) {
                    if ($contrato->fields['id_cuenta2'] > 0) {
                        $query = "	SELECT b.nombre, cb.numero, cb.cod_swift, cb.CCI, cb.glosa
									FROM cuenta_banco cb
									LEFT JOIN prm_banco b ON b.id_banco = cb.id_banco
									WHERE cb.id_cuenta = '" . $contrato->fields['id_cuenta2'] . "'";
                        $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                        list($glosa_banco, $numero_cuenta, $codigo_swift, $codigo_cci, $glosa_cuenta) = mysql_fetch_array($resp);
                        $html2 = str_replace('%numero_cuenta_contrato2%', $numero_cuenta, $html2);
                        $html2 = str_replace('%glosa_banco_contrato2%', $glosa_banco, $html2);
                        $html2 = str_replace('%glosa_cuenta_contrato2%', $glosa_cuenta, $html2);
                        $html2 = str_replace('%codigo_swift2%', $codigo_swift, $html2);
                        $html2 = str_replace('%codigo_cci2%', $codigo_cci, $html2);
                    } else {
                        $html2 = str_replace('%numero_cuenta_contrato2%', '', $html2);
                        $html2 = str_replace('%glosa_banco_contrato2%', '', $html2);
                        $html2 = str_replace('%glosa_cuenta_contrato2%', '', $html2);
                        $html2 = str_replace('%codigo_swift2%', '', $html2);
                        $html2 = str_replace('%codigo_cci2%', '', $html2);
                    }
                }

                $queryasuntosrel = "SELECT asunto.glosa_asunto
										FROM trabajo
									LEFT JOIN asunto ON ( asunto.codigo_asunto = trabajo.codigo_asunto) WHERE id_cobro='" . $this->fields['id_cobro'] . "' GROUP BY asunto.glosa_asunto ";
                $resultado = mysql_query($queryasuntosrel, $this->sesion->dbh) or Utiles::errorSQL($queryasuntosrel, __FILE__, __LINE__, $this->sesion->dbh);

                while ($data = mysql_fetch_assoc($resultado)) {
                    $asuntos_rel[] = $data;
                }

                $asuntosrelacionados = '';

                for ($k = 0; $k < count($asuntos_rel); $k++) {
                    $espace_rel = $k < count($asuntos_rel) - 1 ? ', ' : '';
                    $asuntos_relacionados .= $asuntos_rel[$k]['glosa_asunto'] . '' . $espace_rel;
                }

                $html2 = str_replace('%asuntos_relacionados%', $asuntos_relacionados, $html2);

                // Numero de cuenta segun contrato

                $query_cuenta = "SELECT cuenta_banco.numero,prm_banco.nombre
									FROM contrato
										LEFT JOIN cuenta_banco ON contrato.id_cuenta = cuenta_banco.id_cuenta
										LEFT JOIN prm_banco ON cuenta_banco.id_banco = prm_banco.id_banco
											WHERE contrato.id_cuenta = '" . $contrato->fields['id_cuenta'] . "' LIMIT 1";

                $resp = mysql_query($query_cuenta, $this->sesion->dbh) or Utiles::errorSQL($query_cuenta, __FILE__, __LINE__, $this->sesion->dbh);
                list($numero_cuenta_contrato, $nombre_banco) = mysql_fetch_array($resp);

                $html2 = str_replace('%numero_cuenta_contrato%', $numero_cuenta_contrato, $html2);
                $html2 = str_replace('%nombre_banco_contrato%', $nombre_banco, $html2);

                // FIN cuenta segun contrato

                $html2 = str_replace('%DETALLE_LIQUIDACIONES%', $this->GenerarDocumentoCartaComun($parser_carta, 'DETALLE_LIQUIDACIONES', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);

                break;
        }

        return $html2;
    }

    function GenerarDocumentoCartaComun($parser_carta, $theTag = '', $lang, $moneda_cliente_cambio, $moneda_cli, & $idioma, $moneda, $moneda_base, $trabajo, & $profesionales, $gasto, & $totales, $tipo_cambio_moneda_total, $cliente, $id_carta) {

        global $id_carta;
        global $contrato;
        global $cobro_moneda;
        global $moneda_total;
        global $x_resultados;
        global $x_cobro_gastos;

        if (!isset($parser_carta->tags[$theTag])) {
            return;
        }

        $html2 = $parser_carta->tags[$theTag];

        switch ($theTag) {
            case 'FECHA': //GenerarDocumentoCartaComun

                if ($lang == 'es') {
                    $fecha_lang = UtilesApp::GetConf($this->sesion, 'CiudadEstudio') . ', ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%e de %B de %Y'));
                } else {
                    $fecha_lang = UtilesApp::GetConf($this->sesion, 'CiudadEstudio') . ' (' . Conf::GetConf($this->sesion, 'PaisEstudio') . '), ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%e de %B de %Y'));
                }

                $transformar = array('De' => 'de', 'DE' => 'de');
                $fecha_lang_esp = 'Santiago, ' . strtr(ucwords(Utiles::sql3fecha(date('Y-m-d'), '%e de %B de %Y')), $transformar);
                $fecha_espanol = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%e de %B de %Y'));
                $fecha_espanol_del = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%e de %B del %Y'));

                $query = "SELECT prm_codigo.glosa AS texto_segun_serie
							FROM factura
								LEFT JOIN prm_codigo ON factura.serie_documento_legal = prm_codigo.codigo
									WHERE id_cobro = '" . $this->fields['id_cobro'] . "' AND estado != 'ANULADA' ";

                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                list($texto_segun_serie) = mysql_fetch_array($resp);

                $html2 = str_replace('%ciudad_segun_serie%', $texto_segun_serie, $html2);
                $html2 = str_replace('%fecha_especial%', $fecha_lang, $html2);
                $html2 = str_replace('%fecha_especial2%', $fecha_lang_esp, $html2);
                $html2 = str_replace('%fecha_espanol%', $fecha_espanol, $html2);
                $html2 = str_replace('%fecha_espanol_del%', $fecha_espanol_del, $html2);
                $html2 = str_replace('%fecha_slash%', date('d/m/Y'), $html2);

                if ($lang == 'es') {
                    $fecha_lang_con_de = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %d de %Y'));
                    $fecha_lang = ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%B %d, %Y'));
                } else {
                    $fecha_lang_con_de = date('F d de Y');
                    $fecha_lang = date('F d, Y');
                }

                $fecha_ingles = date('F d, Y');
                $fecha_ingles_ordinal = date('F jS, Y');
                $ciudad_fecha_ingles = UtilesApp::GetConf($this->sesion, 'CiudadEstudio') . ' ' . date('F d, Y');

                $html2 = str_replace('%fecha%', $fecha_lang, $html2);
                $html2 = str_replace('%fecha_con_de%', $fecha_lang_con_de, $html2);
                $html2 = str_replace('%fecha_ingles%', $fecha_ingles, $html2);
                $html2 = str_replace('%ciudad_fecha_ingles%', $ciudad_fecha_ingles, $html2);

                $fecha_diff_con_de = $datediff > 0 && $datediff < 12 ? $texto_fecha_es : __(' ') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%Y'));

                $html2 = str_replace('%ANO%', $fecha_diff_con_de, $html2);

                $html2 = str_replace('%numero_cobro%', $this->fields['id_cobro'], $html2);

                $query = "SELECT CONCAT_WS(' ',usuario.nombre,usuario.apellido1,usuario.apellido2) as nombre_encargado
										FROM usuario
										JOIN contrato ON usuario.id_usuario=contrato.id_usuario_responsable
									 	JOIN cobro ON contrato.id_contrato=cobro.id_contrato
									 WHERE cobro.id_cobro=" . $this->fields['id_cobro'];

                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                list($nombre_encargado) = mysql_fetch_array($resp);

                $iniciales_encargado = $this->iniciales($nombre_encargado);

                $html2 = str_replace('%inciales_encargado%', $iniciales_encargado, $html2);
                $html2 = str_replace('%encargado_comercial%', $nombre_encargado, $html2);
                $html2 = str_replace('%xrut%', $contrato->fields['rut'], $html2);
                $html2 = str_replace('%ciudad_estudio%', UtilesApp::GetConf($this->sesion, 'CiudadEstudio'), $html2);
                $html2 = str_replace('%pais_estudio%', UtilesApp::GetConf($this->sesion, 'PaisEstudio'), $html2);

                break;

            case 'ENVIO_DIRECCION': //GenerarDocumentoCartaComun

                $query = "SELECT glosa_cliente FROM cliente
									WHERE codigo_cliente='" . $contrato->fields['codigo_cliente'] . "'";
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                list($glosa_cliente) = mysql_fetch_array($resp);

                if (!empty($contrato->fields['titulo_contacto']) && $contrato->fields['titulo_contacto'] != '-1') {
                    $html2 = str_replace('%SR%', __($contrato->fields['titulo_contacto']), $html2);
                } else {
                    $html2 = str_replace('%SR%', __('Sr.'), $html2);
                }

                $html2 = str_replace('%glosa_codigo_postal%', __('C�digo Postal'), $html2);
                $html2 = str_replace('%codigo_postal%', $contrato->fields['factura_codigopostal'], $html2);
                $html2 = str_replace('%titulo_contacto%', $contrato->fields['titulo_contacto'], $html2);
                $html2 = str_replace('%nombre_contacto_mb%', __('%nombre_contacto_mb%'), $html2);

                if (UtilesApp::GetConf($this->sesion, 'TituloContacto')) {
                    $html2 = str_replace('%NombreContacto%', $contrato->fields['contacto'] . ' ' . $contrato->fields['apellido_contacto'], $html2);

                    $html2 = str_replace('%NombreContacto_mayuscula%', mb_strtoupper($contrato->fields['contacto'] . ' ' . $contrato->fields['apellido_contacto']), $html2);
                } else {
                    $html2 = str_replace('%NombreContacto%', $contrato->fields['contacto'], $html2);
                    $html2 = str_replace('%NombreContacto_mayuscula%', mb_strtoupper($contrato->fields['contacto']), $html2);
                }

                $html2 = str_replace('%xrut%', $contrato->fields['rut'], $html2);
                $html2 = str_replace('%solicitante%', $trabajo->fields['solicitante'], $html2);
                $html2 = str_replace('%contrato_solo_nombre_contacto%', $contrato->fields['contacto'], $html2);
                $html2 = str_replace('%contrato_solo_apellido_contacto%', $contrato->fields['apellido_contacto'], $html2);
                $html2 = str_replace('%nombre_cliente%', $glosa_cliente, $html2);
                $html2 = str_replace('%nombre_cliente_ucfirst%', ucfirst($glosa_cliente), $html2);
                $html2 = str_replace('%factura_razon_social_ucfirst%', ucfirst($contrato->fields['factura_razon_social']), $html2);
                $html2 = str_replace('%glosa_cliente%', $contrato->fields['factura_razon_social'], $html2);
                $html2 = str_replace('%glosa_cliente_mayuscula%', strtoupper($contrato->fields['factura_razon_social']), $html2);
                $html2 = str_replace('%factura_giro%', $contrato->fields['factura_giro'], $html2);
                $html2 = str_replace('%factura_razon_social%', $contrato->fields['factura_razon_social'], $html2);

                $direccion = explode('//', $contrato->fields['direccion_contacto']);

                $html2 = str_replace('%valor_direccion%', nl2br($direccion[0]), $html2);
                $html2 = str_replace('%valor_direccion_uc%', ucwords(strtolower(nl2br($direccion[0]))), $html2);

                if ($lang == 'es') {
                    $fecha_lang = 'Santiago, ' . ucfirst(Utiles::sql3fecha(date('Y-m-d'), '%e de %B de %Y'));
                } else {
                    $fecha_lang = 'Santiago (Chile), ' . date('F d, Y');
                }

                $html2 = str_replace('%fecha_especial%', $fecha_lang, $html2);
                $html2 = str_replace('%fecha_especial_minusculas%', strtolower($fecha_lang), $html2);
                $html2 = str_replace('%NumeroCliente%', $cliente->fields['id_cliente'], $html2);

                $this->loadAsuntos();

                $asuntos_doc = '';
                for ($k = 0; $k < count($this->asuntos); $k++) {
                    $asunto = new Asunto($this->sesion);
                    $asunto->LoadByCodigo($this->asuntos[$k]);
                    $espace = $k < count($this->asuntos) - 1 ? ', ' : '';
                    $salto_linea = $k < count($this->asuntos) - 1 ? '<br>' : '';
                    $asuntos_doc .= $asunto->fields['glosa_asunto'] . '' . $espace;
                    $asuntos_doc_con_salto .= $asunto->fields['glosa_asunto'] . '' . $salto_linea;
                    $codigo_asunto .= $asunto->fields['codigo_asunto'] . '' . $espace;
                }

                $html2 = str_replace('%Asunto%', $asuntos_doc, $html2);
                $html2 = str_replace('%asunto_salto_linea%', $asuntos_doc_con_salto, $html2);

                if (count($this->asuntos) == 1) {
                    $html2 = str_replace('%CodigoAsunto%', $codigo_asunto, $html2);
                } else {
                    $html2 = str_replace('%CodigoAsunto%', '', $html2);
                }

                $html2 = str_replace('%pais%', 'Chile', $html2);
                $html2 = str_replace('%num_letter%', $this->fields['id_cobro'], $html2);
                $html2 = str_replace('%num_letter_documento%', $this->fields['documento'], $html2);
                $html2 = str_replace('%num_letter_baz%', $this->fields['documento'], $html2);
                $html2 = str_replace('%asunto_mb%', __('%asunto_mb%'), $html2);
                $html2 = str_replace('%presente%', __('Presente'), $html2);

                if ($contrato->fields['id_pais'] > 0) {

                    $query = "SELECT nombre FROM prm_pais WHERE id_pais=" . $contrato->fields['id_pais'];
                    $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                    list($nombre_pais) = mysql_fetch_array($resp);
                    $html2 = str_replace('%nombre_pais%', __($nombre_pais), $html2);
                    $html2 = str_replace('%nombre_pais_mayuscula%', strtoupper($nombre_pais), $html2);
                } else {
                    $html2 = str_replace('%nombre_pais%', '', $html2);
                    $html2 = str_replace('%nombre_pais_mayuscula%', '', $html2);
                }

                $fecha_diff_con_de = $datediff > 0 && $datediff < 12 ? $texto_fecha_es : __('correspondientes al mes de') . ' ' . ucfirst(Utiles::sql3fecha($this->fields['fecha_fin'], '%B de %Y'));
                $html2 = str_replace('%fecha_con_de%', $fecha_diff_con_de, $html2);

                if (strtolower($nombre_pais) != 'colombia') {
                    $html2 = str_replace('%factura_desc_mta%', 'cuenta de cobro', $html2);
                } else {
                    $html2 = str_replace('%factura_desc_mta%', 'factura', $html2);
                }

                $query = "SELECT factura.numero as documentos
							FROM factura
							LEFT JOIN prm_documento_legal ON factura.id_documento_legal = prm_documento_legal.id_documento_legal
								WHERE id_cobro = '" . $this->fields['id_cobro'] . "' AND anulado != 1";

                $result = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);

                while ($data = mysql_fetch_assoc($result)) {
                    $documentos_relacionados[] = $data;
                }

                $documentos_rel = '';

                for ($k = 0; $k < count($documentos_relacionados); $k++) {
                    $espace = $k < count($documentos_relacionados) - 1 ? ', ' : '';
                    $documentos_rel .= $documentos_relacionados[$k]['documentos'] . '' . $espace;
                }

                $html2 = str_replace('%doc_tributario', $documentos_rel, $html2);

                $html2 = str_replace('%xdireccion%', nl2br($contrato->fields['factura_direccion']), $html2);
                $html2 = str_replace('%num_factura%', $this->fields['documento'], $html2);
                $html2 = str_replace('%ciudad_cliente%', $contrato->fields['factura_ciudad'], $html2);
                $html2 = str_replace('%comuna_cliente%', $contrato->fields['factura_comuna'], $html2);

                $comuna_ciudad_cliente = '';

                if ($contrato->fields['factura_comuna'] != '') {
                    $comuna_ciudad_cliente .= $contrato->fields['factura_comuna'];
                }
                if ($contrato->fields['factura_comuna'] != '' && $contrato->fields['factura_ciudad'] != '') {
                    $comuna_ciudad_cliente .= ', ';
                }
                if ($contrato->fields['factura_ciudad'] != '') {
                    $comuna_ciudad_cliente .= $contrato->fields['factura_ciudad'];
                }

                $html2 = str_replace('%comuna_ciudad_cliente%', $comuna_ciudad_cliente, $html2);
                $html2 = str_replace('%codigo_postal_cliente%', $contrato->fields['factura_codigopostal'], $html2);
                $html2 = str_replace('%encargado_comercial%', $nombre_encargado, $html2);
                $html2 = str_replace('%cliente_fax%', $contrato->fields['fono_contacto'], $html2);
                $html2 = str_replace('%cliente_correo%', $contrato->fields['email_contacto'], $html2);
                $html2 = str_replace('%factura_giro%', $contrato->fields['factura_giro'], $html2);

                $queryasuntosrel = "SELECT asunto.glosa_asunto
											FROM trabajo
											LEFT JOIN asunto ON ( asunto.codigo_asunto = trabajo.codigo_asunto) WHERE id_cobro='" . $this->fields['id_cobro'] . "' GROUP BY asunto.glosa_asunto ";
                $resultado = mysql_query($queryasuntosrel, $this->sesion->dbh) or Utiles::errorSQL($queryasuntosrel, __FILE__, __LINE__, $this->sesion->dbh);

                while ($data = mysql_fetch_assoc($resultado)) {
                    $asuntos_rel[] = $data;
                }

                $asuntosrelacionados = '';

                for ($k = 0; $k < count($asuntos_rel); $k++) {
                    $espace_rel = $k < count($asuntos_rel) - 1 ? ', ' : '';
                    $asuntos_relacionados .= $asuntos_rel[$k]['glosa_asunto'] . '' . $espace_rel;
                }

                $html2 = str_replace('%asuntos_relacionados%', $asuntos_relacionados, $html2);

                break;

            case 'ADJ': //GenerarDocumentoCartaComun

                $html2 = str_replace('%firma_careyallende%', __('%firma_careyallende%'), $html2);
                $query = "SELECT CONCAT(a.nombre, ' ', a.apellido1, ' ', a.apellido2) FROM usuario AS a JOIN contrato ON a.id_usuario=contrato.id_usuario_responsable JOIN cobro ON cobro.id_contrato=contrato.id_contrato WHERE cobro.id_cobro=" . $this->fields['id_cobro'];
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                list($nombre_encargado) = mysql_fetch_array($resp);
                list( $nombre, $apellido1, $apellido2 ) = explode(' ', $nombre_encargado);
                $iniciales = substr($nombre, 0, 1) . substr($apellido1, 0, 1) . substr($apellido2, 0, 1);
                $html2 = str_replace('%iniciales_encargado_comercial%', $iniciales, $html2);
                $html2 = str_replace('%nombre_encargado_comercial%', $nombre_encargado, $html2);

                $html2 = str_replace('%nro_factura%', $this->fields['documento'], $html2);
                $html2 = str_replace('%num_letter%', $this->fields['id_cobro'], $html2);
                $html2 = str_replace('%num_letter_documento%', $this->fields['documento'], $html2);
                $html2 = str_replace('%num_letter_baz%', $this->fields['documento'], $html2);
                $html2 = str_replace('%cliente_fax%', $contrato->fields['fono_contacto'], $html2);
                $html2 = str_replace('%cliente_correo%', $contrato->fields['email_contacto'], $html2);
                break;

            case 'PIE': //GenerarDocumentoCartaComun
                if (method_exists('Conf', 'GetConf')) {
                    $PdfLinea1 = Conf::GetConf($this->sesion, 'PdfLinea1');
                    $PdfLinea2 = Conf::GetConf($this->sesion, 'PdfLinea3');
                    $SitioWeb = Conf::GetConf($this->sesion, 'SitioWeb');
                    $Email = Conf::GetConf($this->sesion, 'Email');
                } else {
                    $PdfLinea1 = Conf::PdfLinea1();
                    $PdfLinea2 = Conf::PdfLinea3();
                    $SitioWeb = Conf::SitioWeb();
                    $Email = Conf::Email();
                }

                $html2 = str_replace('%logo_carta%', Conf::Server() . Conf::ImgDir(), $html2);
                $pie_pagina = $PdfLinea2 . ' ' . $PdfLinea3 . '<br>' . $SitioWeb . ' - E-mail: ' . $Email;
                $html2 = str_replace('%direccion%', $pie_pagina, $html2);
                $html2 = str_replace('%num_letter%', $this->fields['id_cobro'], $html2);
                $html2 = str_replace('%num_letter_documento%', $this->fields['documento'], $html2);
                $html2 = str_replace('%salto_pagina%', '<br style="page-break-after:always;">', $html2);
                break;

            case 'DATOS_CLIENTE': //GenerarDocumentoCartaComun

                /* Datos detalle */
                if (!empty($contrato->fields['titulo_contacto']) && $contrato->fields['titulo_contacto'] != '-1') {
                    $html2 = str_replace('%SR%', __($contrato->fields['titulo_contacto']), $html2);
                } else {
                    $html2 = str_replace('%SR%', __('Sr.'), $html2);
                }

                if (Conf::GetConf($this->sesion, 'TituloContacto')) {
                    $html2 = str_replace('%sr%', __($contrato->fields['titulo_contacto']), $html2);
                    $html2 = str_replace('%NombrePilaContacto%', $contrato->fields['contacto'], $html2);
                    $html2 = str_replace('%ApellidoContacto%', $contrato->fields['apellido_contacto'], $html2);
                } else {
                    $html2 = str_replace('%sr%', __('Se�or'), $html2);
                    $NombreContacto = explode(' ', $contrato->fields['contacto']);
                    $html2 = str_replace('%NombrePilaContacto%', $NombreContacto[0], $html2);
                    $html2 = str_replace('%ApellidoContacto%', $NombreContacto[1], $html2);
                }

                $html2 = str_replace('%glosa_cliente%', $contrato->fields['factura_razon_social'], $html2);

                if (strtolower($contrato->fields['titulo_contacto']) == 'sra.' || strtolower($contrato->fields['titulo_contacto']) == 'srta.') {
                    $html2 = str_replace('%estimado%', __('Estimada'), $html2);
                } else {
                    $html2 = str_replace('%estimado%', __('Estimado'), $html2);
                }

                $query = "SELECT CONCAT_WS(' ',usuario.nombre,usuario.apellido1,usuario.apellido2) as nombre_encargado
										FROM usuario
										JOIN contrato ON usuario.id_usuario=contrato.id_usuario_responsable
									 	JOIN cobro ON contrato.id_contrato=cobro.id_contrato
									 WHERE cobro.id_cobro=" . $this->fields['id_cobro'];
                $resp = mysql_query($query, $this->sesion->dbh) or Utiles::errorSQL($query, __FILE__, __LINE__, $this->sesion->dbh);
                list($nombre_encargado) = mysql_fetch_array($resp);
                $nombre_encargado_mayuscula = strtoupper($nombre_encargado);
                $html2 = str_replace('%encargado_comercial_mayusculas%', $nombre_encargado_mayuscula, $html2);

                break;


            case 'FILAS_FACTURAS_DEL_COBRO':

                $row_template = $html2;
                $html2 = '';


                $facturasRS = $this->ArrayFacturasDelContrato; //($this->sesion,$nuevomodulofactura,$this->fields['id_cobro']);

                foreach ($facturasRS as $numfact => $factura) {
                    $row = $row_template;
                    if ($factura[0]['id_cobro'] == $this->fields['id_cobro']) {
                        $row = str_replace('%factura_numero%', $numfact, $row);
                        $row = str_replace('%factura_moneda%', $factura[0]['simbolo_moneda_total'], $row);
                        $row = str_replace('%factura_total_sin_impuesto%', number_format($factura[0]['total_sin_impuesto'], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $row);
                        $row = str_replace('%factura_impuesto%', number_format($factura[0]['iva'], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $row);
                        $row = str_replace('%factura_total%', number_format($factura[0]['total'], $moneda_total->fields['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $row);
                        $row = str_replace('%factura_periodo%', $factura[0]['periodo'], $row);
                        $html2.=$row;
                    }
                }

                break;

            case 'FILA_FACTURAS_PENDIENTES': //GenerarDocumentoCartaComun

                $query = "SELECT numero  FROM `factura` WHERE `id_estado` NOT IN (1, 3, 4) AND `anulado` != 1 AND codigo_cliente=" . $this->fields['codigo_cliente'];
                $facturasST = $this->sesion->pdodbh->query($query);
                $facturasRS = $facturasST->fetchAll();
                $row_template = $html2;
                $html2 = '';

                foreach ($facturasRS as $facturaspendientes) {
                    $row = $row_template;
                    $row = str_replace('%facturas_pendientes%', 'No ' . $facturaspendientes['numero'], $row);

                    $html2.=$row;
                }

                break;


            case 'FILAS_ASUNTOS_RESUMEN': //GenerarDocumentoCarta2
                /**
                 * Esto se hizo para Mu?oz Tamayo y Asociados. (ESM)
                 */
                global $subtotal_hh, $subtotal_gasto, $impuesto_hh, $impuesto_gasto, $simbolo, $cifras_decimales;

                $query_desglose_asuntos = "SELECT pm.cifras_decimales, pm.simbolo, @rownum:=@rownum+1 as rownum, ca.id_cobro, ca.codigo_asunto,a.glosa_asunto
						    ,if(@rownum=kant,@sumat1:=(1.0000-@sumat1), round(ifnull(trabajos.trabajos_thh/monto_thh,0),4)) pthh
						    ,@sumat1:=@sumat1+round(ifnull(trabajos.trabajos_thh/monto_thh,0),4) pthhac
						    ,if(@rownum=kant,@sumat2:=(1.0000-@sumat2), round(ifnull(trabajos.trabajos_thh_estandar/monto_thh_estandar,0),4)) pthhe
						    ,@sumat2:=@sumat2+round(ifnull(trabajos.trabajos_thh_estandar/monto_thh_estandar,0),4) pthheac
						    ,if(@rownum=kant,@sumag:=(1.0000-@sumag), round(ifnull(gastos.gastos/subtotal_gastos,0),4))  pg
						    ,@sumag:=@sumag+round(ifnull(gastos.gastos/subtotal_gastos,0),4) pgac
  					            ,c.monto_trabajos
						    ,c.monto_thh
						    ,c.monto_thh_estandar
						    ,c.subtotal_gastos , c.impuesto, c.impuesto_gastos
						    ,kant.kant

						    FROM cobro_asunto ca
							join cobro c ON (c.id_cobro = ca.id_cobro)
							join asunto a ON (a.codigo_asunto = ca.codigo_asunto)
						    join (select id_cobro, count(codigo_asunto) kant from cobro_asunto group by id_cobro) kant on kant.id_cobro=c.id_cobro
						    join (select @rownum:=0, @sumat1:=0, @sumat2:=0, @sumag:=0) fff
						    join prm_moneda pm on pm.id_moneda=c.id_moneda
						    left join (SELECT id_cobro, codigo_asunto, SUM( TIME_TO_SEC( duracion_cobrada ) /3600 * tarifa_hh ) AS trabajos_thh, SUM( TIME_TO_SEC( duracion_cobrada ) /3600 * tarifa_hh_estandar ) AS trabajos_thh_estandar
						    FROM trabajo

						    GROUP BY codigo_asunto,id_cobro) trabajos on trabajos.id_cobro=c.id_cobro and trabajos.codigo_asunto=ca.codigo_asunto
						    left join (select id_cobro, codigo_asunto, sum(ifnull(egreso,0)-ifnull(ingreso,0)) gastos
						    from cta_corriente where cobrable=1
						    group by id_cobro, codigo_asunto) gastos on gastos.id_cobro=c.id_cobro and gastos.codigo_asunto=ca.codigo_asunto
						    WHERE ca.id_cobro=" . $this->fields['id_cobro'];


                $rest_desglose_asuntos = mysql_query($query_desglose_asuntos, $this->sesion->dbh) or Utiles::errorSQL($query_desglose_asuntos, __FILE__, __LINE__, $this->sesion->dbh);
                $moneda_actual = $this->fields['id_cobro'];
                $row_tmpl = $html2;
                $html2 = '';
                $filas = 1;

                while ($rowdesglose = mysql_fetch_array($rest_desglose_asuntos)) {
                    list($subtotal_hh, $subtotal_gasto, $impuesto_hh, $impuesto_gasto, $simbolo, $cifras_decimales) = array($rowdesglose['monto_trabajos'], $rowdesglose['subtotal_gastos'], $rowdesglose['impuesto'], $rowdesglose['impuesto_gastos'], $rowdesglose['simbolo'], $rowdesglose['cifras_decimales']);
                    $row = $row_tmpl;

                    // _mi = moneda seleccionada para descargar el documento
                    $subtotal_hh_mi = ( $subtotal_hh * $cobro_moneda->moneda[$this->fields['id_moneda']]['tipo_cambio'] ) / $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['tipo_cambio'];
                    $subtotal_gasto_mi = ( $subtotal_gasto * $cobro_moneda->moneda[$this->fields['id_moneda']]['tipo_cambio'] ) / $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['tipo_cambio'];
                    $impuesto_hh_mi = ( $impuesto_hh * $cobro_moneda->moneda[$this->fields['id_moneda']]['tipo_cambio'] ) / $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['tipo_cambio'];
                    $impuesto_gasto_mi = ( $impuesto_gasto * $cobro_moneda->moneda[$this->fields['id_moneda']]['tipo_cambio'] ) / $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['tipo_cambio'];
                    $simbolo_mi = $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['simbolo'];
                    $cifras_decimales_mi = $cobro_moneda->moneda[$this->fields['opc_moneda_total']]['cifras_decimales'];

                    list($pthh, $pg) = array($rowdesglose['monto_trabajos'], $rowdesglose['subtotal_gastos'], $rowdesglose['impuesto'], $rowdesglose['impuesto_gastos'], $rowdesglose['simbolo'], $rowdesglose['cifras_decimales']);
                    $row = str_replace('%glosa_asunto%', $rowdesglose['glosa_asunto'], $row);
                    $row = str_replace('%simbolo%', $simbolo, $row);
                    $row = str_replace('%honorarios_asunto%', round($rowdesglose['monto_trabajos'] * $rowdesglose['pthh'], $cifras_decimales), $row);
                    $row = str_replace('%gastos_asunto%', round($rowdesglose['subtotal_gastos'] * $rowdesglose['pg'], $cifras_decimales), $row);

                    $row = str_replace('%total_asunto%', round(floatval($subtotal_hh) + floatval($subtotal_gasto) + floatval($impuesto_hh) + floatval($impuesto_gasto), $cifras_decimales), $row);

                    $row = str_replace('%simbolo_mi%', $simbolo_mi, $row);
                    $row = str_replace('%honorarios_asunto_mi%', round($subtotal_hh_mi * $rowdesglose['pthh'], $cifras_decimales_mi), $row);
                    $row = str_replace('%gastos_asunto_mi%', round($subtotal_gasto_mi * $rowdesglose['pg'], $cifras_decimales_mi), $row);

                    $total_asunto_mi = round(floatval($subtotal_hh_mi) + floatval($subtotal_gasto_mi) + floatval($impuesto_hh_mi) + floatval($impuesto_gasto_mi), $cifras_decimales_mi);
                    $row = str_replace('%total_asunto_mi%', number_format($total_asunto_mi, $cifras_decimales_mi, $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $row);

                    $fecha_mta_emision = $this->fields['fecha_emision'] ? Utiles::sql2fecha($this->fields['fecha_emision'], '%d/%m/%Y') : '';
                    $fecha_mta_facturacion = $this->fields['fecha_facturacion'] ? Utiles::sql2fecha($this->fields['fecha_facturacion'], '%d/%m/%Y') : $fecha_mta_emision;
                    list($fecha_mta_dia, $fecha_mta_mes, $fecha_mta_agno) = explode("/", $fecha_mta_facturacion);

                    if ($filas > 1) {
                        $row = str_replace('%num_letter%', '', $row);
                        $row = str_replace('%num_factura%', '', $row);
                        $row = str_replace('%fecha_mta%', '', $row);
                    } else {
                        $row = str_replace('%num_letter%', $this->fields['id_cobro'], $row);
                        $row = str_replace('%num_factura%', $this->fields['documento'], $row);
                        $row = str_replace('%fecha_mta%', $fecha_mta_facturacion, $row);
                    }
                    $html2 .= $row;
                    $filas++;
                }


                break;

            case 'DETALLE_LIQUIDACIONES':
                if (empty($this->DetalleLiquidaciones)) {
                    $this->DetalleLiquidaciones = array(
                        "{$this->fields['id_cobro']}" => array(
                            'totales' => $x_resultados,
                            'campos' => $this->fields,
                            'asuntos' => $this->asuntos
                        )
                    );
                }

                $totales = array();
                foreach ($this->DetalleLiquidaciones as $id_cobro => $detalle) {
                    $opc_moneda_total = $detalle['campos']['opc_moneda_total'];
                    if (!array_key_exists($opc_moneda_total, $totales)) {
                        $totales[$opc_moneda_total] = 0;
                    }
                    $totales[$opc_moneda_total] += $detalle['totales']['monto_cobro_original_con_iva'][$opc_moneda_total];
                }
                $total = '';
                foreach ($totales as $id_moneda => $total) {
                    $totales[$id_moneda] = $this->monedas[$id_moneda]['simbolo'] . $this->espacio . number_format($total, $this->monedas[$id_moneda]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']);
                }

                $html2 = str_replace('%detalle_liquidaciones_total%', implode('<br />', $totales), $html2);
                $html2 = str_replace('%DETALLE_LIQUIDACIONES_FILAS%', $this->GenerarDocumentoCartaComun($parser_carta, 'DETALLE_LIQUIDACIONES_FILAS', $lang, $moneda_cliente_cambio, $moneda_cli, $idioma, $moneda, $moneda_base, $trabajo, $profesionales, $gasto, $totales, $tipo_cambio_moneda_total, $cliente, $id_carta), $html2);
                break;

            case 'DETALLE_LIQUIDACIONES_FILAS':
                $row_template = $html2;
                $html2 = '';

                foreach ($this->DetalleLiquidaciones as $id_cobro => $detalle) {
                    $row = $row_template;

                    $row = str_replace('%detalle_liquidacion_numero%', $detalle['campos']['id_cobro'], $row);

                    $modalidad = __($detalle['campos']['forma_cobro']);
                    $detalle_modalidad = $this->ObtenerDetalleModalidad($detalle['campos'], $this->monedas[$detalle['campos']['opc_moneda_total']], $idioma);
                    $modalidad .=!empty($detalle_modalidad) ? "<br/>$detalle_modalidad" : "";
                    $row = str_replace('%detalle_liquidacion_forma_cobro%', $modalidad, $row);

                    $asuntos = array();
                    $Asunto = new Asunto($this->sesion);
                    foreach ($detalle['asuntos'] as $codigo_asunto) {
                        $Asunto->LoadByCodigo($codigo_asunto);
                        $asuntos[] = $Asunto->fields['glosa_asunto'];
                    }
                    $row = str_replace('%detalle_liquidacion_asuntos%', implode('<br/>', $asuntos), $row);

                    $opc_moneda_total = $detalle['campos']['opc_moneda_total'];
                    $row = str_replace('%detalle_liquidacion_valor%', $this->monedas[$opc_moneda_total]['simbolo'] . $this->espacio . number_format($detalle['totales']['monto_cobro_original_con_iva'][$opc_moneda_total], $this->monedas[$opc_moneda_total]['cifras_decimales'], $idioma->fields['separador_decimales'], $idioma->fields['separador_miles']), $row);

                    $html2 .= $row;
                }

                break;

            case 'SALTO_PAGINA': //GenerarDocumentoComun
                //no borrarle al css el BR.divisor
                break;
        }

        return $html2;
    }

}
