<!-- show Google Map -->
<div id="googleMapRoutes" class="map-container" style="width: 100%; max-width: 100%; height: auto; min-height: 500px;"></div>

<?php if (validateUserRole("conductor")) { ?>
<!-- show Request List -->
<div id="drivers-reqlist">
	<div id="drivers-reqlist-content"></div>
	<div id="drivers-reqlist-button" class="inputRequest">
		<button id="genRequest">Listar Solicitudes</button>
		<button id="routeFinish">Terminar Ruta</button>
	</div>
</div>
<?php } ?>

<?php if (validateUserRole("customer")) { ?>
	<!-- Form Request  -->
	<div id="customer-request">
		<!-- show Messages -->
		<div id="solErroMessage" class="msg_error">&nbsp;</div>
		<div id="solSuccessMessage" class="msg_success">&nbsp;</div>
		<div class="msg_address">
			Para definir la dirección origen, favor seleccione sobre la ruta, el punto exacto 
			donde desea que pasemos por usted!
		</div>
		<!-- show Form -->
		<div id="customer-request-input">
			<label>Nombre:</label>
			<input type="text" name="solName"  id="solName" placeholder="Nombre" required="required">
		</div>
		<div id="customer-request-input">
			<label>Número De Celular:</label>
			<input type="number" name="solPhone" id="solPhone" placeholder="Número de Celular" required="required">
		</div>
		<div id="customer-request-input">
			<label>Dirección origen: </label>
			<input type="text" name="solAddressOrigin" id="solAddressOrigin" placeholder="Dirección Origen" required="required">
		</div>
		<div id="customer-request-input">
			<label>Dirección destino: </label>
			<input type="text" name="solAddressDestination" id="solAddressDestination" placeholder="Dirección Destino" required="required">
		</div>
		<div id="customer-request-input" class="inputRequest">
			<button id="solSubmit" type="submit" name="solSubmit">Solicitar Servicio</button>
		</div>
	</div>
<?php } ?>


<!-- show Map Routes -->
<div id="googleMapRoutesAddressess"></div>