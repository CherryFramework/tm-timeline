<?php
/**
 * Custom meta-box view
 *
 * @package    Tm_Timeline
 * @subpackage Tm_Timeline_Admin
 * @author     Template Monster
 * @license    GPL-3.0+
 * @copyright  2017 Template Monster
 */
?><input type="hidden" name="<?php print esc_attr( sprintf( '%s-nonce', $this->id ) ); ?>" value="<?php print esc_attr( $this->nonce ); ?>">

<input type="text" id="<?php print esc_attr( sprintf( '%s-value', $this->id ) ); ?>" name="<?php print esc_attr( $this->id ); ?>" value="<?php print esc_attr( $this->value ); ?>" placeholder="<?php echo esc_html__( 'Choose date', 'tm-timeline' ); ?>">

<script type="text/javascript">
	(function ($) {
		'use strict';
		$(document).ready(function () {

			var $field = $('#<?php print esc_attr( sprintf( '%s-value', $this->id ) ); ?>');

			$field.datepicker({
				changeMonth: true,
				changeYear : true,
				dateFormat : '<?php echo esc_html( $this->date_format ); ?>',
				buttonText : '<?php  echo esc_html__( 'Choose', 'tm-timeline' ); ?>',
				showOn     : 'both',
				beforeShow : function (input, $input) {
					$('#ui-datepicker-div').addClass('<?php print esc_attr( sprintf( '%s-select-value', $this->id ) ); ?>');
				}
			});
		});
	}(jQuery || $));
</script>
