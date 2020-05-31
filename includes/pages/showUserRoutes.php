<div class="container">
	<div class="row">
		<?php
		// get User Purshased Products
		$lstProducts = getUserPurshasedProducts();
		// validate List
		if (count($lstProducts) > 0) {
			foreach ($lstProducts as $Product) {
		?>
		<!-- show Product -->
		<a href="">
			<div class="col-4">
				<div class="prd-image">
					<img src="" align="">
				</div>
				<div class="prd-name">
					Nombre Producto
				</div>
			</div>
		</a>
		<!-- end Product -->
		<?php
			}
		} else {
		?>
		<div class="col-12">
			No tiene productos vigentes
		</div>
		<?php } ?>			
	</div>
</div>
