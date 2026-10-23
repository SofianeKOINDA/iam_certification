<?php
/**
 * Générateur de shortcode (Formations → Shortcodes).
 *
 * L'utilisateur assemble des blocs (type, domaines, outils, formations,
 * mise en page) par glisser-déposer ; le shortcode [iam_certifications]
 * s'écrit au fur et à mesure, avec un aperçu réel de la grille. Les
 * shortcodes peuvent être enregistrés sous un nom pour être réutilisés.
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Écran du générateur, aperçu et shortcodes enregistrés.
 */
class IAM_Cert_Shortcode_Builder {

	const PAGE   = 'iam-certifications-shortcodes';
	const OPTION = 'iam_cert_shortcodes';

	/**
	 * Attributs acceptés par l'aperçu (liste blanche).
	 *
	 * @var string[]
	 */
	const ATTRS = array( 'type', 'domaine', 'outil', 'ids', 'limit', 'columns', 'featured', 'orderby', 'order', 'group_by_domaine', 'filters', 'max_tools' );

	/**
	 * Enregistre les hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'menu' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_post_iam_cert_save_shortcode', array( $this, 'save' ) );
		add_action( 'admin_post_iam_cert_delete_shortcode', array( $this, 'delete' ) );
		add_action( 'template_redirect', array( $this, 'preview' ) );
	}

	/**
	 * Sous-menu sous « Formations ».
	 *
	 * @return void
	 */
	public function menu() {
		add_submenu_page(
			IAM_Cert_Admin_UI::PAGE_LIST,
			__( 'Générateur de shortcode', 'iam-certifications' ),
			__( 'Shortcodes', 'iam-certifications' ),
			IAM_Cert_Admin_UI::CAP,
			self::PAGE,
			array( $this, 'render' )
		);
	}

	/**
	 * Script et styles de l'écran (en plus de iam-admin.css, chargé par l'admin).
	 *
	 * @param string $hook Page courante.
	 * @return void
	 */
	public function assets( $hook ) {
		if ( false === strpos( $hook, self::PAGE ) ) {
			return;
		}

		wp_enqueue_style( 'iam-cert-builder', IAM_CERT_URL . 'assets/css/iam-builder.css', array( 'iam-cert-admin' ), IAM_CERT_VERSION );
		wp_enqueue_script( 'iam-cert-builder', IAM_CERT_URL . 'assets/js/iam-builder.js', array(), IAM_CERT_VERSION, true );
		wp_localize_script( 'iam-cert-builder', 'iamBuilder', $this->data() );
	}

	/**
	 * Données du catalogue pour le générateur : domaines, outils, formations.
	 *
	 * @return array
	 */
	private function data() {
		$termes = static function ( $taxonomie ) {
			$liste = get_terms(
				array(
					'taxonomy'   => $taxonomie,
					'hide_empty' => true,
				)
			);
			if ( is_wp_error( $liste ) ) {
				return array();
			}
			return array_map(
				static function ( $t ) {
					return array(
						'slug' => $t->slug,
						'nom'  => html_entity_decode( $t->name, ENT_QUOTES, 'UTF-8' ),
					);
				},
				$liste
			);
		};

		$formations = array();
		$posts      = get_posts(
			array(
				'post_type'   => IAM_CERT_CPT,
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby'     => 'menu_order title',
				'order'       => 'ASC',
			)
		);
		foreach ( $posts as $p ) {
			$type         = get_post_meta( $p->ID, '_iam_type', true );
			$formations[] = array(
				'id'       => $p->ID,
				'titre'    => html_entity_decode( get_the_title( $p ), ENT_QUOTES, 'UTF-8' ),
				'type'     => 'seminaire' === $type ? 'seminaire' : 'certificat',
				'featured' => '1' === get_post_meta( $p->ID, '_iam_featured', true ),
				'domaines' => wp_get_object_terms( $p->ID, IAM_CERT_TAX_DOMAINE, array( 'fields' => 'slugs' ) ),
				'outils'   => wp_get_object_terms( $p->ID, IAM_CERT_TAX_OUTIL, array( 'fields' => 'slugs' ) ),
				'image'    => (string) get_the_post_thumbnail_url( $p->ID, 'thumbnail' ),
			);
		}

		return array(
			'domaines'   => $termes( IAM_CERT_TAX_DOMAINE ),
			'outils'     => $termes( IAM_CERT_TAX_OUTIL ),
			'formations' => $formations,
			'apercu'     => add_query_arg(
				array(
					'iam_cert_apercu' => 1,
					'_wpnonce'        => wp_create_nonce( 'iam_cert_apercu' ),
				),
				home_url( '/' )
			),
		);
	}

