<?php
  global $wpdb;
  $tableRoutes        = $wpdb->prefix . 'routes';
  $tableRoutesStation = $wpdb->prefix . 'routes_station';
  // drivers List
  $lstDrivers         = loadDriversUsers();
  // create
  if (isset($_POST['newsubmit'])) {
    $newName        = $_POST['newName'];
    $newDescription = $_POST['newDescription'];
    $newDrivers     = implode(",", $_POST['newDrivers']);
    $newState       = $_POST['newState'];
    // print_r($newDrivers);die;
    unset($_POST);
    $wpdb->query("INSERT INTO $tableRoutes (name, description, drivers_list, status) 
      VALUES('$newName','$newDescription', '$newDrivers', '$newState')");
    echo "<script>location.reload();</script>";
  }
  // update
  if (isset($_POST['uptsubmit'])) {
    $id             = $_POST['uptid'];
    $uptName        = $_POST['uptName'];
    $uptDescription = $_POST['uptDescription'];
    $uptDrivers     = implode(",", $_POST['uptDrivers']);
    $uptState       = $_POST['uptState'];
    $wpdb->query("UPDATE $tableRoutes SET 
      name         = '$uptName', 
      description  = '$uptDescription', 
      status       = '$uptState',
      drivers_list ='$uptDrivers' 
      WHERE id = '$id'");
    echo "<script>location.reload();</script>";
  }
  // delete
  if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $wpdb->query("DELETE FROM $tableRoutes WHERE id='$id'");
    $wpdb->query("DELETE FROM $tableRoutesStation WHERE id_routes='$id'");
    echo "<script>location.href('admin.php?page=adminRoutes');</script>";
  }
  ?>
  <div class="wrap">
    <h2>Creación de Rutas</h2>
    <table class="wp-list-table widefat striped">
      <thead>
        <tr>
          <th width="20%">Nombre</th>
          <th width="25%">Descripción</th>
          <th width="25%">Conductores Actuales</th>
          <th width="15%">Estado</th>
          <th width="15%">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <form action="" method="post">
          <tr>
            <td><input type="text" id="newName" name="newName"></td>
            <td><textarea id="newDescription" name="newDescription"></textarea></td>
            <td>
              <select name="newDrivers[]" id="newDrivers" multiple="multiple">
                <?php
                // validate Drivers List
                if (count($lstDrivers) > 0) {
                  // iterate Drivers List
                  foreach ($lstDrivers as $driver) {
                ?>
                  <option value="<?php echo $driver->ID; ?>"><?php echo $driver->display_name; ?></option>
                <?php
                  }
                }
                ?>
              </select>
            </td>
            <td>
            	<select name="newState" id="newState">
            		<option value="1">Activa</option>
            		<option value="2">Inactiva</option>
            	</select>
            </td>
            <td>
            	<button id="newsubmit" name="newsubmit" type="submit" class='button button-primary'>
                Crear Ruta
              </button>
            </td>
          </tr>
        </form>
        <?php
            $result = $wpdb->get_results("SELECT * FROM {$tableRoutes} order by id DESC");
            // create table and Header
            $strResult = "
	        	<tr>
	        		<td width='100%' colspan='5'>
	        			<table width='100%'>
							<tr>
								<td colspan='6'  class='table-header'><b>Listado Rutas Rutery</b></td>
							</tr>
              <tr>
                <td width='5%'><b>ID</b></td>
                <td width='15%'><b>Nombre</b></td>
                <td width='20%'><b>Descripción</b></td>
                <td width='20%'><b>Conductores Actuales</b></td>
                <td width='15%'><b>Estado</b></td>
                <td width='25%'><b>Acciones</b></td>
              </tr>";
			// iterate Items
			foreach ($result as $pos => $print) {
			  // create String Result
        $strResult .= "
	        	<tr>
	        		<td width='100%' colspan='4'>	        		
						<tr>
							<td>".$print->id."</td>
							<td>".$print->name."</td>
							<td>".$print->description."</td>
              <td>
                <ul style='margin: 0;'>";            
              // get Actual Drivers
              $actDrivers = ($print->drivers_list != "") ? explode(",", $print->drivers_list) : array();
              // validate Drivers List
              if (count($actDrivers) > 0) {
                // iterate Drivers
                foreach ($actDrivers as $idDriver) {
                  $strResult .= "<li>- ".loadRouteDriverName($idDriver)."</li>";          
                }
              }
        $strResult .= "</ul>
              </td>
							<td>".(($print->status == 1)?'Activa':'Inactiva')."</td>
							<td>
								<a href='admin.php?page=adminRoutesStations&rId=".$print->id."'>
									<button type='button'class='button button-primary'>Agregar Parada</button>
								</a>
								<a href='admin.php?page=adminRoutes&upt=".$print->id."#tbl-update'>
									<button type='button'class='button button-primary'>Actualizar</button>
								</a>
								<a href='admin.php?page=adminRoutes&del=".$print->id."'>
									<button type='button' onclick='confirm(\"¿Está seguro de eliminar la ruta seleccionada?\")'class='button button-secundary'>Eliminar</button>
								</a>
							</td>
						</tr>
					</td>
				</tr>";
      	}
      	$strResult .= "
      			</table>
				</td>
			</tr>";
      // show String Result
      echo $strResult;
        ?>
      </tbody>  
    </table>
    <br>
    <br>
    <?php
    if (isset($_GET['upt'])) {
        $upt_id    = $_GET['upt'];
        $item      = $wpdb->get_row("SELECT * FROM $tableRoutes WHERE id='$upt_id'");
        $strUpdate = "
        <table id='tbl-update' class='wp-list-table widefat striped'>
          <thead>
            <tr>
              <td colspan='5' class='table-header'>Editar Ruta</td>
            <tr/>
            <tr>
              <th width='20%'>Nombre</th>
              <th width='25%'>Descripción</th>
              <th width='25%'>Conductores Actuales</th>
              <th width='15%'>Estado</th>
              <th width='15%'>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <form action='admin.php?page=adminRoutes' method='post'>
              <tr>
                <td width='20%'>
                	<input type='hidden' id='uptid' name='uptid' value='$item->id'>
                	<input type='text' id='uptName' name='uptName' value='$item->name'>
                </td>
                <td width='25%'><input type='text' id='uptDescription' name='uptDescription' value='$item->description'></td>                
                <td width='25%'>
                  <select name='uptDrivers[]' id='uptDrivers' multiple='multiple'>";
                  // get Actual Drivers
                  $actDrivers = ($item->drivers_list != "") ? explode(",", $item->drivers_list) : array();
                  // validate Drivers List
                  if (count($lstDrivers) > 0) {
                    // iterate Drivers List
                    foreach ($lstDrivers as $driver) {
                      $strUpdate .= "<option value='".$driver->ID."' ".
                      (in_array($driver->ID, $actDrivers) ? 'selected': '').">".
                      $driver->display_name."</option>";
                    }
                  }
              $strUpdate .= "</select>
                </td>
                <td width='15%'>
					        <select name='uptState' id='uptState'>
            		    <option value='1' ".(($item->status == 1)?'selected':'').">Activa</option>
            		    <option value='2' ".(($item->status != 1)?'selected':'').">Inactiva</option>
            	    </select>
                </td>
                <td width='15%'>
                	<button id='uptsubmit' name='uptsubmit' type='submit'class='button button-primary'>Actualizar</button>
                	<a href='admin.php?page=adminRoutes'class='button button-secundary'><button type='button'>CANCEL</button></a>
            	</td>
              </tr>
            </form>
          </tbody>
        </table>";
        // show Update Form
        echo $strUpdate;
	}
	?>
	</div>