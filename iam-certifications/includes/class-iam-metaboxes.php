<?php
/**
 * Metabox d'édition d'une certification : infos générales, programme (modules
 * & leçons), et liaison au formulaire IAM Leads.
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gère la metabox d'édition.
 */
class IAM_Cert_Metaboxes {

	const NONCE = 'iam_cert_meta_nonce';

	/**
	 * Enregistre les hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_' . IAM_CERT_CPT, array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Champs simples : clé meta => réglages.
	 *
	 * - step  : étape de l'assistant d'édition (1 à 5).
	 * - scope : « both », « certificat » ou « seminaire » (champ masqué pour l'autre type).
	 * - label / desc : libellés ; « labels » / « descs » les surchargent par type.
	 * - required : obligatoire pour publier (dans la portée du champ).
	 *
	 * @return array
	 */
	public static function fields() {
		return array(
			/* Type de formation (cartes de choix dans l'assistant, liste déroulante en metabox). */
			'_iam_type'             => array(
				'label'   => __( 'Type de formation', 'iam-certifications' ),
				'type'    => 'select',
				'options' => iam_cert_types(),
				'step'    => 0,
			),

			/* Étape 1 : Identité */
			'_iam_hero'             => array(
				'required'    => true,
				'label' => __( 'Accroche', 'iam-certifications' ),
				'type'  => 'textarea',
				'step'  => 1,
				'desc'  => __( 'Une ou deux phrases sous le titre, en haut de la page. Reprise sur la carte du catalogue.', 'iam-certifications' ),
				'descs' => array(
					'seminaire' => __( 'La promesse du séminaire en une phrase (ex. « Diviser par deux le temps de recrutement… »).', 'iam-certifications' ),
				),
			),

			/* Étape 2 : Format */
			'_iam_duree_texte'      => array(
				'required'    => true,
				'label'       => __( 'Durée', 'iam-certifications' ),
				'type'        => 'text',
				'step'        => 2,
				'width'       => 'half',
				'placeholder' => array(
					'certificat' => __( '10 semaines', 'iam-certifications' ),
					'seminaire'  => __( '3 jours', 'iam-certifications' ),
				),
			),
			'_iam_duree_heures'     => array(
				'required'    => true,
				'label'       => __( 'Volume horaire (heures)', 'iam-certifications' ),
				'type'        => 'number',
				'step'        => 2,
				'width'       => 'half',
				'placeholder' => array(
					'certificat' => '50',
					'seminaire'  => '24',
				),
			),
			'_iam_format'           => array(
				'required'    => true,
				'label'       => __( 'Format', 'iam-certifications' ),
				'type'        => 'text',
				'step'        => 2,
				'width'       => 'half',
				'placeholder' => array(
					'certificat' => __( '100 % en ligne, coaching', 'iam-certifications' ),
					'seminaire'  => __( 'Présentiel Dakar ou intra', 'iam-certifications' ),
				),
			),
			'_iam_niveau'           => array(
				'required'    => true,
				'label'       => __( 'Niveau', 'iam-certifications' ),
				'type'        => 'text',
				'step'        => 2,
				'width'       => 'half',
				'scope'       => 'certificat',
				'placeholder' => __( 'débutant, intermédiaire, avancé', 'iam-certifications' ),
			),
			'_iam_rythme'           => array(
				'label'       => __( 'Rythme', 'iam-certifications' ),
				'type'        => 'text',
				'step'        => 2,
				'scope'       => 'certificat',
				'placeholder' => __( '5 h par semaine, à votre rythme', 'iam-certifications' ),
			),
			'_iam_participants_max' => array(
				'required'    => true,
				'label'       => __( 'Participants maximum', 'iam-certifications' ),
				'type'        => 'number',
				'step'        => 2,
				'width'       => 'half',
				'scope'       => 'seminaire',
				'placeholder' => '16',
			),
			'_iam_pratique_pct'     => array(
				'required'    => true,
				'label'       => __( 'Part de pratique (%)', 'iam-certifications' ),
				'type'        => 'number',
				'step'        => 2,
				'width'       => 'half',
				'scope'       => 'seminaire',
				'placeholder' => '70',
				'desc'        => __( 'Affiché en jauge animée sur la page.', 'iam-certifications' ),
			),
			'_iam_horaires'         => array(
				'required'    => true,
				'label'       => __( 'Horaires', 'iam-certifications' ),
				'type'        => 'text',
				'step'        => 2,
				'width'       => 'half',
				'scope'       => 'seminaire',
				'placeholder' => __( 'Matin 8h30 – 12h30 · Après-midi 14h – 17h30', 'iam-certifications' ),
			),
			'_iam_elearning'        => array(
				'label'       => __( 'Suivi après le séminaire', 'iam-certifications' ),
				'type'        => 'text',
				'step'        => 2,
				'width'       => 'half',
				'scope'       => 'seminaire',
				'placeholder' => __( 'Accès 3 mois à la plateforme eLearning (29 contenus)', 'iam-certifications' ),
			),
			'_iam_sessions'         => array(
				'label'       => __( 'Prochaines sessions', 'iam-certifications' ),
				'type'        => 'list',
				'step'        => 2,
				'scope'       => 'seminaire',
				'placeholder' => __( "12-14 novembre 2026 | Dakar, campus IAM\n26-28 novembre 2026 | Dakar", 'iam-certifications' ),
				'desc'        => __( 'Une session par ligne : « dates | lieu ». Vide = « sessions intra sur demande ».', 'iam-certifications' ),
			),
			'_iam_infos_pratiques'  => array(
				'label'       => __( 'Informations pratiques', 'iam-certifications' ),
				'type'        => 'list',
				'step'        => 2,
				'scope'       => 'seminaire',
				'placeholder' => __( "Venir avec son ordinateur portable\nPauses café et déjeuner incluses\nFormation en français", 'iam-certifications' ),
				'desc'        => __( 'Une information par ligne.', 'iam-certifications' ),
			),

			/* Étape 3 : Contenu & public */
			'_iam_objectifs'        => array(
				'required'    => true,
				'label'  => __( 'Objectifs', 'iam-certifications' ),
				'labels' => array( 'seminaire' => __( 'Objectifs pédagogiques', 'iam-certifications' ) ),
				'type'   => 'list',
				'step'   => 3,
				'desc'   => __( 'Un objectif par ligne (idéalement 3 à 6). Affichés en cartes numérotées.', 'iam-certifications' ),
			),
			'_iam_public'           => array(
				'required'    => true,
				'label'  => __( 'Public concerné', 'iam-certifications' ),
				'labels' => array( 'seminaire' => __( 'Public visé', 'iam-certifications' ) ),
				'type'   => 'textarea',
				'step'   => 3,
				'desc'   => __( 'Les profils, séparés par des virgules.', 'iam-certifications' ),
			),
			'_iam_prerequis'        => array(
				'required'    => true,
				'label' => __( 'Prérequis / conditions d’admission', 'iam-certifications' ),
				'type'  => 'list',
				'step'  => 3,
				'desc'  => __( 'Une condition par ligne.', 'iam-certifications' ),
			),
			'_iam_debouches'        => array(
				'label' => __( 'Débouchés', 'iam-certifications' ),
				'type'  => 'list',
				'step'  => 3,
				'scope' => 'certificat',
				'desc'  => __( 'Un métier par ligne. Laisser vide pour masquer.', 'iam-certifications' ),
			),
			'_iam_projet_final'     => array(
				'label' => __( 'Projet final', 'iam-certifications' ),
				'type'  => 'textarea',
				'step'  => 3,
				'scope' => 'certificat',
				'desc'  => __( 'Le livrable soutenu devant jury. Mis en avant à la fin du parcours.', 'iam-certifications' ),
			),
			'_iam_livrables'        => array(
				'required'    => true,
				'label' => __( 'Livrables', 'iam-certifications' ),
				'type'  => 'list',
				'step'  => 3,
				'scope' => 'seminaire',
				'desc'  => __( 'Ce que chaque participant emporte, un par ligne (ex. « Feuille de route personnalisée »).', 'iam-certifications' ),
			),
			'_iam_evaluation'       => array(
				'required'    => true,
				'label'  => __( 'Évaluation & certification', 'iam-certifications' ),
				'labels' => array( 'seminaire' => __( 'Évaluation & attestation', 'iam-certifications' ) ),
				'type'   => 'textarea',
				'step'   => 3,
				'desc'   => __( 'Modalités d’évaluation et de délivrance.', 'iam-certifications' ),
			),
			'_iam_cout'             => array(
				'label' => __( 'Coût et financement', 'iam-certifications' ),
				'type'  => 'textarea',
				'step'  => 3,
				'desc'  => __( 'Optionnel. Laisser vide pour masquer le bloc.', 'iam-certifications' ),
			),

			/* Étape 5 : Médias & liens */
			'_iam_video_url'        => array(
				'label' => __( 'Vidéo de présentation', 'iam-certifications' ),
				'type'  => 'url',
				'step'  => 5,
				'desc'  => __( 'Lien YouTube, Vimeo ou fichier .mp4. Affiche le bouton « Voir la vidéo ».', 'iam-certifications' ),
			),
			'_iam_image2_url'       => array(
				'label' => __( 'Image secondaire', 'iam-certifications' ),
				'type'  => 'media',
				'media' => 'image',
				'step'  => 5,
				'desc'  => __( 'Affichée dans la section « Pour qui ». Par défaut : l’image principale.', 'iam-certifications' ),
			),
			'_iam_brochure_url'     => array(
				'label' => __( 'Brochure (PDF)', 'iam-certifications' ),
				'type'  => 'media',
				'media' => 'application/pdf',
				'step'  => 5,
			),
			'_iam_dossier_url'      => array(
				'label'  => __( 'Dossier de candidature', 'iam-certifications' ),
				'labels' => array( 'seminaire' => __( 'Bulletin d’inscription', 'iam-certifications' ) ),
				'type'   => 'media',
				'media'  => '',
				'step'   => 5,
			),
			'_iam_rdv_url'          => array(
				'label' => __( 'Lien de prise de rendez-vous', 'iam-certifications' ),
				'type'  => 'url',
				'step'  => 5,
				'desc'  => __( 'Calendly, Google Agenda ou WhatsApp. Affiche « Prendre rendez-vous ».', 'iam-certifications' ),
			),
			'_iam_cta_texte'        => array(
				'label'       => __( 'Texte du bouton principal', 'iam-certifications' ),
				'type'        => 'text',
				'step'        => 5,
				'placeholder' => array(
					'certificat' => __( 'Candidater', 'iam-certifications' ),
					'seminaire'  => __( 'Réserver ma place', 'iam-certifications' ),
				),
			),
		);
	}