	/**
	 * Shortcodes enregistrés.
	 *
	 * @return array[] Liste de { id, nom, code, blocs }.
	 */
	private function saved() {
		$liste = get_option( self::OPTION, array() );
		return is_array( $liste ) ? $liste : array();
	}

	/**
	 * Écran du générateur.
	 *
	 * @return void
	 */
	public function render() {
		$saved = $this->saved();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$message = isset( $_GET['iam_msg'] ) ? sanitize_key( $_GET['iam_msg'] ) : '';
		?>
		<div class="wrap iam-cert-admin iam-builder">
			<header class="iam-adm-head">
				<div>
					<p class="iam-adm-eyebrow"><?php esc_html_e( 'Intégration', 'iam-certifications' ); ?></p>
					<h1 class="iam-adm-title"><?php esc_html_e( 'Générateur de shortcode', 'iam-certifications' ); ?></h1>
					<p class="iam-adm-intro"><?php esc_html_e( 'Glissez des blocs dans la zone de construction, ou cliquez dessus. Le shortcode s\'écrit au fur et à mesure : copiez-le dans n\'importe quelle page.', 'iam-certifications' ); ?></p>
				</div>
			</header>
			<hr class="wp-header-end">

			<?php if ( 'saved' === $message ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Shortcode enregistré.', 'iam-certifications' ); ?></p></div>
			<?php elseif ( 'deleted' === $message ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Shortcode supprimé.', 'iam-certifications' ); ?></p></div>
			<?php endif; ?>

			<div class="iam-builder__layout">

				<aside class="iam-sec iam-builder__palette" aria-label="<?php esc_attr_e( 'Blocs disponibles', 'iam-certifications' ); ?>">
					<div class="iam-sec__head">
						<span class="iam-sec__icon dashicons dashicons-screenoptions" aria-hidden="true"></span>
						<div>
							<h2 class="iam-sec__title"><?php esc_html_e( 'Blocs', 'iam-certifications' ); ?></h2>
							<p class="iam-sec__desc"><?php esc_html_e( 'Glissez-les à droite, ou cliquez pour les ajouter.', 'iam-certifications' ); ?></p>
						</div>
					</div>
					<div class="iam-sec__body">
						<input type="search" class="iam-builder__search" placeholder="<?php esc_attr_e( 'Rechercher un bloc…', 'iam-certifications' ); ?>" data-iam-search>
						<div data-iam-palette></div>
					</div>
				</aside>

				<div class="iam-builder__main">

					<section class="iam-sec">
						<div class="iam-sec__head">
							<span class="iam-sec__icon dashicons dashicons-hammer" aria-hidden="true"></span>
							<div>
								<h2 class="iam-sec__title"><?php esc_html_e( 'Zone de construction', 'iam-certifications' ); ?></h2>
								<p class="iam-sec__desc"><?php esc_html_e( 'Réglez chaque bloc ; glissez les blocs entre eux pour changer l\'ordre des formations choisies.', 'iam-certifications' ); ?></p>
							</div>
							<div class="iam-builder__count" data-iam-count></div>
						</div>
						<div class="iam-sec__body">
							<div class="iam-builder__zone" data-iam-zone>
								<div class="iam-builder__empty" data-iam-empty>
									<span class="dashicons dashicons-move" aria-hidden="true"></span>
									<strong><?php esc_html_e( 'Déposez vos blocs ici', 'iam-certifications' ); ?></strong>
									<span><?php esc_html_e( 'Sans bloc, le shortcode affiche toutes les formations sur 3 colonnes.', 'iam-certifications' ); ?></span>
								</div>
							</div>
							<ul class="iam-builder__notes" data-iam-notes></ul>
						</div>
					</section>

					<section class="iam-sec iam-builder__result">
						<div class="iam-sec__head">
							<span class="iam-sec__icon dashicons dashicons-shortcode" aria-hidden="true"></span>
							<div>
								<h2 class="iam-sec__title"><?php esc_html_e( 'Votre shortcode', 'iam-certifications' ); ?></h2>
								<p class="iam-sec__desc"><?php esc_html_e( 'Collez-le dans une page, un article ou un widget Elementor « Shortcode ».', 'iam-certifications' ); ?></p>
							</div>
						</div>
						<div class="iam-sec__body">
							<div class="iam-builder__code">
								<code data-iam-code>[iam_certifications]</code>
								<button type="button" class="iam-adm-btn" data-iam-copy>
									<span class="dashicons dashicons-admin-page" aria-hidden="true"></span>
									<span data-iam-copy-label><?php esc_html_e( 'Copier', 'iam-certifications' ); ?></span>
								</button>
							</div>
							<form class="iam-builder__save" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-iam-save>
								<input type="hidden" name="action" value="iam_cert_save_shortcode">
								<input type="hidden" name="code" value="[iam_certifications]" data-iam-save-code>
								<input type="hidden" name="blocs" value="[]" data-iam-save-blocs>
								<?php wp_nonce_field( 'iam_cert_save_shortcode' ); ?>
								<input type="text" name="nom" required maxlength="80" placeholder="<?php esc_attr_e( 'Nom, ex. : Séminaires Data – page d\'accueil', 'iam-certifications' ); ?>">
								<button type="submit" class="iam-adm-btn iam-adm-btn--ghost">
									<span class="dashicons dashicons-saved" aria-hidden="true"></span>
									<?php esc_html_e( 'Enregistrer', 'iam-certifications' ); ?>
								</button>
							</form>
						</div>
					</section>

					<section class="iam-sec">
						<div class="iam-sec__head">
							<span class="iam-sec__icon dashicons dashicons-visibility" aria-hidden="true"></span>
							<div>
								<h2 class="iam-sec__title"><?php esc_html_e( 'Aperçu', 'iam-certifications' ); ?></h2>
								<p class="iam-sec__desc"><?php esc_html_e( 'Rendu réel du shortcode, mis à jour à chaque changement.', 'iam-certifications' ); ?></p>
							</div>
						</div>
						<div class="iam-builder__preview">
							<iframe title="<?php esc_attr_e( 'Aperçu du shortcode', 'iam-certifications' ); ?>" data-iam-preview loading="lazy"></iframe>
						</div>
					</section>

					<section class="iam-sec">
						<div class="iam-sec__head">
							<span class="iam-sec__icon dashicons dashicons-list-view" aria-hidden="true"></span>
							<div>
								<h2 class="iam-sec__title"><?php esc_html_e( 'Shortcodes enregistrés', 'iam-certifications' ); ?></h2>
								<p class="iam-sec__desc"><?php esc_html_e( 'Rechargez-en un pour le modifier, ou copiez-le directement.', 'iam-certifications' ); ?></p>
							</div>
						</div>
						<?php if ( ! $saved ) : ?>
							<div class="iam-sec__body"><p class="iam-field__hint"><?php esc_html_e( 'Aucun shortcode enregistré pour le moment.', 'iam-certifications' ); ?></p></div>
						<?php else : ?>
							<ul class="iam-builder__saved">
								<?php foreach ( $saved as $item ) : ?>
									<li>
										<div>
											<strong><?php echo esc_html( $item['nom'] ); ?></strong>
											<code><?php echo esc_html( $item['code'] ); ?></code>
										</div>
										<div class="iam-adm-row__actions">
											<button type="button" class="iam-adm-btn iam-adm-btn--ghost iam-adm-btn--sm" data-iam-load="<?php echo esc_attr( $item['blocs'] ); ?>"><?php esc_html_e( 'Charger', 'iam-certifications' ); ?></button>
											<button type="button" class="iam-adm-icon" data-iam-copy-text="<?php echo esc_attr( $item['code'] ); ?>" title="<?php esc_attr_e( 'Copier', 'iam-certifications' ); ?>"><span class="dashicons dashicons-admin-page" aria-hidden="true"></span></button>
											<a class="iam-adm-icon iam-adm-icon--danger" title="<?php esc_attr_e( 'Supprimer', 'iam-certifications' ); ?>"
												href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=iam_cert_delete_shortcode&id=' . rawurlencode( $item['id'] ) ), 'iam_cert_delete_shortcode' ) ); ?>"
												onclick="return confirm('<?php echo esc_js( __( 'Supprimer ce shortcode enregistré ? Les pages qui l\'utilisent déjà ne sont pas modifiées.', 'iam-certifications' ) ); ?>');">
												<span class="dashicons dashicons-trash" aria-hidden="true"></span>
											</a>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</section>

				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Enregistre un shortcode nommé.
	 *
	 * @return void
	 */
	public function save() {
		if ( ! current_user_can( IAM_Cert_Admin_UI::CAP ) ) {
			wp_die( esc_html__( 'Accès refusé.', 'iam-certifications' ) );
		}
		check_admin_referer( 'iam_cert_save_shortcode' );

		$nom   = sanitize_text_field( wp_unslash( $_POST['nom'] ?? '' ) );
		$code  = sanitize_text_field( wp_unslash( $_POST['code'] ?? '' ) );
		$blocs = json_decode( wp_unslash( $_POST['blocs'] ?? '[]' ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- décodé puis nettoyé champ par champ.

		// Seul notre shortcode est accepté : jamais d'autre balise en base.
		if ( '' !== $nom && 0 === strpos( $code, '[iam_certifications' ) && 1 === substr_count( $code, '[' ) ) {
			$propres = array();
			foreach ( (array) $blocs as $bloc ) {
				if ( is_array( $bloc ) && isset( $bloc['cle'] ) ) {
					$propres[] = array(
						'cle'    => sanitize_text_field( $bloc['cle'] ),
						'valeur' => isset( $bloc['valeur'] ) ? sanitize_text_field( (string) $bloc['valeur'] ) : '',
					);
				}
			}

			$liste   = $this->saved();
			$liste[] = array(
				'id'    => wp_generate_uuid4(),
				'nom'   => $nom,
				'code'  => $code,
				'blocs' => wp_json_encode( $propres ),
			);
			update_option( self::OPTION, $liste, false );
		}

		wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE . '&iam_msg=saved' ) );
		exit;
	}

	/**
	 * Supprime un shortcode enregistré.
	 *
	 * @return void
	 */
	public function delete() {
		if ( ! current_user_can( IAM_Cert_Admin_UI::CAP ) ) {
			wp_die( esc_html__( 'Accès refusé.', 'iam-certifications' ) );
		}
		check_admin_referer( 'iam_cert_delete_shortcode' );

		$id    = sanitize_text_field( wp_unslash( $_GET['id'] ?? '' ) );
		$liste = array_values(
			array_filter(
				$this->saved(),
				static function ( $item ) use ( $id ) {
					return $item['id'] !== $id;
				}
			)
		);
		update_option( self::OPTION, $liste, false );

		wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE . '&iam_msg=deleted' ) );
		exit;
	}

	/**
	 * Aperçu : page minimale du site (styles du thème compris) qui ne
	 * contient que le shortcode. Réservé aux administrateurs.
	 *
	 * @return void
	 */
	public function preview() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- vérifié juste après.
		if ( empty( $_GET['iam_cert_apercu'] ) ) {
			return;
		}
		if ( ! current_user_can( IAM_Cert_Admin_UI::CAP ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ?? '' ) ), 'iam_cert_apercu' ) ) {
			wp_die( esc_html__( 'Aperçu indisponible.', 'iam-certifications' ), '', array( 'response' => 403 ) );
		}

		$recus = json_decode( wp_unslash( $_GET['a'] ?? '{}' ), true ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- liste blanche ci-dessous.
		$code  = '[iam_certifications';
		foreach ( (array) $recus as $cle => $valeur ) {
			if ( in_array( $cle, self::ATTRS, true ) && is_scalar( $valeur ) ) {
				$code .= sprintf( ' %s="%s"', $cle, esc_attr( sanitize_text_field( (string) $valeur ) ) );
			}
		}
		$code .= ']';

		show_admin_bar( false );
		$contenu = do_shortcode( $code ); // Avant wp_head() : les styles demandés par le shortcode sont imprimés dans l'en-tête.

		?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<?php wp_head(); ?>
	<style>html, body { margin: 0 !important; padding: 0 !important; background: #fff; } .iam-apercu { padding: 24px; } .iam-cert .iam-reveal, .iam-cert [data-reveal] { opacity: 1 !important; transform: none !important; }</style>
</head>
<body class="iam-apercu-body">
	<div class="iam-apercu"><?php echo $contenu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sortie du shortcode. ?></div>
	<?php wp_footer(); ?>
	<script>
		( function () {
			function hauteur() {
				// Hauteur du seul contenu : celle du document suit la hauteur
				// de l'iframe (thèmes en 100vh) et grandirait sans fin.
				var bloc = document.querySelector( '.iam-apercu' );
				parent.postMessage( { iamApercu: Math.ceil( bloc.getBoundingClientRect().height ) }, '*' );
			}
			window.addEventListener( 'load', hauteur );
			new ResizeObserver( hauteur ).observe( document.querySelector( '.iam-apercu' ) );
			// Liens neutralisés : l'aperçu ne quitte pas la page.
			document.addEventListener( 'click', function ( e ) {
				if ( e.target.closest( 'a' ) ) {
					e.preventDefault();
				}
			} );
		}() );
	</script>
</body>
</html>
		<?php
		exit;
	}
}
