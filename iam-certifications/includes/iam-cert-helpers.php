<?php
/**
 * Fonctions utilitaires : chargement de templates et accès aux données.
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Charge un template du plugin, surchargeable par le thème.
 *
 * Le thème peut remplacer un template en plaçant un fichier de même nom dans
 * son dossier « iam-certifications/ » (ex. wp-content/themes/mon-theme/iam-certifications/card.php).
 *
 * @param string $name Nom du fichier (ex. « card.php »).
 * @param array  $vars Variables mises à disposition du template.
 * @return void
 */
function iam_cert_get_template( $name, $vars = array() ) {
	$name = ltrim( $name, '/' );

	// 1) Surcharge par le thème.
	$located = locate_template( 'iam-certifications/' . $name );

	// 2) Template du plugin par défaut.
	if ( ! $located ) {
		$candidate = IAM_CERT_PATH . 'templates/' . $name;
		$located   = file_exists( $candidate ) ? $candidate : '';
	}

	if ( ! $located ) {
		return;
	}

	if ( $vars ) {
		extract( $vars, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	}

	include $located;
}

/**
 * Construit le libellé de durée « 50 h · 10 semaines ».
 *
 * @param int $post_id ID de la certification.
 * @return string
 */
function iam_cert_duree_label( $post_id ) {
	$heures = get_post_meta( $post_id, '_iam_duree_heures', true );
	$texte  = get_post_meta( $post_id, '_iam_duree_texte', true );

	$parts = array();
	if ( '' !== $heures ) {
		/* translators: %s : nombre d'heures */
		$parts[] = sprintf( __( '%s h', 'iam-certifications' ), $heures );
	}
	if ( $texte ) {
		$parts[] = $texte;
	}

	return implode( ' · ', $parts );
}

/**
 * Modules du programme : tableau de { titre, lecons[] }.
 *
 * @param int $post_id ID de la certification.
 * @return array
 */
function iam_cert_get_modules( $post_id ) {
	$modules = get_post_meta( $post_id, '_iam_modules', true );
	if ( ! is_array( $modules ) ) {
		return array();
	}

	$sortie = array();
	foreach ( $modules as $m ) {
		$sortie[] = array(
			'titre'  => isset( $m['titre'] ) ? (string) $m['titre'] : '',
			'lecons' => ( isset( $m['lecons'] ) && is_array( $m['lecons'] ) ) ? $m['lecons'] : array(),
		);
	}
	return $sortie;
}

/**
 * Compteurs du programme : nombre de modules et total de leçons.
 *
 * @param int $post_id ID de la certification.
 * @return array { modules:int, lecons:int }
 */
function iam_cert_counts( $post_id ) {
	$modules = iam_cert_get_modules( $post_id );
	$lecons  = 0;
	foreach ( $modules as $m ) {
		$lecons += count( $m['lecons'] );
	}
	return array(
		'modules' => count( $modules ),
		'lecons'  => $lecons,
	);
}

/**
 * Texte du bouton CTA (valeur personnalisée ou libellé par défaut).
 *
 * @param int $post_id ID de la certification.
 * @return string
 */
function iam_cert_cta_label( $post_id ) {
	$cta = get_post_meta( $post_id, '_iam_cta_texte', true );
	if ( $cta ) {
		return $cta;
	}
	return 'seminaire' === iam_cert_type( $post_id )
		? __( 'Réserver ma place', 'iam-certifications' )
		: __( 'Candidater', 'iam-certifications' );
}

/* =========================================================================
 * Types de formation : certificat (parcours long) ou séminaire (intensif)
 * ====================================================================== */

/**
 * Types disponibles : clé => libellé.
 *
 * @return array
 */
function iam_cert_types() {
	return array(
		'certificat' => __( 'Certificat professionnel', 'iam-certifications' ),
		'seminaire'  => __( 'Séminaire intensif', 'iam-certifications' ),
	);
}

/**
 * Type d'une formation. Les contenus antérieurs (sans type) sont des certificats.
 *
 * @param int $post_id ID.
 * @return string « certificat » ou « seminaire ».
 */
function iam_cert_type( $post_id ) {
	$type = get_post_meta( $post_id, '_iam_type', true );
	return array_key_exists( $type, iam_cert_types() ) ? $type : 'certificat';
}

/**
 * Programme jour par jour d'un séminaire : tableau de { titre, matin, apres_midi }.
 *
 * @param int $post_id ID.
 * @return array
 */
function iam_cert_get_jours( $post_id ) {
	$jours = get_post_meta( $post_id, '_iam_jours', true );
	if ( ! is_array( $jours ) ) {
		return array();
	}
	$sortie = array();
	foreach ( $jours as $j ) {
		$sortie[] = array(
			'titre'      => isset( $j['titre'] ) ? (string) $j['titre'] : '',
			'matin'      => isset( $j['matin'] ) ? (string) $j['matin'] : '',
			'apres_midi' => isset( $j['apres_midi'] ) ? (string) $j['apres_midi'] : '',
		);
	}
	return $sortie;
}

/**
 * Nettoie les jours postés par le formulaire d'admin.
 *
 * @param mixed $raw Valeur brute (déjà « unslashée »).
 * @return array
 */
function iam_cert_sanitize_jours( $raw ) {
	$jours = array();
	if ( ! is_array( $raw ) ) {
		return $jours;
	}
	foreach ( $raw as $j ) {
		$jour = array(
			'titre'      => isset( $j['titre'] ) ? sanitize_text_field( $j['titre'] ) : '',
			'matin'      => isset( $j['matin'] ) ? sanitize_textarea_field( $j['matin'] ) : '',
			'apres_midi' => isset( $j['apres_midi'] ) ? sanitize_textarea_field( $j['apres_midi'] ) : '',
		);
		if ( '' === $jour['titre'] && '' === $jour['matin'] && '' === $jour['apres_midi'] ) {
			continue;
		}
		$jours[] = $jour;
	}
	return $jours;
}

/**
 * Sessions d'un séminaire : lignes « dates | lieu ».
 *
 * @param int $post_id ID.
 * @return array[] { dates, lieu }
 */
function iam_cert_get_sessions( $post_id ) {
	$sessions = array();
	foreach ( iam_cert_lines( get_post_meta( $post_id, '_iam_sessions', true ) ) as $ligne ) {
		$parts      = array_map( 'trim', explode( '|', $ligne, 2 ) );
		$sessions[] = array(
			'dates' => $parts[0],
			'lieu'  => isset( $parts[1] ) ? $parts[1] : '',
		);
	}
	return $sessions;
}

/**
 * HTML d'un jour pour le répéteur d'admin du séminaire.
 *
 * @param int|string $index Index.
 * @param array      $jour  { titre, matin, apres_midi }.
 * @return string
 */
function iam_cert_day_row_html( $index, $jour ) {
	$jour = wp_parse_args( $jour, array( 'titre' => '', 'matin' => '', 'apres_midi' => '' ) );
	$name = 'iam_jours[' . $index . ']';
	ob_start();
	?>
	<div class="iam-day">
		<div class="iam-day__head">
			<span class="iam-day__num" aria-hidden="true"></span>
			<input type="text" class="iam-day__titre widefat" name="<?php echo esc_attr( $name ); ?>[titre]"
				value="<?php echo esc_attr( $jour['titre'] ); ?>"
				placeholder="<?php esc_attr_e( 'Thème de la journée (optionnel), ex. « Capturer le savoir »', 'iam-certifications' ); ?>">
			<button type="button" class="iam-row-remove" title="<?php esc_attr_e( 'Supprimer ce jour', 'iam-certifications' ); ?>"><span class="dashicons dashicons-trash"></span></button>
		</div>
		<div class="iam-day__slots">
			<label class="iam-day__slot">
				<span class="iam-day__slot-lbl"><span class="dashicons dashicons-cloud"></span> <?php esc_html_e( 'Matin', 'iam-certifications' ); ?></span>
				<textarea rows="3" name="<?php echo esc_attr( $name ); ?>[matin]" placeholder="<?php esc_attr_e( 'Contenus et ateliers de la matinée', 'iam-certifications' ); ?>"><?php echo esc_textarea( $jour['matin'] ); ?></textarea>
			</label>
			<label class="iam-day__slot">
				<span class="iam-day__slot-lbl"><span class="dashicons dashicons-admin-site-alt3"></span> <?php esc_html_e( 'Après-midi', 'iam-certifications' ); ?></span>
				<textarea rows="3" name="<?php echo esc_attr( $name ); ?>[apres_midi]" placeholder="<?php esc_attr_e( 'Ateliers de l’après-midi', 'iam-certifications' ); ?>"><?php echo esc_textarea( $jour['apres_midi'] ); ?></textarea>
			</label>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Coordonnées du contact « certifications » (responsable des demandes).
 * Filtrable via « iam_cert_contact ».
 *
 * @return array { nom, email, tel, tel_lien }
 */
function iam_cert_contact() {
	$contact = array(
		'nom'      => __( 'Banel', 'iam-certifications' ),
		'email'    => 'banel@groupeiam.com',
		'tel'      => '+221 77 714 39 91',
		'tel_lien' => '+221777143991',
	);

	/**
	 * Permet de personnaliser le contact sans modifier le plugin.
	 *
	 * @param array $contact Coordonnées par défaut.
	 */
	return apply_filters( 'iam_cert_contact', $contact );
}

/**
 * URL du logo du Groupe IAM (diplôme de la page de détail).
 * Filtrable via « iam_cert_logo_url ».
 *
 * @return string
 */
function iam_cert_logo_url() {
	return (string) apply_filters( 'iam_cert_logo_url', IAM_CERT_URL . 'assets/img/logo-groupe-iam.webp' );
}

/**
 * Identifiant du formulaire IAM Leads à utiliser pour une certification.
 * Priorité : réglage de la certification, puis formulaire « certifications »
 * par défaut (option posée au moment du seed).
 *
 * @param int $post_id ID de la certification.
 * @return int 0 si aucun formulaire résolu.
 */
function iam_cert_form_id( $post_id ) {
	$id = absint( get_post_meta( $post_id, '_iam_form_id', true ) );

	if ( ! $id ) {
		$id = absint( get_option( 'iam_cert_default_form_id', 0 ) );
	}

	/**
	 * Filtre l'identifiant du formulaire résolu pour une certification.
	 *
	 * @param int $id      Identifiant du formulaire.
	 * @param int $post_id Certification.
	 */
	return (int) apply_filters( 'iam_cert_form_id', $id, $post_id );
}

/**
 * Rend le formulaire IAM Leads associé, ou un repli si le plugin/formulaire
 * est absent. Le formulaire est enveloppé pour permettre l'injection JS du
 * nom de la certification (champ caché + UTM).
 *
 * @param int $post_id ID de la certification.
 * @return string HTML.
 */
function iam_cert_render_form( $post_id ) {
	$contact = iam_cert_contact();

	// Repli si IAM Leads n'est pas actif.
	if ( ! shortcode_exists( 'iam_form' ) ) {
		return iam_cert_contact_fallback( $contact );
	}

	$form_id = iam_cert_form_id( $post_id );
	if ( ! $form_id ) {
		return iam_cert_contact_fallback( $contact );
	}

	$slug  = get_post_field( 'post_name', $post_id );
	$titre = get_the_title( $post_id );

	$html  = sprintf(
		'<div class="iam-cert-form" data-cert-title="%1$s" data-cert-slug="%2$s">',
		esc_attr( $titre ),
		esc_attr( $slug )
	);
	$html .= do_shortcode( sprintf( '[iam_form id="%d"]', $form_id ) );
	$html .= '</div>';

	return $html;
}

/**
 * Bloc de repli quand aucun formulaire n'est disponible : contact direct.
 *
 * @param array $contact Coordonnées.
 * @return string
 */
function iam_cert_contact_fallback( $contact ) {
	return sprintf(
		'<div class="iam-cert-form-fallback"><p>%1$s</p><p><a class="iam-cert-btn" href="mailto:%2$s">%3$s %4$s</a></p></div>',
		esc_html__( 'Pour candidater ou obtenir le programme détaillé, contactez-nous :', 'iam-certifications' ),
		esc_attr( $contact['email'] ),
		iam_cert_icon( 'mail' ), // phpcs:ignore
		esc_html( $contact['email'] )
	);
}

/**
 * HTML d'une ligne de module pour le répéteur d'admin.
 *
 * @param int|string $index  Index du module.
 * @param string     $titre  Titre.
 * @param array      $lecons Leçons.
 * @return string
 */
function iam_cert_module_row_html( $index, $titre, $lecons ) {
	$lecons_txt = is_array( $lecons ) ? implode( "\n", $lecons ) : '';
	ob_start();
	?>
	<div class="iam-module">
		<div class="iam-module__head">
			<span class="iam-module__num" aria-hidden="true"></span>
			<input type="text" class="iam-module__titre widefat"
				name="iam_modules[<?php echo esc_attr( $index ); ?>][titre]"
				value="<?php echo esc_attr( $titre ); ?>"
				placeholder="<?php esc_attr_e( 'Titre du module (ex. « Module 1 — Fondamentaux »)', 'iam-certifications' ); ?>" />
			<button type="button" class="iam-row-remove" title="<?php esc_attr_e( 'Supprimer ce module', 'iam-certifications' ); ?>"><span class="dashicons dashicons-trash"></span></button>
		</div>
		<textarea class="iam-module__lecons widefat" rows="3"
			name="iam_modules[<?php echo esc_attr( $index ); ?>][lecons]"
			placeholder="<?php esc_attr_e( 'Une leçon par ligne', 'iam-certifications' ); ?>"><?php echo esc_textarea( $lecons_txt ); ?></textarea>
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Icône SVG inline, par nom. Renvoie une chaîne HTML sûre (SVG statique).
 *
 * @param string $name Nom de l'icône (clock, users, arrow, mail, phone).
 * @return string
 */
function iam_cert_icon( $name ) {
	$icons = array(
		'clock' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
		'users' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
		'arrow' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
		'mail'  => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>',
		'phone' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
		'play'     => '<svg viewBox="0 0 24 24" width="34" height="34" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z"/></svg>',
		'download' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11M7 10l5 5 5-5M5 20h14"/></svg>',
		'calendar' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/></svg>',
		'close'    => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>',
		'target'   => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/></svg>',
		'layers'   => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5z"/><path d="m3 13 9 5 9-5"/></svg>',
		'sun'      => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>',
		'sunset'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 18a5 5 0 0 0-10 0M12 9V2M4.2 10.2l1.4 1.4M1 18h2M21 18h2M18.4 11.6l1.4-1.4M23 22H1M16 6l-4 4-4-4"/></svg>',
		'pin'      => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>',
		'award'    => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path d="M15.5 13 17 22l-5-3-5 3 1.5-9"/></svg>',
		'briefcase' => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2M2 13h20"/></svg>',
		'spark'    => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M6 18l2.5-2.5M15.5 8.5 18 6"/></svg>',
		'monitor'  => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>',
		'gauge'    => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 14l4-4"/><path d="M3.3 19a10 10 0 1 1 17.4 0"/></svg>',
		'wallet'   => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-3"/><path d="M21 11h-5a2 2 0 0 0 0 4h5z"/></svg>',
		'package'  => '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m7.5 4.3 9 5.2M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4a2 2 0 0 0 1-1.7z"/><path d="M3.3 7 12 12l8.7-5M12 22V12"/></svg>',
		'info'     => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 16v-4M12 8h.01"/></svg>',
		'check'    => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg>',
	);

	return isset( $icons[ $name ] ) ? $icons[ $name ] : '';
}

/* =========================================================================
 * Champs d'administration (partagés : écran maison + metabox classique)
 * ====================================================================== */

/**
 * Texte d'un réglage de champ, éventuellement décliné par type.
 * Renvoie un tableau type => texte (la clé « both » quand il n'y a pas de variante).
 *
 * @param array  $args  Réglages du champ.
 * @param string $key   « label », « desc » ou « placeholder ».
 * @return array
 */
function iam_cert_field_variants( $args, $key ) {
	$base = isset( $args[ $key ] ) ? $args[ $key ] : '';
	if ( is_array( $base ) ) {
		return $base;
	}
	$plural = $key . 's';
	if ( ! empty( $args[ $plural ] ) && is_array( $args[ $plural ] ) ) {
		$out = array();
		foreach ( array_keys( iam_cert_types() ) as $t ) {
			$out[ $t ] = isset( $args[ $plural ][ $t ] ) ? $args[ $plural ][ $t ] : $base;
		}
		return $out;
	}
	return '' === $base ? array() : array( 'both' => $base );
}

/**
 * Libellé d'un champ pour un type donné.
 *
 * @param array  $args Réglages du champ.
 * @param string $type certificat|seminaire.
 * @return string
 */
function iam_cert_field_label( $args, $type ) {
	$v = iam_cert_field_variants( $args, 'label' );
	return isset( $v[ $type ] ) ? $v[ $type ] : (string) reset( $v );
}

/**
 * Rend un texte décliné par type : un <span data-for> par variante,
 * la feuille d'admin n'affiche que celui du type choisi.
 *
 * @param array $variants Tableau type => texte.
 * @return string HTML.
 */
function iam_cert_variants_html( $variants ) {
	if ( isset( $variants['both'] ) ) {
		return esc_html( $variants['both'] );
	}
	$html = '';
	foreach ( $variants as $type => $txt ) {
		$html .= '<span data-for="' . esc_attr( $type ) . '">' . esc_html( $txt ) . '</span>';
	}
	return $html;
}

/**
 * Affiche une ligne de champ d'administration selon son type.
 * Types : text, number, textarea, list (« une entrée par ligne »), url,
 * media (URL + bouton médiathèque), select.
 *
 * @param string $key   Clé meta (sert aussi d'id / name).
 * @param array  $args  Réglages du champ.
 * @param string $value Valeur courante.
 * @return void
 */
function iam_cert_render_field_row( $key, $args, $value ) {
	$type  = isset( $args['type'] ) ? $args['type'] : 'text';
	$scope = isset( $args['scope'] ) ? $args['scope'] : 'both';
	$width = isset( $args['width'] ) ? $args['width'] : 'full';
	$value = is_string( $value ) ? $value : '';

	// Placeholder : variante par type portée en data-*, appliquée par le JS.
	$ph      = iam_cert_field_variants( $args, 'placeholder' );
	$ph_attr = '';
	foreach ( $ph as $t => $txt ) {
		$ph_attr .= sprintf( ' data-ph-%1$s="%2$s"', esc_attr( $t ), esc_attr( $txt ) );
	}
	if ( $ph ) {
		$ph_attr .= sprintf( ' placeholder="%s"', esc_attr( reset( $ph ) ) );
	}

	printf(
		'<div class="iam-field iam-field--%1$s iam-field--%2$s" data-scope="%3$s">',
		esc_attr( $type ),
		esc_attr( $width ),
		esc_attr( $scope )
	);
	$required = ! empty( $args['required'] );
	$req_attr = $required ? ' data-required="1"' : '';

	printf(
		'<label class="iam-field__label" for="%1$s">%2$s%3$s</label>',
		esc_attr( $key ),
		iam_cert_variants_html( iam_cert_field_variants( $args, 'label' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$required ? ' <span class="iam-req" aria-hidden="true">*</span>' : ''
	);

	switch ( $type ) {
		case 'textarea':
		case 'list':
			printf(
				'<textarea id="%1$s" name="%1$s" rows="%2$d" class="widefat"%4$s%5$s>%3$s</textarea>',
				esc_attr( $key ),
				'list' === $type ? 4 : 3,
				esc_textarea( $value ),
				$ph_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$req_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
			break;

		case 'select':
			printf( '<select id="%1$s" name="%1$s" class="widefat">', esc_attr( $key ) );
			foreach ( $args['options'] as $opt => $lbl ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $opt ), selected( $value, $opt, false ), esc_html( $lbl ) );
			}
			echo '</select>';
			break;

		case 'media':
			echo '<span class="iam-media-field">';
			printf( '<input type="url" id="%1$s" name="%1$s" class="widefat" value="%2$s" placeholder="https://">', esc_attr( $key ), esc_attr( $value ) );
			printf(
				'<button type="button" class="button iam-media-pick" data-target="%1$s" data-library="%2$s"><span class="dashicons dashicons-admin-media"></span> %3$s</button>',
				esc_attr( $key ),
				esc_attr( isset( $args['media'] ) ? $args['media'] : '' ),
				esc_html__( 'Médiathèque', 'iam-certifications' )
			);
			echo '</span>';
			break;

		default:
			printf(
				'<input type="%1$s" id="%2$s" name="%2$s" class="widefat" value="%3$s"%4$s%5$s%6$s>',
				esc_attr( in_array( $type, array( 'number', 'url' ), true ) ? $type : 'text' ),
				esc_attr( $key ),
				esc_attr( $value ),
				'url' === $type && ! $ph ? ' placeholder="https://"' : $ph_attr, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'number' === $type ? ' min="0" inputmode="numeric"' : '',
				$req_attr // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);
	}

	$desc = iam_cert_field_variants( $args, 'desc' );
	if ( $desc ) {
		printf( '<p class="iam-field__hint">%s</p>', iam_cert_variants_html( $desc ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

/* =========================================================================
 * Aides d'affichage pour la page de détail
 * ====================================================================== */

/**
 * Découpe un texte « une entrée par ligne » en tableau propre.
 * Les puces saisies à la main (-, •, *) sont retirées.
 *
 * @param string $text Texte brut.
 * @return string[]
 */
function iam_cert_lines( $text ) {
	if ( ! is_string( $text ) || '' === trim( $text ) ) {
		return array();
	}
	$lines = preg_split( '/\r\n|\r|\n/', $text );
	$lines = array_map(
		function ( $l ) {
			return trim( preg_replace( '/^\s*(?:[-*•·]|\d+[.)])\s*/u', '', $l ) );
		},
		$lines
	);
	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * Rend une liste à puces (ou un paragraphe si une seule entrée).
 *
 * @param string $text  Texte « une entrée par ligne ».
 * @param string $class Classe CSS de la liste.
 * @return string HTML.
 */
function iam_cert_list_html( $text, $class = 'iam-cert-list' ) {
	$lines = iam_cert_lines( $text );
	if ( ! $lines ) {
		return '';
	}
	if ( 1 === count( $lines ) ) {
		return '<p class="' . esc_attr( $class ) . '__single">' . esc_html( $lines[0] ) . '</p>';
	}
	$html = '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $lines as $line ) {
		$html .= '<li>' . esc_html( $line ) . '</li>';
	}
	return $html . '</ul>';
}

/**
 * Code d'intégration de la vidéo de présentation.
 * YouTube / Vimeo via oEmbed (WordPress), fichier vidéo via <video>.
 *
 * @param string $url URL de la vidéo.
 * @return string HTML (vide si l'URL n'est pas exploitable).
 */
function iam_cert_video_embed( $url ) {
	if ( ! $url ) {
		return '';
	}

	if ( preg_match( '/\.(mp4|webm|ogg)(\?.*)?$/i', $url ) ) {
		return sprintf( '<video src="%s" controls playsinline preload="none"></video>', esc_url( $url ) );
	}

	$embed = wp_oembed_get( $url, array( 'width' => 1280 ) );
	if ( ! $embed ) {
		return '';
	}

	// Lecture automatique à l'ouverture de la fenêtre (YouTube / Vimeo).
	return preg_replace_callback(
		'/src="([^"]+)"/',
		function ( $m ) {
			$src = html_entity_decode( $m[1] );
			$src = add_query_arg( array( 'autoplay' => 1, 'rel' => 0 ), $src );
			return 'src="' . esc_url( $src ) . '" allow="autoplay; fullscreen; picture-in-picture"';
		},
		$embed,
		1
	);
}
