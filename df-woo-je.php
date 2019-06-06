<?php
/*
	Plugin Name: DF WOO JE
	Plugin URI: http://www.jj.com/
	description: Plugin realizado por Joser
	Version: 1.2
	Author: Joser
	Author URI: http://www.jj.com/
	License: GPL2
*/

//PRIMERO CARGAMOS LA HOJA PARA LOS JS DEL CLIENTE
function theme_name_scripts() {
	wp_enqueue_script( 'script-name', plugins_url( '/js/clientes.js', __FILE__ ), array('jquery'), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );

//add_action( 'wp_ajax_foobar', 'my_ajax_foobar_handler' );

add_action( 'wp_enqueue_scripts', 'ajax_scripts' );
function ajax_scripts() {
    wp_register_script( 'main-ajax', get_template_directory_uri() . '/assets/js/main-ajax.js', array(), '', true );
    $arr = array(
        'ajaxurl' => admin_url('admin-ajax.php')
    );
    wp_localize_script('main-ajax','obj',$arr );
    wp_enqueue_script('main-ajax');
}
add_action('wp_ajax_my_product_ajaxss', 'my_action_productos_v');
add_action('wp_ajax_nopriv_my_product_ajaxss', 'my_action_productos_v');

//INCLUIMOS EL ARCHIVO ADMINISTRATIVO DE LA WEB
include("df-admin.php");
//INCLUIMOS EL ARCHIVO DE LAS VISTAS
include("df-view.php");



//ESTA ES LA FUNCION PARA BUSCAR TODOS LOS PRODUCTOS DE WOOCOMMERCE QUE SEAN VARIABLES
function my_action_productos_v() {
  global $wpdb;

	$devuelve = "";
	
  if(isset($_POST['idform'])){

    //YA CON LA ID PROCEDEMOS A CONSULTAR EL SLUG
    $slug = "";
    $idFormulario = $_POST['idform'];
    $idCategoria = "";

    //BUSCAMOS LA RELACION QUE TENGA EL FORMULARIOS
    $rela = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}df_form WHERE form = '{$idFormulario}' ");
    foreach ($rela as $keysr) {
      $idCategoria = $keysr->product;
    }

    $sslug = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}terms WHERE term_id = '{$idCategoria}' ");
    foreach ($sslug as $keysl) {
      $slug = $keysl->slug;
    }
    $args = array( 'post_type' => 'product', 'product_cat' => $slug, 'orderby' => 'asc' );
  }else{
    $args = array( 'post_type' => 'product', 'orderby' => 'asc' );
  }
        $loop = new WP_Query( $args );
        while ( $loop->have_posts() ) : $loop->the_post(); global $product; 
        	if($product->is_type('variable')){
        		//the_title();

        		//AQUI SACAMOS LAS DISTINTAS VARIABLES DE ESTE PRODUCTO
        		$available_variations = $product->get_available_variations();
  			    foreach ($available_variations as $key => $value){
  			    	//echo "<h1>------------------------------------</h1>"; 
  			        //get values HERE  

  			        //echo "<h1>".$value['price_html']."  -->".$value['display_regular_price']."<-- (".$value['variation_id'].")</h1>";

                //LE MANDAMOS COMO ATRIBUTOS SI CON ESTO VAMOS A TENER UNA LIGA O COMPETICION
                $ligaJ = "";
                $competicionJ = "";

                $consulta = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}df_tags WHERE form = '{$idFormulario}' AND product = '".$product->get_id()."' AND product_hijo = '".$value['variation_id']."' ");
                foreach ($consulta as $competicioness) {
                  if($competicioness->tags == "Liga"){
                    $ligaJ = $competicioness->value;
                  }

                  if($competicioness->tags == "competicion"){
                    $competicionJ = $competicioness->value;
                  }
                }

  			        $devuelve .= "<option data-liga='".$ligaJ."' data-competicion='".$competicionJ."' value='".$product->get_id().",".$value['variation_id']."'>".$value['price_html']."</option>";

  			        //foreach ($value as $keys => $value) {
  			    	//foreach ($value as $keys){
  			        	//AQUI ESTAN TODOS LOS PARAMETROS
  			        	//printf($keys);
  			        //}

  			        //echo "<h1>------------------------------------</h1>"; 
  			    }
        	}
        	/*
        ?>
            <h2>Shoes</h2>
                <li class="product">    
                    <a href="<?php echo get_permalink( $loop->post->ID ) ?>" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>">
                        <?php woocommerce_show_product_sale_flash( $post, $product ); ?>
                        <?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="300px" height="300px" />'; ?>
                        <h3><?php the_title(); ?></h3>
                        <span class="price"><?php echo $product->get_price_html(); ?></span>                    
                    </a>
                    <?php woocommerce_template_loop_add_to_cart( $loop->post, $product ); ?>
                </li>
    <?php 
    	*/
    	endwhile; 

    	echo $devuelve;
