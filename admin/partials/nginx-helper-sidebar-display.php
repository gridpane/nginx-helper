<?php
/**
 * Display sidebar.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      9.9.10
 *
 * @package    nginx-helper
 * @subpackage nginx-helper/admin/partials
 */

$purge_url  = add_query_arg(
	array(
		'nginx_helper_action' => 'purge',
		'nginx_helper_urls'   => 'all',
	)
);
$nonced_url = wp_nonce_url( $purge_url, 'nginx_helper-purge_all' );
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<form id="purgeall" action="" method="post" class="clearfix">
	<a href="<?php echo esc_url( $nonced_url ); ?>" class="button-primary">
		<?php esc_html_e( 'Purge Entire Cache', 'nginx-helper' ); ?>
	</a>
</form>
<div class="postbox" id="support">
	<h3 class="hndle">
		<span><?php esc_html_e( 'Found a bug?', 'nginx-helper' ); ?></span>
	</h3>
	<div class="inside">
		<p>
			<?php
			printf(
				'%s <a href=\'%s\'>%s</a>.',
				esc_html__( 'Please create a GitHub Issue', 'nginx-helper' ),
				esc_url( 'https://github.com/gridpane/nginx-helper/issues' )
			);
			?>
		</p>
	</div>
</div>

