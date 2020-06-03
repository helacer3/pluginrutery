<!-- show Google Map -->
<div id="googleMapRoutes" class="map-container" style="width: 500px; height: 500px;"></div>

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
		<!-- show Form -->
		<div id="customer-request-input">
			<label>Nombre:</label>
			<input type="text" name="solName"  id="solName" placeholder="Nombre" required="required">
		</div>
		<div id="customer-request-input">
			<label>Número De Celular:</label>
			<input type="number" name="solPhone" id="solPhone" placeholder="Número de Celular" required="required">
		</div>
		<div id="customer-request-input" class="inputRequest">
			<button id="solSubmit" type="submit" name="solSubmit">Solicitar Servicio</button>
		</div>
	</div>
<?php } ?>


<!-- show Map Routes -->
<div id="googleMapRoutesAddressess"></div>

