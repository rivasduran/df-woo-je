<?php
/*
**
	PAGINA DONDE CREAMOS LOS SHORTCODES
**
*/
//CREAMOS EL SHORCODE DE PRUEBA
// [showaj foo="foo-value"]
function show_admin_jleague( $atts ) {
	/*
	$a = shortcode_atts( array(
		'foo' => 'something',
		'bar' => 'something else',
	), $atts );
	*/

	//return "el resultado del foo es = {$a['foo']}";

	//AQUI COMENZAMOS A VER EL TEMA DEL CERTIFICADO
	//ver_formularios_j();

	//VISTA DEL FORMULARO PARA RELACIONAR FORMULARIO GF Y LAS CATEGORIAS DEL PRODUCTO
	ver_formularios_cat_pro();


	//AHORA AGREGAREMOS LOS TAGS DE LOS FORMULARIOS LIGA COMPETICION Y DEMAS
	ver_formularios_j();


	//AL FINAL TENEMOS LA RELACION DE LOS FORMULARIOS Y LOS ID DE LA RED
	ver_formularios_red();

	//ESTO SI ESTA FUNCIONANDO
}
add_shortcode( 'showaj', 'show_admin_jleague' );


/*
**
	LUEGO DE LA PAGINA DEL ADMIN PROCEDEMOS A CREAR LA PAGINA DONDE TENEMOS LOS DESCARGABLES EXCEL
*
*/

function excel_descargable_admin($atts){
	//echo "<h1>Soy el mejor</h1>";
	vista_excel_d();
}

add_shortcode("showaj-excel", "excel_descargable_admin");

?>