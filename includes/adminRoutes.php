<?php
  global $wpdb;
  $tableRoutes        = $wpdb->prefix . 'routes';
  $tableRoutesStation = $wpdb->prefix . 'routes_station';
  // create
  if (isset($_POST['newsubmit'])) {
    $newName        = $_POST['newName'];
    $newDescription = $_POST['newDescription'];
    $newState       = $_POST['newState'];
    unset($_POST);
    $wpdb->query("INSERT INTO $tableRoutes (name, description, status) VALUES('$newName','$newDescription','$newState')");
    echo "<script>location.replace('".PLG_RUTA."');</script>";
  }
  // update
  if (isset($_POST['uptsubmit'])) {
    $id             = $_POST['uptid'];
    $uptName        = $_POST['uptName'];
    $uptDescription = $_POST['uptDescription'];
    $uptState       = $_POST['uptState'];
    $wpdb->query("UPDATE $tableRoutes SET name='$uptName',description='$uptDescription', status='$uptState' WHERE id = '$id'");
    echo "<script>location.replace('".PLG_RUTA."');</script>";
  }
  // delete
  if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $wpdb->query("DELETE FROM $tableRoutes WHERE id='$id'");
    $wpdb->query("DELETE FROM $tableRoutesStation WHERE id_routes='$id'");
    echo "<script>location.replace('".PLG_RUTA."');</script>";
  }
  ?>
  <div class="wrap">
    <h2>Creación de Rutas</h2>
    <table class="wp-list-table widefat striped">
      <thead>
        <tr>
          <th width="25%">Nombre</th>
          <th width="25%">Descripción</th>
          <th width="25%">Estado</th>
          <th width="25%">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <form action="" method="post">
          <tr>
            <td><input type="text" id="newName" name="newName"></td>
            <td><textarea id="newDescription" name="newDescription"></textarea></td>
            <td>
            	<select name="newState" id="newState">
            		<option value="1">Activa</option>
            		<option value="2">Inactiva</option>
            	</select>
            </td>
            <td>
            	<button id="newsubmit" name="newsubmit" type="submit">Crear Ruta</button>
            </td>
          </tr>
        </form>
        <?php
            $result = $wpdb->get_results("SELECT * FROM {$tableRoutes} order by id DESC");
            // create table and Header
            echo "
	        	<tr>
	        		<td width='100%' colspan='4'>
	        			<table width='100%'>
							<tr>
								<td colspan='5' style='text-align:center;'><b>Listado Rutas Rutery</b></td>
							</tr>
              <tr>
                <td width='10%'><b>ID</b></td>
                <td width='15%'><b>Nombre</b></td>
                <td width='25%'><b>Descripción</b></td>
                <td width='15%'><b>Estado</b></td>
                <td width='35%'><b>Acciones</b></td>
              </tr>";
			// iterate Items
			foreach ($result as $pos => $print) {
			echo "
	        	<tr>
	        		<td width='100%' colspan='4'>	        		
						<tr>
							<td>".$print->id."</td>
							<td>".$print->name."</td>
							<td>".$print->description."</td>
							<td>".(($print->status == 1)?'Activa':'Inactiva')."</td>
							<td>
								<a href='admin.php?page=adminRoutesStations&rId=".$print->id."'>
									<button type='button'>Agregar Parada</button>
								</a>
								<a href='admin.php?page=adminRoutes&upt=".$print->id."'>
									<button type='button'>Actualizar</button>
								</a>
								<a href='admin.php?page=adminRoutes&del=".$print->id."'>
									<button type='button' onclick='confirm(\"¿Está seguro de eliminar la ruta seleccionada?\")'>Eliminar</button>
								</a>
							</td>
						</tr>
					</td>
				</tr>";
          	}
          	echo "
        			</table>
				</td>
			</tr>";
        ?>
      </tbody>  
    </table>
    <br>
    <br>
    <?php
    if (isset($_GET['upt'])) {
        $upt_id = $_GET['upt'];
        $item   = $wpdb->get_row("SELECT * FROM $tableRoutes WHERE id='$upt_id'");
        echo "
        <table class='wp-list-table widefat striped'>
          <thead>
            <tr>
              <th width='25%'>Nombre</th>
              <th width='25%'>Descripción</th>
              <th width='25%'>Estado</th>
              <th width='25%'>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <form action='admin.php?page=adminRoutes' method='post'>
              <tr>
                <td width='25%'>
                	<input type='hidden' id='uptid' name='uptid' value='$item->id'>
                	<input type='text' id='uptName' name='uptName' value='$item->name'>
                </td>
                <td width='25%'><input type='text' id='uptDescription' name='uptDescription' value='$item->description'></td>
                <td width='25%'>
					<select name='uptState' id='uptState'>
            		<option value='1' ".(($item->status == 1)?'selected':'').">Activa</option>
            		<option value='2' ".(($item->status != 1)?'selected':'').">Inactiva</option>
            	</select>
                </td>
                <td width='25%'>
                	<button id='uptsubmit' name='uptsubmit' type='submit'>Actualizar</button>
                	<a href='admin.php?page=adminRoutes'><button type='button'>CANCEL</button></a>
            	</td>
              </tr>
            </form>
          </tbody>
        </table>";
	}
	?>
	</div>