	/**
	 * Nettoie une valeur brute selon le type du champ.
	 *
	 * @param mixed $raw  Valeur brute (déjà « unslashée »).
	 * @param array $args Réglages du champ.
	 * @return string
	 */
	public static function sanitize( $raw, $args ) {
		$raw = is_string( $raw ) ? $raw : '';
		switch ( $args['type'] ) {
			case 'textarea':
			case 'list':
				return sanitize_textarea_field( $raw );
			case 'number':
				return '' === trim( $raw ) ? '' : (string) absint( $raw );
			case 'url':
			case 'media':
				return esc_url_raw( trim( $raw ) );
			case 'select':
				return isset( $args['options'][ $raw ] ) ? $raw : (string) key( $args['options'] );
			default:
				return sanitize_text_field( $raw );
		}
	}

	/**
	 * Déclare les metaboxes.
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'iam_cert_infos',
			__( 'Infos générales', 'iam-certifications' ),
			array( $this, 'render_infos' ),
			IAM_CERT_CPT,
			'normal',
			'high'
		);

		add_meta_box(
			'iam_cert_programme',
			__( 'Programme (modules & leçons)', 'iam-certifications' ),
			array( $this, 'render_programme' ),
			IAM_CERT_CPT,
			'normal',
			'default'
		);

		add_meta_box(
			'iam_cert_form',
			__( 'Formulaire de candidature', 'iam-certifications' ),
			array( $this, 'render_form_box' ),
			IAM_CERT_CPT,
			'side',
			'default'
		);
	}

	/**
	 * Metabox « Infos générales ».
	 *
	 * @param WP_Post $post Post.
	 * @return void
	 */
	public function render_infos( $post ) {
		wp_nonce_field( 'iam_cert_save_meta', self::NONCE );

		echo '<div class="iam-cert-metabox">';

		foreach ( self::fields() as $key => $args ) {
			iam_cert_render_field_row( $key, $args, get_post_meta( $post->ID, $key, true ) );
		}

		$featured = get_post_meta( $post->ID, '_iam_featured', true );
		echo '<p class="iam-cert-row">';
		printf(
			'<label><input type="checkbox" name="_iam_featured" value="1" %s /> <strong>%s</strong></label>',
			checked( $featured, '1', false ),
			esc_html__( 'Mettre en avant cette certification', 'iam-certifications' )
		);
		echo '</p>';

		echo '</div>';
	}

