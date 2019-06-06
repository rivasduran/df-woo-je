jQuery(document).ready(function($) {
	var ligaDefault 		= "";
	var competicionDefault 	= "";

	if(jQuery(".insertalo").length > 0){
		var idFormulario = jQuery("form").attr("id");

		idFormulario = idFormulario.replace("gform_", "");

		console.log(idFormulario);

		var data = {
			action: 'my_product_ajaxss',
			//security : MyAjax.security,
			acciones: 'certificados',
			idform: idFormulario
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$.post(obj.ajaxurl, data, function(response) {
			//alert('Got this from the server joser: ' + response.length);
			//console.log(response);
			//jQuery(".insertalo select").append(response);
			console.log("el formulario : "+idFormulario);

			//AQUI RECUPERAMOS LOS VALORES DE FABRICA DE LA LIGA Y LA COMPETICION
			ligaDefault = jQuery(".ligaJ").val();
			competicionDefault = jQuery(".competicionJ").val();

			jQuery(".insertalo select option[value='Seleccione']").val("");
			jQuery(".insertalo select").append(response);

			//AQUI TENEMOS LOS CAMBIOS DEL SELECT GENERAL
			jQuery(".insertalo select").change(function(){
				var liga = jQuery('.insertalo option:selected').attr("data-liga");
				var competicion = jQuery('.insertalo option:selected').attr("data-competicion");

				if(liga == ""){
					liga = ligaDefault;
				}

				if(competicion == ""){
					competicion = competicionDefault;
				}

				//LUEGO DE ESTO REEMPLAZAMOS LOS VALORES ACTUALES DE LA LIGA Y COMPETICION
				jQuery(".ligaJ input").val(liga);
				jQuery(".competicionJ input").val(competicion);
				jQuery(".competicionJ input").attr("value", competicion);

				console.log("la liga: "+liga+" la competicion: "+competicion);

				//jQuery('.insertalo option:selected').hide();
			});
		});

	}

	//ACCIONES DEL ADMINISTRATIVO
	jQuery(".formularioTags .formularios").change(function(){
		var idFormulario = jQuery(this).val();

		var data = {
			'action': 'my_trae_categorias',
			'formulario': idFormulario
		};

		jQuery(".formularioTags .metodoPagoFiltrado").html("");

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(obj.ajaxurl, data, function(response) {
			//alert('Got this from the server: ' + response);

			//YA CUANDO VUELVE LA INFORMACION PROCEDEMOS A VACIAR EL CAMPO Y AGREGAR 
			//console.log(response);
			jQuery(".formularioTags .metodoPagoFiltrado").append(response);
		});

		console.log("El formulario es: "+idFormulario);
	});
});