?>
    <?php wp_reset_query();
	die();
}

//AL CAMBIAR LOS FORMULARIOS DEBEMOS DEVOLVER LOS PORDUCTOS DE UNA CATEGORIA
add_action( 'wp_ajax_my_trae_categorias', 'trae_categorias_espe' );
add_action( 'wp_ajax_nopriv_my_trae_categorias', 'trae_categorias_espe' );

function trae_categorias_espe() {
  global $wpdb; // this is how you get access to the database
  //$whatever = 10;

  $diaHoy = date("Y-m-d");

  $idFormulario = $_POST['formulario'];
  $idCategoria = "";

  //BUSCAMOS LA ID RELACIONADA
  $cat = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}df_form WHERE form = {$idFormulario} ");

  foreach ($cat as $key) {
    $idCategoria = $key->product;
  }

  //BUSCAMOS EL SLUG
  $slugs = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}terms WHERE term_id = {$idCategoria} ");
  $slugCate = "";
  foreach ($slugs as $keys) {
    $slugCate = $keys->slug;
  }

  //echo "--------------> la categoria es: ".$idCategoria." y el formulario es: ".$idFormulario." (".$slugCate.")";

  echo devuelve_productos_j($slugCate);

  //echo $diaHoy;

  die();
}

//ES LA MISMA QUE ARRIBA SOLO QUE TRAE PRODUCTOS DE UNA CATEGORIA EN PARTICULAR
function devuelve_productos_j($categoria = "") {
  $devuelve = "";
  if($categoria == ""){
    $args = array( 'post_type' => 'product', 'orderby' => 'asc' );
  }else{
    //product_cat
    $args = array( 'post_type' => 'product', 'product_cat' => $categoria, 'orderby' => 'asc' );
  }
  $loop = new WP_Query( $args );
  while ( $loop->have_posts() ) : $loop->the_post(); global $product; 
    if($product->is_type('variable')){
      //the_title();
      //AQUI SACAMOS LAS DISTINTAS VARIABLES DE ESTE PRODUCTO
      $available_variations = $product->get_available_variations();
      foreach ($available_variations as $key => $value){
        $devuelve .= "<option value='".$product->get_id().",".$value['variation_id']."'>".$value['price_html']."</option>";
      }
    }
  endwhile; 

  echo $devuelve;
  wp_reset_query();
  //die();
}


//ES LA MISMA QUE ARRIBA SOLO QUE TRAE SOLO LOS PRODUCTOS
function devuelve_productos_unicos($categoria = "") {
  $devuelve = "";
  if($categoria == ""){
    $args = array( 'post_type' => 'product', 'orderby' => 'asc' );
  }else{
    //product_cat
    $args = array( 'post_type' => 'product', 'product_cat' => $categoria, 'orderby' => 'asc' );
  }
  $loop = new WP_Query( $args );
  while ( $loop->have_posts() ) : $loop->the_post(); global $product; 
    if($product->is_type('variable')){
      //the_title();
      //AQUI SACAMOS LAS DISTINTAS VARIABLES DE ESTE PRODUCTO
      $devuelve .= "<option value='".$product->get_id()."'>".$product->get_title()."</option>";    }
  endwhile; 

  echo $devuelve;
  wp_reset_query();
  //die();
}

//TRAEMOS SOLO LAS CATEGORIAS DE LOS PRODUCTOS
function categorias_productos(){
  $devuelve = "";
  $orderby = 'name';
  $order = 'asc';
  $hide_empty = false ;
  $cat_args = array(
      'orderby'    => $orderby,
      'order'      => $order,
      'hide_empty' => $hide_empty,
  );
   
  $product_categories = get_terms( 'product_cat', $cat_args );
   
  if(!empty($product_categories)){
    foreach ($product_categories as $key => $category) {
      //get_term_link($category)
      $devuelve .= "<option value='".$category->term_id."'>".$category->name."</option>";
    }
  }

  echo $devuelve;
}

