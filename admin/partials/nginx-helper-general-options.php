<?php
/**
 * Display general options of the plugin.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    gridpane-nginx-helper
 * @subpackage nginx-helper/admin/partials
 */

global $nginx_helper_admin;

$error_log_filesize = false;

$args = array(
	'enable_purge',
	'enable_stamp',
	'purge_method',
	'is_submit',
	'redis_hostname',
	'redis_port',
	'redis_prefix',
	'redis_unix_socket',
	'redis_username',
	'redis_password',
	'redis_database',
//	'redis_database',
//	'redis_username',
//	'redis_password',
//	'redis_unix_socket',
//	'redis_socket_enabled_by_constant',
//	'redis_acl_enabled_by_constant',
	'purge_homepage_on_edit',
	'purge_homepage_on_del',
	'purge_url',
	'log_level',
	'log_filesize',
	'smart_http_expire_save',
	'cache_method',
	'enable_map',
	'enable_log',
	'purge_archive_on_edit',
	'purge_archive_on_del',
	'purge_archive_on_new_comment',
	'purge_archive_on_deleted_comment',
	'purge_page_on_mod',
	'purge_page_on_new_comment',
	'purge_page_on_deleted_comment',
	'purge_feeds',
	'smart_http_expire_form_nonce',
	'purge_amp_urls',
	'preload_cache',
	'purge_on_update',
	'purge_on_plugin_activation',
	'purge_on_plugin_deactivation',
	'purge_on_theme_change',
);

$all_inputs = array();

foreach ( $args as $val ) {
	if ( isset( $_POST[ $val ] ) ) {
		$all_inputs[ $val ] = wp_strip_all_tags( $_POST[ $val ] );
	}
}

if ( isset( $all_inputs['smart_http_expire_save'] ) && wp_verify_nonce( $all_inputs['smart_http_expire_form_nonce'], 'smart-http-expire-form-nonce' ) ) {
	unset( $all_inputs['smart_http_expire_save'] );
	unset( $all_inputs['is_submit'] );

	$nginx_settings = wp_parse_args(
		$all_inputs,
		$nginx_helper_admin->nginx_helper_default_settings()
	);

	$site_options = get_site_option( 'rt_wp_nginx_helper_options', array() );

	foreach ( $nginx_helper_admin->nginx_helper_default_settings() as $default_setting_field => $default_setting_value ) {

		// Uncheck checkbox fields whose default value is `1` but user has unchecked.
		if ( 1 === $default_setting_value && isset( $site_options[ $default_setting_field ] ) && empty( $all_inputs[ $default_setting_field ] ) ) {

			$nginx_settings[ $default_setting_field ] = 0;

		}

		// Populate the setting field with default value when it is empty.
		if ( '' === $nginx_settings[ $default_setting_field ] ) {

			$nginx_settings[ $default_setting_field ] = $default_setting_value;

		}
	}

	if ( ( ! is_numeric( $nginx_settings['log_filesize'] ) ) || ( empty( $nginx_settings['log_filesize'] ) ) ) {
		$error_log_filesize = __( 'Log file size must be a number.', 'gridpane-nginx-helper' );
		unset( $nginx_settings['log_filesize'] );
	}

	if ( $nginx_settings['enable_map'] ) {
		$nginx_helper_admin->update_map();
	}

	update_site_option( 'rt_wp_nginx_helper_options', $nginx_settings );

	echo '<div class="updated"><p>' . esc_html__( 'Settings saved.', 'gridpane-nginx-helper' ) . '</p></div>';

}

$php_version                              = phpversion();
$nginx_helper_settings                    = $nginx_helper_admin->nginx_helper_settings();
$log_path                                 = $nginx_helper_admin->functional_asset_path();
$log_url                                  = $nginx_helper_admin->functional_asset_url();
$cache_method                             = $nginx_helper_settings['cache_method'];
$cache_method_set_by_constant             = $nginx_helper_settings['cache_method_set_by_constant'];
$purge_method_constant_warning            = false;
$get_purge_method_radio_disabled          = false;
$torden_get_purge_method_radio_disabled   = false;
$unlink_files_purge_method_radio_disabled = false;
$purge_method_php_version_unsupported     = false;
$redis_hostname_set_by_constant           = false;
$redis_unix_socket_set_by_constant        = false;
$redis_port_set_by_constant               = false;
$redis_prefix_set_by_constant             = false;
$redis_database_set_by_constant           = false;
$redis_username_set_by_constant           = false;
$redis_password_set_by_constant           = false;

// For testing
//$php_version                              = 5.4;

if ( 'enable_fastcgi' === $cache_method ) {
	$purge_method                         = $nginx_helper_settings['purge_method'];
	$purge_method_set_by_constant         = $nginx_helper_settings['purge_method_set_by_constant'];

	if (version_compare($php_version, '5.5', '<') && $purge_method === 'get_request_torden' ) {
		$purge_method_php_version_unsupported = true;
	}

	if ( ! $purge_method_set_by_constant ) {
		if ( $purge_method_php_version_unsupported ) {
			$torden_get_purge_method_radio_disabled = true;
			$purge_method = 'get_request';
		}
	} else {
		$get_purge_method_radio_disabled = true;
		$torden_get_purge_method_radio_disabled  = true;
		$unlink_files_purge_method_radio_disabled = true;
		if ( $purge_method_php_version_unsupported ) {
			$purge_method_constant_warning = true;
		}
	}
}

