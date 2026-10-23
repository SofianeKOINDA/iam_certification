<?php
/**
 * Interface d'administration dédiée (intégrée au plugin) : liste et formulaire
 * de création / édition d'une certification, sans passer par l'éditeur WordPress.
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu + écrans d'admin maison.
 */
class IAM_Cert_Admin_UI {

	const PAGE_LIST = 'iam-certifications';
	const PAGE_EDIT = 'iam-certifications-edit';
	const PAGE_DOMAINES = 'iam-certifications-domaines';
	const PAGE_OUTILS   = 'iam-certifications-outils';
	const CAP       = 'edit_posts';

	/**
	 * Enregistre les hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_post_iam_cert_save', array( $this, 'handle_save' ) );
		add_action( 'admin_post_iam_cert_delete', array( $this, 'handle_delete' ) );
		add_action( 'admin_post_iam_cert_term_save', array( $this, 'handle_term_save' ) );
		add_action( 'admin_post_iam_cert_term_delete', array( $this, 'handle_term_delete' ) );

		// Les écrans natifs (éditeur, taxonomies) renvoient vers l'interface du plugin.
		add_action( 'load-post.php', array( $this, 'redirect_native_editor' ) );
		add_action( 'load-post-new.php', array( $this, 'redirect_native_editor' ) );
		add_action( 'load-edit.php', array( $this, 'redirect_native_editor' ) );
		add_action( 'load-edit-tags.php', array( $this, 'redirect_native_terms' ) );
		add_action( 'load-term.php', array( $this, 'redirect_native_terms' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_filter( 'admin_title', array( $this, 'admin_title' ), 10, 2 );
	}

	/**
	 * Construit le menu du plugin.
	 *
	 * @return void
	 */
	public function menu() {
		add_menu_page(
			__( 'Formations', 'iam-certifications' ),
			__( 'Formations', 'iam-certifications' ),
			self::CAP,
			self::PAGE_LIST,
			array( $this, 'render_list' ),
			'dashicons-awards',
			25
		);

		add_submenu_page(
			self::PAGE_LIST,
			__( 'Catalogue', 'iam-certifications' ),
			__( 'Catalogue', 'iam-certifications' ),
			self::CAP,
			self::PAGE_LIST,
			array( $this, 'render_list' )
		);

		add_submenu_page(
			self::PAGE_LIST,
			__( 'Ajouter', 'iam-certifications' ),
			__( 'Ajouter', 'iam-certifications' ),
			self::CAP,
			self::PAGE_EDIT,
			array( $this, 'render_edit' )
		);

		// Taxonomies : écrans maison (les écrans natifs redirigent ici).
		add_submenu_page(
			self::PAGE_LIST,
			__( 'Domaines & collections', 'iam-certifications' ),
			__( 'Domaines', 'iam-certifications' ),
			self::CAP,
			self::PAGE_DOMAINES,
			array( $this, 'render_domaines' )
		);

		add_submenu_page(
			self::PAGE_LIST,
			__( 'Outils & technologies', 'iam-certifications' ),
			__( 'Outils', 'iam-certifications' ),
			self::CAP,
			self::PAGE_OUTILS,
			array( $this, 'render_outils' )
		);
	}

