<div class="container container-products">
	<div class="row row-products">
		<?php
			// get User Orders
			$orders = getUserOrders();
			// validate Orders
			if (count($orders) > 0) {
				// iterate Orders
				foreach ($orders as $order) {
					// iterate Order Items
					foreach ($order->get_items() as $item) {
						// load Actual Product
						//$actProduct = wc_get_product($item->get_product_id());
		?>
			<!-- show Product -->
			<a href="<?php echo get_permalink($item->get_product_id()); ?>">
				<div class="col-4 col-singleproduct">
					<div class="prd-image">
						<?php echo get_the_post_thumbnail($item->get_product_id(), 'posts'); ?>
					</div>
					<div class="prd-name">
						<?php echo $item->get_name(); ?>
					</div>
				</div>
			</a>
			<!-- end Product -->
		<?php
				}
			}
		} else {
		?>
		<div class="col-12 col-noproducts">
			No tiene productos vigentes
		</div>
		<?php } ?>			
	</div>
</div>
