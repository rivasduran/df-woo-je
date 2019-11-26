<?php
/*
**
	PAGINA DE VISTAS
**
*/

//VISTA DE FORMULARIOS
function ver_formularios_j(){
	global $wpdb;

	//AQUI TENEMOS LA RELACION DE LOS PRODUCTOS Y LOS TAGS
	if(isset($_POST['crearc_produc_tags'])){
		$products = explode(",", $_POST['productos']);

		$product = $products[0];
		$product_hijo = $products[1];

		$forms = $_POST['formularios'];

		//TAGS
		$liga = $_POST['nombreLiga'];
		$competicion = $_POST['nombreAno'];

		//PRIMERO DEBEMOS REVISAR QUE CADA UNO DE ESTOS PUNTOS NO ESTE YA RELACIONADO
		$rela1 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}df_tags WHERE form = '{$forms}' AND product = '{$product}' AND product_hijo = '$product_hijo' AND tags = 'Liga' AND value = '{$liga}' ");

		if(count($rela1) <= 0){
			//SI ESTO NO EXISTE PROCEDEMOS A INSERTAR
			$wpdb->insert(
					"{$wpdb->prefix}df_tags",
					array(
						'form' 			=> $forms,
						'product'		=> $product,
						'product_hijo'	=> $product_hijo,
						'tags'			=> 'Liga',
						'value' 		=> $liga
					)
				);
		}

		//HACEMOS LO MISO CON LA COMPETICION
		$rela1 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}df_tags WHERE form = '{$forms}' AND product = '{$product}' AND product_hijo = '$product_hijo' AND tags = 'competicion' AND value = '{$competicion}' ");

		if(count($rela1) <= 0){
			//SI ESTO NO EXISTE PROCEDEMOS A INSERTAR
			$wpdb->insert(
					"{$wpdb->prefix}df_tags",
					array(
						'form' 			=> $forms,
						'product'		=> $product,
						'product_hijo'	=> $product_hijo,
						'tags'			=> 'competicion',
						'value' 		=> $competicion
					)
				);
		}

	}

	if(isset($_POST['borrarc_produc_tags'])){
		$forms = $_POST['borrarc_produc_tags'];

		//AQUI ELIMINAREMOS

		$wpdb->delete(
					"{$wpdb->prefix}df_tags",
					array(
						'form' 			=> $forms
					)
				);

	}