/*
**
	ANCLA QUE SE EJECUTA CUANDO SE ENVIA EXITOSAMENTE EL FORMULARIO
**
*/
//FUNCTION QUE DEVUELVE LABEL DE FORMULARIO
function devuelveLabelFinal($label){
	global $wpdb;
	global $user;

	$idUsuario = $user->ID;


	//FORMULARIOS QUE QUEREMOS RELACIONAR 
	$formulariosR = $_SESSION['formulario-consultar'];

	//ARREGLO QUE RECORREREMOS SEGUN EL FORMULARIO
	$datosForm = [];


	//AQUI SACAMOS LA CONSULTA PARA TRAERNOS TODOS LOS CAMPOS 
	$parametrosForm = [];

	//return 'Esto es el contenido';

	//EN ESTE VAMOS A TRAER LOS DATOS DEL FORMULARIO
	//SACAMOS EL NOMBRE DEL USUARIO
	$nombreSuser = $wpdb->get_results("SELECT display_meta FROM {$wpdb->prefix}rg_form_meta WHERE form_id = ".$formulariosR." ");

	//echo "SELECT value FROM wp_2_rg_lead_detail WHERE lead_id = ".$_GET['id']." AND form_id = 12 AND field_number = $formulariosR ";

	$cualNombre = "";
	foreach ($nombreSuser as $atributoss) {
		//ARREGLO MOMENTANEO
		$momentaneo = [];
		//relacionMetas($con3['display_meta']);
		//echo "<br>/<br>";

		//echo "<h1>DISPLAY META -> ".$atributoss->display_meta."</h1>";

		array_push($momentaneo, $formulariosR);
		array_push($momentaneo, relacionMetas($atributoss->display_meta));

		//GUARDANDO LA DATA EN EL ARREGLO DE FORMULARIO
		array_push($datosForm, $momentaneo);

		//VACIANDO ARREGLO MOMENTANEO
		$momentaneo = [];
	}


	//CONSULTAMOS EL ULTIMO PARAMETRO INSERTADO EN EL FORMULARIO
	for ($i=0; $i < count($datosForm); $i++) { 
		//
		for ($u=0; $u < count($datosForm[$i][1]); $u++) { 
			//echo $datosForm[$i][0]." ".$datosForm[$i][1][$u][0]." ".$datosForm[$i][1][$u][1]."<br>";
			if($label == $datosForm[$i][1][$u][0]){
				return $datosForm[$i][1][$u][1];//AQUI DEVOLVEMOS EL LABEL NECESITADO
			}
		}
	}
}

//AQUI VIENE EL SUBMIT
$formularioFinalizado = 26;
//ESTE ES EL FORMULARIO PRO
$formularioFinalizadoPro = 23;

//add_action( 'gform_after_submission_'.$formularioFinalizado, 'producto_seleccionado', 10, 2 );
//add_action( 'gform_after_submission_'.$formularioFinalizadoPro, 'producto_seleccionado', 10, 2 );
add_action( 'gform_after_submission', 'producto_seleccionado', 10, 2 );

