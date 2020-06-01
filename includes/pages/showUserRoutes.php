<div class="container">
	<div class="row">
		<?php
		// get User Products
		$lstProducts = getUserProducts();
		// validate List
		if (count($lstProducts) > 0) {
			foreach ($lstProducts as $product) {
		?>
		<!-- show Product -->
		<a href="<?php echo $product->guid; ?>">
			<div class="col-4">
				<div class="prd-image">
					<img src="<?php echo $product->post_title; ?>" alt="<?php echo $product->post_title; ?>">
				</div>
				<div class="prd-name">
					<?php echo $product->post_title; ?>
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
