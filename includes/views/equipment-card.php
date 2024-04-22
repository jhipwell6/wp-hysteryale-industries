<div class="equipment-block">
    <?php if ( $Equipment->get_image() ) : ?>
    <div class="equipment-image">
    	<img src="<?php echo $Equipment->get_image(); ?>" alt="<?php echo $Equipment->get_title(); ?>" />
    </div>
    <?php endif; ?>
    <div class="equipment-text">
		<h5 class="equipment-tagline"><?php echo $Equipment->get_hygapi_tagline(); ?></h5>
        <h3 class="equipment-title"><?php echo $Equipment->get_manufacturer(); ?> <?php echo $Equipment->get_title(); ?></h3>
        <p class="equipment-description"><?php echo wp_trim_words( $Equipment->get_hygapi_description(), 15, '&hellip;' ); ?></p>
    </div>
    <div class="equipment-link">
		<a href="<?php echo $Equipment->get_url(); ?>">View Details</a>
    </div>
</div>