//HAY QUE HACER UN PREVIO AQUI PARA PODER ENVIAR EL FORMULARIO QUE ES --> https://docs.gravityforms.com/form-object/
//SEGUN LO DE ARRIVA LO PODEMOS ENVIAR CON EL $form['id'] 
function producto_seleccionado($entry, $form){
  global $wpdb;
	global $formularioFinalizado;

	$pos = get_post( $entry['id'] );

	$_SESSION['entrada-creada'] = $entry['id'];

	$_SESSION['formulario-consultar'] = $form['id'];
	$user = wp_get_current_user();

	$nombreCompleto = "";
	$temporadaJ 	= "";
	$competicionJ 	= "";
	$ligaJ 			= "";
	$dorsalJ 		= "";
	$nacionalidadJ	= "";
	$equipoJ		= "";
	$producto1		= "";
	$producto2		= "";

	$datosEnviados	= [];


	foreach ( $form['fields'] as $field ) {
        $inputs = $field->get_entry_inputs();
        if ( is_array( $inputs ) ) {
            foreach ( $inputs as $input ) {
                $value = rgar( $entry, (string) $input['id'] );
                // do something with the value
                if($value != ""){

                  $inputId = "";
                  $cual = ".";

                  $pos = strpos($input['id'], $cual);
                  $inputId = substr($input['id'], 0, $pos);

                  //MOMENTANEO
                  $momentaneo = [];
                  array_push($momentaneo, $user->ID);
                  array_push($momentaneo, $inputId);
                  array_push($momentaneo, $value);

                  //AGREGAMOS AL ARREGLO GENERAL
                  array_push($datosEnviados, $momentaneo);
                  $momentaneo = [];

                  //CREAREMOS EL NOMBRE DEL USUARIO REGISTRADO
                  if(devuelveLabelFinal($input['id']) == "Nombre" || devuelveLabelFinal($input['id']) == "Apellido" || devuelveLabelFinal($input['id']) == "Apellido materno"){
                    //echo "<h1>Este es el nombre -> ".$value."</h1>";
                    if($cuentaN == 0){
                      $nombreCompleto .= $value;
                    }else{
                      $nombreCompleto .= " ".$value;
                    }

                    $cuentaN++;
                  }

                  //GUARDAMOS EL ID DEL USUARIO PARA PODER RELACIONARLO DE NO EXISTIR
                  if(devuelveLabelFinal($input['id']) == "Elige tu equipo"){
                    $equipoJ = $value;
                  }

                  //DORSAL
                  if(devuelveLabelFinal($input['id']) == "Número que desea en camiseta"){
                    $dorsalJ = $value;
                  }

                  //NACIONALIDAD
                  if(devuelveLabelFinal($input['id']) == "País"){
                    $nacionalidadJ = $value;
                  }

                  //COMPETICION
                  if(devuelveLabelFinal($input['id']) == "Competiciones"){
                    $competicionJ = $value;
                    //echo "<h1>llega a los ID</h1>";
                  }

                  //TEMPORADA
                  if(devuelveLabelFinal($input['id']) == "Temporada"){
                    $temporadaJ = $value;
                  }

                  //LIGA
                  if(devuelveLabelFinal($input['id']) == "Liga"){
                    $ligaJ = $value;
                  }

                  	//Plan de pago
		            if(devuelveLabelFinal($input['id']) == "Plan de pago"){
		            	//echo "<h1>Pasas</h1>";
		            	$tipos = explode(",", $value);
						$producto1 = $tipos[0];
						$producto2 = $tipos[1];
		            }

                  //echo "<h1> ".$input['id'] ." - ".$value."</h1>";
                }
            }
        }else{
            $value = rgar( $entry, (string) $field->id );

            //MOMENTANEO
            $momentaneo = [];
            array_push($momentaneo, $user->ID);
            array_push($momentaneo, $field->id);
            array_push($momentaneo, $value);

            //AGREGAMOS AL ARREGLO GENERAL
            array_push($datosEnviados, $momentaneo);
            $momentaneo = [];

            //CREAREMOS EL NOMBRE DEL USUARIO REGISTRADO
            if(devuelveLabelFinal($field->id) == "Nombre" || devuelveLabelFinal($field->id) == "Apellido" || devuelveLabelFinal($field->id) == "Apellido materno"){
              //echo "<h1>Este es el nombre -> ".$value."</h1>";
              if($cuentaN == 0){
                $nombreCompleto .= $value;
              }else{
                $nombreCompleto .= " ".$value;
              }

              $cuentaN++;
            }

            //GUARDAMOS EL ID DEL USUARIO PARA PODER RELACIONARLO DE NO EXISTIR
            if(devuelveLabelFinal($field->id) == "Elige tu equipo"){
              $equipoJ = $value;
            }

            //DORSAL
            if(devuelveLabelFinal($field->id) == "Número que desea en camiseta"){
              $dorsalJ = $value;
            }

            //NACIONALIDAD
            if(devuelveLabelFinal($field->id) == "País"){
              $nacionalidadJ = $value;
            }

            //COMPETICION
            if(devuelveLabelFinal($field->id) == "Competiciones"){
              $competicionJ = $value;
              //echo "<h1>llega a los Field</h1>";
            }

            //TEMPORADA
            if(devuelveLabelFinal($field->id) == "Temporada"){
              $temporadaJ = $value;
            }

            //LIGA
            if(devuelveLabelFinal($field->id) == "Liga"){
              $ligaJ = $value;
            }

            //Plan de pago
            if(devuelveLabelFinal($field->id) == "Plan de pago"){
            	//echo "<h1>Pasas</h1>";
            	$tipos = explode(",", $value);
				$producto1 = $tipos[0];
				$producto2 = $tipos[1];
            }

            // do something with the value

            //echo "<h1> ".$field->id." - ".$value."</h1>";
            //echo "<h1>".$valorCompleto."</h1>";

            //LO OCULTAMOS PORQUE YA NO LO NECESITAMOS
            //funcionInsert($value, $valorCompleto);
        }
    }

    //DATOS DEL JUGADOR
    $datosJugador = [];
    array_push($datosJugador, $nombreCompleto);
    array_push($datosJugador, $equipoJ);
    array_push($datosJugador, $dorsalJ);
    array_push($datosJugador, $nacionalidadJ);
    array_push($datosJugador, $competicionJ);
    array_push($datosJugador, $temporadaJ);

    //CREAMOS UNA SESION Y GUARDAMOS LOS DOS DATOS
    $arregloUsuarios = [];

    array_push($arregloUsuarios, $datosEnviados);
    array_push($arregloUsuarios, $datosJugador);

    //DATOS JUGADOR
    $_SESSION['gf-datos-usuario-registro'] 	= $arregloUsuarios;
    $_SESSION['gf-datos-competicion'] 		= $competicionJ;
    $_SESSION['gf-datos-temporada'] 		= $temporadaJ;
    $_SESSION['gf-datos-liga'] 				= $ligaJ;

    //DATOS DEL PRODUCTO
    $_SESSION['gf-datos-producto1'] 		= $producto1;
    $_SESSION['gf-datos-producto2'] 		= $producto2;

    $idFormulario = $form['id'];

    //PRIMERO REVISAMOS QUE ESTE FORMULARIO TENGA UN PRODUCTO ASIGNADO, SI NO ES ASI PROCEDEMOS A GUARDAR COMO SUB
    $revP = $wpdb->get_results("SELECT f.*, d.product AS product, d.product_hijo AS product_hijo, d.value AS value FROM wp_df_tags AS d, wp_rg_form AS f WHERE d.form = {$idFormulario} AND d.form = f.id AND f.is_active = '1' AND f.is_trash = '0' ");
    $saberSihay = 0;
    foreach ($revP as $keysz) {
      $saberSihay++;
    }

    //ENVIAMOS EL ARREGLO DE TODA LA INFORMACION ENVIADA A UNA NUEVA FUNCTION QUE CONSULTE O INSERTE TODO
    if($saberSihay > 0){
      //AGREGAMOS TODO ESTO AL CARRITO DE COMPRAS
      add_action( 'template_redirect', 'add_product_to_cart' );
    }
}

