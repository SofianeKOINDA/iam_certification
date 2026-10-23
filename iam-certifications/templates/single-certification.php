<?php
/**
 * Template : page de détail d'une formation (certificat ou séminaire).
 *
 * Les deux types partagent la même ossature, mais pas les mêmes sections :
 *   - Certificat : chiffres clés, objectifs, parcours en modules (frise),
 *     projet final, débouchés, certificat délivré.
 *   - Séminaire  : chiffres clés, objectifs, programme jour par jour,
 *     jauge de pratique, livrables, sessions à réserver.
 *
 * Ossature : hero → bandeau d'outils → navigation collante → sections →
 * téléchargements → candidature → formations proches → barre mobile.
 * Chaque section dont le contenu est vide est masquée.
 * Surchargeable : « votre-theme/single-certification.php ».
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();
	$m       = function ( $key ) use ( $post_id ) {
		$v = get_post_meta( $post_id, $key, true );
		return is_string( $v ) ? trim( $v ) : '';
	};

	$type   = iam_cert_type( $post_id );
	$is_sem = 'seminaire' === $type;
	$types  = iam_cert_types();
	$titre  = get_the_title();
	$cta    = iam_cert_cta_label( $post_id );

	$hero        = $m( '_iam_hero' );
	$public      = $m( '_iam_public' );
	$prerequis   = iam_cert_lines( $m( '_iam_prerequis' ) );
	$objectifs   = iam_cert_lines( $m( '_iam_objectifs' ) );
	$debouches   = iam_cert_lines( $m( '_iam_debouches' ) );
	$livrables   = iam_cert_lines( $m( '_iam_livrables' ) );
	$infos       = iam_cert_lines( $m( '_iam_infos_pratiques' ) );
	$evaluation  = $m( '_iam_evaluation' );
	$projet      = $m( '_iam_projet_final' );
	$cout        = $m( '_iam_cout' );
	$niveau      = $m( '_iam_niveau' );
	$format      = $m( '_iam_format' );
	$heures      = $m( '_iam_duree_heures' );
	$duree       = $m( '_iam_duree_texte' );
	$rythme      = $m( '_iam_rythme' );
	$horaires    = $m( '_iam_horaires' );
	$elearning   = $m( '_iam_elearning' );
	$places      = absint( $m( '_iam_participants_max' ) );
	$pratique    = min( 100, absint( $m( '_iam_pratique_pct' ) ) );
	$video       = iam_cert_video_embed( $m( '_iam_video_url' ) );
	$rdv_url     = $m( '_iam_rdv_url' );
	$brochure    = $m( '_iam_brochure_url' );
	$dossier     = $m( '_iam_dossier_url' );
	$image2      = $m( '_iam_image2_url' );
	$modules     = $is_sem ? array() : iam_cert_get_modules( $post_id );
	$jours       = $is_sem ? iam_cert_get_jours( $post_id ) : array();
	$sessions    = $is_sem ? iam_cert_get_sessions( $post_id ) : array();
	$counts      = iam_cert_counts( $post_id );
	$contact     = iam_cert_contact();
	$thumb_id    = get_post_thumbnail_id( $post_id );
	$public_list = array_values(
		array_filter(
			array_map(
				function ( $p ) {
					$p = trim( $p );
					return mb_strtoupper( mb_substr( $p, 0, 1 ) ) . mb_substr( $p, 1 );
				},
				preg_split( '/[,;]\s*/u', $public )
			)
		)
	);

	$domaines = get_the_terms( $post_id, IAM_CERT_TAX_DOMAINE );
	$domaine  = ( $domaines && ! is_wp_error( $domaines ) ) ? $domaines[0] : null;
	$outils   = get_the_terms( $post_id, IAM_CERT_TAX_OUTIL );
	$outils   = ( $outils && ! is_wp_error( $outils ) ) ? $outils : array();

	// Nombre contenu dans un texte (« 10 semaines » → 10), pour les compteurs.
	$nombre = function ( $txt ) {
		return preg_match( '/\d+/', (string) $txt, $mm ) ? (int) $mm[0] : 0;
	};

	/* ---------------------------------------------------------------
	 * Chiffres clés (compteurs animés)
	 * ------------------------------------------------------------- */
	// Carte : icône, titre, nombre animé (0 = pas de compteur) et texte qui suit.
	$kpi  = function ( $icon, $title, $num, $after ) {
		return array(
			'icon'  => $icon,
			'title' => $title,
			'num'   => (int) $num,
			'after' => $after,
		);
	};
	$kpis = array();
	if ( $is_sem ) {
		$nb_jours = count( $jours ) ? count( $jours ) : $nombre( $duree );
		$kpis[]   = $kpi( 'calendar', __( 'Durée', 'iam-certifications' ), $nb_jours, ' ' . _n( 'jour', 'jours', max( 1, $nb_jours ), 'iam-certifications' ) );
		$kpis[]   = $kpi( 'clock', __( 'Volume', 'iam-certifications' ), $heures, ' ' . __( 'heures', 'iam-certifications' ) );
		$kpis[]   = $kpi( 'target', __( 'Pratique', 'iam-certifications' ), $pratique, ' % ' . __( 'ateliers', 'iam-certifications' ) );
		$kpis[]   = $kpi( 'users', __( 'Groupe', 'iam-certifications' ), $places, ' ' . __( 'places max.', 'iam-certifications' ) );
	} else {
		// « 10 semaines » → 10 + « semaines » ; « 10 à 12 semaines » → 10 + « –12 semaines ».
		if ( preg_match( '/^\s*(\d+)\s*(?:à|-|–)\s*(\d+)\s*(.*)$/u', $duree, $rg ) ) {
			$kpis[] = $kpi( 'calendar', __( 'Durée', 'iam-certifications' ), $rg[1], '–' . $rg[2] . ' ' . $rg[3] );
		} elseif ( $nombre( $duree ) ) {
			$kpis[] = $kpi( 'calendar', __( 'Durée', 'iam-certifications' ), $nombre( $duree ), ' ' . preg_replace( '/^\s*\d+\s*/u', '', $duree ) );
		} elseif ( $duree ) {
			$kpis[] = $kpi( 'calendar', __( 'Durée', 'iam-certifications' ), 0, $duree );
		}
		$kpis[] = $kpi( 'clock', __( 'Volume', 'iam-certifications' ), $heures, ' ' . __( 'heures', 'iam-certifications' ) );
		$kpis[] = $kpi(
			'layers',
			__( 'Programme', 'iam-certifications' ),
			$counts['modules'],
			' ' . _n( 'module', 'modules', max( 1, $counts['modules'] ), 'iam-certifications' )
		);
		$kpis[] = $niveau
			? $kpi( 'gauge', __( 'Niveau', 'iam-certifications' ), 0, ucfirst( $niveau ) )
			: $kpi( 'monitor', __( 'Format', 'iam-certifications' ), 0, $format );
	}
	// Une carte à compteur sans nombre (unité seule, « heures ») ou un texte vide est retiré.
	$kpis = array_values(
		array_filter(
			$kpis,
			function ( $k ) {
				return $k['num'] > 0 || ( '' !== trim( $k['after'] ) && ! preg_match( '/^[\s–]/u', $k['after'] ) );
			}
		)
	);

	/* ---------------------------------------------------------------
	 * Fiche « en bref » (carte du hero)
	 * ------------------------------------------------------------- */
	$facts = array(
		array( 'clock', __( 'Durée', 'iam-certifications' ), trim( $duree . ( '' !== $heures ? ' · ' . $heures . ' h' : '' ), ' ·' ) ),
		array( 'monitor', __( 'Format', 'iam-certifications' ), $format ),
	);
	if ( $is_sem ) {
		$facts[] = array( 'users', __( 'Groupe', 'iam-certifications' ), $places ? sprintf( __( '%d participants maximum', 'iam-certifications' ), $places ) : '' );
		$facts[] = array( 'sun', __( 'Horaires', 'iam-certifications' ), $horaires );
		$facts[] = array( 'calendar', __( 'Prochaine session', 'iam-certifications' ), $sessions ? $sessions[0]['dates'] : __( 'Sur demande, en inter ou en intra', 'iam-certifications' ) );
	} else {
		$facts[] = array( 'gauge', __( 'Niveau', 'iam-certifications' ), $niveau ? ucfirst( $niveau ) : '' );
		$facts[] = array( 'calendar', __( 'Rythme', 'iam-certifications' ), $rythme );
		$facts[] = array( 'award', __( 'Délivrance', 'iam-certifications' ), $evaluation ? __( 'Certificat après évaluation', 'iam-certifications' ) : '' );
	}

	/*
	 * Tarif : montant et devise lus dans « Coût et financement »
	 * (ex. « 400 000 FCFA pour le certificat complet… »). Il est affiché dans
	 * la fiche du hero et dans la section « Investissement ».
	 */
	$prix_montant = 0;
	$prix_devise  = '';
	$prix_note    = '';
	if ( $cout && preg_match( '/(\d[\d\s.\x{202F}\x{00A0}]*)\s?(FCFA|F CFA|XOF|€|EUR)/u', $cout, $tarif ) ) {
		$prix_montant = (int) preg_replace( '/\D/', '', $tarif[1] );
		$prix_devise  = 'F CFA' === $tarif[2] ? 'FCFA' : $tarif[2];
		$prix_note    = trim( str_replace( $tarif[0], '', $cout ), " \t\n\r\0\x0B.,;:–—-" );
		$facts[]      = array( 'wallet', __( 'Tarif', 'iam-certifications' ), number_format_i18n( $prix_montant ) . ' ' . $prix_devise );
	}
	$facts = array_filter(
		$facts,
		function ( $f ) {
			return '' !== $f[2];
		}
	);

	/* ---------------------------------------------------------------
	 * Sections présentes → navigation collante
	 * ------------------------------------------------------------- */
	$has_programme = $is_sem ? (bool) $jours : (bool) $modules;
	$has_methode   = $is_sem ? ( $pratique || $places || $elearning || $infos || $horaires ) : ( $format || $rythme || $niveau || $heures );
	$has_public    = $public || $prerequis;
	$has_certif    = $evaluation || $livrables || $projet;
	$nav           = array_filter(
		array(
			'iam-objectifs'     => $objectifs ? __( 'Objectifs', 'iam-certifications' ) : '',
			'iam-programme'     => $has_programme ? ( $is_sem ? __( 'Programme', 'iam-certifications' ) : __( 'Parcours', 'iam-certifications' ) ) : '',
			'iam-methode'       => $has_methode ? ( $is_sem ? __( 'Format', 'iam-certifications' ) : __( 'Modalités', 'iam-certifications' ) ) : '',
			'iam-public'        => $has_public ? __( 'Pour qui', 'iam-certifications' ) : '',
			'iam-sessions'      => $is_sem ? __( 'Sessions', 'iam-certifications' ) : '',
			'iam-debouches'     => $debouches ? __( 'Débouchés', 'iam-certifications' ) : '',
			'iam-certification' => $has_certif ? ( $is_sem ? __( 'Livrables', 'iam-certifications' ) : __( 'Certification', 'iam-certifications' ) ) : '',
			'iam-tarif'         => $cout ? __( 'Tarif', 'iam-certifications' ) : '',
		)
	);

	// « KM × IA — Knowledge Management RH… » : titre court + sous-titre.
	$titre_parts = preg_split( '/\s+[—–]\s+/u', $titre, 2 );
	$titre_main  = $titre_parts[0];
	$titre_sub   = isset( $titre_parts[1] ) ? $titre_parts[1] : '';

	// Titre découpé en mots (révélation animée), le texte reste lisible sans JS.
	$titre_html = implode(
		' ',
		array_map(
			function ( $w ) {
				return '<span class="iam-w"><span>' . esc_html( $w ) . '</span></span>';
			},
			preg_split( '/\s+/u', $titre_main )
		)
	);

	$section_num = 0;
	$num         = function () use ( &$section_num ) {
		return sprintf( '%02d', ++$section_num );
	};
	?>
	<div class="iam-cert iam-lp iam-lp--<?php echo esc_attr( $type ); ?>">

		<!-- ============================================================ HERO -->
		<header class="iam-lp-hero<?php echo $thumb_id ? ' has-image' : ''; ?>">
			<div class="iam-lp-hero__bg" aria-hidden="true">
				<?php if ( $thumb_id ) : ?>
					<?php echo wp_get_attachment_image( $thumb_id, 'full', false, array( 'class' => 'iam-lp-hero__img', 'alt' => '', 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
				<?php endif; ?>
				<span class="iam-lp-hero__veil"></span>
				<span class="iam-lp-hero__grid"></span>
				<span class="iam-lp-orb iam-lp-orb--a"></span>
				<span class="iam-lp-orb iam-lp-orb--b"></span>
			</div>

			<div class="iam-lp-wrap iam-lp-hero__inner">
				<div class="iam-lp-hero__text">
					<nav class="iam-lp-crumbs" aria-label="<?php esc_attr_e( 'Fil d’Ariane', 'iam-certifications' ); ?>">
						<a href="<?php echo esc_url( get_post_type_archive_link( IAM_CERT_CPT ) ); ?>"><?php esc_html_e( 'Formations', 'iam-certifications' ); ?></a>
						<?php if ( $domaine ) : ?>
							<span aria-hidden="true">/</span>
							<span><?php echo esc_html( $domaine->name ); ?></span>
						<?php endif; ?>
					</nav>

					<p class="iam-lp-type">
						<span class="iam-lp-type__dot" aria-hidden="true"></span>
						<?php echo esc_html( $types[ $type ] ); ?>
						<?php if ( $is_sem && $duree ) : ?>
							<span class="iam-lp-type__sep" aria-hidden="true">·</span> <?php echo esc_html( $duree ); ?>
						<?php endif; ?>
					</p>

					<h1 class="iam-lp-hero__title<?php echo mb_strlen( $titre_main ) > 42 ? ' is-long' : ''; ?>">
						<?php echo $titre_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- mots échappés ci-dessus. ?>
						<?php if ( $titre_sub ) : ?>
							<span class="iam-lp-hero__sub"><?php echo esc_html( $titre_sub ); ?></span>
						<?php endif; ?>
					</h1>

					<?php if ( $hero ) : ?>
						<p class="iam-lp-hero__lead"><?php echo esc_html( $hero ); ?></p>
					<?php endif; ?>

					<div class="iam-lp-hero__ctas">
						<a class="iam-lp-btn iam-lp-btn--lime" href="#iam-candidature" data-iam-magnetic>
							<?php echo esc_html( $cta ); ?>
							<span class="iam-lp-btn__icon"><?php echo iam_cert_icon( 'arrow' ); // phpcs:ignore ?></span>
						</a>
						<?php if ( $video ) : ?>
							<button type="button" class="iam-lp-play" data-iam-video-open aria-haspopup="dialog">
								<span class="iam-lp-play__circle"><?php echo iam_cert_icon( 'play' ); // phpcs:ignore ?></span>
								<span><?php esc_html_e( 'Voir la vidéo', 'iam-certifications' ); ?></span>
							</button>
						<?php elseif ( $brochure ) : ?>
							<a class="iam-lp-btn iam-lp-btn--glass" href="<?php echo esc_url( $brochure ); ?>" download>
								<span class="iam-lp-btn__icon"><?php echo iam_cert_icon( 'download' ); // phpcs:ignore ?></span>
								<?php esc_html_e( 'Brochure', 'iam-certifications' ); ?>
							</a>
						<?php elseif ( $rdv_url ) : ?>
							<a class="iam-lp-btn iam-lp-btn--glass" href="<?php echo esc_url( $rdv_url ); ?>" target="_blank" rel="noopener">
								<span class="iam-lp-btn__icon"><?php echo iam_cert_icon( 'calendar' ); // phpcs:ignore ?></span>
								<?php esc_html_e( 'Prendre rendez-vous', 'iam-certifications' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( $facts ) : ?>
					<aside class="iam-lp-fiche" aria-label="<?php esc_attr_e( 'En bref', 'iam-certifications' ); ?>" data-iam-tilt>
						<p class="iam-lp-fiche__eyebrow"><?php esc_html_e( 'En bref', 'iam-certifications' ); ?></p>
						<dl class="iam-lp-fiche__list">
							<?php foreach ( $facts as $f ) : ?>
								<div class="iam-lp-fiche__row">
									<dt><span class="iam-lp-fiche__icon"><?php echo iam_cert_icon( $f[0] ); // phpcs:ignore ?></span><?php echo esc_html( $f[1] ); ?></dt>
									<dd><?php echo esc_html( $f[2] ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
						<a class="iam-lp-btn iam-lp-btn--green iam-lp-btn--block" href="#iam-candidature"><?php echo esc_html( $cta ); ?></a>
						<p class="iam-lp-fiche__note">
							<?php echo esc_html( $is_sem ? __( 'Inter-entreprises ou intra, sur un campus IAM ou dans vos locaux.', 'iam-certifications' ) : __( 'Réponse de notre équipe sous 48 h.', 'iam-certifications' ) ); ?>
						</p>
					</aside>
				<?php endif; ?>
			</div>

			<?php if ( $kpis ) : ?>
				<div class="iam-lp-wrap">
					<ul class="iam-lp-kpis" style="--n:<?php echo (int) count( $kpis ); ?>">
						<?php foreach ( $kpis as $k ) : ?>
							<li class="iam-lp-kpi">
								<span class="iam-lp-kpi__icon" aria-hidden="true"><?php echo iam_cert_icon( $k['icon'] ); // phpcs:ignore ?></span>
								<span class="iam-lp-kpi__body">
									<span class="iam-lp-kpi__title"><?php echo esc_html( $k['title'] ); ?></span>
									<span class="iam-lp-kpi__value">
										<?php if ( $k['num'] ) : ?>
											<span class="iam-lp-kpi__num" data-iam-count="<?php echo (int) $k['num']; ?>"><?php echo (int) $k['num']; ?></span><?php echo esc_html( $k['after'] ); ?>
										<?php else : ?>
											<?php echo esc_html( $k['after'] ); ?>
										<?php endif; ?>
									</span>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

		</header>

		<?php if ( $video ) : ?>
			<dialog class="iam-cert-video" aria-label="<?php esc_attr_e( 'Vidéo de présentation', 'iam-certifications' ); ?>">
				<button type="button" class="iam-cert-video__close" data-iam-video-close>
					<?php echo iam_cert_icon( 'close' ); // phpcs:ignore ?>
					<span class="screen-reader-text"><?php esc_html_e( 'Fermer la vidéo', 'iam-certifications' ); ?></span>
				</button>
				<div class="iam-cert-video__frame"></div>
				<template class="iam-cert-video__tpl"><?php echo $video; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- oEmbed WordPress. ?></template>
			</dialog>
		<?php endif; ?>

		<!-- ============================================== BANDEAU D'OUTILS -->
		<?php if ( $outils ) : ?>
			<div class="iam-lp-marquee" aria-label="<?php esc_attr_e( 'Outils utilisés', 'iam-certifications' ); ?>">
				<div class="iam-lp-marquee__track">
					<?php for ( $rep = 0; $rep < 2; $rep++ ) : ?>
						<ul class="iam-lp-marquee__list"<?php echo $rep ? ' aria-hidden="true"' : ''; ?>>
							<?php
							// Assez d'éléments pour couvrir les grands écrans même avec peu d'outils.
							$loop = max( 1, (int) ceil( 10 / count( $outils ) ) );
							for ( $l = 0; $l < $loop; $l++ ) :
								foreach ( $outils as $outil ) :
									?>
									<li><span class="iam-lp-marquee__star" aria-hidden="true">✦</span><?php echo esc_html( $outil->name ); ?></li>
									<?php
								endforeach;
							endfor;
							?>
						</ul>
					<?php endfor; ?>
				</div>
			</div>
		<?php endif; ?>

		<!-- ============================================ NAVIGATION COLLANTE -->
		<?php if ( count( $nav ) > 1 ) : ?>
			<nav class="iam-lp-nav" aria-label="<?php esc_attr_e( 'Sur cette page', 'iam-certifications' ); ?>" data-iam-nav>
				<div class="iam-lp-wrap iam-lp-nav__inner">
					<ul class="iam-lp-nav__list">
						<?php foreach ( $nav as $anchor => $lbl ) : ?>
							<li><a href="#<?php echo esc_attr( $anchor ); ?>"><?php echo esc_html( $lbl ); ?></a></li>
						<?php endforeach; ?>
					</ul>
					<a class="iam-lp-btn iam-lp-btn--green iam-lp-btn--sm iam-lp-nav__cta" href="#iam-candidature"><?php echo esc_html( $cta ); ?></a>
				</div>
				<span class="iam-lp-nav__progress" data-iam-progress aria-hidden="true"></span>
			</nav>
		<?php endif; ?>

		<!-- ====================================================== OBJECTIFS -->
		<?php if ( $objectifs ) : ?>
			<section class="iam-lp-sec" id="iam-objectifs" aria-labelledby="iam-objectifs-t">
				<div class="iam-lp-wrap">
					<header class="iam-lp-head">
						<span class="iam-lp-head__num"><?php echo esc_html( $num() ); ?></span>
						<p class="iam-lp-eyebrow"><?php echo esc_html( $is_sem ? __( 'Objectifs pédagogiques', 'iam-certifications' ) : __( 'Objectifs', 'iam-certifications' ) ); ?></p>
						<h2 class="iam-lp-h2" id="iam-objectifs-t" data-iam-reveal><?php echo esc_html( $is_sem ? __( 'Ce que vous saurez faire en repartant', 'iam-certifications' ) : __( 'Ce que vous saurez faire à la fin du parcours', 'iam-certifications' ) ); ?></h2>
					</header>
					<ol class="iam-lp-goals" data-iam-stagger>
						<?php foreach ( $objectifs as $i => $obj ) : ?>
							<li class="iam-lp-goal">
								<span class="iam-lp-goal__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
								<p><?php echo esc_html( $obj ); ?></p>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</section>
		<?php endif; ?>

		<!-- ====================================================== PROGRAMME -->
		<?php if ( $has_programme ) : ?>
			<section class="iam-lp-sec iam-lp-sec--green" id="iam-programme" aria-labelledby="iam-programme-t">
				<div class="iam-lp-wrap">
					<header class="iam-lp-head iam-lp-head--split">
						<div>
							<span class="iam-lp-head__num"><?php echo esc_html( $num() ); ?></span>
							<p class="iam-lp-eyebrow"><?php echo esc_html( $is_sem ? __( 'Programme détaillé', 'iam-certifications' ) : __( 'Le parcours', 'iam-certifications' ) ); ?></p>
							<h2 class="iam-lp-h2" id="iam-programme-t" data-iam-reveal>
								<?php
								if ( $is_sem ) {
									/* translators: %d : nombre de jours */
									echo esc_html( sprintf( _n( '%d jour pour transformer votre pratique', '%d jours pour transformer votre pratique', count( $jours ), 'iam-certifications' ), count( $jours ) ) );
								} else {
									/* translators: %d : nombre de modules */
									echo esc_html( sprintf( _n( '%d module, une progression pensée pour l’emploi', '%d modules, une progression pensée pour l’emploi', count( $modules ), 'iam-certifications' ), count( $modules ) ) );
								}
								?>
							</h2>
						</div>
						<p class="iam-lp-head__aside">
							<?php
							if ( $is_sem ) {
								echo esc_html( $horaires ? $horaires : __( 'Matin et après-midi : apports courts, puis ateliers sur vos cas réels.', 'iam-certifications' ) );
							} else {
								echo esc_html( trim( implode( ' · ', array_filter( array( $counts['lecons'] ? sprintf( _n( '%d leçon', '%d leçons', $counts['lecons'], 'iam-certifications' ), $counts['lecons'] ) : '', '' !== $heures ? $heures . ' h' : '', $duree ) ) ) ) );
							}
							?>
						</p>
					</header>

					<?php if ( $is_sem ) : ?>
						<div class="iam-lp-days" data-iam-stagger>
							<?php foreach ( $jours as $i => $jour ) : ?>
								<article class="iam-lp-day">
									<header class="iam-lp-day__head">
										<span class="iam-lp-day__big" aria-hidden="true">J<?php echo (int) $i + 1; ?></span>
										<p class="iam-lp-day__kicker"><?php echo esc_html( sprintf( __( 'Jour %d', 'iam-certifications' ), $i + 1 ) ); ?></p>
										<?php if ( $jour['titre'] ) : ?>
											<h3 class="iam-lp-day__title"><?php echo esc_html( $jour['titre'] ); ?></h3>
										<?php endif; ?>
									</header>
									<?php
									$slots = array(
										array( 'sun', __( 'Matin', 'iam-certifications' ), '8h30 – 12h30', $jour['matin'] ),
										array( 'sunset', __( 'Après-midi', 'iam-certifications' ), '14h – 17h30', $jour['apres_midi'] ),
									);
									foreach ( $slots as $slot ) :
										if ( '' === trim( $slot[3] ) ) {
											continue;
										}
										?>
										<div class="iam-lp-slot">
											<p class="iam-lp-slot__when">
												<span class="iam-lp-slot__icon"><?php echo iam_cert_icon( $slot[0] ); // phpcs:ignore ?></span>
												<strong><?php echo esc_html( $slot[1] ); ?></strong>
												<?php if ( ! $horaires || false !== strpos( $horaires, $slot[2] ) ) : ?>
													<span class="iam-lp-slot__time"><?php echo esc_html( $slot[2] ); ?></span>
												<?php endif; ?>
											</p>
											<?php echo iam_cert_list_html( $slot[3], 'iam-lp-slot__list' ); // phpcs:ignore ?>
										</div>
									<?php endforeach; ?>
								</article>
							<?php endforeach; ?>
						</div>
					<?php else : ?>
						<div class="iam-lp-path" data-iam-path>
							<span class="iam-lp-path__rail" aria-hidden="true"><span class="iam-lp-path__fill" data-iam-path-fill></span></span>
							<ol class="iam-lp-path__list">
							<?php
							$last = count( $modules ) - 1;
							foreach ( $modules as $i => $module ) :
								$is_final = $i === $last && preg_match( '/projet|capstone|tutor/i', $module['titre'] );
								// « Module 3 — Titre » → étiquette + titre.
								$parts = preg_split( '/\s+[—–-]\s+/u', $module['titre'], 2 );
								$tag   = 2 === count( $parts ) ? $parts[0] : sprintf( __( 'Module %d', 'iam-certifications' ), $i + 1 );
								$name  = 2 === count( $parts ) ? $parts[1] : $module['titre'];
								?>
								<li class="iam-lp-step<?php echo $is_final ? ' is-final' : ''; ?>">
									<span class="iam-lp-step__dot" aria-hidden="true"><?php echo $is_final ? iam_cert_icon( 'award' ) : esc_html( sprintf( '%02d', $i + 1 ) ); // phpcs:ignore ?></span>
									<details class="iam-lp-step__card"<?php echo 0 === $i || $is_final ? ' open' : ''; ?>>
										<summary>
											<span class="iam-lp-step__tag"><?php echo esc_html( $tag ); ?></span>
											<span class="iam-lp-step__title"><?php echo esc_html( $name ); ?></span>
											<?php if ( $module['lecons'] ) : ?>
												<span class="iam-lp-step__count"><?php echo esc_html( sprintf( _n( '%d leçon', '%d leçons', count( $module['lecons'] ), 'iam-certifications' ), count( $module['lecons'] ) ) ); ?></span>
											<?php endif; ?>
											<span class="iam-lp-step__chev" aria-hidden="true"></span>
										</summary>
										<?php if ( $module['lecons'] || ( $is_final && $projet ) ) : ?>
											<div class="iam-lp-step__body">
												<?php if ( $is_final && $projet ) : ?>
													<p class="iam-lp-step__projet"><?php echo esc_html( $projet ); ?></p>
												<?php endif; ?>
												<?php if ( $module['lecons'] ) : ?>
													<ul>
														<?php foreach ( $module['lecons'] as $lecon ) : ?>
															<li><?php echo esc_html( $lecon ); ?></li>
														<?php endforeach; ?>
													</ul>
												<?php endif; ?>
											</div>
										<?php endif; ?>
									</details>
								</li>
							<?php endforeach; ?>
							</ol>
						</div>
						<?php if ( $projet && ! preg_match( '/projet|capstone|tutor/i', end( $modules )['titre'] ) ) : ?>
							<div class="iam-lp-projet" data-iam-reveal>
								<span class="iam-lp-projet__icon"><?php echo iam_cert_icon( 'award' ); // phpcs:ignore ?></span>
								<div>
									<p class="iam-lp-eyebrow"><?php esc_html_e( 'Projet final', 'iam-certifications' ); ?></p>
									<p class="iam-lp-projet__text"><?php echo esc_html( $projet ); ?></p>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<!-- ================================================ FORMAT / MÉTHODE -->
		<?php if ( $has_methode ) : ?>
			<section class="iam-lp-sec" id="iam-methode" aria-labelledby="iam-methode-t">
				<div class="iam-lp-wrap">
					<header class="iam-lp-head">
						<span class="iam-lp-head__num"><?php echo esc_html( $num() ); ?></span>
						<p class="iam-lp-eyebrow"><?php echo esc_html( $is_sem ? __( 'Format', 'iam-certifications' ) : __( 'Modalités', 'iam-certifications' ) ); ?></p>
						<h2 class="iam-lp-h2" id="iam-methode-t" data-iam-reveal><?php echo esc_html( $is_sem ? __( 'Peu de théorie, beaucoup de terrain', 'iam-certifications' ) : __( 'Un format compatible avec votre activité', 'iam-certifications' ) ); ?></h2>
					</header>

					<div class="iam-lp-bento" data-iam-stagger>
						<?php if ( $is_sem && $pratique ) : ?>
							<div class="iam-lp-tile iam-lp-tile--ring">
								<svg class="iam-lp-ring" viewBox="0 0 120 120" aria-hidden="true">
									<circle class="iam-lp-ring__bg" cx="60" cy="60" r="52"/>
									<circle class="iam-lp-ring__fg" cx="60" cy="60" r="52" pathLength="100" style="--p:<?php echo (int) $pratique; ?>" data-iam-ring/>
								</svg>
								<p class="iam-lp-tile__big"><span data-iam-count="<?php echo (int) $pratique; ?>"><?php echo (int) $pratique; ?></span>%</p>
								<p class="iam-lp-tile__label"><?php esc_html_e( 'de pratique : ateliers sur des outils réels et des cas d’entreprises d’Afrique de l’Ouest', 'iam-certifications' ); ?></p>
							</div>
						<?php endif; ?>

						<?php
						$tiles = $is_sem
							? array(
								array( 'users', $places ? sprintf( __( '%d participants max.', 'iam-certifications' ), $places ) : '', __( 'Un petit groupe pour un accompagnement individualisé.', 'iam-certifications' ) ),
								array( 'clock', $horaires ? __( 'Horaires', 'iam-certifications' ) : '', $horaires ),
								array( 'monitor', $elearning ? __( 'Et après ?', 'iam-certifications' ) : '', $elearning ),
								array( 'pin', $format ? __( 'Lieu', 'iam-certifications' ) : '', $format ),
							)
							: array(
								array( 'monitor', $format ? __( 'Format', 'iam-certifications' ) : '', $format ),
								array( 'clock', $duree || $heures ? __( 'Durée', 'iam-certifications' ) : '', trim( $duree . ( '' !== $heures ? ' — ' . $heures . ' heures encadrées' : '' ), ' —' ) ),
								array( 'calendar', $rythme ? __( 'Rythme', 'iam-certifications' ) : '', $rythme ),
								array( 'gauge', $niveau ? __( 'Niveau', 'iam-certifications' ) : '', $niveau ? ucfirst( $niveau ) : '' ),
							);
						foreach ( $tiles as $tile ) :
							if ( '' === $tile[1] || '' === $tile[2] ) {
								continue;
							}
							?>
							<div class="iam-lp-tile">
								<span class="iam-lp-tile__icon"><?php echo iam_cert_icon( $tile[0] ); // phpcs:ignore ?></span>
								<p class="iam-lp-tile__title"><?php echo esc_html( $tile[1] ); ?></p>
								<p class="iam-lp-tile__text"><?php echo esc_html( $tile[2] ); ?></p>
							</div>
						<?php endforeach; ?>

						<?php if ( $infos ) : ?>
							<div class="iam-lp-tile iam-lp-tile--wide">
								<span class="iam-lp-tile__icon"><?php echo iam_cert_icon( 'info' ); // phpcs:ignore ?></span>
								<p class="iam-lp-tile__title"><?php esc_html_e( 'Informations pratiques', 'iam-certifications' ); ?></p>
								<ul class="iam-lp-ticks">
									<?php foreach ( $infos as $info ) : ?>
										<li><?php echo esc_html( $info ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- ======================================================== PUBLIC -->
		<?php if ( $has_public ) : ?>
			<section class="iam-lp-sec iam-lp-sec--sand" id="iam-public" aria-labelledby="iam-public-t">
				<div class="iam-lp-wrap iam-lp-public">
					<div class="iam-lp-public__media" data-iam-parallax>
						<?php if ( $image2 ) : ?>
							<img src="<?php echo esc_url( $image2 ); ?>" alt="" loading="lazy" decoding="async">
						<?php elseif ( $thumb_id ) : ?>
							<?php echo wp_get_attachment_image( $thumb_id, 'large', false, array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
						<?php else : ?>
							<span class="iam-lp-public__fallback" aria-hidden="true"></span>
						<?php endif; ?>
						<?php if ( $is_sem && $places ) : ?>
							<span class="iam-lp-public__badge"><?php echo iam_cert_icon( 'users' ); // phpcs:ignore ?> <?php echo esc_html( sprintf( __( '%d places par session', 'iam-certifications' ), $places ) ); ?></span>
						<?php endif; ?>
					</div>
					<div class="iam-lp-public__text">
						<span class="iam-lp-head__num"><?php echo esc_html( $num() ); ?></span>
						<p class="iam-lp-eyebrow"><?php esc_html_e( 'Pour qui ?', 'iam-certifications' ); ?></p>
						<h2 class="iam-lp-h2" id="iam-public-t" data-iam-reveal><?php echo esc_html( $is_sem ? __( 'Pensé pour les professionnels en poste', 'iam-certifications' ) : __( 'À qui s’adresse ce certificat', 'iam-certifications' ) ); ?></h2>
						<?php if ( $public_list ) : ?>
							<ul class="iam-lp-chips" data-iam-stagger>
								<?php foreach ( $public_list as $p ) : ?>
									<li><?php echo esc_html( $p ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
						<?php if ( $prerequis ) : ?>
							<div class="iam-lp-prereq">
								<p class="iam-lp-prereq__title"><?php echo iam_cert_icon( 'check' ); // phpcs:ignore ?> <?php esc_html_e( 'Prérequis', 'iam-certifications' ); ?></p>
								<ul class="iam-lp-ticks">
									<?php foreach ( $prerequis as $pr ) : ?>
										<li><?php echo esc_html( $pr ); ?></li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- ======================================================= SESSIONS -->
		<?php if ( $is_sem ) : ?>
			<section class="iam-lp-sec iam-lp-sec--green" id="iam-sessions" aria-labelledby="iam-sessions-t">
				<div class="iam-lp-wrap">
					<header class="iam-lp-head iam-lp-head--split">
						<div>
							<span class="iam-lp-head__num"><?php echo esc_html( $num() ); ?></span>
							<p class="iam-lp-eyebrow"><?php esc_html_e( 'Calendrier', 'iam-certifications' ); ?></p>
							<h2 class="iam-lp-h2" id="iam-sessions-t" data-iam-reveal><?php echo esc_html( $sessions ? __( 'Choisissez votre session', 'iam-certifications' ) : __( 'Une session à votre rythme', 'iam-certifications' ) ); ?></h2>
						</div>
						<?php if ( $places ) : ?>
							<p class="iam-lp-head__aside"><?php echo esc_html( sprintf( __( 'Groupes limités à %d participants : les inscriptions sont prises dans l’ordre d’arrivée.', 'iam-certifications' ), $places ) ); ?></p>
						<?php endif; ?>
					</header>
					<div class="iam-lp-sessions" data-iam-stagger>
						<?php foreach ( $sessions as $s ) : ?>
							<a class="iam-lp-ticket" href="#iam-candidature" data-iam-session="<?php echo esc_attr( trim( $s['dates'] . ( $s['lieu'] ? ' — ' . $s['lieu'] : '' ) ) ); ?>">
								<span class="iam-lp-ticket__date"><?php echo iam_cert_icon( 'calendar' ); // phpcs:ignore ?> <?php echo esc_html( $s['dates'] ); ?></span>
								<?php if ( $s['lieu'] ) : ?>
									<span class="iam-lp-ticket__place"><?php echo iam_cert_icon( 'pin' ); // phpcs:ignore ?> <?php echo esc_html( $s['lieu'] ); ?></span>
								<?php endif; ?>
								<span class="iam-lp-ticket__cta"><?php esc_html_e( 'Réserver', 'iam-certifications' ); ?> <?php echo iam_cert_icon( 'arrow' ); // phpcs:ignore ?></span>
							</a>
						<?php endforeach; ?>
						<a class="iam-lp-ticket iam-lp-ticket--intra" href="#iam-candidature" data-iam-session="<?php esc_attr_e( 'Session intra-entreprise', 'iam-certifications' ); ?>">
							<span class="iam-lp-ticket__date"><?php echo iam_cert_icon( 'briefcase' ); // phpcs:ignore ?> <?php esc_html_e( 'En intra-entreprise', 'iam-certifications' ); ?></span>
							<span class="iam-lp-ticket__place"><?php esc_html_e( 'Dans vos locaux ou sur un campus IAM, aux dates de votre choix, à Dakar comme en région.', 'iam-certifications' ); ?></span>
							<span class="iam-lp-ticket__cta"><?php esc_html_e( 'Demander un devis', 'iam-certifications' ); ?> <?php echo iam_cert_icon( 'arrow' ); // phpcs:ignore ?></span>
						</a>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- ====================================================== DÉBOUCHÉS -->
		<?php if ( $debouches ) : ?>
			<section class="iam-lp-sec iam-lp-sec--green" id="iam-debouches" aria-labelledby="iam-debouches-t">
				<div class="iam-lp-wrap">
					<header class="iam-lp-head">
						<span class="iam-lp-head__num"><?php echo esc_html( $num() ); ?></span>
						<p class="iam-lp-eyebrow"><?php esc_html_e( 'Débouchés', 'iam-certifications' ); ?></p>
						<h2 class="iam-lp-h2" id="iam-debouches-t" data-iam-reveal><?php esc_html_e( 'Et après ? Les métiers visés', 'iam-certifications' ); ?></h2>
					</header>
					<ul class="iam-lp-jobs" data-iam-stagger>
						<?php foreach ( $debouches as $job ) : ?>
							<li><span class="iam-lp-jobs__icon"><?php echo iam_cert_icon( 'briefcase' ); // phpcs:ignore ?></span><?php echo esc_html( $job ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
		<?php endif; ?>

		<!-- ======================================== CERTIFICATION / LIVRABLES -->
		<?php if ( $has_certif ) : ?>
			<section class="iam-lp-sec iam-lp-sec--sand" id="iam-certification" aria-labelledby="iam-certification-t">
				<div class="iam-lp-wrap iam-lp-certif">
					<div class="iam-lp-certif__text">
						<span class="iam-lp-head__num"><?php echo esc_html( $num() ); ?></span>
						<p class="iam-lp-eyebrow"><?php echo esc_html( $is_sem ? __( 'Livrables & attestation', 'iam-certifications' ) : __( 'Évaluation & certification', 'iam-certifications' ) ); ?></p>
						<h2 class="iam-lp-h2" id="iam-certification-t" data-iam-reveal><?php echo esc_html( $is_sem ? __( 'Vous repartez avec du concret', 'iam-certifications' ) : __( 'Un certificat qui se mérite', 'iam-certifications' ) ); ?></h2>

						<?php if ( $livrables ) : ?>
							<ul class="iam-lp-deliver" data-iam-stagger>
								<?php foreach ( $livrables as $l ) : ?>
									<li><span class="iam-lp-deliver__icon"><?php echo iam_cert_icon( 'package' ); // phpcs:ignore ?></span><?php echo esc_html( $l ); ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( ! $is_sem && $projet && ! $has_programme ) : ?>
							<p class="iam-lp-p"><strong><?php esc_html_e( 'Projet final :', 'iam-certifications' ); ?></strong> <?php echo esc_html( $projet ); ?></p>
						<?php endif; ?>

						<?php if ( $evaluation ) : ?>
							<div class="iam-lp-p"><?php echo wpautop( esc_html( $evaluation ) ); // phpcs:ignore ?></div>
						<?php endif; ?>
					</div>

					<div class="iam-lp-diploma-wrap" aria-hidden="true">
						<div class="iam-lp-diploma" data-iam-tilt>
							<span class="iam-lp-diploma__corner iam-lp-diploma__corner--tl"></span>
							<span class="iam-lp-diploma__corner iam-lp-diploma__corner--br"></span>
							<img class="iam-lp-diploma__logo" src="<?php echo esc_url( iam_cert_logo_url() ); ?>" alt="" width="150" height="150" loading="lazy" decoding="async">
							<p class="iam-lp-diploma__kind"><?php echo esc_html( $is_sem ? __( 'Attestation de réussite', 'iam-certifications' ) : __( 'Certificat professionnel', 'iam-certifications' ) ); ?></p>
							<p class="iam-lp-diploma__to"><?php esc_html_e( 'décerné à', 'iam-certifications' ); ?></p>
							<p class="iam-lp-diploma__name"><?php esc_html_e( 'Votre nom', 'iam-certifications' ); ?></p>
							<p class="iam-lp-diploma__title"><?php echo esc_html( $titre_main ); ?></p>
							<div class="iam-lp-diploma__foot">
								<span class="iam-lp-diploma__sign"></span>
								<span class="iam-lp-diploma__seal"><?php echo iam_cert_icon( 'award' ); // phpcs:ignore ?></span>
								<span class="iam-lp-diploma__sign"></span>
							</div>
							<span class="iam-lp-diploma__shine"></span>
						</div>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- ===================================================== CONTENU LIBRE -->
		<?php if ( trim( get_the_content() ) ) : ?>
			<section class="iam-lp-sec">
				<div class="iam-lp-wrap iam-lp-narrow">
					<div class="iam-lp-prose"><?php the_content(); ?></div>
				</div>
			</section>
		<?php endif; ?>

		<!-- ================================================== INVESTISSEMENT -->
		<?php if ( $cout ) : ?>
			<?php
			$inclus = array_filter(
				array(
					trim( $duree . ( '' !== $heures ? ' · ' . $heures . ' h' : '' ), ' ·' ) ? sprintf( /* translators: %s : durée */ __( 'Formation complète : %s', 'iam-certifications' ), trim( $duree . ( '' !== $heures ? ' · ' . $heures . ' h' : '' ), ' ·' ) ) : '',
					$format,
					$is_sem
						? ( $jours ? sprintf( _n( '%d jour de programme détaillé', '%d jours de programme détaillé', count( $jours ), 'iam-certifications' ), count( $jours ) ) : '' )
						: ( $modules ? sprintf( _n( '%d module et tous ses supports', '%d modules et tous leurs supports', count( $modules ), 'iam-certifications' ), count( $modules ) ) : '' ),
					$elearning,
					$evaluation ? ( $is_sem ? __( 'Attestation de réussite après évaluation', 'iam-certifications' ) : __( 'Certificat délivré après évaluation', 'iam-certifications' ) ) : '',
				)
			);
			?>
			<section class="iam-lp-price" id="iam-tarif">
				<span class="iam-lp-price__glow" aria-hidden="true"></span>
				<div class="iam-lp-wrap iam-lp-price__grid">
					<div class="iam-lp-price__main" data-iam-reveal>
						<p class="iam-lp-eyebrow"><?php esc_html_e( 'Investissement', 'iam-certifications' ); ?></p>
						<h2 class="iam-lp-h2"><?php esc_html_e( 'Un tarif clair, tout compris.', 'iam-certifications' ); ?></h2>

						<?php if ( $prix_montant ) : ?>
							<p class="iam-lp-price__amount">
								<span class="iam-lp-price__num" data-iam-price="<?php echo (int) $prix_montant; ?>"><?php echo esc_html( number_format_i18n( $prix_montant ) ); ?></span>
								<span class="iam-lp-price__cur"><?php echo esc_html( $prix_devise ); ?></span>
							</p>
							<?php if ( $prix_note ) : ?>
								<p class="iam-lp-price__note"><?php echo esc_html( ucfirst( $prix_note ) ); ?></p>
							<?php endif; ?>
						<?php else : ?>
							<div class="iam-lp-price__text"><?php echo wpautop( esc_html( $cout ) ); // phpcs:ignore ?></div>
						<?php endif; ?>

						<div class="iam-lp-price__ctas">
							<a class="iam-lp-btn iam-lp-price__cta" href="#iam-candidature" data-iam-magnetic>
								<?php echo esc_html( $cta ); ?>
								<span class="iam-lp-btn__icon"><?php echo iam_cert_icon( 'arrow' ); // phpcs:ignore ?></span>
							</a>
							<?php if ( $brochure ) : ?>
								<a class="iam-lp-price__link" href="<?php echo esc_url( $brochure ); ?>" download><?php echo iam_cert_icon( 'download' ); // phpcs:ignore ?><?php esc_html_e( 'Télécharger la brochure', 'iam-certifications' ); ?></a>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( $inclus ) : ?>
						<div class="iam-lp-price__card" data-iam-tilt>
							<p class="iam-lp-price__card-title"><?php echo iam_cert_icon( 'check' ); // phpcs:ignore ?><?php esc_html_e( 'Ce qui est inclus', 'iam-certifications' ); ?></p>
							<ul class="iam-lp-price__list" data-iam-stagger>
								<?php foreach ( $inclus as $ligne ) : ?>
									<li><span class="iam-lp-price__tick" aria-hidden="true"><?php echo iam_cert_icon( 'check' ); // phpcs:ignore ?></span><?php echo esc_html( $ligne ); ?></li>
								<?php endforeach; ?>
							</ul>
							<span class="iam-lp-price__shine" aria-hidden="true"></span>
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<!-- ================================================ TÉLÉCHARGEMENTS -->
		<?php if ( $brochure || $dossier ) : ?>
			<section class="iam-lp-dl">
				<div class="iam-lp-wrap iam-lp-dl__inner">
					<p class="iam-lp-dl__title"><?php esc_html_e( 'Tout le programme, à emporter.', 'iam-certifications' ); ?></p>
					<div class="iam-lp-dl__btns">
						<?php if ( $brochure ) : ?>
							<a class="iam-lp-btn iam-lp-btn--green" href="<?php echo esc_url( $brochure ); ?>" download><span class="iam-lp-btn__icon"><?php echo iam_cert_icon( 'download' ); // phpcs:ignore ?></span><?php esc_html_e( 'Brochure', 'iam-certifications' ); ?></a>
						<?php endif; ?>
						<?php if ( $dossier ) : ?>
							<a class="iam-lp-btn iam-lp-btn--outline" href="<?php echo esc_url( $dossier ); ?>" download><span class="iam-lp-btn__icon"><?php echo iam_cert_icon( 'download' ); // phpcs:ignore ?></span><?php echo esc_html( $is_sem ? __( 'Bulletin d’inscription', 'iam-certifications' ) : __( 'Dossier de candidature', 'iam-certifications' ) ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- ==================================================== CANDIDATURE -->
		<section class="iam-lp-apply" id="iam-candidature">
			<div class="iam-lp-wrap iam-lp-apply__grid">
				<div class="iam-lp-apply__intro">
					<p class="iam-lp-eyebrow"><?php echo esc_html( $is_sem ? __( 'Inscription', 'iam-certifications' ) : __( 'Candidature', 'iam-certifications' ) ); ?></p>
					<h2 class="iam-lp-h2"><?php echo esc_html( $is_sem ? __( 'Réservez votre place', 'iam-certifications' ) : __( 'Candidatez en 3 minutes', 'iam-certifications' ) ); ?></h2>
					<p class="iam-lp-apply__selected" data-iam-session-label hidden></p>
					<ol class="iam-lp-apply__steps">
						<?php
						$etapes = $is_sem
							? array(
								__( 'Vous envoyez votre demande d’inscription', 'iam-certifications' ),
								__( 'Nous confirmons la session et la convention', 'iam-certifications' ),
								__( 'Vous suivez les 3 jours et repartez avec vos livrables', 'iam-certifications' ),
							)
							: array(
								__( 'Vous déposez votre candidature', 'iam-certifications' ),
								__( 'Un conseiller vous rappelle pour un diagnostic', 'iam-certifications' ),
								__( 'Vous démarrez le parcours à la prochaine rentrée', 'iam-certifications' ),
							);
						foreach ( $etapes as $e ) :
							?>
							<li><?php echo esc_html( $e ); ?></li>
						<?php endforeach; ?>
					</ol>
					<div class="iam-lp-contact">
						<p class="iam-lp-contact__title"><?php esc_html_e( 'Une question ? Parlez à', 'iam-certifications' ); ?> <?php echo esc_html( $contact['nom'] ); ?></p>
						<a href="mailto:<?php echo esc_attr( $contact['email'] ); ?>"><span><?php echo iam_cert_icon( 'mail' ); // phpcs:ignore ?></span><?php echo esc_html( $contact['email'] ); ?></a>
						<a href="tel:<?php echo esc_attr( $contact['tel_lien'] ); ?>"><span><?php echo iam_cert_icon( 'phone' ); // phpcs:ignore ?></span><?php echo esc_html( $contact['tel'] ); ?></a>
						<?php if ( $rdv_url ) : ?>
							<a href="<?php echo esc_url( $rdv_url ); ?>" target="_blank" rel="noopener"><span><?php echo iam_cert_icon( 'calendar' ); // phpcs:ignore ?></span><?php esc_html_e( 'Prendre rendez-vous', 'iam-certifications' ); ?></a>
						<?php endif; ?>
					</div>
				</div>
				<div class="iam-lp-apply__card">
					<?php echo iam_cert_render_form( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</section>

		<!-- ============================================= FORMATIONS PROCHES -->
		<?php
		$related = get_posts(
			array(
				'post_type'      => IAM_CERT_CPT,
				'posts_per_page' => 3,
				'post__not_in'   => array( $post_id ),
				'orderby'        => 'menu_order title',
				'order'          => 'ASC',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					$is_sem
						? array(
							'key'   => '_iam_type',
							'value' => 'seminaire',
						)
						: array(
							'relation' => 'OR',
							array(
								'key'   => '_iam_type',
								'value' => 'certificat',
							),
							array(
								'key'     => '_iam_type',
								'compare' => 'NOT EXISTS',
							),
						),
				),
				'tax_query'      => $domaine ? array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
					array(
						'taxonomy' => IAM_CERT_TAX_DOMAINE,
						'terms'    => $domaine->term_id,
					),
				) : array(),
			)
		);
		if ( $related ) :
			?>
			<section class="iam-lp-sec iam-lp-related">
				<div class="iam-lp-wrap">
					<header class="iam-lp-head iam-lp-head--split">
						<h2 class="iam-lp-h2"><?php echo esc_html( $is_sem ? __( 'Dans la même collection', 'iam-certifications' ) : __( 'Vous aimerez aussi', 'iam-certifications' ) ); ?></h2>
						<a class="iam-lp-link" href="<?php echo esc_url( get_post_type_archive_link( IAM_CERT_CPT ) ); ?>"><?php esc_html_e( 'Tout le catalogue', 'iam-certifications' ); ?> <?php echo iam_cert_icon( 'arrow' ); // phpcs:ignore ?></a>
					</header>
					<div class="iam-cert-grid" style="--iam-cols:3">
						<?php
						foreach ( $related as $rel ) :
							iam_cert_get_template( 'card.php', array( 'post' => $rel, 'atts' => array( 'max_tools' => 4 ) ) );
						endforeach;
						?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<!-- Barre d'action mobile -->
		<div class="iam-lp-mbar" hidden>
			<span class="iam-lp-mbar__title"><?php echo esc_html( $titre ); ?></span>
			<a class="iam-lp-btn iam-lp-btn--lime iam-lp-btn--sm" href="#iam-candidature"><?php echo esc_html( $cta ); ?></a>
		</div>
	</div>
	<?php
endwhile;

get_footer();
