<?php
/**
 * Template for the carousel.
 *
 * @package AdapterPostPreview
 */

namespace AdapterPostPreview;

?>
<div id="<?php echo esc_attr( $this->carousel_id ); ?>" class="carousel slide" data-ride="carousel">
	<?php
	$number_of_post_previews = count( $this->post_previews );
	if ( count( $this->post_previews ) > 1 ) :
		?>
		<ol class="carousel-indicators">
			<?php
			for ( $i = 0; $i < $number_of_post_previews; $i++ ) :
				$active_class = ( 0 === $i ) ? 'active' : '';
				?>
				<li class="<?php echo esc_attr( $active_class ); ?>" data-target="#<?php echo esc_attr( $this->carousel_id ); ?>" data-slide-to="<?php echo esc_attr( $i ); ?>"></li>
				<?php
			endfor;
			?>
		</ol>
	<?php endif; ?>
	<div class="carousel-inner">
		<?php
		$i = 0;
		foreach ( $this->post_previews as $post_preview ) :
			$active_class = ( 0 === $i ) ? 'active' : '';
			?>
			<div class="item carousel-item <?php echo esc_attr( $active_class ); ?>">
				<?php echo wp_kses_post( $post_preview ); ?>
			</div>
			<?php
			$i++;
		endforeach;
		?>
	</div>
	<?php if ( $number_of_post_previews > 1 ) : ?>
		<a class="left carousel-control" href="#<?php echo esc_attr( $this->carousel_id ); ?>" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
		<a class="right carousel-control" href="#<?php echo esc_attr( $this->carousel_id ); ?>" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
	<?php endif; ?>
</div>
