<div class="card industry-card">
	<img src="<?php echo $Industry->get_image(); ?>" alt="<?php echo esc_attr( $Industry->get_title() ); ?>" class="card-img industry-card--image" />
	<div class="card-img-overlay industry-card--body">
		<h5 class="card-title industry-card--title"><?php echo $Industry->get_title(); ?></h5>
		<p class="card-text industry-card--text"><?php echo $Industry->get_excerpt(); ?></p>
		<a href="<?php echo esc_url( $Industry->get_url() ); ?>" class="industry-card--button stretched-link">Learn More</a>
	</div>
</div>