?>
	<h1>Crear Tags de formulario:</h1>

	<div>
		<div class="formularios-approval formularioCertificados formularioTags">
			<form method="post" action="">
			    <input type="text" name="crearc_produc_tags" value="1" required style="display: none;" />

			    <!-- TITULO DEL FORMULARIO -->
				<div class="form-group">
					<label for="inputTitle">Formulario</label>
					<select class="form-control formularios" name="formularios">
						<option value="">Seleccione</option>

	<?php
					//CONSULTAMOS TODOS LOS CERTIFICADOS QUE ESTEN ACTIVOS
					$certA = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rg_form WHERE is_active = '1' AND is_trash = '0' ");
					foreach ($certA as $key) {
	?>
						<option value="<?php echo $key->id; ?>"><?php echo $key->title; ?></option>
	<?php
					}	
	?>
					</select>
					<small id="title" class="form-text text-muted">Selecciona el Formulario.</small>
				</div>


				<!-- SELECCIONAMOS LA VARIANTE DEL PRODUCTO -->
				<div class="form-group">
					<label for="inputTitle">Metodos de pago</label>
					<select class="form-control metodoPagoFiltrado" name="productos">
						<option value="">Seleccione</option>
	<?php
						//devuelve_productos_j();	
	?>
					</select>
					<small id="title" class="form-text text-muted">Selecciona el metodo.</small>
				</div>

				<!-- Liga -->
				<div class="form-group">
					<label for="inputTitle">Nombre de la liga</label>
					<input type="text" class="form-control" name="nombreLiga" id="inputTexto" placeholder="Enter Texto">
					<small id="title" class="form-text text-muted">Nombre de la liga, puede ser Apertura, Cierre o Apertura - Cierre.</small>
				</div>

				<!-- Año -->
				<div class="form-group">
					<label for="inputTitle">Año</label>
					<input type="text" class="form-control" name="nombreAno" id="inputTexto" placeholder="Enter Texto">
					<small id="title" class="form-text text-muted">Nombre del año.</small>
				</div>

				<input type="submit" value="Relacionar" />
				
			</form>
		</div>

	<!-- AQUI PROCEDEMOS A AMOSTRAR LOS TAGS JUNTO CON SUS CERTIFICADOS -->
	<div class="formularios-approval">
		<table class="wp-list-table widefat fixed striped pages">
			<thead>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="cb-select-all-1">Seleccionar todos</label>
						<input id="cb-select-all-1" type="checkbox">
					</td>
					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Formulario</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>

					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Categoria Producto</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Tags</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Eliminar</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>

			<!-- -->
	<?php
			$misTags = $wpdb->get_results("SELECT f.*, d.product AS product, d.product_hijo AS product_hijo, d.value AS value FROM {$wpdb->prefix}df_tags AS d, {$wpdb->prefix}rg_form AS f WHERE d.form = f.id AND f.is_active = '1' AND f.is_trash = '0' ");
			//$misTags = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ap_categoria ");

			//d.product = '{$categoriaP}' 
			foreach ($misTags as $tags) {
				//echo  "<h1>".$user->term_id."</h1>";
				$idTags = $tags->id;
				//echo "<h1>---> {$idTags}</h1>";
	?>
				<tbody id="the-list">
					<tr id="post-<?php echo $idTags; ?>" class="iedit author-self level-0 post-<?php echo $idTags; ?> type-page status-publish hentry">
						<th scope="row" class="check-column">			
							<label class="screen-reader-text" for="cb-select-<?php echo $idTags; ?>">Elige Página de ejemplo</label>
							<input id="cb-select-<?php echo $idTags; ?>" type="checkbox" name="post[]" value="<?php echo $idTags; ?>">
							<div class="locked-indicator">
							<span class="locked-indicator-icon" aria-hidden="true"></span>
							<span class="screen-reader-text">“Página de ejemplo” está bloqueado</span>
							</div>
						</th>
						<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
							<div class="locked-info">
								<span class="locked-avatar"></span> 
								<span class="locked-text"></span>
							</div>
							<strong>
								<!--
								<a class="row-title" href="http://localhost/gvivir-plugin/wp-admin/post.php?post=2&amp;action=edit" aria-label="“Página de ejemplo” (Editar)">
								-->
								<?php echo $tags->title; ?>
								<!--
								</a>
								-->
							</strong>

							<!--
							<div class="row-actions">
								<span class="edit">
									<a href="<?php echo $actual_link."&amp;edit=".$tags->id; ?>" aria-label="Editar “Página de ejemplo”">Editar</a> | 
								</span>

								<span class="trash">
									<a href="<?php echo $actual_link."&amp;eliminarUsuario=".$tags->id; ?>" class="submitdelete" aria-label="Mover “Página de ejemplo” a la papelera">Papelera</a> | 
								</span>
							</div>
							-->
						</td>	

						<td>
							<strong>
								<?php 
									$args = array( 'post_type' => 'product', 'orderby' => 'asc' );
									$loop = new WP_Query( $args );
									  while ( $loop->have_posts() ) : $loop->the_post(); global $product; 
									    if($product->is_type('variable')){
									    	if($tags->product == $product->get_id()){
												the_title();
												//AQUI SACAMOS LAS DISTINTAS VARIABLES DE ESTE PRODUCTO
												$available_variations = $product->get_available_variations();
												foreach ($available_variations as $key => $value){
													if($tags->product_hijo == $value['variation_id']){
														//$devuelve .= "<option value='".$product->get_id().",".$value['variation_id']."'>".$value['price_html']."</option>";
														echo " - ".$value['price_html'];
													}
												}
									    	}
									    }
									  endwhile; 
								?>
							</strong>
						</td>

						<td>
							<strong>
								<?php echo $tags->value; ?>
							</strong>
						</td>

						<td>
							<strong>
	<?php 
								/*
								$categori = $tags->status;

								$statusG = $wpdb->get_results("SELECT name FROM {$table_categorias} WHERE id = {$categori}");

								foreach ($statusG as $tags) {
									$categori = $tags->name;
								}

								echo $categori; 
								*/
	?>
							</strong>

							<form method="post" action="">
								<!-- AQUI ENVIAMOS A ELIMINAR LA RELACION COMLETA ENTRE EL FORMULARIO  -->
								<input type="hidden" name="borrarc_produc_tags" value="<?php echo $tags->id; ?>">
								<button type="submit">Eliminar</button>
							</form>

						</td>
					</tr>
				</tbody>
	<?php
			}
	?>
			</table>
		</div>
	</div>
