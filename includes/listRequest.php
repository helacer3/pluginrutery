<table class="table-request">
	<tbody>
		<tr class="table-request-label">
			<td>Nombre</td>		
			<td>Teléfono</td>		
			<td>Fecha</td>		
		</tr>
		<?php
		foreach($lstRequest as $request) {
		?>
		<tr class="table-request-content">
			<td><?php echo $request->name; ?></td>		
			<td><?php echo $request->phone; ?></td>		
			<td><?php echo $request->request_date; ?></td>		
		</tr>
		<?php } ?>
	</tbody>
</table>