	/**
	 * Titre de l'onglet du navigateur : « Modifier » plutôt que « Ajouter » en édition.
	 *
	 * @param string $admin_title Titre complet.
	 * @param string $title       Titre de la page.
	 * @return string
	 */
	public function admin_title( $admin_title, $title ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['page'], $_GET['id'] ) && self::PAGE_EDIT === $_GET['page'] && absint( $_GET['id'] ) ) {
			return str_replace( $title, __( 'Modifier la formation', 'iam-certifications' ), $admin_title );
		}
		return $admin_title;
	}

	/**
	 * Charge les assets (media + répéteur) sur nos écrans uniquement.
	 *
	 * @param string $hook Hook de la page courante.
	 * @return void
	 */
	public function assets( $hook ) {
		if ( false === strpos( $hook, self::PAGE_LIST ) ) {
			return;
		}
		wp_enqueue_media();
		wp_enqueue_style( 'iam-cert-admin', IAM_CERT_URL . 'assets/css/iam-admin.css', array(), IAM_CERT_VERSION );
		wp_enqueue_script( 'iam-gsap', IAM_CERT_URL . 'assets/lib/gsap.min.js', array(), '3.12.5', true );
		wp_enqueue_script( 'iam-cert-admin', IAM_CERT_URL . 'assets/js/iam-admin.js', array( 'jquery', 'iam-gsap' ), IAM_CERT_VERSION, true );
	}

	/* =====================================================================
	 * Liste
	 * ================================================================== */

	/**
	 * Affiche la liste des formations, filtrable par type.
	 *
	 * @return void
	 */
	public function render_list() {
		if ( ! current_user_can( self::CAP ) ) {
			return;
		}

		$this->notice();

		$certifs = get_posts(
			array(
				'post_type'   => IAM_CERT_CPT,
				'post_status' => array( 'publish', 'draft', 'pending' ),
				'numberposts' => -1,
				'orderby'     => 'menu_order title',
				'order'       => 'ASC',
			)
		);

		$types  = iam_cert_types();
		$counts = array_fill_keys( array_keys( $types ), 0 );
		foreach ( $certifs as $c ) {
			$counts[ iam_cert_type( $c->ID ) ]++;
		}

		$add_url = admin_url( 'admin.php?page=' . self::PAGE_EDIT );
		?>
		<div class="wrap iam-cert-admin">
			<header class="iam-adm-head">
				<div>
					<p class="iam-adm-eyebrow"><?php esc_html_e( 'Catalogue de formations', 'iam-certifications' ); ?></p>
					<h1 class="iam-adm-title"><?php esc_html_e( 'Certificats & séminaires', 'iam-certifications' ); ?></h1>
				</div>
				<div class="iam-adm-head__actions">
					<a href="<?php echo esc_url( add_query_arg( 'type', 'seminaire', $add_url ) ); ?>" class="iam-adm-btn iam-adm-btn--ghost"><span class="dashicons dashicons-groups"></span> <?php esc_html_e( 'Nouveau séminaire', 'iam-certifications' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'type', 'certificat', $add_url ) ); ?>" class="iam-adm-btn"><span class="dashicons dashicons-awards"></span> <?php esc_html_e( 'Nouveau certificat', 'iam-certifications' ); ?></a>
				</div>
			</header>
			<hr class="wp-header-end">

			<div class="iam-adm-tabs" role="tablist" data-iam-list-tabs>
				<button type="button" class="is-active" data-type=""><?php esc_html_e( 'Tout', 'iam-certifications' ); ?> <span><?php echo (int) count( $certifs ); ?></span></button>
				<?php foreach ( $types as $key => $lbl ) : ?>
					<button type="button" data-type="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( 'seminaire' === $key ? __( 'Séminaires', 'iam-certifications' ) : __( 'Certificats', 'iam-certifications' ) ); ?> <span><?php echo (int) $counts[ $key ]; ?></span></button>
				<?php endforeach; ?>
			</div>

			<div class="iam-adm-list">
				<?php if ( ! $certifs ) : ?>
					<div class="iam-adm-empty">
						<span class="dashicons dashicons-welcome-learn-more"></span>
						<p><?php esc_html_e( 'Aucune formation pour le moment. Commencez par créer un certificat ou un séminaire.', 'iam-certifications' ); ?></p>
					</div>
				<?php endif; ?>
				<?php foreach ( $certifs as $c ) : ?>
					<?php
					$type     = iam_cert_type( $c->ID );
					$edit_url = admin_url( 'admin.php?page=' . self::PAGE_EDIT . '&id=' . $c->ID );
					$del_url  = wp_nonce_url( admin_url( 'admin-post.php?action=iam_cert_delete&id=' . $c->ID ), 'iam_cert_delete_' . $c->ID );
					$domaines = get_the_terms( $c->ID, IAM_CERT_TAX_DOMAINE );
					$domaine  = ( $domaines && ! is_wp_error( $domaines ) ) ? $domaines[0]->name : '';
					$thumb    = get_the_post_thumbnail_url( $c->ID, 'thumbnail' );
					$n        = 'seminaire' === $type ? count( iam_cert_get_jours( $c->ID ) ) : iam_cert_counts( $c->ID )['modules'];
					$unit     = 'seminaire' === $type ? _n( 'jour', 'jours', $n, 'iam-certifications' ) : _n( 'module', 'modules', $n, 'iam-certifications' );
					$status   = get_post_status( $c );
					?>
					<article class="iam-adm-row iam-adm-row--<?php echo esc_attr( $type ); ?>" data-type="<?php echo esc_attr( $type ); ?>">
						<a class="iam-adm-row__thumb" href="<?php echo esc_url( $edit_url ); ?>"<?php echo $thumb ? ' style="background-image:url(' . esc_url( $thumb ) . ')"' : ''; ?>>
							<?php if ( ! $thumb ) : ?><span class="dashicons <?php echo 'seminaire' === $type ? 'dashicons-groups' : 'dashicons-awards'; ?>"></span><?php endif; ?>
						</a>
						<div class="iam-adm-row__main">
							<p class="iam-adm-row__meta">
								<span class="iam-adm-pill iam-adm-pill--<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $types[ $type ] ); ?></span>
								<?php if ( $domaine ) : ?><span><?php echo esc_html( $domaine ); ?></span><?php endif; ?>
								<?php if ( get_post_meta( $c->ID, '_iam_featured', true ) ) : ?><span class="iam-adm-star" title="<?php esc_attr_e( 'Mise en avant', 'iam-certifications' ); ?>">★</span><?php endif; ?>
							</p>
							<h2 class="iam-adm-row__title"><a href="<?php echo esc_url( $edit_url ); ?>"><?php echo esc_html( get_the_title( $c ) ); ?></a></h2>
							<p class="iam-adm-row__facts">
								<?php echo esc_html( implode( ' · ', array_filter( array( iam_cert_duree_label( $c->ID ), $n ? $n . ' ' . $unit : '' ) ) ) ?: '—' ); ?>
							</p>
						</div>
						<span class="iam-adm-status iam-adm-status--<?php echo esc_attr( $status ); ?>"><?php echo esc_html( 'publish' === $status ? __( 'Publié', 'iam-certifications' ) : __( 'Brouillon', 'iam-certifications' ) ); ?></span>
						<div class="iam-adm-row__actions">
							<a href="<?php echo esc_url( $edit_url ); ?>" class="iam-adm-btn iam-adm-btn--sm"><?php esc_html_e( 'Modifier', 'iam-certifications' ); ?></a>
							<a href="<?php echo esc_url( get_permalink( $c ) ); ?>" class="iam-adm-icon" target="_blank" rel="noopener" title="<?php esc_attr_e( 'Voir la page', 'iam-certifications' ); ?>"><span class="dashicons dashicons-external"></span></a>
							<a href="<?php echo esc_url( $del_url ); ?>" class="iam-adm-icon iam-adm-icon--danger iam-cert-del" title="<?php esc_attr_e( 'Mettre à la corbeille', 'iam-certifications' ); ?>"><span class="dashicons dashicons-trash"></span></a>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/* =====================================================================
	 * Formulaire d'édition (assistant à étapes)
	 * ================================================================== */

	/**
	 * Étapes de l'assistant. Libellés déclinés par type (certificat / séminaire).
	 *
	 * @return array[] { icon, label: array type => texte, desc: array type => texte }
	 */
	private function steps() {
		return array(
			array(
				'icon'  => 'dashicons-id-alt',
				'label' => array(
					'certificat' => __( 'Identité', 'iam-certifications' ),
					'seminaire'  => __( 'Identité', 'iam-certifications' ),
				),
				'desc'  => array(
					'certificat' => __( 'Type de formation, titre, accroche, domaine et visuel.', 'iam-certifications' ),
					'seminaire'  => __( 'Type de formation, titre, accroche, collection et visuel.', 'iam-certifications' ),
				),
			),
			array(
				'icon'  => 'dashicons-clock',
				'label' => array(
					'certificat' => __( 'Format & durée', 'iam-certifications' ),
					'seminaire'  => __( 'Format & sessions', 'iam-certifications' ),
				),
				'desc'  => array(
					'certificat' => __( 'Volume, durée, rythme et niveau : les chiffres clés du hero.', 'iam-certifications' ),
					'seminaire'  => __( 'Durée, jauge, part de pratique, horaires et calendrier des sessions.', 'iam-certifications' ),
				),
			),
			array(
				'icon'  => 'dashicons-welcome-learn-more',
				'label' => array(
					'certificat' => __( 'Contenu & public', 'iam-certifications' ),
					'seminaire'  => __( 'Contenu & public', 'iam-certifications' ),
				),
				'desc'  => array(
					'certificat' => __( 'Objectifs, public, prérequis, débouchés, projet final et évaluation.', 'iam-certifications' ),
					'seminaire'  => __( 'Objectifs pédagogiques, public visé, prérequis, livrables et attestation.', 'iam-certifications' ),
				),
			),
			array(
				'icon'  => 'dashicons-list-view',
				'label' => array(
					'certificat' => __( 'Modules', 'iam-certifications' ),
					'seminaire'  => __( 'Jour par jour', 'iam-certifications' ),
				),
				'desc'  => array(
					'certificat' => __( 'Les modules du parcours et leurs leçons. Le dernier module est mis en avant comme projet final.', 'iam-certifications' ),
					'seminaire'  => __( 'Le programme détaillé : un bloc par journée, matin et après-midi.', 'iam-certifications' ),
				),
			),
			array(
				'icon'  => 'dashicons-format-video',
				'label' => array(
					'certificat' => __( 'Médias & liens', 'iam-certifications' ),
					'seminaire'  => __( 'Médias & liens', 'iam-certifications' ),
				),
				'desc'  => array(
					'certificat' => __( 'Vidéo, brochure, dossier, rendez-vous. Tout est optionnel.', 'iam-certifications' ),
					'seminaire'  => __( 'Vidéo, brochure, bulletin d’inscription, rendez-vous. Tout est optionnel.', 'iam-certifications' ),
				),
			),
			array(
				'icon'  => 'dashicons-megaphone',
				'label' => array(
					'certificat' => __( 'Publication', 'iam-certifications' ),
					'seminaire'  => __( 'Publication', 'iam-certifications' ),
				),
				'desc'  => array(
					'certificat' => __( 'Outils, formulaire de candidature, mise en avant et vérification finale.', 'iam-certifications' ),
					'seminaire'  => __( 'Outils, formulaire d’inscription, mise en avant et vérification finale.', 'iam-certifications' ),
				),
			),
		);
	}

	/**
	 * Ouvre une section-carte (en-tête avec icône, titre et description).
	 *
	 * @param string       $icon  Classe dashicons.
	 * @param string|array $title Titre (ou variantes par type).
	 * @param string|array $desc  Description (ou variantes par type).
	 * @param string       $scope both|certificat|seminaire.
	 * @param string       $group Groupe obligatoire contrôlé par le JS (domaine, modules, jours).
	 * @return void
	 */
	private function section_open( $icon, $title, $desc = '', $scope = 'both', $group = '' ) {
		$v = function ( $txt ) {
			return iam_cert_variants_html( is_array( $txt ) ? $txt : array( 'both' => $txt ) );
		};
		?>
		<section class="iam-sec" data-scope="<?php echo esc_attr( $scope ); ?>"<?php echo $group ? ' data-required-group="' . esc_attr( $group ) . '"' : ''; ?>>
			<header class="iam-sec__head">
				<span class="iam-sec__icon dashicons <?php echo esc_attr( $icon ); ?>" aria-hidden="true"></span>
				<div>
					<h3 class="iam-sec__title"><?php echo $v( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h3>
					<?php if ( $desc ) : ?><p class="iam-sec__desc"><?php echo $v( $desc ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p><?php endif; ?>
				</div>
			</header>
			<div class="iam-sec__body">
		<?php
	}

	/**
	 * Ferme une section-carte.
	 *
	 * @return void
	 */
	private function section_close() {
		echo '</div></section>';
	}

	/**
	 * Affiche le formulaire de création / édition.
	 *
	 * @return void
	 */
	public function render_edit() {
		if ( ! current_user_can( self::CAP ) ) {
			return;
		}

		$id   = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$post = $id ? get_post( $id ) : null;

		if ( $id && ( ! $post || IAM_CERT_CPT !== $post->post_type ) ) {
			echo '<div class="wrap"><p>' . esc_html__( 'Formation introuvable.', 'iam-certifications' ) . '</p></div>';
			return;
		}

		// Type : celui de la formation, sinon celui demandé dans l'URL (boutons de la liste).
		if ( $id ) {
			$type = iam_cert_type( $id );
		} else {
			$type = isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : 'certificat'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$type = array_key_exists( $type, iam_cert_types() ) ? $type : 'certificat';
		}

		$titre      = $post ? $post->post_title : '';
		$statut     = $post ? $post->post_status : 'draft';
		$meta       = function ( $k ) use ( $id ) {
			return $id ? get_post_meta( $id, $k, true ) : '';
		};
		$domaine_id = 0;
		$outils_csv = '';
		if ( $id ) {
			$terms = get_the_terms( $id, IAM_CERT_TAX_DOMAINE );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$domaine_id = $terms[0]->term_id;
			}
			$terms = get_the_terms( $id, IAM_CERT_TAX_OUTIL );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$outils_csv = implode( ', ', wp_list_pluck( $terms, 'name' ) );
			}
		}
		$thumb_id  = $id ? (int) get_post_thumbnail_id( $id ) : 0;
		$thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'large' ) : '';
		$modules   = $id ? iam_cert_get_modules( $id ) : array();
		$jours     = $id ? iam_cert_get_jours( $id ) : array();
		if ( ! $jours ) {
			// Un séminaire type dure 3 jours : on prépare les trois blocs.
			$jours = array( array(), array(), array() );
		}

		$fields = IAM_Cert_Metaboxes::fields();
		$field  = function ( $key ) use ( $fields, $meta ) {
			iam_cert_render_field_row( $key, $fields[ $key ], $meta( $key ) );
		};
		$steps  = $this->steps();
		$types  = iam_cert_types();

		$this->notice();
		?>
		<div class="wrap iam-cert-admin">
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
				class="iam-wiz iam-kind--<?php echo esc_attr( $type ); ?>" id="iam-cert-wizard"
				data-edit="<?php echo $id ? '1' : '0'; ?>" novalidate>
				<input type="hidden" name="action" value="iam_cert_save">
				<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>">
				<input type="hidden" name="statut" value="<?php echo esc_attr( $statut ); ?>" data-iam-statut>
				<?php wp_nonce_field( 'iam_cert_save', 'iam_cert_nonce' ); ?>

				<header class="iam-adm-head">
					<div>
						<p class="iam-adm-eyebrow">
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::PAGE_LIST ) ); ?>">← <?php esc_html_e( 'Catalogue', 'iam-certifications' ); ?></a>
						</p>
						<h1 class="iam-adm-title">
							<?php if ( $id ) : ?>
								<?php echo esc_html( $titre ); ?>
							<?php else : ?>
								<span data-for="certificat"><?php esc_html_e( 'Nouveau certificat', 'iam-certifications' ); ?></span><span data-for="seminaire"><?php esc_html_e( 'Nouveau séminaire', 'iam-certifications' ); ?></span>
							<?php endif; ?>
						</h1>
					</div>
					<div class="iam-adm-head__actions">
						<span class="iam-adm-pill iam-adm-pill--certificat" data-for="certificat"><?php echo esc_html( $types['certificat'] ); ?></span>
						<span class="iam-adm-pill iam-adm-pill--seminaire" data-for="seminaire"><?php echo esc_html( $types['seminaire'] ); ?></span>
						<span class="iam-adm-status iam-adm-status--<?php echo esc_attr( $statut ); ?>"><?php echo esc_html( 'publish' === $statut ? __( 'Publié', 'iam-certifications' ) : __( 'Brouillon', 'iam-certifications' ) ); ?></span>
						<?php if ( $id ) : ?>
							<a href="<?php echo esc_url( get_permalink( $id ) ); ?>" class="iam-adm-btn iam-adm-btn--ghost" target="_blank" rel="noopener"><span class="dashicons dashicons-external"></span> <?php esc_html_e( 'Voir la page', 'iam-certifications' ); ?></a>
						<?php endif; ?>
					</div>
				</header>
				<hr class="wp-header-end">

				<!-- Stepper -->
				<nav class="iam-stepper" aria-label="<?php esc_attr_e( 'Étapes', 'iam-certifications' ); ?>">
					<div class="iam-stepper__top">
						<div>
							<p class="iam-stepper__eyebrow"><span data-for="certificat"><?php esc_html_e( 'Parcours certificat', 'iam-certifications' ); ?></span><span data-for="seminaire"><?php esc_html_e( 'Parcours séminaire', 'iam-certifications' ); ?></span></p>
							<h2 class="iam-stepper__title" data-iam-step-title></h2>
						</div>
						<p class="iam-stepper__next" data-iam-step-next></p>
					</div>
					<div class="iam-stepper__bar" aria-hidden="true"><span data-iam-step-fill></span></div>
					<ol class="iam-stepper__steps">
						<?php foreach ( $steps as $i => $st ) : ?>
							<li class="iam-stepper__step" data-step="<?php echo (int) $i; ?>"
								data-label-certificat="<?php echo esc_attr( $st['label']['certificat'] ); ?>"
								data-label-seminaire="<?php echo esc_attr( $st['label']['seminaire'] ); ?>">
								<button type="button" class="iam-stepper__btn">
									<span class="iam-stepper__dot"><span class="dashicons <?php echo esc_attr( $st['icon'] ); ?>"></span></span>
									<span class="iam-stepper__lbl"><?php echo iam_cert_variants_html( $st['label'] ); // phpcs:ignore ?></span>
								</button>
							</li>
						<?php endforeach; ?>
					</ol>
				</nav>

				<!-- Étape 1 : Identité -->
				<div class="iam-step is-active" data-step="0">
					<?php $this->section_open( 'dashicons-forms', __( 'Type de formation', 'iam-certifications' ), __( 'Les deux formats n’affichent pas les mêmes informations : le formulaire et la page s’adaptent.', 'iam-certifications' ) ); ?>
						<div class="iam-kinds" role="radiogroup">
							<label class="iam-kind iam-kind--card-certificat">
								<input type="radio" name="_iam_type" value="certificat" <?php checked( $type, 'certificat' ); ?>>
								<span class="iam-kind__icon dashicons dashicons-awards"></span>
								<span class="iam-kind__title"><?php esc_html_e( 'Certificat professionnel', 'iam-certifications' ); ?></span>
								<span class="iam-kind__desc"><?php esc_html_e( 'Parcours de 6 à 12 semaines, en ligne : modules et leçons, coaching, projet final soutenu devant jury, débouchés.', 'iam-certifications' ); ?></span>
								<span class="iam-kind__tags"><span><?php esc_html_e( 'Modules', 'iam-certifications' ); ?></span><span><?php esc_html_e( 'Projet final', 'iam-certifications' ); ?></span><span><?php esc_html_e( 'Débouchés', 'iam-certifications' ); ?></span></span>
							</label>
							<label class="iam-kind iam-kind--card-seminaire">
								<input type="radio" name="_iam_type" value="seminaire" <?php checked( $type, 'seminaire' ); ?>>
								<span class="iam-kind__icon dashicons dashicons-groups"></span>
								<span class="iam-kind__title"><?php esc_html_e( 'Séminaire intensif', 'iam-certifications' ); ?></span>
								<span class="iam-kind__desc"><?php esc_html_e( 'Quelques jours en présentiel ou en intra : programme jour par jour, petits groupes, ateliers, livrables et sessions datées.', 'iam-certifications' ); ?></span>
								<span class="iam-kind__tags"><span><?php esc_html_e( 'Jour par jour', 'iam-certifications' ); ?></span><span><?php esc_html_e( 'Sessions', 'iam-certifications' ); ?></span><span><?php esc_html_e( 'Livrables', 'iam-certifications' ); ?></span></span>
							</label>
						</div>
					<?php $this->section_close(); ?>

					<?php $this->section_open( 'dashicons-edit-large', __( 'Titre & accroche', 'iam-certifications' ), __( 'Le titre et l’accroche ouvrent la page et apparaissent sur la carte du catalogue.', 'iam-certifications' ) ); ?>
						<div class="iam-field iam-field--full" data-scope="both">
							<label class="iam-field__label" for="iam_titre"><?php esc_html_e( 'Titre', 'iam-certifications' ); ?> <span class="iam-req">*</span></label>
							<input type="text" id="iam_titre" name="titre" class="widefat iam-input-xl" required value="<?php echo esc_attr( $titre ); ?>"
								data-ph-certificat="<?php esc_attr_e( 'Ex. Marketing Automation & Growth Hacking', 'iam-certifications' ); ?>"
								data-ph-seminaire="<?php esc_attr_e( 'Ex. Acquisition × IA — Recruter avec l’IA', 'iam-certifications' ); ?>">
							<p class="iam-field__error" data-iam-error-for="iam_titre" hidden><?php esc_html_e( 'Donnez un titre à la formation pour continuer.', 'iam-certifications' ); ?></p>
						</div>
						<?php $field( '_iam_hero' ); ?>
					<?php $this->section_close(); ?>

					<div class="iam-grid-2">
						<?php $this->section_open( 'dashicons-category', array( 'certificat' => __( 'Domaine *', 'iam-certifications' ), 'seminaire' => __( 'Collection *', 'iam-certifications' ) ), array( 'certificat' => __( 'Regroupe les cartes du catalogue.', 'iam-certifications' ), 'seminaire' => __( 'Ex. « Talents × IA » ou « Marketing Digital & IA ».', 'iam-certifications' ) ), 'both', 'domaine' ); ?>
							<?php
							wp_dropdown_categories(
								array(
									'taxonomy'          => IAM_CERT_TAX_DOMAINE,
									'name'              => 'domaine_id',
									'id'                => 'iam_domaine_id',
									'selected'          => $domaine_id,
									'show_option_none'  => __( '— Choisir —', 'iam-certifications' ),
									'option_none_value' => 0,
									'hierarchical'      => true,
									'hide_empty'        => false,
									'class'             => 'widefat',
								)
							);
							?>
							<p class="iam-inline-new">
								<span class="dashicons dashicons-plus-alt2"></span>
								<input type="text" name="domaine_new" class="widefat" placeholder="<?php esc_attr_e( '…ou créer un nouveau', 'iam-certifications' ); ?>">
							</p>
							<p class="iam-field__error" data-iam-group-error hidden><?php esc_html_e( 'Choisissez ou créez un domaine.', 'iam-certifications' ); ?></p>
						<?php $this->section_close(); ?>

						<?php $this->section_open( 'dashicons-format-image', __( 'Visuel principal', 'iam-certifications' ), __( 'Fond du hero et image de la carte. Recommandé : 1600 × 900 px.', 'iam-certifications' ) ); ?>
							<input type="hidden" id="iam_thumb_id" name="_thumbnail_id" value="<?php echo esc_attr( $thumb_id ); ?>">
							<div class="iam-drop<?php echo $thumb_url ? ' has-image' : ''; ?>" id="iam_thumb_drop">
								<button type="button" class="iam-drop__empty" id="iam_thumb_btn">
									<span class="iam-drop__icon dashicons dashicons-format-image"></span>
									<strong><?php esc_html_e( 'Choisir une image', 'iam-certifications' ); ?></strong>
									<span><?php esc_html_e( 'Depuis la médiathèque ou votre ordinateur', 'iam-certifications' ); ?></span>
									<span class="iam-drop__cta"><?php esc_html_e( 'Parcourir', 'iam-certifications' ); ?></span>
								</button>
								<div class="iam-drop__card">
									<div class="iam-drop__media" id="iam_thumb_preview"><?php if ( $thumb_url ) : ?><img src="<?php echo esc_url( $thumb_url ); ?>" alt=""><?php endif; ?></div>
									<div class="iam-drop__foot">
										<span class="iam-drop__badge"><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Image définie', 'iam-certifications' ); ?></span>
										<span>
											<button type="button" class="iam-adm-btn iam-adm-btn--ghost iam-adm-btn--sm" data-iam-thumb-replace><?php esc_html_e( 'Remplacer', 'iam-certifications' ); ?></button>
											<button type="button" class="iam-adm-icon iam-adm-icon--danger" id="iam_thumb_remove" title="<?php esc_attr_e( 'Retirer', 'iam-certifications' ); ?>"><span class="dashicons dashicons-trash"></span></button>
										</span>
									</div>
								</div>
							</div>
						<?php $this->section_close(); ?>
					</div>
				</div>

				<!-- Étape 2 : Format -->
				<div class="iam-step" data-step="1">
					<?php $this->section_open( 'dashicons-chart-bar', __( 'Chiffres clés', 'iam-certifications' ), __( 'Affichés en grand, avec compteurs animés, en haut de la page.', 'iam-certifications' ) ); ?>
						<div class="iam-fields">
							<?php
							foreach ( array( '_iam_duree_texte', '_iam_duree_heures', '_iam_format', '_iam_niveau', '_iam_participants_max', '_iam_pratique_pct', '_iam_rythme' ) as $k ) {
								$field( $k );
							}
							?>
						</div>
					<?php $this->section_close(); ?>

					<?php $this->section_open( 'dashicons-calendar-alt', __( 'Sessions & logistique', 'iam-certifications' ), __( 'Les sessions deviennent des cartes « Réserver » sur la page.', 'iam-certifications' ), 'seminaire' ); ?>
						<div class="iam-fields">
							<?php
							foreach ( array( '_iam_horaires', '_iam_elearning', '_iam_sessions', '_iam_infos_pratiques' ) as $k ) {
								$field( $k );
							}
							?>
						</div>
					<?php $this->section_close(); ?>
				</div>

				<!-- Étape 3 : Contenu & public -->
				<div class="iam-step" data-step="2">
					<?php $this->section_open( 'dashicons-flag', __( 'Objectifs', 'iam-certifications' ), __( 'Ce que les participants sauront faire à la fin.', 'iam-certifications' ) ); ?>
						<?php $field( '_iam_objectifs' ); ?>
					<?php $this->section_close(); ?>

					<?php $this->section_open( 'dashicons-groups', __( 'Public & prérequis', 'iam-certifications' ) ); ?>
						<div class="iam-fields">
							<?php $field( '_iam_public' ); ?>
							<?php $field( '_iam_prerequis' ); ?>
						</div>
					<?php $this->section_close(); ?>

					<?php $this->section_open( 'dashicons-awards', array( 'certificat' => __( 'Débouchés, projet & certification', 'iam-certifications' ), 'seminaire' => __( 'Livrables & attestation', 'iam-certifications' ) ) ); ?>
						<div class="iam-fields">
							<?php
							foreach ( array( '_iam_debouches', '_iam_projet_final', '_iam_livrables', '_iam_evaluation', '_iam_cout' ) as $k ) {
								$field( $k );
							}
							?>
						</div>
					<?php $this->section_close(); ?>
				</div>

				<!-- Étape 4 : Programme -->
				<div class="iam-step" data-step="3">
					<?php $this->section_open( 'dashicons-list-view', __( 'Modules du parcours *', 'iam-certifications' ), __( 'Un module = un titre + ses leçons (une par ligne). Les compteurs de la page se calculent tout seuls.', 'iam-certifications' ), 'certificat', 'modules' ); ?>
						<div class="iam-repeater" id="iam-modules" data-kind="module">
							<div class="iam-repeater__list iam-modules__list">
								<?php
								if ( $modules ) {
									foreach ( $modules as $i => $m ) {
										echo iam_cert_module_row_html( $i, $m['titre'], $m['lecons'] ); // phpcs:ignore
									}
								} else {
									echo iam_cert_module_row_html( 0, '', array() ); // phpcs:ignore
								}
								?>
							</div>
							<p class="iam-field__error" data-iam-group-error hidden><?php esc_html_e( 'Ajoutez au moins un module avec son titre.', 'iam-certifications' ); ?></p>
							<button type="button" class="iam-repeater__add" data-template="iam-module-template"><span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e( 'Ajouter un module', 'iam-certifications' ); ?></button>
							<script type="text/html" id="iam-module-template"><?php echo iam_cert_module_row_html( '__index__', '', array() ); // phpcs:ignore ?></script>
						</div>
					<?php $this->section_close(); ?>

					<?php $this->section_open( 'dashicons-calendar', __( 'Programme jour par jour *', 'iam-certifications' ), __( 'Un bloc par journée. Retours à la ligne = puces sur la page.', 'iam-certifications' ), 'seminaire', 'jours' ); ?>
						<div class="iam-repeater" id="iam-days" data-kind="day">
							<div class="iam-repeater__list">
								<?php
								foreach ( $jours as $i => $j ) {
									echo iam_cert_day_row_html( $i, $j ); // phpcs:ignore
								}
								?>
							</div>
							<p class="iam-field__error" data-iam-group-error hidden><?php esc_html_e( 'Décrivez au moins une journée (matin ou après-midi).', 'iam-certifications' ); ?></p>
							<button type="button" class="iam-repeater__add" data-template="iam-day-template"><span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e( 'Ajouter une journée', 'iam-certifications' ); ?></button>
							<script type="text/html" id="iam-day-template"><?php echo iam_cert_day_row_html( '__index__', array() ); // phpcs:ignore ?></script>
						</div>
					<?php $this->section_close(); ?>
				</div>

				<!-- Étape 5 : Médias & liens -->
				<div class="iam-step" data-step="4">
					<?php $this->section_open( 'dashicons-format-video', __( 'Médias', 'iam-certifications' ), __( 'Un bouton ou un bloc dont le champ est vide n’apparaît pas sur la page.', 'iam-certifications' ) ); ?>
						<div class="iam-fields">
							<?php $field( '_iam_video_url' ); ?>
							<?php $field( '_iam_image2_url' ); ?>
						</div>
					<?php $this->section_close(); ?>
					<?php $this->section_open( 'dashicons-download', __( 'Documents & appels à l’action', 'iam-certifications' ) ); ?>
						<div class="iam-fields">
							<?php
							foreach ( array( '_iam_brochure_url', '_iam_dossier_url', '_iam_rdv_url', '_iam_cta_texte' ) as $k ) {
								$field( $k );
							}
							?>
						</div>
					<?php $this->section_close(); ?>
				</div>

				<!-- Étape 6 : Publication -->
				<div class="iam-step" data-step="5">
					<div class="iam-grid-2">
						<?php $this->section_open( 'dashicons-admin-tools', __( 'Outils & technologies', 'iam-certifications' ), __( 'Séparés par des virgules. Défilent en bandeau sur la page.', 'iam-certifications' ) ); ?>
							<textarea name="outils_csv" rows="4" class="widefat" placeholder="<?php esc_attr_e( 'ChatGPT, Make, Zapier, HubSpot…', 'iam-certifications' ); ?>"><?php echo esc_textarea( $outils_csv ); ?></textarea>
						<?php $this->section_close(); ?>

						<?php $this->section_open( 'dashicons-feedback', array( 'certificat' => __( 'Formulaire de candidature', 'iam-certifications' ), 'seminaire' => __( 'Formulaire d’inscription', 'iam-certifications' ) ), __( 'Formulaire IAM Leads affiché en bas de page.', 'iam-certifications' ) ); ?>
							<?php $this->render_form_select( $id ); ?>
							<label class="iam-switch">
								<input type="checkbox" name="_iam_featured" value="1" <?php checked( $meta( '_iam_featured' ), '1' ); ?>>
								<span class="iam-switch__track"></span>
								<span><?php esc_html_e( 'Mettre en avant dans le catalogue', 'iam-certifications' ); ?></span>
							</label>
						<?php $this->section_close(); ?>
					</div>

					<?php $this->section_open( 'dashicons-yes-alt', __( 'Vérification avant publication', 'iam-certifications' ), __( 'Les éléments marqués * sont requis pour publier ; les autres blocs vides sont simplement masqués sur la page.', 'iam-certifications' ) ); ?>
						<ul class="iam-checklist" data-iam-checklist>
							<li data-check="titre"><?php esc_html_e( 'Titre *', 'iam-certifications' ); ?></li>
							<li data-check="_iam_hero"><?php esc_html_e( 'Accroche *', 'iam-certifications' ); ?></li>
							<li data-check="domaine"><?php esc_html_e( 'Domaine / collection *', 'iam-certifications' ); ?></li>
							<li data-check="_iam_duree_texte"><?php esc_html_e( 'Durée *', 'iam-certifications' ); ?></li>
							<li data-check="_iam_objectifs"><?php esc_html_e( 'Objectifs *', 'iam-certifications' ); ?></li>
							<li data-check="_iam_public"><?php esc_html_e( 'Public *', 'iam-certifications' ); ?></li>
							<li data-check="_iam_evaluation"><?php esc_html_e( 'Évaluation *', 'iam-certifications' ); ?></li>
							<li data-check="modules" data-scope="certificat"><?php esc_html_e( 'Au moins un module *', 'iam-certifications' ); ?></li>
							<li data-check="jours" data-scope="seminaire"><?php esc_html_e( 'Programme jour par jour *', 'iam-certifications' ); ?></li>
							<li data-check="_iam_livrables" data-scope="seminaire"><?php esc_html_e( 'Livrables *', 'iam-certifications' ); ?></li>
							<li data-check="_thumbnail_id"><?php esc_html_e( 'Visuel principal (recommandé)', 'iam-certifications' ); ?></li>
							<li data-check="_iam_sessions" data-scope="seminaire"><?php esc_html_e( 'Sessions datées (sinon « sur demande »)', 'iam-certifications' ); ?></li>
						</ul>
					<?php $this->section_close(); ?>
				</div>

				<!-- Pied d'assistant -->
				<footer class="iam-wiz-foot">
					<button type="button" class="iam-adm-btn iam-adm-btn--ghost" data-iam-prev><span class="dashicons dashicons-arrow-left-alt2"></span> <?php esc_html_e( 'Précédent', 'iam-certifications' ); ?></button>
					<span class="iam-wiz-foot__hint" data-iam-autosave-hint><?php esc_html_e( 'Les champs marqués * sont requis pour publier. Le brouillon s’enregistre à tout moment.', 'iam-certifications' ); ?></span>
					<div class="iam-wiz-foot__right">
						<button type="submit" class="iam-adm-btn iam-adm-btn--ghost" name="statut" value="draft" data-iam-submit="draft"><span class="dashicons dashicons-media-default"></span> <?php echo 'publish' === $statut ? esc_html__( 'Repasser en brouillon', 'iam-certifications' ) : esc_html__( 'Brouillon', 'iam-certifications' ); ?></button>
						<button type="button" class="iam-adm-btn" data-iam-next><?php esc_html_e( 'Étape suivante', 'iam-certifications' ); ?> <span class="dashicons dashicons-arrow-right-alt2"></span></button>
						<button type="submit" class="iam-adm-btn iam-adm-btn--publish" name="statut" value="publish" data-iam-submit="publish"><span class="dashicons dashicons-megaphone"></span> <?php echo 'publish' === $statut ? esc_html__( 'Mettre à jour', 'iam-certifications' ) : esc_html__( 'Publier', 'iam-certifications' ); ?></button>
					</div>
				</footer>
			</form>
		</div>
		<?php
	}

	/**
	 * Sélecteur du formulaire IAM Leads associé.
	 *
	 * @param int $id ID.
	 * @return void
	 */
	private function render_form_select( $id ) {
		$current = $id ? get_post_meta( $id, '_iam_form_id', true ) : '';
		if ( ! class_exists( 'IAM_Leads_Form_Builder' ) ) {
			echo '<p class="iam-field__hint">' . esc_html__( 'IAM Leads inactif : le formulaire par défaut sera utilisé.', 'iam-certifications' ) . '</p>';
			printf( '<input type="hidden" name="_iam_form_id" value="%s">', esc_attr( $current ) );
			return;
		}
		echo '<select name="_iam_form_id" class="widefat">';
		printf( '<option value="">%s</option>', esc_html__( '— Formulaire par défaut —', 'iam-certifications' ) );
		foreach ( IAM_Leads_Form_Builder::all() as $form ) {
			printf( '<option value="%1$d" %2$s>%3$s</option>', (int) $form->id, selected( (string) $current, (string) $form->id, false ), esc_html( $form->nom ) );
		}
		echo '</select>';
	}

	/* =====================================================================
	 * Domaines et outils (taxonomies)
	 * ================================================================== */

	/**
	 * Écran « Domaines ».
	 *
	 * @return void
	 */
	public function render_domaines() {
		$this->render_terms(
			IAM_CERT_TAX_DOMAINE,
			array(
				'eyebrow' => __( 'Classement du catalogue', 'iam-certifications' ),
				'title'   => __( 'Domaines & collections', 'iam-certifications' ),
				'intro'   => __( 'Un domaine regroupe les certificats (ex. « Marketing Digital ») ; une collection regroupe les séminaires (ex. « Talents × IA »). Ils titrent les sections du catalogue et le fil d’Ariane des pages.', 'iam-certifications' ),
				'add'     => __( 'Nouveau domaine', 'iam-certifications' ),
				'icon'    => 'dashicons-category',
				'desc'    => true,
			)
		);
	}

	/**
	 * Écran « Outils ».
	 *
	 * @return void
	 */
	public function render_outils() {
		$this->render_terms(
			IAM_CERT_TAX_OUTIL,
			array(
				'eyebrow' => __( 'Badges technologiques', 'iam-certifications' ),
				'title'   => __( 'Outils & technologies', 'iam-certifications' ),
				'intro'   => __( 'Les outils s’affichent en badges sur les cartes et défilent en bandeau sur les pages. On peut aussi les saisir directement dans une formation.', 'iam-certifications' ),
				'add'     => __( 'Nouvel outil', 'iam-certifications' ),
				'icon'    => 'dashicons-admin-tools',
				'desc'    => false,
			)
		);
	}

	/**
	 * Écran de gestion d'une taxonomie : ajout, renommage, suppression.
	 *
	 * @param string $taxonomy Taxonomie.
	 * @param array  $ui       Textes et options de l'écran.
	 * @return void
	 */
	private function render_terms( $taxonomy, $ui ) {
		if ( ! current_user_can( self::CAP ) ) {
			return;
		}
		$this->notice();

		$terms  = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => 'name',
			)
		);
		$terms  = is_wp_error( $terms ) ? array() : $terms;
		$action = admin_url( 'admin-post.php' );
		?>
		<div class="wrap iam-cert-admin">
			<header class="iam-adm-head">
				<div>
					<p class="iam-adm-eyebrow"><?php echo esc_html( $ui['eyebrow'] ); ?></p>
					<h1 class="iam-adm-title"><?php echo esc_html( $ui['title'] ); ?></h1>
					<p class="iam-adm-intro"><?php echo esc_html( $ui['intro'] ); ?></p>
				</div>
			</header>
			<hr class="wp-header-end">

			<div class="iam-terms">
				<form method="post" action="<?php echo esc_url( $action ); ?>" class="iam-sec iam-terms__add">
					<header class="iam-sec__head">
						<span class="iam-sec__icon dashicons dashicons-plus-alt2" aria-hidden="true"></span>
						<div>
							<h2 class="iam-sec__title"><?php echo esc_html( $ui['add'] ); ?></h2>
							<p class="iam-sec__desc"><?php esc_html_e( 'Le nom apparaît tel quel sur le site.', 'iam-certifications' ); ?></p>
						</div>
					</header>
					<div class="iam-sec__body">
						<input type="hidden" name="action" value="iam_cert_term_save">
						<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>">
						<?php wp_nonce_field( 'iam_cert_term_' . $taxonomy ); ?>
						<div class="iam-field">
							<label class="iam-field__label" for="iam_term_name"><?php esc_html_e( 'Nom', 'iam-certifications' ); ?> <span class="iam-req">*</span></label>
							<input type="text" id="iam_term_name" name="name" class="widefat" required>
						</div>
						<?php if ( $ui['desc'] ) : ?>
							<div class="iam-field">
								<label class="iam-field__label" for="iam_term_desc"><?php esc_html_e( 'Description', 'iam-certifications' ); ?></label>
								<textarea id="iam_term_desc" name="description" rows="3" class="widefat"></textarea>
								<p class="iam-field__hint"><?php esc_html_e( 'Optionnelle, usage interne.', 'iam-certifications' ); ?></p>
							</div>
						<?php endif; ?>
						<button type="submit" class="iam-adm-btn iam-adm-btn--block"><span class="dashicons dashicons-plus-alt2"></span> <?php esc_html_e( 'Ajouter', 'iam-certifications' ); ?></button>
					</div>
				</form>

				<div class="iam-terms__list">
					<?php if ( ! $terms ) : ?>
						<div class="iam-adm-empty">
							<span class="dashicons <?php echo esc_attr( $ui['icon'] ); ?>"></span>
							<p><?php esc_html_e( 'Rien pour le moment.', 'iam-certifications' ); ?></p>
						</div>
					<?php endif; ?>
					<?php foreach ( $terms as $term ) : ?>
						<?php
						$del_url = wp_nonce_url(
							add_query_arg(
								array(
									'action'   => 'iam_cert_term_delete',
									'taxonomy' => $taxonomy,
									'term_id'  => $term->term_id,
								),
								$action
							),
							'iam_cert_term_delete_' . $term->term_id
						);
						?>
						<details class="iam-term">
							<summary class="iam-term__row">
								<span class="iam-term__icon dashicons <?php echo esc_attr( $ui['icon'] ); ?>" aria-hidden="true"></span>
								<span class="iam-term__main">
									<strong class="iam-term__name"><?php echo esc_html( $term->name ); ?></strong>
									<span class="iam-term__slug"><?php echo esc_html( $term->slug ); ?></span>
								</span>
								<span class="iam-term__count"><?php echo esc_html( sprintf( _n( '%d formation', '%d formations', $term->count, 'iam-certifications' ), $term->count ) ); ?></span>
								<span class="iam-adm-btn iam-adm-btn--ghost iam-adm-btn--sm"><?php esc_html_e( 'Modifier', 'iam-certifications' ); ?></span>
							</summary>
							<form method="post" action="<?php echo esc_url( $action ); ?>" class="iam-term__edit">
								<input type="hidden" name="action" value="iam_cert_term_save">
								<input type="hidden" name="taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>">
								<input type="hidden" name="term_id" value="<?php echo (int) $term->term_id; ?>">
								<?php wp_nonce_field( 'iam_cert_term_' . $taxonomy ); ?>
								<div class="iam-fields">
									<div class="iam-field">
										<label class="iam-field__label"><?php esc_html_e( 'Nom', 'iam-certifications' ); ?> <span class="iam-req">*</span></label>
										<input type="text" name="name" class="widefat" required value="<?php echo esc_attr( $term->name ); ?>">
									</div>
									<div class="iam-field">
										<label class="iam-field__label"><?php esc_html_e( 'Identifiant d’URL', 'iam-certifications' ); ?></label>
										<input type="text" name="slug" class="widefat" value="<?php echo esc_attr( $term->slug ); ?>">
									</div>
									<?php if ( $ui['desc'] ) : ?>
										<div class="iam-field iam-field--textarea">
											<label class="iam-field__label"><?php esc_html_e( 'Description', 'iam-certifications' ); ?></label>
											<textarea name="description" rows="2" class="widefat"><?php echo esc_textarea( $term->description ); ?></textarea>
										</div>
									<?php endif; ?>
								</div>
								<div class="iam-term__actions">
									<a href="<?php echo esc_url( $del_url ); ?>" class="iam-adm-icon iam-adm-icon--danger iam-term-del" title="<?php esc_attr_e( 'Supprimer', 'iam-certifications' ); ?>" data-count="<?php echo (int) $term->count; ?>"><span class="dashicons dashicons-trash"></span></a>
									<button type="submit" class="iam-adm-btn iam-adm-btn--sm"><?php esc_html_e( 'Enregistrer', 'iam-certifications' ); ?></button>
								</div>
							</form>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Ajout ou mise à jour d'un terme.
	 *
	 * @return void
	 */
	public function handle_term_save() {
		$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_key( $_POST['taxonomy'] ) : '';
		if ( ! in_array( $taxonomy, array( IAM_CERT_TAX_DOMAINE, IAM_CERT_TAX_OUTIL ), true ) || ! current_user_can( self::CAP ) || ! check_admin_referer( 'iam_cert_term_' . $taxonomy ) ) {
			wp_die( esc_html__( 'Action non autorisée.', 'iam-certifications' ) );
		}

		$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$term_id = isset( $_POST['term_id'] ) ? absint( $_POST['term_id'] ) : 0;
		$args    = array();
		if ( isset( $_POST['description'] ) ) {
			$args['description'] = sanitize_textarea_field( wp_unslash( $_POST['description'] ) );
		}
		if ( ! empty( $_POST['slug'] ) ) {
			$args['slug'] = sanitize_title( wp_unslash( $_POST['slug'] ) );
		}

		$res = new WP_Error();
		if ( '' !== $name ) {
			$res = $term_id
				? wp_update_term( $term_id, $taxonomy, array_merge( $args, array( 'name' => $name ) ) )
				: wp_insert_term( $name, $taxonomy, $args );
		}

		$this->redirect_terms( $taxonomy, is_wp_error( $res ) ? 'term_err' : 'term' );
	}

	/**
	 * Suppression d'un terme (les formations ne sont pas supprimées).
	 *
	 * @return void
	 */
	public function handle_term_delete() {
		$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_key( $_GET['taxonomy'] ) : '';
		$term_id  = isset( $_GET['term_id'] ) ? absint( $_GET['term_id'] ) : 0;
		if ( ! $term_id || ! in_array( $taxonomy, array( IAM_CERT_TAX_DOMAINE, IAM_CERT_TAX_OUTIL ), true ) || ! current_user_can( self::CAP ) || ! check_admin_referer( 'iam_cert_term_delete_' . $term_id ) ) {
			wp_die( esc_html__( 'Action non autorisée.', 'iam-certifications' ) );
		}
		wp_delete_term( $term_id, $taxonomy );
		$this->redirect_terms( $taxonomy, 'term_del' );
	}

	/**
	 * Redirige vers l'écran de la taxonomie.
	 *
	 * @param string $taxonomy Taxonomie.
	 * @param string $code     Code de notice.
	 * @return void
	 */
	private function redirect_terms( $taxonomy, $code ) {
		$page = IAM_CERT_TAX_DOMAINE === $taxonomy ? self::PAGE_DOMAINES : self::PAGE_OUTILS;
		wp_safe_redirect( admin_url( 'admin.php?page=' . $page . '&iam_notice=' . $code ) );
		exit;
	}

	/**
	 * Éditeur natif d'une formation → assistant du plugin.
	 *
	 * @return void
	 */
	public function redirect_native_editor() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- simple redirection de navigation.
		$post_id   = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
		$post_type = isset( $_GET['post_type'] ) ? sanitize_key( $_GET['post_type'] ) : '';
		$action    = isset( $_GET['action'] ) ? sanitize_key( $_GET['action'] ) : '';
		// phpcs:enable
		if ( $post_id ) {
			$post_type = get_post_type( $post_id );
		}
		if ( IAM_CERT_CPT !== $post_type || ( $post_id && 'edit' !== $action ) ) {
			return;
		}
		$url = $post_id
			? admin_url( 'admin.php?page=' . self::PAGE_EDIT . '&id=' . $post_id )
			: admin_url( 'admin.php?page=' . ( 'load-edit.php' === current_action() ? self::PAGE_LIST : self::PAGE_EDIT ) );
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Écrans natifs des taxonomies → écrans du plugin.
	 *
	 * @return void
	 */
	public function redirect_native_terms() {
		$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_key( $_GET['taxonomy'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( IAM_CERT_TAX_DOMAINE === $taxonomy ) {
			wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE_DOMAINES ) );
			exit;
		}
		if ( IAM_CERT_TAX_OUTIL === $taxonomy ) {
			wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE_OUTILS ) );
			exit;
		}
	}

	/* =====================================================================
	 * Traitement
	 * ================================================================== */

	/**
	 * Enregistre la certification (création ou mise à jour).
	 *
	 * @return void
	 */
	public function handle_save() {
		if ( ! current_user_can( self::CAP ) || ! isset( $_POST['iam_cert_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['iam_cert_nonce'] ), 'iam_cert_save' ) ) {
			wp_die( esc_html__( 'Action non autorisée.', 'iam-certifications' ) );
		}

		$id     = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		$titre  = isset( $_POST['titre'] ) ? sanitize_text_field( wp_unslash( $_POST['titre'] ) ) : '';
		$statut = ( isset( $_POST['statut'] ) && 'draft' === $_POST['statut'] ) ? 'draft' : 'publish';

		if ( '' === $titre ) {
			$this->redirect_edit( $id, 'error' );
		}

		// Publication : les champs obligatoires doivent être remplis, sinon on
		// enregistre en brouillon et on liste ce qui manque.
		$manquants   = 'publish' === $statut ? $this->missing_required() : array();
		$deja_publie = $id && 'publish' === get_post_status( $id );
		if ( $manquants && ! $deja_publie ) {
			// Une fiche déjà en ligne n'est jamais dépubliée : on avertit seulement.
			$statut = 'draft';
		}

		$postarr = array(
			'post_type'   => IAM_CERT_CPT,
			'post_title'  => $titre,
			'post_status' => $statut,
		);

		if ( $id ) {
			$postarr['ID'] = $id;
			wp_update_post( $postarr );
		} else {
			$postarr['post_excerpt'] = isset( $_POST['_iam_hero'] ) ? sanitize_textarea_field( wp_unslash( $_POST['_iam_hero'] ) ) : '';
			$id                      = wp_insert_post( $postarr );
		}

		if ( ! $id || is_wp_error( $id ) ) {
			$this->redirect_edit( 0, 'error' );
		}

		// Champs simples.
		foreach ( IAM_Cert_Metaboxes::fields() as $key => $args ) {
			$value = IAM_Cert_Metaboxes::sanitize( isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '', $args ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			update_post_meta( $id, $key, $value );
		}

		update_post_meta( $id, '_iam_featured', isset( $_POST['_iam_featured'] ) ? '1' : '' );

		// Hero -> aussi dans l'extrait (utilisé par les cartes).
		if ( isset( $_POST['_iam_hero'] ) ) {
			wp_update_post(
				array(
					'ID'           => $id,
					'post_excerpt' => sanitize_textarea_field( wp_unslash( $_POST['_iam_hero'] ) ),
				)
			);
		}

		// Modules.
		$modules = array();
		if ( isset( $_POST['iam_modules'] ) && is_array( $_POST['iam_modules'] ) ) {
			foreach ( wp_unslash( $_POST['iam_modules'] ) as $raw ) {
				$m_titre = isset( $raw['titre'] ) ? sanitize_text_field( $raw['titre'] ) : '';
				$brut    = isset( $raw['lecons'] ) ? (string) $raw['lecons'] : '';
				$lecons  = array_values( array_filter( array_map( 'sanitize_text_field', array_map( 'trim', preg_split( '/\r\n|\r|\n/', $brut ) ) ) ) );
				if ( '' === $m_titre && ! $lecons ) {
					continue;
				}
				$modules[] = array(
					'titre'  => $m_titre,
					'lecons' => $lecons,
				);
			}
		}
		update_post_meta( $id, '_iam_modules', $modules );

		// Programme jour par jour (séminaires).
		update_post_meta( $id, '_iam_jours', iam_cert_sanitize_jours( isset( $_POST['iam_jours'] ) ? wp_unslash( $_POST['iam_jours'] ) : array() ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		// Formulaire associé.
		$form_id = isset( $_POST['_iam_form_id'] ) ? absint( wp_unslash( $_POST['_iam_form_id'] ) ) : 0;
		update_post_meta( $id, '_iam_form_id', $form_id ? (string) $form_id : '' );

		// Domaine (sélection ou création).
		$domaine_terms = array();
		$domaine_new   = isset( $_POST['domaine_new'] ) ? sanitize_text_field( wp_unslash( $_POST['domaine_new'] ) ) : '';
		if ( '' !== $domaine_new ) {
			$t = term_exists( $domaine_new, IAM_CERT_TAX_DOMAINE );
			if ( ! $t ) {
				$t = wp_insert_term( $domaine_new, IAM_CERT_TAX_DOMAINE );
			}
			if ( ! is_wp_error( $t ) ) {
				$domaine_terms[] = (int) $t['term_id'];
			}
		} elseif ( ! empty( $_POST['domaine_id'] ) ) {
			$domaine_terms[] = absint( $_POST['domaine_id'] );
		}
		wp_set_object_terms( $id, $domaine_terms, IAM_CERT_TAX_DOMAINE, false );

		// Outils (CSV).
		$outils_csv = isset( $_POST['outils_csv'] ) ? sanitize_textarea_field( wp_unslash( $_POST['outils_csv'] ) ) : '';
		$outils     = array_values( array_filter( array_map( 'trim', explode( ',', $outils_csv ) ) ) );
		wp_set_object_terms( $id, $outils, IAM_CERT_TAX_OUTIL, false );

		// Image mise en avant.
		$thumb_id = isset( $_POST['_thumbnail_id'] ) ? absint( $_POST['_thumbnail_id'] ) : 0;
		if ( $thumb_id ) {
			set_post_thumbnail( $id, $thumb_id );
		} else {
			delete_post_thumbnail( $id );
		}

		if ( $manquants ) {
			set_transient( 'iam_cert_missing_' . get_current_user_id(), $manquants, 5 * MINUTE_IN_SECONDS );
			$this->redirect_edit( $id, $deja_publie ? 'incomplete_pub' : 'incomplete' );
		}

		$this->redirect_edit( $id, 'saved' );
	}

	/**
	 * Champs obligatoires manquants dans la requête de sauvegarde.
	 *
	 * @return string[] Libellés des champs manquants.
	 */
	private function missing_required() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce vérifié par handle_save().
		$post = wp_unslash( $_POST );
		// phpcs:enable
		$type = ( isset( $post['_iam_type'] ) && 'seminaire' === $post['_iam_type'] ) ? 'seminaire' : 'certificat';
		$out  = array();

		foreach ( IAM_Cert_Metaboxes::fields() as $key => $args ) {
			$scope = isset( $args['scope'] ) ? $args['scope'] : 'both';
			if ( empty( $args['required'] ) || ( 'both' !== $scope && $scope !== $type ) ) {
				continue;
			}
			if ( ! isset( $post[ $key ] ) || '' === trim( (string) $post[ $key ] ) ) {
				$out[] = iam_cert_field_label( $args, $type );
			}
		}

		if ( empty( $post['domaine_id'] ) && '' === trim( isset( $post['domaine_new'] ) ? (string) $post['domaine_new'] : '' ) ) {
			$out[] = 'seminaire' === $type ? __( 'Collection', 'iam-certifications' ) : __( 'Domaine', 'iam-certifications' );
		}

		if ( 'certificat' === $type ) {
			$ok = false;
			foreach ( isset( $post['iam_modules'] ) && is_array( $post['iam_modules'] ) ? $post['iam_modules'] : array() as $m ) {
				$ok = $ok || ( isset( $m['titre'] ) && '' !== trim( $m['titre'] ) );
			}
			if ( ! $ok ) {
				$out[] = __( 'Au moins un module', 'iam-certifications' );
			}
		} elseif ( ! iam_cert_sanitize_jours( isset( $post['iam_jours'] ) ? $post['iam_jours'] : array() ) ) {
			$out[] = __( 'Programme jour par jour', 'iam-certifications' );
		}

		return $out;
	}

	/**
	 * Supprime (met à la corbeille) une certification.
	 *
	 * @return void
	 */
	public function handle_delete() {
		$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		if ( ! $id || ! current_user_can( 'delete_post', $id ) || ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'iam_cert_delete_' . $id ) ) {
			wp_die( esc_html__( 'Action non autorisée.', 'iam-certifications' ) );
		}
		wp_trash_post( $id );
		wp_safe_redirect( admin_url( 'admin.php?page=' . self::PAGE_LIST . '&iam_notice=deleted' ) );
		exit;
	}

	/**
	 * Redirige vers le formulaire d'édition avec un code de notice.
	 *
	 * @param int    $id   ID.
	 * @param string $code Code de notice.
	 * @return void
	 */
	private function redirect_edit( $id, $code ) {
		$url = $id
			? admin_url( 'admin.php?page=' . self::PAGE_EDIT . '&id=' . $id . '&iam_notice=' . $code )
			: admin_url( 'admin.php?page=' . self::PAGE_EDIT . '&iam_notice=' . $code );
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Affiche les notices.
	 *
	 * @return void
	 */
	private function notice() {
		$code = isset( $_GET['iam_notice'] ) ? sanitize_key( $_GET['iam_notice'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! $code ) {
			return;
		}
		$map = array(
			'saved'   => array( 'success', __( 'Formation enregistrée.', 'iam-certifications' ) ),
			'deleted' => array( 'success', __( 'Formation mise à la corbeille.', 'iam-certifications' ) ),
			'error'   => array( 'error', __( 'Le titre est obligatoire.', 'iam-certifications' ) ),
			'term'    => array( 'success', __( 'Enregistré.', 'iam-certifications' ) ),
			'term_del' => array( 'success', __( 'Supprimé.', 'iam-certifications' ) ),
			'term_err' => array( 'error', __( 'Nom manquant ou déjà utilisé.', 'iam-certifications' ) ),
		);
		if ( 'incomplete' === $code || 'incomplete_pub' === $code ) {
			$manquants = (array) get_transient( 'iam_cert_missing_' . get_current_user_id() );
			delete_transient( 'iam_cert_missing_' . get_current_user_id() );
			printf(
				'<div class="notice notice-warning is-dismissible"><p><strong>%1$s</strong> %2$s</p></div>',
				'incomplete' === $code
					? esc_html__( 'Enregistrée en brouillon : il manque des informations pour publier.', 'iam-certifications' )
					: esc_html__( 'Mise à jour enregistrée (la page reste en ligne), mais il manque :', 'iam-certifications' ),
				esc_html( implode( ', ', array_filter( $manquants ) ) )
			);
			return;
		}
		if ( isset( $map[ $code ] ) ) {
			printf( '<div class="notice notice-%1$s is-dismissible"><p>%2$s</p></div>', esc_attr( $map[ $code ][0] ), esc_html( $map[ $code ][1] ) );
		}
	}
}
