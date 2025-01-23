<?php
/**
 * Display support options of the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      9.9.10
 *
 * @package    nginx-helper
 * @subpackage nginx-helper/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="postbox">
	<h3 class="hndle">
		<span><?php esc_html_e( 'Issues', 'nginx-helper' ); ?></span>
	</h3>
	<div class="inside">
		<table class="form-table">
			<tr valign="top">
				<th>
					<?php esc_html_e( 'Report Issues', 'nginx-helper' ); ?>
				</th>
				<td>
					<a href="https://github.com/gridpane/nginx-helper/issues" title="<?php esc_attr_e( 'Report Issues', 'nginx-helper' ); ?>" target="_blank">
						<?php esc_html_e( 'Link to Github Issues', 'nginx-helper' ); ?>
					</a>
				</td>
			</tr>
		</table>
	</div>
</div>
