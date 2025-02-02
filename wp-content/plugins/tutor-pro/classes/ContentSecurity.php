<?php
/**
 * Handle Content Security Logics
 *
 * @author themeum
 * @link https://themeum.com
 * @package TutorPro
 * @since 2.2.5
 */

namespace TUTOR_PRO;

use TUTOR\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Content Security Class
 *
 * @since 2.2.5
 */
class ContentSecurity {

	const HOTLINKING_COMMENT     = 'TUTOR_PREVENT_HOTLINKING';
	const HOTLINKING_OPTION      = 'hotlink_protection';
	const COPY_PROTECTION_OPTION = 'copy_protection';
	const HTACCESS_FILE          = ABSPATH . '.htaccess';

	/**
	 * Register hooks
	 *
	 * @since 2.2.5
	 */
	public function __construct() {
		add_filter( 'tutor/options/extend/attr', array( $this, 'extend_settings_option' ) );
		add_action( 'tutor_option_save_after', array( $this, 'toggle_hotlinking_protection' ) );
		add_action( 'wp_footer', array( $this, 'copy_protection' ), 9 );
	}

	/**
	 * Toggle hotlinking protection depends on settings.
	 *
	 * @since 2.2.5
	 *
	 * @return void
	 */
	public function toggle_hotlinking_protection() {
		(bool) get_tutor_option( self::HOTLINKING_OPTION )
				? $this->add_hotlinking_rule()
				: $this->remove_hotlinking_rule();
	}

	/**
	 * Extend tutor settings options.
	 *
	 * @since 2.2.5
	 *
	 * @param array $attr settings options.
	 *
	 * @return array
	 */
	public function extend_settings_option( $attr ) {
		$content_security_section = array(
			'label'      => __( 'Content Security', 'tutor-pro' ),
			'slug'       => 'options',
			'block_type' => 'uniform',
			'fields'     => array(
				array(
					'key'     => self::HOTLINKING_OPTION,
					'type'    => 'toggle_switch',
					'label'   => __( 'Prevent Hotlinking', 'tutor-pro' ),
					'default' => 'off',
					'desc'    => __( 'Use hotlink protection for your self-hosted images and videos', 'tutor-pro' ),
				),
				array(
					'key'     => self::COPY_PROTECTION_OPTION,
					'type'    => 'toggle_switch',
					'label'   => __( 'Copy Protection', 'tutor-pro' ),
					'default' => 'off',
					'desc'    => __( 'Prevent right-click and copy actions on your website', 'tutor-pro' ),
				),
			),
		);

		$attr['advanced']['blocks'][] = $content_security_section;

		return $attr;
	}

	/**
	 * Write content to htaccess file.
	 *
	 * @since 2.2.5
	 *
	 * @param string $content text content.
	 *
	 * @return void
	 */
	public static function write_to_htaccess( $content ) {
		file_put_contents( self::HTACCESS_FILE, $content );
	}

	/**
	 * Get htaccess file content.
	 *
	 * @since 2.2.5
	 *
	 * @return string
	 */
	public static function get_htaccess_content() {
		return file_get_contents( self::HTACCESS_FILE );
	}

	/**
	 * Check HTACCESS file has hotlinking prevention rule.
	 *
	 * @since 2.2.5
	 *
	 * @return boolean
	 */
	public static function has_hotlinking_rule() {
		if ( file_exists( self::HTACCESS_FILE ) ) {
			$file_content = self::get_htaccess_content();
			return str_contains( $file_content, self::HOTLINKING_COMMENT );
		}

		return false;
	}

	/**
	 * Modify option saved message if HTACCESS has no write permission.
	 *
	 * @since 2.2.5
	 *
	 * @return void
	 */
	private static function modify_option_saved_message() {
		add_filter(
			'tutor_option_saved_data',
			function( $arr ) {
				$arr['success'] = false;
				$arr['message'] = __( 'Settings saved, but unable to modify the .htaccess file. Please review file permissions.', 'tutor-pro' );

				return $arr;
			}
		);
	}

	/**
	 * Check is URL of current site.
	 *
	 * @since 3.0.0
	 *
	 * @param string $url url.
	 *
	 * @return boolean
	 */
	public function is_current_site_url( $url ) {
		if ( ! wp_http_validate_url( $url ) ) {
			return false;
		}

		$site_host = wp_parse_url( home_url(), PHP_URL_HOST );
		$url_host  = wp_parse_url( $url, PHP_URL_HOST );

		return $url_host && $url_host === $site_host;
	}