<?php
}


/*
**
	FORMULARIO RELACIONAR GF PRODUCTOS CATEGORIAS
**
*/
function ver_formularios_cat_pro(){
	global $wpdb;

	//REALIZAMOS LA RELACION SI SE ENVIA EL FORMULARIO
	if(isset($_POST['df_form_product'])){
		//RECUPERAMOS LAS VARIABLES
		$formulario = $_POST['formulario'];
		$categoriaP = $_POST['categoria_producto'];

		//PRIMERO CONSULTAMOS SI ESTA RELACION YA EXISTE
		$exist = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}df_form WHERE form = '{$formulario}' AND product = '{$categoriaP}' ");
		if(count($exist) <= 0){
			//SI NO EXISTE NADA PROCEDEMOS A INSERTAR EN DB

			$wpdb->insert(
					"{$wpdb->prefix}df_form",
					array(
						'form'		=> $formulario,
						'product'		=> $categoriaP
					)
			);

			echo "<div class='respuestaResultado'><h1>Relacionado con exito</h1></div>";
		}
	}

	//SI EL ENVIO ES DE ELIMINAR
	if(isset($_POST['eliminar_relacion_f_p'])){
		//RESCATAMOS EL ID DEL INPUT QUE QUIERO ELIMINAR
		$formulario = $_POST['eliminar_relacion_f_p'];
		
		$wpdb->delete(
				"{$wpdb->prefix}df_form",
				array(
					'form'		=> $formulario
				)
		);
		
		echo "<div class='respuestaResultado'><h1>Eliminado adecuadamente</h1></div>";
	}