	/**
	 * Metabox « Programme » : répéteur de modules, chaque module ayant une liste
	 * de leçons (une par ligne).
	 *
	 * @param WP_Post $post Post.
	 * @return void
	 */
	public function render_programme( $post ) {
		$modules = iam_cert_get_modules( $post->ID );

		echo '<div class="iam-modules" id="iam-modules">';
		echo '<p class="description">' . esc_html__( 'Un module = un titre + ses leçons (une leçon par ligne). Les compteurs de la fiche se calculent automatiquement.', 'iam-certifications' ) . '</p>';

		echo '<div class="iam-modules__list">';
		if ( $modules ) {
			foreach ( $modules as $i => $module ) {
				$this->render_module_row( $i, $module['titre'], $module['lecons'] );
			}
		} else {
			$this->render_module_row( 0, '', array() );
		}
		echo '</div>';

		printf(
			'<p><button type="button" class="button iam-modules__add">%s</button></p>',
			esc_html__( '+ Ajouter un module', 'iam-certifications' )
		);

		// Gabarit pour les nouveaux modules (utilisé par le JS).
		echo '<script type="text/html" id="iam-module-template">';
		$this->render_module_row( '__index__', '', array() );
		echo '</script>';

		echo '</div>';
	}

	/**
	 * Rend une ligne de module.
	 *
	 * @param int|string $index  Index du module.
	 * @param string     $titre  Titre.
	 * @param array      $lecons Leçons.
	 * @return void
	 */
	private function render_module_row( $index, $titre, $lecons ) {
		$lecons_txt = is_array( $lecons ) ? implode( "\n", $lecons ) : '';
		?>
		<div class="iam-module">
			<div class="iam-module__head">
				<span class="iam-module__drag" title="<?php esc_attr_e( 'Glisser pour réordonner', 'iam-certifications' ); ?>">⋮⋮</span>
				<input type="text" class="iam-module__titre widefat"
					name="iam_modules[<?php echo esc_attr( $index ); ?>][titre]"
					value="<?php echo esc_attr( $titre ); ?>"
					placeholder="<?php esc_attr_e( 'Titre du module (ex. « Module 1 — Fondamentaux »)', 'iam-certifications' ); ?>" />
				<button type="button" class="button-link iam-module__remove" title="<?php esc_attr_e( 'Supprimer', 'iam-certifications' ); ?>">&times;</button>
			</div>
			<textarea class="iam-module__lecons widefat" rows="3"
				name="iam_modules[<?php echo esc_attr( $index ); ?>][lecons]"
				placeholder="<?php esc_attr_e( 'Une leçon par ligne', 'iam-certifications' ); ?>"><?php echo esc_textarea( $lecons_txt ); ?></textarea>
		</div>
		<?php
	}

