<?php
/**
 * Chargement des feuilles de style et scripts (admin et front).
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enregistre et charge les assets du plugin.
 */
class IAM_Cert_Assets {

	/**
	 * Enregistre les hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'front' ) );
	}

	/**
	 * Assets d'administration, chargés uniquement sur l'écran d'édition du CPT.
	 *
	 * @param string $hook Page d'admin courante.
	 * @return void
	 */
	public function admin( $hook ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || IAM_CERT_CPT !== $screen->post_type ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_style(
			'iam-cert-admin',
			IAM_CERT_URL . 'assets/css/iam-admin.css',
			array(),
			IAM_CERT_VERSION
		);

		wp_enqueue_script(
			'iam-cert-admin',
			IAM_CERT_URL . 'assets/js/iam-admin.js',
			array(),
			IAM_CERT_VERSION,
			true
		);
	}

	/**
	 * Assets front. La feuille de styles est enregistrée ici et réellement
	 * chargée par le shortcode / le template (chargement à la demande).
	 *
	 * @return void
	 */
	public function front() {
		wp_register_style(
			'iam-cert-front',
			IAM_CERT_URL . 'assets/css/iam-front.css',
			array(),
			IAM_CERT_VERSION
		);

		wp_register_style(
			'iam-cert-single',
			IAM_CERT_URL . 'assets/css/iam-single.css',
			array( 'iam-cert-front' ),
			IAM_CERT_VERSION
		);

		wp_register_script(
			'iam-cert-front',
			IAM_CERT_URL . 'assets/js/iam-front.js',
			array(),
			IAM_CERT_VERSION,
			true
		);

		// GSAP + ScrollTrigger (bundlés localement, pas de CDN à l'exécution).
		wp_register_script(
			'iam-gsap',
			IAM_CERT_URL . 'assets/lib/gsap.min.js',
			array(),
			'3.12.5',
			true
		);
		wp_register_script(
			'iam-scrolltrigger',
			IAM_CERT_URL . 'assets/lib/ScrollTrigger.min.js',
			array( 'iam-gsap' ),
			'3.12.5',
			true
		);
		// Le thème peut charger sa propre version de GSAP (globale « gsap ») : on
		// mémorise nos instances juste après leur chargement pour ne pas en dépendre.
		wp_add_inline_script( 'iam-gsap', 'window.iamGsap = window.gsap;' );
		wp_add_inline_script( 'iam-scrolltrigger', 'window.iamScrollTrigger = window.ScrollTrigger;' );

		wp_register_script(
			'iam-cert-animations',
			IAM_CERT_URL . 'assets/js/iam-animations.js',
			array( 'iam-gsap', 'iam-scrolltrigger' ),
			IAM_CERT_VERSION,
			true
		);
	}
}