?>
	<h1>Relacionar Formularios con productos:</h1>

	<div>
		<div class="formularios-approval formularioCertificados">
			<form method="post" action="">
			    <input type="text" name="df_form_product" value="1" required style="display: none;" />

			    <!-- TITULO DEL FORMULARIO -->
				<div class="form-group">
					<label for="inputTitle">Formulario</label>
					<select class="form-control" name="formulario">
						<option value="">Seleccione</option>

	<?php
					//CONSULTAMOS TODOS LOS CERTIFICADOS QUE ESTEN ACTIVOS
					$certA = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rg_form WHERE is_active = '1' AND is_trash = '0' ");
					foreach ($certA as $key) {
	?>
						<option value="<?php echo $key->id; ?>"><?php echo $key->title; ?></option>
	<?php
					}	
	?>
					</select>
					<small id="title" class="form-text text-muted">Selecciona el Formulario.</small>
				</div>


				<!-- SELECCIONAMOS LA CATEGORIA -->
				<div class="form-group">
					<label for="inputTitle">Categoria de productos.</label>
					<select class="form-control" name="categoria_producto">
						<option value="">Seleccione</option>
	<?php
						categorias_productos();	
	?>
					</select>
					<small id="title" class="form-text text-muted">Selecciona la categoria de producto.</small>
				</div>

				<input type="submit" value="Relacionar" />

				
			</form>
		</div>

	<!-- AQUI PROCEDEMOS A AMOSTRAR LOS TAGS JUNTO CON SUS CERTIFICADOS -->
	<div class="formularios-approval ">
		<table class="wp-list-table widefat fixed striped pages">
			<thead>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="cb-select-all-1">Seleccionar todos</label>
						<input id="cb-select-all-1" type="checkbox">
					</td>
					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Formulario</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>

					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Categoria Producto</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Eliminar</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>

			<!-- -->
	<?php
			$misTags = $wpdb->get_results("SELECT f.*, d.product AS product FROM {$wpdb->prefix}df_form AS d, {$wpdb->prefix}rg_form AS f WHERE d.form = f.id AND f.is_active = '1' AND f.is_trash = '0' ");
			//$misTags = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ap_categoria ");

			//d.product = '{$categoriaP}' 
			foreach ($misTags as $tags) {
				//echo  "<h1>".$user->term_id."</h1>";
				$idTags = $tags->id;
				//echo "<h1>---> {$idTags}</h1>";
	?>
				<tbody id="the-list">
					<tr id="post-<?php echo $idTags; ?>" class="iedit author-self level-0 post-<?php echo $idTags; ?> type-page status-publish hentry">
						<th scope="row" class="check-column">			
							<label class="screen-reader-text" for="cb-select-<?php echo $idTags; ?>">Elige Página de ejemplo</label>
							<input id="cb-select-<?php echo $idTags; ?>" type="checkbox" name="post[]" value="<?php echo $idTags; ?>">
							<div class="locked-indicator">
							<span class="locked-indicator-icon" aria-hidden="true"></span>
							<span class="screen-reader-text">“Página de ejemplo” está bloqueado</span>
							</div>
						</th>
						<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
							<div class="locked-info">
								<span class="locked-avatar"></span> 
								<span class="locked-text"></span>
							</div>
							<strong>
								<!--
								<a class="row-title" href="http://localhost/gvivir-plugin/wp-admin/post.php?post=2&amp;action=edit" aria-label="“Página de ejemplo” (Editar)">
								-->
								<?php echo $tags->title; ?>
								<!--
								</a>
								-->
							</strong>

							<!--
							<div class="row-actions">
								<span class="edit">
									<a href="<?php echo $actual_link."&amp;edit=".$tags->id; ?>" aria-label="Editar “Página de ejemplo”">Editar</a> | 
								</span>

								<span class="trash">
									<a href="<?php echo $actual_link."&amp;eliminarUsuario=".$tags->id; ?>" class="submitdelete" aria-label="Mover “Página de ejemplo” a la papelera">Papelera</a> | 
								</span>
							</div>
							-->
						</td>	

						<td>
							<strong>
								<?php 
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
											if($tags->product == $category->term_id){
												echo $category->name;
											}
										}
									}
								?>
							</strong>
						</td>

						<td>
							<strong>
	<?php 
								/*
								$categori = $tags->status;

								$statusG = $wpdb->get_results("SELECT name FROM {$table_categorias} WHERE id = {$categori}");

								foreach ($statusG as $tags) {
									$categori = $tags->name;
								}

								echo $categori; 

								echo "<br>";
								*/
	?>
								<form method="post" action="">
									<!-- AQUI ENVIAMOS A ELIMINAR LA RELACION COMLETA ENTRE EL FORMULARIO  -->
									<input type="hidden" name="eliminar_relacion_f_p" value="<?php echo $tags->id; ?>">
									<button type="submit">Eliminar</button>
								</form>
							</strong>
						</td>
					</tr>
				</tbody>
	<?php
			}
	?>
			</table>
		</div>
	</div>
<?php
}

/*
**
	SACAMOS LOS DATOS PARA LA RED
**
*/
function ver_formularios_red(){
	global $wpdb;

	//AQUI TENEMOS LA RELACION DE LOS PRODUCTOS Y LOS TAGS
	if(isset($_POST['crearc_produc_red'])){
		$idCategoria = $_POST['categoria_producto_red'];
		$numeroRed = $_POST['numeroRed'];
		$idFormulario = $_POST['formularios'];

		//PRIMERO DEBEMOS REVISAR QUE CADA UNO DE ESTOS PUNTOS NO ESTE YA RELACIONADO
		$rela1 = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}df_red WHERE form = '{$idFormulario}' AND product_cat = '{$idCategoria}' AND red = '{$numeroRed}' ");

		if(count($rela1) <= 0){
			//SI ESTO NO EXISTE PROCEDEMOS A INSERTAR
			$wpdb->insert(
					"{$wpdb->prefix}df_red",
					array(
						'form'			=> $idFormulario,
						'product_cat' 	=> $idCategoria,
						'red'			=> $numeroRed
					)
				);
		}
	}


	//ELIMINAMOS LO CREADO
	if(isset($_POST['eliminarc_produc_red'])){
		$idFormulario = $_POST['eliminarc_produc_red'];
		$wpdb->delete(
					"{$wpdb->prefix}df_red",
					array(
						'form'			=> $idFormulario
					)
				);
	}
