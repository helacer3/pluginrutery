<?php
	global $wpdb;
	$actRoute           = loadSingleRoute($_REQUEST['rId']);
	$tableRoutesStation = $wpdb->prefix . 'routes_station';
	// validate Route Exist
	if ($actRoute != null) {
		// create
		if (isset($_POST['newsubmit'])) { 
			$newIdRoute     = $_POST['rId'];
			$newName        = $_POST['newName'];
			$newAddress     = $_POST['newAddress'];
			$newState       = $_POST['newState'];
			unset($_POST);
			$wpdb->query("INSERT INTO $tableRoutesStation (id_routes, name, address, status) 
				VALUES('$newIdRoute','$newName','$newAddress','$newState')");
			echo "<script>location.replace('".PLG_RUTA."');</script>";
		}
		// update
		if (isset($_POST['uptsubmit'])) {
			$id         = $_POST['uptid'];
			$uptName    = $_POST['uptName'];
			$uptAddress = $_POST['uptAddress'];
			$uptState   = $_POST['uptState'];
			$wpdb->query("UPDATE $tableRoutesStation SET name='$uptName',address='$uptAddress', status='$uptState' WHERE id = '$id'");
			echo "<script>location.replace('".PLG_RUTA."');</script>";
		}
		// delete
		if (isset($_GET['del'])) {
			$id = $_GET['del'];
			$wpdb->query("DELETE FROM $tableRoutesStation WHERE id='$id'");
			echo "<script>location.replace('".PLG_RUTA."');</script>";
		}
    ?>
	  <div class="wrap">
	    <h2>Creación de Estaciones Ruta: <?php echo $actRoute->name; ?> </h2>
	    <table class="wp-list-table widefat striped">
	      <thead>
	        <tr>
	          <th width="25%">Nombre</th>
	          <th width="25%">Dirección</th>
	          <th width="25%">Estado</th>
	          <th width="25%">Acciones</th>
	        </tr>
	      </thead>
	      <tbody>
	        <form action="" method="post">
	          <tr>
	            <td>
	            	<input type="hidden" id="rId" name="rId" value="<?php echo $_GET['rId']; ?>">
	            	<input type="text" id="newName"  name="newName">
	            </td>
	            <td><textarea id="newAddress" name="newAddress"></textarea></td>
	            <td>
	            	<select name="newState" id="newState">
	            		<option value="1">Activa</option>
	            		<option value="2">Inactiva</option>
	            	</select>
	            </td>
	            <td>
	            	<button id="newsubmit" name="newsubmit" type="submit">Crear Estación Ruta</button>
	            </td>
	          </tr>
	        </form>
	        <?php
	        $result = $wpdb->get_results("SELECT * FROM {$tableRoutesStation} where id_routes = ".
	          	(int)$_REQUEST['rId']." order by id DESC");
            // create table and Header
            echo "
	        	<tr>
	        		<td width='100%' colspan='4'>
	        			<table width='100%'>
							<tr>
								<td colspan='5' style='text-align:center;'><b>Estaciones de la Ruta</b></td>
							</tr>";
			// iterate Items
	        foreach ($result as $print) {
	            echo "
	        	<tr>
	        		<td width='100%' colspan='4'>
						<tr>
							<td width='10%'>".$print->id."</td>
							<td width='15%'>".$print->name."</td>
							<td width='25%'>".$print->address."</td>
							<td width='15%'>".(($print->status == 1)?'Activa':'Inactiva')."</td>
							<td width='35%'>
								<a href='admin.php?page=adminRoutesStations&rId=".(int)$_REQUEST['rId']."&upt=".$print->id."'>
									<button type='button'>Actualizar</button>
								</a>
								<a href='admin.php?page=adminRoutesStations&rId=".(int)$_REQUEST['rId']."&del=".$print->id."'>
									<button type='button' onclick='confirm(\"¿Está seguro de eliminar la estación seleccionada?\")'>Eliminar</button>
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
	        $item   = $wpdb->get_row("SELECT * FROM $tableRoutesStation WHERE id=".(int)$upt_id);
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
	            <form action='admin.php?page=adminRoutesStations' method='post'>
	              <tr>
	                <td width='25%'>
	                	<input type='hidden' id='rId' name='rId' value='".(int)$_REQUEST['rId']."'>
	                	<input type='hidden' id='uptid' name='uptid' value='$item->id'>
	                	<input type='text' id='uptName' name='uptName' value='$item->name'>
	                </td>
	                <td width='25%'><input type='text' id='uptAddress' name='uptAddress' value='$item->address'></td>
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
	<?php
	} else {
		echo "<script>location.replace('admin.php?page=adminRoutes');</script>";
	}