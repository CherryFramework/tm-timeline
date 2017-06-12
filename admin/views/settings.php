<?php
/**
 * Plugin settings page
 *
 * @package    Tm_Timeline
 * @subpackage Tm_Timeline_Admin
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2017 Template Monster
 */
?><div class="wrap timeline-settings-page">
	<h1><?php echo esc_html__( 'Settings', 'tm-timeline' ); ?></h1>
	<form id="timeline-shortcode-form" action="<?php print admin_url( 'edit.php?post_type=timeline_post' ); ?>">
		<table class="timeline-options">
			<tbody>
			<tr>
				<td>
					<div class="field-group">
						<label for="timeline-layout"><?php echo esc_html__( 'Layout:', 'tm-timeline' ); ?></label>
						<select id="timeline-layout" name="layout">
							<?php foreach ( $this->timeline_layouts as $id => $layout ) : ?>
								<option value="<?php print esc_attr( (int) $id ); ?>"><?php print esc_html( $layout['title'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="info-text"><?php echo esc_html__( 'Timeline look and feel', 'tm-timeline' ); ?></p>
					</div>
				</td>
				<td>
					<div class="field-group">
						<label for="timeline-visible-items"><?php echo esc_html__( 'Visible items limit:', 'tm-timeline' ); ?></label>
						<input id="timeline-visible-items" type="number" name="visible-items" value="1" min="1">
						<p class="info-text"><?php echo esc_html__( 'Visible items limit is used for "Horizontal" layout.', 'tm-timeline' ); ?></p>
					</div>
				</td>
				<td>
					<div class="field-group slight-offset">
						<label for="timeline-date-format"><?php echo esc_html__( 'Date format:', 'tm-timeline' ); ?></label>
						<select id="timeline-date-format" name="date-format">
							<?php foreach ( $this->timeline_date_format as $id => $date_format ) : ?>
								<option value="<?php print esc_attr( (int) $id ); ?>"><?php print esc_html( $date_format['title'] ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="info-text"><?php echo esc_html__( 'Date format used for displaying posts in the timeline.', 'tm-timeline' ); ?></p>
					</div>
				</td>
			</tr>
			<tr>
				<td class="mid">
					<div class="field-group">
						<label for="timeline-tag-slug"><?php echo esc_html__( 'Tag:', 'tm-timeline' ); ?></label>
						<select id="timeline-tag-slug" name="tag" class="full-width-input">
							<option value=""><?php echo esc_html__( ' - None - ', 'tm-timeline' ); ?></option>
							<?php if ( $this->has_var( 'tags' ) ) : ?>
								<?php $tags = $this->get_var( 'tags', array() ); ?>
								<?php if ( sizeof( $tags ) > 0 ) : ?>
									<?php foreach ( $tags as $tag ) : ?>
										<?php if ( property_exists( $tag, 'slug' ) && property_exists( $tag, 'name' )
										) : ?>
											<option value="<?php print esc_attr( $tag->slug ); ?>">
												<?php print esc_html( $tag->name ); ?>
											</option>
										<?php endif; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							<?php endif; ?>
						</select>
						<p class="info-text"><?php echo esc_html__( 'Show only posts which contain following tag. If none selected, will show all timeline posts.', 'tm-timeline' ); ?></p>
					</div>
				</td>
				<td class="bot">
					<div class="field-group">
						<label for="timeline-order"><?php echo esc_html__( 'Sort Order:', 'tm-timeline' ); ?></label>
						<select id="timeline-order" name="order" class="full-width-input">
							<?php if ( $this->has_var( 'sort_orders' ) ) : ?>
								<?php $sort_orders = $this->get_var( 'sort_orders', array() ); ?>
								<?php if ( sizeof( $sort_orders ) > 0 ) : ?>
									<?php foreach ( $sort_orders as $order => $name ) : ?>
										<option value="<?php print esc_attr( $order ); ?>">
											<?php print esc_html( $name ); ?>
										</option>
									<?php endforeach; ?>
								<?php endif; ?>
							<?php endif; ?>
						</select>
						<p class="info-text"><?php echo esc_html__( 'Show only posts which contain following tag. If none selected, will show all timeline posts.', 'tm-timeline' ); ?></p>
					</div>
				</td>
				<td class="bot">
					<div class="field-group checkbox slight-offset">
						<label for="timeline-anchors"><input id="timeline-anchors" type="checkbox" name="anchors"> <?php echo esc_html__( 'Display anchors', 'tm-timeline' ); ?>
						</label>
						<p class="info-text"><?php echo esc_html__( 'Timeline post title will be clickable and will lead user to the post.', 'tm-timeline' ); ?></p>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="center">
					<div class="field-group with-offset">
						<input type="submit" value="<?php echo esc_html__( 'Generate shortcode', 'tm-timeline' ); ?>" class="button button-primary button-large" id="timeline-shorcode-generate">
					</div>
				</td>
				<td></td>
			</tr>
			<tr id="timeline-shorcode-result-wrapper" class="hidden">
				<td colspan="2">
					<div class="field-group">
						<label for="timeline-shortcode-result"><?php echo esc_html__( 'Shortcode:', 'tm-timeline' ); ?></label>
						<textarea id="timeline-shortcode-result" readonly class="full-width-input" rows="4" onclick="this.select();"></textarea>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
	</form>
</div>