?>
	<h1>Relacionar productos con la red:</h1>

	<div>
		<div class="formularios-approval formularioCertificados formularioTags">
			<form method="post" action="">
			    <input type="text" name="crearc_produc_red" value="1" required style="display: none;" />

			    <!-- TITULO DEL FORMULARIO -->
				<div class="form-group">
					<label for="inputTitle">Formulario</label>
					<select class="form-control" name="formularios">
						<option value="">Seleccione</option>

	<?php
					//CONSULTAMOS TODOS LOS CERTIFICADOS QUE ESTEN ACTIVOS
					$certA = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rg_form WHERE is_active = '1' AND is_trash = '0' ");
					foreach ($certA as $key) {
	?>
						<option value="<?php echo $key->id; ?>"><?php echo $key->title; ?></option>
	<?php
					}	
	?>
					</select>
					<small id="title" class="form-text text-muted">Selecciona el Formulario.</small>
				</div>

			    <!-- CATEGORIA FORMULARIO -->
				<div class="form-group">
					<label for="inputTitle">Categoria de productos.</label>
					<select class="form-control" name="categoria_producto_red">
						<option value="">Seleccione</option>
	<?php
						categorias_productos();	
	?>
					</select>
					<small id="title" class="form-text text-muted">Selecciona la categoria de producto.</small>
				</div>

				<!-- Liga -->
				<div class="form-group">
					<label for="inputTitle">Numero de red</label>
					<input type="text" class="form-control" name="numeroRed" id="inputTexto" placeholder="Enter Texto">
					<small id="title" class="form-text text-muted">Numero de la red.</small>
				</div>

				<input type="submit" value="Relacionar" />
				
			</form>
		</div>

	<!-- AQUI PROCEDEMOS A AMOSTRAR LOS TAGS JUNTO CON SUS CERTIFICADOS -->
	<div class="formularios-approval ">
		<table class="wp-list-table widefat fixed striped pages">
			<thead>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="cb-select-all-1">Seleccionar todos</label>
						<input id="cb-select-all-1" type="checkbox">
					</td>

					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Formulario</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>

					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Categoria</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>

					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Numero en la red</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span>Eliminar</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</thead>

			<!-- -->
	<?php
			$misTags = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}df_red");
			//$misTags = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ap_categoria ");

			//d.product = '{$categoriaP}' 
			foreach ($misTags as $tags) {
				//echo  "<h1>".$user->term_id."</h1>";
				$idTags = $tags->id;
				//echo "<h1>---> {$idTags}</h1>";
	?>
				<tbody id="the-list">
					<tr id="post-<?php echo $idTags; ?>" class="iedit author-self level-0 post-<?php echo $idTags; ?> type-page status-publish hentry">
						<th scope="row" class="check-column">			
							<label class="screen-reader-text" for="cb-select-<?php echo $idTags; ?>">Elige Página de ejemplo</label>
							<input id="cb-select-<?php echo $idTags; ?>" type="checkbox" name="post[]" value="<?php echo $idTags; ?>">
							<div class="locked-indicator">
							<span class="locked-indicator-icon" aria-hidden="true"></span>
							<span class="screen-reader-text">“Página de ejemplo” está bloqueado</span>
							</div>
						</th>
						<td class="title column-title has-row-actions column-primary page-title" data-colname="Título">
							<div class="locked-info">
								<span class="locked-avatar"></span> 
								<span class="locked-text"></span>
							</div>
							<strong>
								<!--
								<a class="row-title" href="http://localhost/gvivir-plugin/wp-admin/post.php?post=2&amp;action=edit" aria-label="“Página de ejemplo” (Editar)">
								-->
								<?php 
									//CONSULTAMOS EL NOMBRE DEL TERMINO
									$ter = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rg_form WHERE id = '{$tags->form}' ");
									$titulo_cat = "";

									foreach ($ter as $keyss) {
										$titulo_cat = $keyss->title;
									}
									echo $titulo_cat; 
								?>
								<!--
								</a>
								-->
							</strong>

							<!--
							<div class="row-actions">
								<span class="edit">
									<a href="<?php echo $actual_link."&amp;edit=".$tags->id; ?>" aria-label="Editar “Página de ejemplo”">Editar</a> | 
								</span>

								<span class="trash">
									<a href="<?php echo $actual_link."&amp;eliminarUsuario=".$tags->id; ?>" class="submitdelete" aria-label="Mover “Página de ejemplo” a la papelera">Papelera</a> | 
								</span>
							</div>
							-->
						</td>	

						<td>
							<strong>
								<?php 
									//CONSULTAMOS EL NOMBRE DEL TERMINO
									$ter = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}terms WHERE term_id = '{$tags->product_cat}' ");
									$titulo_cat = "";

									foreach ($ter as $keyss) {
										$titulo_cat = $keyss->name;
									}
									echo $titulo_cat; 
								?>
							</strong>
						</td>

						<td>
							<strong>
								<?php 
									echo $tags->red;
								?>
							</strong>
						</td>

						<td>
							<strong>
	<?php 
								/*
								$categori = $tags->status;

								$statusG = $wpdb->get_results("SELECT name FROM {$table_categorias} WHERE id = {$categori}");

								foreach ($statusG as $tags) {
									$categori = $tags->name;
								}

								echo $categori; 
								*/
	?>
							</strong>
							<form method="post" action="">
								<!-- AQUI ENVIAMOS A ELIMINAR LA RELACION COMLETA ENTRE EL FORMULARIO  -->
								<input type="hidden" name="eliminarc_produc_red" value="<?php echo $tags->form; ?>">
								<button type="submit">Eliminar</button>
							</form>
						</td>
					</tr>
				</tbody>
	<?php
			}
	?>
			</table>
		</div>
	</div>
