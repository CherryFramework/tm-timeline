<?php
/**
 * Horizontal timeline layout
 *
 * @package    Timeline
 * @subpackage Views
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2017 Template Monster
 */

if ( 0 < sizeof( $this->pages ) ) :
?><div class="tm_timeline tm_timeline-layout-horizontal">

	<div class="tm_timeline__container">

		<?php if ( 1 < sizeof( $this->pages ) ) : ?>
			<a href="#" class="tm_timeline__control tm_timeline__control-slide-left">
				<i class="fa fa-chevron-left"></i>
			</a>
		<?php endif; ?>

		<div class="tm_timeline__body">

			<div class="tm_timeline__pages">
				<?php $i = 0; ?>
				<?php foreach ( $this->pages as $index => $page ) : ?>

					<?php if ( 0 < sizeof( $page ) ) : ?>
						<div class="tm_timeline__page<?php print esc_attr( 0 == $i ? ' tm_timeline__page-current' :
							'' ); ?>" data-index="<?php print esc_attr( $index ); ?>">

							<div class="tm_timeline__page__content">
								<?php foreach ( $page as $post ) : ?>
									<?php $date = apply_filters( 'tm_timeline_format_date', get_post_meta( $post->ID, 'post-event-date', true ), $this->config['date-format'] ); ?>
									<div class="tm_timeline__event" data-date="<?php print esc_attr( $date ); ?>">
										<div class="tm_timeline__event__dot"></div>
										<?php if ( isset( $this->config['anchors'] ) && true == $this->config['anchors'] ) : ?>
											<div class="tm_timeline__event__title">
												<a href="<?php print esc_attr( get_permalink( $post->ID ) ); ?>"><?php print esc_html( $post->post_title ); ?></a>
											</div>
										<?php else : ?>
											<div class="tm_timeline__event__title">
												<?php print esc_html( $post->post_title ); ?>
											</div>
										<?php endif; ?>
										<div class="tm_timeline__event__date"><?php print esc_html( $date ); ?></div>
									</div>
								<?php endforeach; ?>
							</div>

						</div>
					<?php endif; ?>

					<?php $i = $i + 1; ?>
				<?php endforeach; ?>
			</div>

			<div class="tm_timeline__tense"></div>

		</div>

		<?php if ( 1 < sizeof( $this->pages ) ) : ?>
			<a href="#" class="tm_timeline__control tm_timeline__control-slide-right">
				<i class="fa fa-chevron-right"></i>
			</a>
		<?php endif; ?>

	</div>

</div>
<?php endif; ?>