if ( 'enable_redis' === $cache_method ) {
	$redis_hostname_set_by_constant    = $nginx_helper_settings['redis_hostname_set_by_constant'];
	$redis_port_set_by_constant        = $nginx_helper_settings['redis_port_set_by_constant'];
	$redis_unix_socket_set_by_constant = $nginx_helper_settings['redis_unix_socket_set_by_constant'];
	$redis_prefix_set_by_constant      = $nginx_helper_settings['redis_prefix_set_by_constant'];
	$redis_database_set_by_constant    = $nginx_helper_settings['redis_database_set_by_constant'];
	$redis_username_set_by_constant    = $nginx_helper_settings['redis_username_set_by_constant'];
	$redis_password_set_by_constant    = $nginx_helper_settings['redis_password_set_by_constant'];
}
?>

<!-- Forms containing nginx helper settings options. -->
<form id="post_form" method="post" action="#" name="smart_http_expire_form" class="clearfix">
	<div class="postbox">
		<h3 class="hndle">
			<span><?php esc_html_e( 'Purging Options', 'gridpane-nginx-helper' ); ?></span>
		</h3>
		<div class="inside">
			<table class="form-table">
				<tr valign="top">
					<td>
						<input type="checkbox" value="1" id="enable_purge" name="enable_purge" <?php checked( $nginx_helper_settings['enable_purge'], 1 ); ?> />
						<label for="enable_purge"><?php esc_html_e( 'Enable Purge', 'gridpane-nginx-helper' ); ?></label>
					</td>
				</tr>
				<tr valign="top">
					<td>
						<input type="checkbox" value="1" id="preload_cache" name="preload_cache" <?php checked( $nginx_helper_settings['preload_cache'], 1 ); ?> />
						<label for="preload_cache"><?php esc_html_e( 'Preload Cache', 'gridpane-nginx-helper' ); ?></label>
					</td>
				</tr>
			</table>
		</div> <!-- End of .inside -->
	</div>

	<?php if ( ! ( ! is_network_admin() && is_multisite() ) ) { ?>
		<div class="postbox enable_purge"<?php echo ( empty( $nginx_helper_settings['enable_purge'] ) ) ? ' style="display: none;"' : ''; ?>>
			<h3 class="hndle">
				<span><?php esc_html_e( 'Caching Method', 'gridpane-nginx-helper' ); ?></span>
			</h3>
			<div class="inside">
				<?php
				if ( $cache_method_set_by_constant  )  {
					echo '<p class="description" style="margin-left:1em;">';
					esc_html_e(
					    sprintf(
					        __("Set by wp-config.php constant: define( 'RT_WP_NGINX_HELPER_CACHE_METHOD',  '%s' );", 'gridpane-nginx-helper'),
					        $cache_method
					    )
					);
					echo '</strong>';
					echo '</p>';
				}
				?>
				<input type="hidden" name="is_submit" value="1" />
				<table class="form-table">
					<tr valign="top">
						<td>
							<input type="radio" value="enable_fastcgi" id="cache_method_fastcgi" name="cache_method" <?php echo checked( $cache_method, 'enable_fastcgi' ); disabled( $cache_method_set_by_constant ); ?> />
							<label for="cache_method_fastcgi">
								<?php printf( esc_html__('Nginx Fastcgi cache', 'gridpane-nginx-helper' ) ); ?>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<td>
							<input type="radio" value="enable_redis" id="cache_method_redis" name="cache_method" <?php echo checked( $cache_method, 'enable_redis' ); disabled( $cache_method_set_by_constant ); ?> />
							<label for="cache_method_redis">
								<?php printf( esc_html__( 'Redis cache', 'gridpane-nginx-helper' ) ); ?>
							</label>
						</td>
					</tr>
				</table>
			</div> <!-- End of .inside -->
		</div>
		<div class="enable_purge">
			<div class="postbox cache_method_fastcgi"  <?php echo ( ! empty( $nginx_helper_settings['enable_purge'] ) && 'enable_fastcgi' === $cache_method ) ? '' : 'style="display: none;"'; ?> >
				<h3 class="hndle">
					<span><?php esc_html_e( 'Purge Method', 'gridpane-nginx-helper' ); ?></span>
				</h3>
				<div class="inside">
					<?php
					if ( $purge_method_set_by_constant )  {
						echo '<p class="description" style="margin-left:1em;"><strong>';
						esc_html_e(
							sprintf(
								__("Set by wp-config.php constant: define( 'RT_WP_NGINX_HELPER_PURGE_METHOD',  '%s' );", 'gridpane-nginx-helper'),
								$purge_method
							)
						);
						if ( $purge_method_constant_warning ) {
							echo '</br></br>';
							echo wp_kses(
								__( 'WARNING!! The running version of PHP does not support this method!!!', 'gridpane-nginx-helper' ),
								array( 'strong' => array() )
							);
						}
						echo '</strong></p>';
					}
					?>
					<table class="form-table rtnginx-table">
						<tr valign="top">
							<td>
								<fieldset>
									<legend class="screen-reader-text">
										<span>
											&nbsp;
											<?php esc_html_e( 'when a post/page/custom post is published.', 'gridpane-nginx-helper' ); ?>
										</span>
									</legend>
									<label for="purge_method_get_request">
										<input type="radio" value="get_request" id="purge_method_get_request" name="purge_method" <?php checked( $purge_method, 'get_request' ); disabled( $get_purge_method_radio_disabled ); ?> />
										&nbsp;
										<?php
											echo wp_kses(
												sprintf(
													'%1$s <strong>PURGE/url</strong> %2$s',
													esc_html__( 'Using a GET request to', 'gridpane-nginx-helper' ),
													esc_html__( '(Default option) - Does not support `purge_all` method', 'gridpane-nginx-helper' )
												),
												array( 'strong' => array() )
											);
										?>
										<br />
										<small>
											<?php
												echo wp_kses(
													sprintf(
														// translators: %s Nginx cache purge module link.
														__( 'Nginx is compiled with the %s module.', 'gridpane-nginx-helper' ),
														'<strong><a href="https://github.com/FRiCKLE/ngx_cache_purge">ngx_cache_purge (FRiCKLE)</a></strong>'
													),
													array(
														'strong' => array(),
														'a'      => array(
															'href' => array(),
														),
													)
												);
											?>
										</small>
									</label>
									<br />
									<label for="purge_method_get_request_torden">
										<input type="radio" value="get_request_torden" id="purge_method_get_request_torden" name="purge_method" <?php checked( $purge_method, 'get_request_torden' ); disabled( $torden_get_purge_method_radio_disabled  ); ?> />
										&nbsp;
										<?php
											echo wp_kses(
												sprintf(
													'%1$s <strong>PURGE/url</strong> %2$s',
													esc_html__( 'Using a GET request to', 'gridpane-nginx-helper' ),
													esc_html__( '(Supports torden\'s `purge_all` method -> Purge Entire Cache)', 'gridpane-nginx-helper' )
												),
												array( 'strong' => array() )
											);
										?>
										<br />
										<small>
											<?php
												echo wp_kses(
													sprintf(
														// translators: %s Nginx cache purge module link.
														__( 'Nginx is compiled with the %s module.', 'gridpane-nginx-helper' ),
														'<strong><a href="https://github.com/torden/ngx_cache_purge">ngx_cache_purge (torden)</a></strong>'
													),
													array(
														'strong' => array(),
														'a'      => array(
															'href' => array(),
														),
													)
												);
												echo '<br />';
												esc_html_e( 'Torden Nginx Cache Purge Requires PHP 5.5+ for curl_setopt CURLOPT_RESOLVE option', 'gridpane-nginx-helper' );
												echo '<br />';
												esc_html_e(
												    sprintf(
												        __('Current PHP Version: %s', 'gridpane-nginx-helper'),
												        $php_version
												) );
 												echo '<br />';
											?>
										</small>
									</label>
									<br />
									<label for="purge_method_unlink_files">
										<input type="radio" value="unlink_files" id="purge_method_unlink_files" name="purge_method" <?php checked( $purge_method, 'unlink_files' ); disabled( $unlink_files_purge_method_radio_disabled  ); ?> />
										&nbsp;
										<?php
											esc_html_e( 'Delete local server cache files', 'gridpane-nginx-helper' );
										?>
										<br />
										<small>
											<?php
												echo wp_kses(
													__( 'Checks for matching cache file in <strong>RT_WP_NGINX_HELPER_CACHE_PATH</strong>. Does not require any other modules. Requires that the cache be stored on the same server as WordPress. You must also be using the default nginx cache options (levels=1:2) and (fastcgi_cache_key "$scheme$request_method$host$request_uri").', 'gridpane-nginx-helper' ),
													array( 'strong' => array() )
												);
											?>
										</small>
									</label>
									<br />
								</fieldset>
							</td>
						</tr>
					</table>
				</div> <!-- End of .inside -->
			</div>
			<div class="postbox cache_method_redis"<?php echo ( ! empty( $nginx_helper_settings['enable_purge'] ) && 'enable_redis' === $cache_method ) ? '' : ' style="display: none;"'; ?>>
				<h3 class="hndle">
					<span><?php esc_html_e( 'Redis Settings', 'gridpane-nginx-helper' ); ?></span>
				</h3>
				<div class="inside">
					<table class="form-table rtnginx-table">
						<tr>
							<th style="vertical-align:top;"><label for="redis_hostname"><?php esc_html_e( 'Hostname', 'gridpane-nginx-helper' ); ?></label></th>
							<td>
								<input id="redis_hostname" class="medium-text" type="text" name="redis_hostname" value="<?php echo esc_attr( $nginx_helper_settings['redis_hostname'] ); ?>" <?php echo ( $redis_hostname_set_by_constant ) ? 'readonly="readonly"' : ''; ?> />
								<?php
								if ( $redis_hostname_set_by_constant ) {

									echo '<p class="description">';
									esc_html_e( 'Set by wp-config.php constant: RT_WP_NGINX_HELPER_REDIS_HOSTNAME', 'gridpane-nginx-helper' );
									echo '</p>';

								}
								if ( $redis_unix_socket_set_by_constant ) {

									echo '<p class="description">';
									esc_html_e( 'Ignored! - UNIX socket is set by wp-config.php constant: RT_WP_NGINX_HELPER_REDIS_UNIX_SOCKET', 'gridpane-nginx-helper' );
									echo '</p>';

								} else {

									if  ( $nginx_helper_settings['redis_unix_socket'] ) {

										echo '<p class="description">';
										esc_html_e( 'Ignored! - UNIX socket is set!', 'gridpane-nginx-helper' );
										echo '</p>';

									}

								}
								?>
							</td>
						</tr>
						<tr>
							<th style="vertical-align:top;"><label for="redis_port"><?php esc_html_e( 'Port', 'gridpane-nginx-helper' ); ?></label></th>
							<td>
								<input id="redis_port" class="medium-text" type="text" name="redis_port" value="<?php echo esc_attr( $nginx_helper_settings['redis_port'] ); ?>" <?php echo ( $redis_port_set_by_constant ) ? 'readonly="readonly"' : ''; ?> />
								<?php
								if ( $redis_port_set_by_constant ) {

									echo '<p class="description">';
									esc_html_e( 'Set by wp-config.php constant: RT_WP_NGINX_HELPER_REDIS_PORT', 'gridpane-nginx-helper' );
									echo '</p>';

								}
								if ( $redis_unix_socket_set_by_constant ) {

									echo '<p class="description">';
									esc_html_e( 'Ignored! - UNIX socket is set by wp-config.php constant: RT_WP_NGINX_HELPER_REDIS_UNIX_SOCKET', 'gridpane-nginx-helper' );
									echo '</p>';

								} else {

									if  ( $nginx_helper_settings['redis_unix_socket'] ) {

									    echo '<p class="description">';
									    esc_html_e( 'Ignored! - UNIX socket is set!', 'gridpane-nginx-helper' );
									    echo '</p>';

									}

								}
								?>
							</td>
						</tr>
						<tr>
							<th style="vertical-align:top;"><label for="redis_unix_socket"><?php esc_html_e( 'Unix Socket', 'gridpane-nginx-helper' ); ?></label></th>
							<td>
								<input id="redis_unix_socket" class="medium-text" type="text" name="redis_unix_socket" value="<?php echo esc_attr( $nginx_helper_settings['redis_unix_socket'] ); ?>" <?php echo ( $redis_unix_socket_set_by_constant ) ? 'readonly="readonly"' : ''; ?> />
                                <?php
								if ( $redis_unix_socket_set_by_constant ) {

									echo '<p class="description">';
									esc_html_e( 'Set by wp-config.php constant: RT_WP_NGINX_HELPER_REDIS_UNIX_SOCKET', 'gridpane-nginx-helper' );
									echo '</p>';

								}
								?>
							</td>
						</tr>
						<tr>
							<th style="vertical-align:top;"><label for="redis_prefix"><?php esc_html_e( 'Prefix', 'gridpane-nginx-helper' ); ?></label></th>
							<td>
								<input id="redis_prefix" class="medium-text" type="text" name="redis_prefix" value="<?php echo esc_attr( $nginx_helper_settings['redis_prefix'] ); ?>" <?php echo ( $redis_prefix_set_by_constant ) ? 'readonly="readonly"' : ''; ?> />
								<?php
								if ( $redis_prefix_set_by_constant ) {

									echo '<p class="description">';
									esc_html_e( 'Set by wp-config.php constant: RT_WP_NGINX_HELPER_REDIS_PREFIX', 'gridpane-nginx-helper' );
									echo '</p>';

								}
								?>
							</td>
						</tr>
						<tr>
                            <th style="vertical-align:top;"><label for="redis_database"><?php esc_html_e( 'Redis Database', 'gridpane-nginx-helper' ); ?></label></th>
							<td>
								<input id="redis_database" class="medium-text" type="text" name="redis_database" value="<?php echo esc_attr( $nginx_helper_settings['redis_database'] ); ?>" <?php echo ( $redis_database_set_by_constant ) ? 'readonly="readonly"' : ''; ?> />
								<?php
								if ( $redis_database_set_by_constant ) {

									echo '<p class="description">';
									esc_html_e( 'Set by wp-config.php constant: RT_WP_NGINX_HELPER_REDIS_DATABASE', 'gridpane-nginx-helper' );
									echo '</p>';

								}
								?>
								<?php
								if ( $nginx_helper_settings['redis_unix_socket'] ) {
									echo '<p class="description">';
									esc_html_e( 'Overridden by unix socket path.', 'gridpane-nginx-helper' );
									echo '</p>';
								}
								?>
							</td>
						</tr>
						<tr>
							<th style="vertical-align:top;"><label for="redis_username"><?php esc_html_e( 'Username', 'gridpane-nginx-helper' ); ?></label></th>
							<td>
								<input id="redis_username" class="medium-text" type="text" name="redis_username" value="<?php echo esc_attr( $nginx_helper_settings['redis_username'] ); ?>" <?php echo ( $redis_username_set_by_constant ) ? 'readonly="readonly"' : ''; ?> />
								<?php
								echo '<p class="description">';
								esc_html_e( 'Optional - only required if you have implmented Redis ACLs ', 'gridpane-nginx-helper' );
								echo '</p>';
								if ( $redis_username_set_by_constant ) {

									echo '<p class="description">';
									esc_html_e( 'Set by wp-config.php constant: RT_WP_NGINX_HELPER_REDIS_USERNAME', 'gridpane-nginx-helper' );
									echo '</p>';

								}
								?>
								<?php
								if ( $nginx_helper_settings['redis_unix_socket'] ) {
									
									echo '<p class="description">';
									esc_html_e( 'Overridden by unix socket path.', 'gridpane-nginx-helper' );
									echo '</p>';
									
								}
								?>
							</td>
						</tr>
						<tr>
							<th style="vertical-align:top;"><label for="redis_password"><?php esc_html_e( 'Password', 'gridpane-nginx-helper' ); ?></label></th>
							<td>
								<input id="redis_password" class="medium-text" type="password" name="redis_password" value="<?php echo esc_attr( $nginx_helper_settings['redis_password'] ); ?>" <?php echo ( $redis_password_set_by_constant ) ? 'readonly="readonly"' : ''; ?> />
								<?php
								echo '<p class="description">';
								esc_html_e( 'Optional - only required if you have implmented Redis ACLs ', 'gridpane-nginx-helper' );
								echo '</p>';
								if ( $redis_password_set_by_constant ) {

									echo '<p class="description">';
									esc_html_e( 'Set by wp-config.php constant: RT_WP_NGINX_HELPER_REDIS_PASSWORD', 'gridpane-nginx-helper' );
									echo '</p>';

								}
								?>
							</td>
						</tr>
					</table>
				</div> <!-- End of .inside -->
			</div>
		</div>
		<div class="postbox enable_purge"<?php echo ( empty( $nginx_helper_settings['enable_purge'] ) ) ? ' style="display: none;"' : ''; ?>>
			<h3 class="hndle">
				<span><?php esc_html_e( 'Purging Conditions', 'gridpane-nginx-helper' ); ?></span>
			</h3>
			<div class="inside">
				<table class="form-table rtnginx-table">
					<tr valign="top">
						<th scope="row"><h4><?php esc_html_e( 'Purge Homepage:', 'gridpane-nginx-helper' ); ?></h4></th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
											esc_html_e( 'when a post/page/custom post is modified or added.', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_homepage_on_edit">
									<input type="checkbox" value="1" id="purge_homepage_on_edit" name="purge_homepage_on_edit" <?php checked( $nginx_helper_settings['purge_homepage_on_edit'], 1 ); ?> />
									&nbsp;
									<?php
										echo wp_kses(
											__( 'when a <strong>post</strong> (or page/custom post) is <strong>modified</strong> or <strong>added</strong>.', 'gridpane-nginx-helper' ),
											array( 'strong' => array() )
										);
									?>
								</label>
								<br />
							</fieldset>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
											esc_html_e( 'when an existing post/page/custom post is modified.', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_homepage_on_del">
									<input type="checkbox" value="1" id="purge_homepage_on_del" name="purge_homepage_on_del" <?php checked( $nginx_helper_settings['purge_homepage_on_del'], 1 ); ?> />
									&nbsp;
									<?php
										echo wp_kses(
											__( 'when a <strong>published post</strong> (or page/custom post) is <strong>trashed</strong>', 'gridpane-nginx-helper' ),
											array( 'strong' => array() )
										);
									?>
								</label>
								<br />
							</fieldset>
						</td>
					</tr>
				</table>
				<table class="form-table rtnginx-table">
					<tr valign="top">
						<th scope="row">
							<h4>
								<?php esc_html_e( 'Purge Post/Page/Custom Post Type:', 'gridpane-nginx-helper' ); ?>
							</h4>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>&nbsp;
										<?php
											esc_html_e( 'when a post/page/custom post is published.', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_page_on_mod">
									<input type="checkbox" value="1" id="purge_page_on_mod" name="purge_page_on_mod" <?php checked( $nginx_helper_settings['purge_page_on_mod'], 1 ); ?>>
									&nbsp;
									<?php
										echo wp_kses(
											__( 'when a <strong>post</strong> is <strong>published</strong>.', 'gridpane-nginx-helper' ),
											array( 'strong' => array() )
										);
									?>
								</label>
								<br />
							</fieldset>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
											esc_html_e( 'when a comment is approved/published.', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_page_on_new_comment">
									<input type="checkbox" value="1" id="purge_page_on_new_comment" name="purge_page_on_new_comment" <?php checked( $nginx_helper_settings['purge_page_on_new_comment'], 1 ); ?>>
									&nbsp;
									<?php
										echo wp_kses(
											__( 'when a <strong>comment</strong> is <strong>approved/published</strong>.', 'gridpane-nginx-helper' ),
											array( 'strong' => array() )
										);
									?>
								</label>
								<br />
							</fieldset>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
											esc_html_e( 'when a comment is unapproved/deleted.', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_page_on_deleted_comment">
									<input type="checkbox" value="1" id="purge_page_on_deleted_comment" name="purge_page_on_deleted_comment" <?php checked( $nginx_helper_settings['purge_page_on_deleted_comment'], 1 ); ?>>
									&nbsp;
									<?php
										echo wp_kses(
											__( 'when a <strong>comment</strong> is <strong>unapproved/deleted</strong>.', 'gridpane-nginx-helper' ),
											array( 'strong' => array() )
										);
									?>
								</label>
								<br />
							</fieldset>
						</td>
					</tr>
				</table>
				<table class="form-table rtnginx-table">
					<tr valign="top">
						<th scope="row">
							<h4>
								<?php esc_html_e( 'Purge Archives:', 'gridpane-nginx-helper' ); ?>
							</h4>
							<small><?php esc_html_e( '(date, category, tag, author, custom taxonomies)', 'gridpane-nginx-helper' ); ?></small>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
											esc_html_e( 'when an post/page/custom post is modified or added', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_archive_on_edit">
									<input type="checkbox" value="1" id="purge_archive_on_edit" name="purge_archive_on_edit" <?php checked( $nginx_helper_settings['purge_archive_on_edit'], 1 ); ?> />
									&nbsp;
									<?php
										echo wp_kses(
											__( 'when a <strong>post</strong> (or page/custom post) is <strong>modified</strong> or <strong>added</strong>.', 'gridpane-nginx-helper' ),
											array( 'strong' => array() )
										);
									?>
								</label>
								<br />
							</fieldset>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
											esc_html_e( 'when an existing post/page/custom post is trashed.', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_archive_on_del">
									<input type="checkbox" value="1" id="purge_archive_on_del" name="purge_archive_on_del"<?php checked( $nginx_helper_settings['purge_archive_on_del'], 1 ); ?> />
									&nbsp;
									<?php
										echo wp_kses(
											__( 'when a <strong>published post</strong> (or page/custom post) is <strong>trashed</strong>.', 'gridpane-nginx-helper' ),
											array( 'strong' => array() )
										);
									?>
								</label>
								<br />
							</fieldset>
							<br />
							<fieldset>
								<legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
											esc_html_e( 'when a comment is approved/published.', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_archive_on_new_comment">
									<input type="checkbox" value="1" id="purge_archive_on_new_comment" name="purge_archive_on_new_comment" <?php checked( $nginx_helper_settings['purge_archive_on_new_comment'], 1 ); ?> />
									&nbsp;
									<?php
										echo wp_kses(
											__( 'when a <strong>comment</strong> is <strong>approved/published</strong>.', 'gridpane-nginx-helper' ),
											array( 'strong' => array() )
										);
									?>
								</label>
								<br />
							</fieldset>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
											esc_html_e( 'when a comment is unapproved/deleted.', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_archive_on_deleted_comment">
									<input type="checkbox" value="1" id="purge_archive_on_deleted_comment" name="purge_archive_on_deleted_comment" <?php checked( $nginx_helper_settings['purge_archive_on_deleted_comment'], 1 ); ?> />
									&nbsp;
									<?php
										echo wp_kses(
											__( 'when a <strong>comment</strong> is <strong>unapproved/deleted</strong>.', 'gridpane-nginx-helper' ),
											array( 'strong' => array() )
										);
									?>
								</label>
								<br />
							</fieldset>
						</td>
					</tr>
				</table>
				<table class="form-table rtnginx-table">
					<tr valign="top">
						<th scope="row">
							<h4>
								<?php esc_html_e( 'Purge Feeds:', 'gridpane-nginx-helper' ); ?>
							</h4>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
											esc_html_e( 'purge feeds', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_feeds">
									<input type="checkbox" value="1" id="purge_feeds" name="purge_feeds" <?php checked( $nginx_helper_settings['purge_feeds'], 1 ); ?> />
									&nbsp;
									<?php
										echo wp_kses(
											__( 'purge <strong>feeds</strong> along with <strong>posts</strong> & <strong>pages</strong>.', 'gridpane-nginx-helper' ),
											array( 'strong' => array() )
										);
									?>
								</label>
								<br />
							</fieldset>
						</td>
					</tr>
				</table>
				<table class="form-table rtnginx-table">
					<tr valign="top">
						<th scope="row">
							<h4>
				                <?php esc_html_e( 'Purge AMP URL:', 'gridpane-nginx-helper' ); ?>
							</h4>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
										esc_html_e( 'purge amp urls', 'gridpane-nginx-helper' );
										?>
									</span>
								</legend>
								<label for="purge_amp_urls">
									<input type="checkbox" value="1" id="purge_amp_urls" name="purge_amp_urls" <?php checked( $nginx_helper_settings['purge_amp_urls'], 1 ); ?> />
									&nbsp;
									<?php
									echo wp_kses(
										__( 'purge <strong>amp urls</strong> along with <strong>posts</strong> & <strong>pages</strong>.', 'gridpane-nginx-helper' ),
										array( 'strong' => array() )
									);
									?>
								</label>
								<br />
							</fieldset>
						</td>
					</tr>
				</table>
                <table class="form-table rtnginx-table">
                    <tr valign="top">
                        <th scope="row">
                            <h4>
								<?php esc_html_e( 'Purge On Update:', 'gridpane-nginx-helper' ); ?>
                            </h4>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
										esc_html_e( 'purge on update', 'gridpane-nginx-helper' );
										?>
									</span>
                                </legend>
                                <label for="purge_on_update">
                                    <input type="checkbox" value="1" id="purge_on_update" name="purge_on_update" <?php checked( $nginx_helper_settings['purge_on_update'], 1 ); ?> />
                                    &nbsp;
									<?php
									echo wp_kses(
										__( 'Purge <strong>ALL</strong> when any plugins, themes, or core <strong>updates</strong>.', 'gridpane-nginx-helper' ),
										array( 'strong' => array() )
									);
									?>
                                </label>
                                <br />
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <table class="form-table rtnginx-table">
                    <tr valign="top">
                        <th scope="row">
                            <h4>
								<?php esc_html_e( 'Purge On Plugin Activation:', 'gridpane-nginx-helper' ); ?>
                            </h4>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
										esc_html_e( 'purge on plugin activation', 'gridpane-nginx-helper' );
										?>
									</span>
                                </legend>
                                <label for="purge_on_plugin_activation">
                                    <input type="checkbox" value="1" id="purge_on_plugin_activation" name="purge_on_plugin_activation" <?php checked( $nginx_helper_settings['purge_on_plugin_activation'], 1 ); ?> />
                                    &nbsp;
									<?php
									echo wp_kses(
										__( 'Purge <strong>ALL</strong> on plugin <strong>activation</strong>.', 'gridpane-nginx-helper' ),
										array( 'strong' => array() )
									);
									?>
                                </label>
                                <br />
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <table class="form-table rtnginx-table">
                    <tr valign="top">
                        <th scope="row">
                            <h4>
								<?php esc_html_e( 'Purge On Plugin Deactivation:', 'gridpane-nginx-helper' ); ?>
                            </h4>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
										esc_html_e( 'purge on plugin deactivation', 'gridpane-nginx-helper' );
										?>
									</span>
                                </legend>
                                <label for="purge_on_plugin_deactivation">
                                    <input type="checkbox" value="1" id="purge_on_plugin_deactivation" name="purge_on_plugin_deactivation" <?php checked( $nginx_helper_settings['purge_on_plugin_deactivation'], 1 ); ?> />
                                    &nbsp;
									<?php
									echo wp_kses(
										__( 'Purge <strong>ALL</strong> on plugin <strong>deactivation</strong>.', 'gridpane-nginx-helper' ),
										array( 'strong' => array() )
									);
									?>
                                </label>
                                <br />
                            </fieldset>
                        </td>
                    </tr>
                </table>
                <table class="form-table rtnginx-table">
                    <tr valign="top">
                        <th scope="row">
                            <h4>
								<?php esc_html_e( 'Purge On Theme Change:', 'gridpane-nginx-helper' ); ?>
                            </h4>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text">
									<span>
										&nbsp;
										<?php
										esc_html_e( 'purge on theme change', 'gridpane-nginx-helper' );
										?>
									</span>
                                </legend>
                                <label for="purge_on_theme_change">
                                    <input type="checkbox" value="1" id="purge_on_theme_change" name="purge_on_theme_change" <?php checked( $nginx_helper_settings['purge_on_theme_change'], 1 ); ?> />
                                    &nbsp;
									<?php
									echo wp_kses(
										__( 'Purge <strong>ALL</strong> on theme <strong>activation</strong>.', 'gridpane-nginx-helper' ),
										array( 'strong' => array() )
									);
									?>
                                </label>
                                <br />
                            </fieldset>
                        </td>
                    </tr>
                </table>
				<table class="form-table rtnginx-table">
					<tr valign="top">
						<th scope="row">
							<h4><?php esc_html_e( 'Custom Purge URL:', 'gridpane-nginx-helper' ); ?></h4>
						</th>
						<td>
							<textarea rows="5"class="rt-purge_url" id="purge_url" name="purge_url"><?php echo esc_textarea( $nginx_helper_settings['purge_url'] ); ?></textarea>
							<p class="description">
								<?php
								esc_html_e( 'Add one URL per line. URL should not contain domain name.', 'gridpane-nginx-helper' );
								echo '<br>';
								echo wp_kses(
									__( 'Eg: To purge http://example.com/sample-page/ add <strong>/sample-page/</strong> in above textarea.', 'gridpane-nginx-helper' ),
									array( 'strong' => array() )
								);
								echo '<br>';
								esc_html_e( "'*' will only work with redis cache server.", 'gridpane-nginx-helper' );
								?>
							</p>
						</td>
					</tr>
				</table>
			</div> <!-- End of .inside -->
		</div>
		<div class="postbox">
			<h3 class="hndle">
				<span><?php esc_html_e( 'Debug Options', 'gridpane-nginx-helper' ); ?></span>
			</h3>
			<div class="inside">
				<input type="hidden" name="is_submit" value="1" />
				<table class="form-table">
				<?php if ( is_network_admin() ) { ?>
					<tr valign="top">
						<td>
							<input type="checkbox" value="1" id="enable_map" name="enable_map" <?php checked( $nginx_helper_settings['enable_map'], 1 ); ?> />
							<label for="enable_map">
								<?php esc_html_e( 'Enable Nginx Map.', 'gridpane-nginx-helper' ); ?>
							</label>
						</td>
					</tr>
				<?php } ?>
					<tr valign="top">
						<td>
							<?php
							$is_checkbox_enabled = false;
							if ( 1 === (int) $nginx_helper_settings['enable_log'] ) {
								$is_checkbox_enabled = true;
							}
							?>
							<input
								type="checkbox" value="1" id="enable_log" name="enable_log"
								<?php checked( $nginx_helper_admin->is_nginx_log_enabled(), true ); ?>
								<?php echo esc_attr( $is_checkbox_enabled ? '' : ' disabled ' ); ?>
							/>
							<label for="enable_log">
								<?php esc_html_e( 'Enable Logging', 'gridpane-nginx-helper' ); ?>
								<?php
								if ( ! $is_checkbox_enabled ) {

									$setting_message_detail = [
										'status' => __( 'disable', 'gridpane-nginx-helper' ),
										'value'  => 'false',
									];

									if ( ! $nginx_helper_admin->is_nginx_log_enabled() ) {
										$setting_message_detail = [
											'status' => __( 'enable', 'gridpane-nginx-helper' ),
											'value'  => 'true',
										];
									}

									printf(
										'<p class="enable-logging-message">(%s)</p>',
										sprintf(
											wp_kses_post(
												/* translators: %1$s: status to change to (enable or disable), %2$s: bool value to set the NGINX_HELPER_LOG as (true or false) */
												__( '<strong>NOTE:</strong> To %1$s the logging feature, you must define the <strong>NGINX_HELPER_LOG</strong> constant as <strong>%2$s</strong> in your <strong>wp-config.php</strong> file', 'gridpane-nginx-helper' )
											),
											esc_html( $setting_message_detail['status'] ),
											esc_html( $setting_message_detail['value'] )
										)
									);
								}
								?>
							</label>
						</td>
					</tr>
					<tr valign="top">
						<td>
							<input type="checkbox" value="1" id="enable_stamp" name="enable_stamp" <?php checked( $nginx_helper_settings['enable_stamp'], 1 ); ?> />
							<label for="enable_stamp">
								<?php esc_html_e( 'Enable Nginx Timestamp in HTML', 'gridpane-nginx-helper' ); ?>
							</label>
						</td>
					</tr>
				</table>
			</div> <!-- End of .inside -->
		</div>
		<?php
	} // End of if.

	if ( is_network_admin() ) {
		?>
		<div class="postbox enable_map"<?php echo ( empty( $nginx_helper_settings['enable_map'] ) ) ? ' style="display: none;"' : ''; ?>>
			<h3 class="hndle">
				<span><?php esc_html_e( 'Nginx Map', 'gridpane-nginx-helper' ); ?></span>
			</h3>
			<div class="inside">
			<?php
			if ( ! is_writable( $log_path . 'map.conf' ) ) {
				?>
					<span class="error fade" style="display: block">
						<p>
							<?php
								esc_html_e( 'Can\'t write on map file.', 'gridpane-nginx-helper' );
								echo '<br /><br />';
								echo wp_kses(
									sprintf(
										// translators: %s file url.
										__( 'Check you have write permission on <strong>%s</strong>', 'gridpane-nginx-helper' ),
										esc_url( $log_path . 'map.conf' )
									),
									array( 'strong' => array() )
								);
							?>
						</p>
					</span>
				<?php
			}
			?>
				<table class="form-table rtnginx-table">
					<tr>
						<th>
						<?php
						printf(
							'%1$s<br /><small>%2$s</small>',
							esc_html__( 'Nginx Map path to include in nginx settings', 'gridpane-nginx-helper' ),
							esc_html__( '(recommended)', 'gridpane-nginx-helper' )
						);
						?>
						</th>
						<td>
							<pre><?php echo esc_url( $log_path . 'map.conf' ); ?></pre>
						</td>
					</tr>
					<tr>
						<th>
							<?php
							printf(
								'%1$s<br />%2$s<br /><small>%3$s</small>',
								esc_html__( 'Or,', 'gridpane-nginx-helper' ),
								esc_html__( 'Text to manually copy and paste in nginx settings', 'gridpane-nginx-helper' ),
								esc_html__( '(if your network is small and new sites are not added frequently)', 'gridpane-nginx-helper' )
							);
							?>
						</th>
						<td>
							<pre id="map">
							<?php echo esc_html( $nginx_helper_admin->get_map() ); ?>
							</pre>
						</td>
					</tr>
				</table>
			</div> <!-- End of .inside -->
		</div>
		<?php
	}
	?>
	<div class="postbox enable_log"<?php echo ( ! $nginx_helper_admin->is_nginx_log_enabled() ) ? ' style="display: none;"' : ''; ?>>
		<h3 class="hndle">
			<span><?php esc_html_e( 'Logging Options', 'gridpane-nginx-helper' ); ?></span>
		</h3>
		<div class="inside">
			<?php
			if ( ! is_dir( $log_path ) ) {
				mkdir( $log_path );
			}
			if ( is_writable( $log_path ) && ! file_exists( $log_path . 'nginx.log' ) ) {
				$log = fopen( $log_path . 'nginx.log', 'w' );
				fclose( $log );
			}
			if ( ! is_writable( $log_path . 'nginx.log' ) ) {
				?>
				<span class="error fade" style="display : block">
					<p>
					<?php
					esc_html_e( 'Can\'t write on log file.', 'gridpane-nginx-helper' );
					echo '<br /><br />';
					echo wp_kses(
						sprintf(
							// translators: %s file url.
							__( 'Check you have write permission on <strong>%s</strong>', 'gridpane-nginx-helper' ),
							esc_url( $log_path . 'nginx.log' )
						),
						array( 'strong' => array() )
					);
					?>
					</p>
				</span>
				<?php
			}
			?>

			<table class="form-table rtnginx-table">
				<tbody>
					<tr>
						<th>
							<label for="rt_wp_nginx_helper_logs_path">
								<?php esc_html_e( 'Logs path', 'gridpane-nginx-helper' ); ?>
							</label>
						</th>
						<td>
							<code>
								<?php echo esc_url( $log_path . 'nginx.log' ); ?>
							</code>
						</td>
					</tr>
					<tr>
						<th>
							<label for="rt_wp_nginx_helper_logs_link">
								<?php esc_html_e( 'View Log', 'gridpane-nginx-helper' ); ?>
							</label>
						</th>
						<td>
							<a target="_blank" href="<?php echo esc_url( $log_url . 'nginx.log' ); ?>">
								<?php esc_html_e( 'Log', 'gridpane-nginx-helper' ); ?>
							</a>
						</td>
					</tr>
					<tr>
						<th>
							<label for="rt_wp_nginx_helper_log_level">
								<?php esc_html_e( 'Log level', 'gridpane-nginx-helper' ); ?>
							</label>
						</th>
						<td>
							<select name="log_level">
								<option value="NONE" <?php selected( $nginx_helper_settings['log_level'], 'NONE' ); ?>> <?php esc_html_e( 'None', 'gridpane-nginx-helper' ); ?> </option>
								<option value="INFO" <?php selected( $nginx_helper_settings['log_level'], 'INFO' ); ?>> <?php esc_html_e( 'Info', 'gridpane-nginx-helper' ); ?> </option>
								<option value="WARNING" <?php selected( $nginx_helper_settings['log_level'], 'WARNING' ); ?>> <?php esc_html_e( 'Warning', 'gridpane-nginx-helper' ); ?> </option>
								<option value="ERROR" <?php selected( $nginx_helper_settings['log_level'], 'ERROR' ); ?>> <?php esc_html_e( 'Error', 'gridpane-nginx-helper' ); ?> </option>
							</select>
						</td>
					</tr>
					<tr>
						<th>
							<label for="log_filesize">
								<?php esc_html_e( 'Max log file size', 'gridpane-nginx-helper' ); ?>
							</label>
						</th>
						<td>
							<input id="log_filesize" class="small-text" type="text" name="log_filesize" value="<?php echo esc_attr( $nginx_helper_settings['log_filesize'] ); ?>" />
							<?php
								esc_html_e( 'Mb', 'gridpane-nginx-helper' );
							if ( $error_log_filesize ) {
								?>
								<p class="error fade" style="display: block;">
								<?php echo esc_html( $error_log_filesize ); ?>
								</p>
								<?php
							}
							?>
						</td>
					</tr>
				</tbody>
			</table>
		</div> <!-- End of .inside -->
	</div>
	<input type="hidden" name="smart_http_expire_form_nonce" value="<?php echo esc_attr( wp_create_nonce( 'smart-http-expire-form-nonce' ) ); ?>" />
	<?php
		submit_button( __( 'Save All Changes', 'gridpane-nginx-helper' ), 'primary large', 'smart_http_expire_save', true );
	?>
</form><!-- End of #post_form -->