	/**
	 * Get exclude URLs from hotlink.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_hotlink_exclude_urls() {
		$exclude_urls = array(
			tutor()->url . 'assets/images/tutor-logo.png',
		);

		// This setting could contain a media ID or an image URL.
		$media_id = get_tutor_option( 'tutor_email_template_logo_id' );
		if ( is_numeric( $media_id ) ) {
			$exclude_urls[] = wp_get_attachment_image_url( (int) $media_id, 'full' );
		} elseif ( $this->is_current_site_url( $media_id ) ) {
			$exclude_urls[] = $media_id;
		}

		return apply_filters( 'tutor_hotlink_exclude_urls', $exclude_urls );
	}

	/**
	 * Prepare exclude conditions for htaccess file.
	 *
	 * @since 3.0.0
	 *
	 * @return string
	 */
	public function prepare_exclude_conditions() {
		$exclude_urls       = $this->get_hotlink_exclude_urls();
		$exclude_conditions = '';

		foreach ( $exclude_urls as $index => $url ) {
			$path = wp_parse_url( $url, PHP_URL_PATH );
			// Escape any dots in the path to match .htaccess syntax.
			$escaped_path = preg_replace( '/\./', '\\.', $path );
			if ( $index > 0 ) {
				$exclude_conditions .= "\t" . 'RewriteCond %{REQUEST_URI} !^' . $escaped_path . " [NC]\n";
			} else {
				$exclude_conditions .= 'RewriteCond %{REQUEST_URI} !^' . $escaped_path . " [NC]\n";
			}
		}

		return $exclude_conditions;
	}

	/**
	 * Set htaccess rule for prevent hotlinking.
	 *
	 * @return void
	 */
	public function add_hotlinking_rule() {
		$domain          = preg_replace( '/(https?:\/\/(?:www\.)?)(.*)/i', '$2', get_home_url() );
		$file_extensions = array( 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'mov', 'mp3', 'avi', 'flv', 'wmv' );
		$extension_str   = implode( '|', $file_extensions );
		$comment_phase   = self::HOTLINKING_COMMENT;

		$exclude_conditions = $this->prepare_exclude_conditions();

		// Hotlinking prevention rule template.
		$rule = <<<HTACCESS_RULE
				# BEGIN $comment_phase
				<IfModule mod_rewrite.c>
					RewriteEngine on

					# Exclude certain files from being blocked
					$exclude_conditions

					# Allow direct access or access with a referer from the current site only.
					RewriteCond %{HTTP_REFERER} !^$ [NC]
					RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?$domain [NC]

					# Block access to files with the specified extensions
					RewriteRule \.($extension_str)$ - [NC,F,L]
				</IfModule>
				# END $comment_phase
				HTACCESS_RULE;

		$file_content        = $rule;
		$required_write_file = true;

		if ( file_exists( self::HTACCESS_FILE ) ) {
			if ( ! self::has_hotlinking_rule() ) {
				$file_content = self::get_htaccess_content() . "\n\n" . $rule;
			} else {
				$required_write_file = false;
			}
		}

		if ( $required_write_file ) {
			try {
				self::write_to_htaccess( $file_content );
			} catch ( \Throwable $th ) {
				tutor_log( $th->getMessage() );
			}

			if ( ! is_writable( self::HTACCESS_FILE ) ) {
				self::modify_option_saved_message();
			}
		}
	}

	/**
	 * Remove hotlinking rules.
	 *
	 * @since 2.2.5
	 *
	 * @return void
	 */
	public function remove_hotlinking_rule() {
		$current_content = self::get_htaccess_content();
		$comment_phase   = self::HOTLINKING_COMMENT;
		$updated_content = preg_replace( "/\n*?# BEGIN $comment_phase.*END $comment_phase/s", '', $current_content );

		if ( self::has_hotlinking_rule() ) {
			try {
				self::write_to_htaccess( $updated_content );
			} catch ( \Throwable $th ) {
				tutor_log( $th->getMessage() );
			}

			if ( ! is_writable( self::HTACCESS_FILE ) ) {
				self::modify_option_saved_message();
			}
		}
	}

	/**
	 * Copy protection on site like disable right-click and content copy to clipboard.
	 *
	 * @since 2.2.5
	 *
	 * @return void
	 */
	public function copy_protection() {
		if ( User::is_admin() ) {
			return;
		}

		$enabled_copy_protection = (bool) get_tutor_option( self::COPY_PROTECTION_OPTION );
		if ( $enabled_copy_protection ) {
			?>
			<script>
				function tutor_prevent_copy(event) {
					const nodeName = event.target?.nodeName
					if (! ['INPUT', 'TEXTAREA'].includes(nodeName) ) {
						event.preventDefault();
					}
				}

				document.addEventListener('contextmenu', tutor_prevent_copy );
				document.addEventListener('copy', tutor_prevent_copy );
			</script>
			<?php
		}
	}
}
