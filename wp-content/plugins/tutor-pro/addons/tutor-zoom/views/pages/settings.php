<?php
/**
 * Settings page.
 *
 * @package TutorPro\Addons
 * @subpackage Zoom\Views
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$zoom_settings_options = apply_filters(
	'zoom_settings_options',
	array(
		'join_before_host'   => array(
			'type'  => 'checkbox',
			'label' => __( 'Join Before Host', 'tutor-pro' ),
			'desc'  => __( 'Join meeting before the host starts the meeting. Only for scheduled or recurring meetings', 'tutor-pro' ),
		),
		'host_video'         => array(
			'type'  => 'checkbox',
			'label' => __( 'Host video', 'tutor-pro' ),
			'desc'  => __( 'Host will join the meeting with video enabled', 'tutor-pro' ),
		),
		'participants_video' => array(
			'type'  => 'checkbox',
			'label' => __( 'Participants video', 'tutor-pro' ),
			'desc'  => __( 'Participant will join the meeting with video enabled', 'tutor-pro' ),
		),
		'mute_participants'  => array(
			'type'  => 'checkbox',
			'label' => __( 'Mute Participants', 'tutor-pro' ),
			'desc'  => __( 'Participants will join the meeting with audio muted', 'tutor-pro' ),
		),
		'enforce_login'      => array(
			'type'  => 'checkbox',
			'label' => __( 'Enforce Login', 'tutor-pro' ),
			'desc'  => __( 'Only users logged into Zoom App can join the meeting', 'tutor-pro' ),
		),
		'auto_recording'     => array(
			'type'    => 'select',
			'label'   => __( 'Recording Settings', 'tutor-pro' ),
			'options' => array(
				'none'  => __( 'No Recordings', 'tutor-pro' ),
				'local' => __( 'Local Drive', 'tutor-pro' ),
				'cloud' => __( 'Zoom Cloud', 'tutor-pro' ),
			),
			'desc'    => __( 'Select Where You Want to Record', 'tutor-pro' ),
		),
	)
);
?>

<div class="tutor-admin-wrap">
	<div class="tutor-admin-body">
		<div class="tutor-admin-container tutor-admin-container-sm">
			<div class="tutor-zoom-settings">
				<?php if ( is_admin() ) : ?>
					<div class="tutor-zoom-page-title tutor-mb-2">
						<h3><?php esc_html_e( 'Settings', 'tutor-pro' ); ?></h3>
					</div>
				<?php endif; ?>

				<form id="tutor-zoom-settings">
					<input type="hidden" name="action" value="tutor_save_zoom_settings">
					<?php foreach ( $zoom_settings_options as $key => $option ) { ?>
						<div class="tutor-card tutor-p-24 tutor-mb-12">
							<?php if ( 'checkbox' === $option['type'] ) : ?>
								<div class="tutor-d-flex tutor-align-center">
									<div class="tutor-mr-24">
										<label class="tutor-form-toggle">
											<input type="checkbox" class="tutor-form-toggle-input" value="1" name="<?php echo esc_attr( $this->settings_key ) . '[' . esc_attr( $key ) . ']'; ?>" <?php checked( $this->get_settings( $key ), '1' ); ?>/>
											<span class="tutor-form-toggle-control"></span>
										</label>
									</div>

									<div class="tutor-w-100">
										<div class="tutor-fs-6 tutor-fw-medium tutor-color-black tutor-mb-4"><?php echo esc_html( $option['label'] ); ?></div>
										<div class="tutor-fs-7 tutor-color-muted"><?php echo esc_html( $option['desc'] ); ?></div>
									</div>
								</div>
							<?php elseif ( 'select' === $option['type'] ) : ?>
								<div class="card-content">
									<div class="tutor-fs-6 tutor-fw-medium tutor-color-black tutor-mb-4"><?php echo esc_html( $option['label'] ); ?></div>
									<div class="tutor-fs-7 tutor-color-muted"><?php echo esc_html( $option['desc'] ); ?></div>
									<div class="tutor-d-flex tutor-mt-24">
										<?php
										$name = $this->settings_key . '[' . $key . ']';
										foreach ( $option['options'] as $opt_key => $opt ) {
											$id_string = 'tutor_zoom_rec_' . $opt_key;
											?>
											<div class="tutor-form-check tutor-mr-16 tutor-align-center">
												<input type="radio" id="<?php echo esc_attr( $id_string ); ?>" class="tutor-form-check-input tutor-flex-shrink-0" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $opt_key ); ?>" <?php checked( $this->get_settings( $key ), $opt_key ); ?>/>
												<label for="<?php echo esc_attr( $id_string ); ?>">
													<?php echo esc_html( $opt ); ?>
												</label>
											</div>
											<?php
										}
										?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					<?php } ?>
				</form>
			</div>
		</div>
	</div>
</div>