	/**
	 * Metabox latérale : sélecteur du formulaire IAM Leads.
	 *
	 * @param WP_Post $post Post.
	 * @return void
	 */
	public function render_form_box( $post ) {
		$current = get_post_meta( $post->ID, '_iam_form_id', true );

		if ( ! class_exists( 'IAM_Leads_Form_Builder' ) ) {
			echo '<p class="description">';
			esc_html_e( 'IAM Leads inactif : le formulaire « certifications » par défaut sera utilisé une fois le plugin activé.', 'iam-certifications' );
			echo '</p>';
			printf( '<input type="hidden" name="_iam_form_id" value="%s" />', esc_attr( $current ) );
			return;
		}

		$forms = IAM_Leads_Form_Builder::all();
		echo '<select id="_iam_form_id" name="_iam_form_id" class="widefat">';
		printf( '<option value="">%s</option>', esc_html__( '— Formulaire par défaut —', 'iam-certifications' ) );
		foreach ( $forms as $form ) {
			printf(
				'<option value="%1$d" %2$s>%3$s</option>',
				(int) $form->id,
				selected( (string) $current, (string) $form->id, false ),
				esc_html( $form->nom )
			);
		}
		echo '</select>';
		echo '<p class="description">' . esc_html__( 'Formulaire embarqué sur la page de détail.', 'iam-certifications' ) . '</p>';
	}

	/**
	 * Enregistre les métadonnées.
	 *
	 * @param int     $post_id ID.
	 * @param WP_Post $post    Post.
	 * @return void
	 */
	public function save( $post_id, $post ) {
		if ( ! isset( $_POST[ self::NONCE ] ) || ! wp_verify_nonce( sanitize_key( $_POST[ self::NONCE ] ), 'iam_cert_save_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Champs simples.
		foreach ( self::fields() as $key => $args ) {
			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}
			$value = self::sanitize( wp_unslash( $_POST[ $key ] ), $args ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			update_post_meta( $post_id, $key, $value );
		}

		update_post_meta( $post_id, '_iam_featured', isset( $_POST['_iam_featured'] ) ? '1' : '' );

		// Modules.
		$modules = array();
		if ( isset( $_POST['iam_modules'] ) && is_array( $_POST['iam_modules'] ) ) {
			foreach ( wp_unslash( $_POST['iam_modules'] ) as $raw ) {
				$titre  = isset( $raw['titre'] ) ? sanitize_text_field( $raw['titre'] ) : '';
				$brut   = isset( $raw['lecons'] ) ? (string) $raw['lecons'] : '';
				$lecons = array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $brut ) ) );
				$lecons = array_values( array_map( 'sanitize_text_field', $lecons ) );

				if ( '' === $titre && ! $lecons ) {
					continue;
				}
				$modules[] = array(
					'titre'  => $titre,
					'lecons' => $lecons,
				);
			}
		}
		update_post_meta( $post_id, '_iam_modules', $modules );

		// Formulaire associé.
		$form_id = isset( $_POST['_iam_form_id'] ) ? absint( wp_unslash( $_POST['_iam_form_id'] ) ) : 0;
		update_post_meta( $post_id, '_iam_form_id', $form_id ? (string) $form_id : '' );
	}
}