/*
**
	AGREGAMOS FUNCION QUE TE AYUDA A ENVIAR A LA FUNCION ANTIGUA QUE RELACIONA LOS DATOS DEL USUARIO
**
*/

function recorta_envia($arreglos, $idRed){
	$datosEnviados = $arreglos[0];
	$datosJugador = $arreglos[1];
	relacionEnviado($datosEnviados, $datosJugador, $idRed);
}

//add_action( 'template_redirect', 'add_product_to_cart' );//LO COMENTAMOS PORQUE YA NO ES AUTOMATICO
/* ESTE ES UN PRODUCTO SIMPLE
function add_product_to_cart() {
	if ( ! is_admin() ) {
		$product_id = 1520; //replace with your own product id
		$found = false;
		//check if product already in cart
		if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				if ( $_product->get_id() == $product_id )
					$found = true;
			}
			// if product not found, add it
			if ( ! $found )
				WC()->cart->add_to_cart( $product_id );
		} else {
			// if no products in cart, add it
			WC()->cart->add_to_cart( $product_id );
		}
	}
}
*/

//ESTE ES UN PRODUCTO VARIABLE
function add_product_to_cart() {
	//DEFINIMOS LOS ID DE LOS PRODUCTOS
	$id_producto = $_SESSION['gf-datos-producto1'];
	$id_variante = $_SESSION['gf-datos-producto2'];

	//echo "<h1>Aqui llegamos {$id_producto} - {$id_variante}</h1>";

	if ( ! is_admin() ) {
		//LO PRIMERO QUE DEBEMOS HACER ES BORRAR EL CONTENIDO DEL CARRO
		WC()->cart->empty_cart();

		//LUEGO PROCEDEMOS A REALIZAR EL RESTO DE TRANSACCIONES CON EL CARRO

		$product_id = $id_producto; //replace with your own product id
		$product_variant = $id_variante;//ID DE LA VARIACION

		//PARAMETROS EXTRA AQUI TENEMOS LA TEMPORADA EN LA QUE SE INSCRIBIO EL USUARIO
		$cart_item_data = array('Liga' => $_SESSION['gf-datos-liga'], 'Competicion' => $_SESSION['gf-datos-competicion'], 'Temporada' => $_SESSION['gf-datos-temporada'], 'Transacción' => $_SESSION['entrada-creada'] );

		$found = false;
		//check if product already in cart
		if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$_product = $values['data'];
				if ( $_product->get_id() == $product_variant ){
					$found = true;
				}
			}
			// if product not found, add it
			if ( ! $found )
				WC()->cart->add_to_cart( $product_id, 1, $product_variant, $cart_item_data);
		} else {
			// if no products in cart, add it
			WC()->cart->add_to_cart( $product_id, 1, $product_variant, $cart_item_data);
		}
	}

  //AL FINALIZAR TODO PROCEDEMOS A HACER EL REDIRECT AL CARRO
	//
	//if($_SESSION['gf-datos-competicion'] != "" && $_SESSION['gf-datos-temporada'] != "" && $_SESSION['entrada-creada'] != ""){
	wp_redirect("https://jleaguepanama.com/?page_id=1417");
	//}else{
		//echo "<h1>".$_SESSION['gf-datos-competicion']." ".$_SESSION['gf-datos-temporada']." ".$_SESSION['entrada-creada']." (".$_SESSION['formulario-consultar'].")</h1>";
	//}
}

