<?php
/**
 * Display support options of the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    gridpane-nginx-helper
 * @subpackage nginx-helper/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="postbox">
	<div class="inside">
		<table class="form-table">
			<tr valign="top">
				<th>
					<?php esc_html_e( 'Community Support', 'gridpane-nginx-helper' ); ?>
				</th>
				<td>
					<a href="https://community.gridpane.com" title="<?php esc_attr_e( 'Community Support Forum', 'gridpane-nginx-helper' ); ?>" target="_blank">
						<?php esc_html_e( 'Link to community.gridpane.com', 'gridpane-nginx-helper' ); ?>
					</a>
				</td>
			</tr>
		</table>
	</div>
</div>