<?php
}



/*
**
	VISTA PARA EL EXCEL DESCARGABLE
**
*/

function vista_excel_d(){
	global $wpdb;

	//AQUI TENEMOS LA RELACION DE LOS PRODUCTOS Y LOS TAGS
	if(isset($_POST['descarga_excel_inscritos'])){
		$form = $_POST['formularios_descarga'];

		wp_redirect(get_site_url()."/wp-content/plugins/bajar-excel/descarga-excel-final.php?formulario=".$form);

	}
?>
	<h1>Seleccione descargable:</h1>

	<div>
		<div class="formularios-approval formularioCertificados formularioTags">
			<form method="post" action="">
			    <input type="text" name="descarga_excel_inscritos" value="1" required style="display: none;" />

			    <!-- TITULO DEL FORMULARIO -->
				<div class="form-group">
					<label for="inputTitle">Formulario</label>
					<select class="form-control formularios" name="formularios_descarga">
						<option value="">Seleccione</option>

	<?php
					//CONSULTAMOS TODOS LOS CERTIFICADOS QUE ESTEN ACTIVOS
					$certA = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rg_form WHERE is_active = '1' AND is_trash = '0' ");
					foreach ($certA as $key) {
	?>
						<option value="<?php echo $key->id; ?>"><?php echo $key->title; ?></option>
	<?php
					}	
	?>
					</select>
					<small id="title" class="form-text text-muted">Selecciona el Formulario.</small>
				</div>


				<!-- SELECCIONAMOS LA VARIANTE DEL PRODUCTO -->
	<?php
		/*
	?>
				<div class="form-group">
					<label for="inputTitle">Metodos de pago</label>
					<select class="form-control metodoPagoFiltrado" name="productos">
						<option value="">Seleccione</option>
	<?php
						//devuelve_productos_j();	
	?>
					</select>
					<small id="title" class="form-text text-muted">Selecciona el metodo.</small>
				</div>

				<!-- Liga -->
				<div class="form-group">
					<label for="inputTitle">Nombre de la liga</label>
					<input type="text" class="form-control" name="nombreLiga" id="inputTexto" placeholder="Enter Texto">
					<small id="title" class="form-text text-muted">Nombre de la liga, puede ser Apertura, Cierre o Apertura - Cierre.</small>
				</div>

				<!-- Año -->
				<div class="form-group">
					<label for="inputTitle">Año</label>
					<input type="text" class="form-control" name="nombreAno" id="inputTexto" placeholder="Enter Texto">
					<small id="title" class="form-text text-muted">Nombre del año.</small>
				</div>
	<?php
		*/
	?>

				<input type="submit" value="Descargar" />
				
			</form>
		</div>
	</div>
<?php
}
?>