//---> ESTE ES EL COMPLEMENTO PARA PASAR LA TRANSACCION DE COMPRA
add_action('woocommerce_thankyou', 'enroll_student', 10, 1);
function enroll_student( $order_id ) {
	global $wpdb;

	$current_user = wp_get_current_user();

    if ( ! $order_id )
        return;

    // Getting an instance of the order object
    $order = wc_get_order( $order_id );

    if($order->is_paid())
        $paid = 'yes';
    else
        $paid = 'no';

    // iterating through each order items (getting product ID and the product object) 
    // (work for simple and variable products)
    foreach ( $order->get_items() as $item_id => $item ) {

        if( $item['variation_id'] > 0 ){
            $product_id = $item['variation_id']; // variable product
        } else {
            $product_id = $item['product_id']; // simple product
        }

        // Get the product object
        $product = wc_get_product( $product_id );

    }

    // Ouptput some data
    if($paid == "yes"){
    	//echo '<p>Order ID: '. $order_id . ' — Order Status: ' . $order->get_status() . ' — Order is paid: ' . $paid . '</p>';
    	//AQUI HACEMOS EL PROCESO DE INSERTAR EN DB LO QUE EL CLIENTE ESTA PAGANDO
    	$wpdb->insert(
					"{$wpdb->prefix}postmeta",
					array(
						'post_id'		=> $current_user->ID,
						'meta_key'		=> 'id_pago_wo',
						'meta_value'	=> $order_id
					)
			);

    	//ADEMAS DE ESTO PROCEDEMOS A CREAR EL JUGADOR Y TODO LO DEMAS
    	$idRed = $_SESSION['id-red'];//ESTO DEBEMOS REVISAR PARA PODER ENVIAR O SACAR DINAMICAMENTE ESTA INFORMACION
		  recorta_envia($_SESSION['gf-datos-usuario-registro'], $idRed);
    }
}

//AL PRINCIPIO AGREGAREMOS ESTO AL FORMULARIO PARA QUE PUEDA MOSTRAR TODOS LOS PRODUCTOS DE UNA CATEGORIA
//https://asdqwe.net/blog/woocommerce-display-products-specific-category/