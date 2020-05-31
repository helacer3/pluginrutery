<?php
// count List Request
if (count($lstRequest) > 0) {
?>
<table class="table-request">
	<tbody>
		<tr class="table-request-label">
			<td>Nombre</td>		
			<td>Teléfono</td>		
			<td>Fecha</td>		
			<td>Contactado</td>		
		</tr>
		<?php
		// iterate Request
		foreach($lstRequest as $request) {
		?>
		<tr class="table-request-content">
			<td><?php echo $request->name; ?></td>		
			<td><?php echo $request->phone; ?></td>		
			<td><?php echo $request->request_date; ?></td>		
			<td><a 
				class="reqContacted" 
				data-reqid="<?php echo $request->id; ?>" 
				onclick=""
				style="cursor: pointer;" ?> Contactado</a></td>		
		</tr>
		<?php } ?>
	</tbody>
</table>
<?php
} else {
?>
	<div class="msg_success" style="display: inline-block;">No existen solicitudes pendientes</div>
<?php
}
?>