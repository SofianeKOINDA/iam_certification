<?php
/**
 * Template : carte de certification (disposition inspirée de la référence 2,
 * branding Groupe IAM via les tokens --ckd-*).
 *
 * Variables disponibles :
 * @var WP_Post $post Certification.
 * @var array   $atts Attributs du shortcode.
 *
 * Surchargeable : copier ce fichier dans « votre-theme/iam-certifications/card.php ».
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id   = $post->ID;
$permalink = get_permalink( $post_id );
$duree     = iam_cert_duree_label( $post_id );
$public    = get_post_meta( $post_id, '_iam_public', true );
$type      = iam_cert_type( $post_id );
$cta       = 'seminaire' === $type ? __( 'Voir le programme', 'iam-certifications' ) : __( 'Voir le parcours', 'iam-certifications' );

// Domaine principal (badge).
$domaines     = get_the_terms( $post_id, IAM_CERT_TAX_DOMAINE );
$domaine_slug = '';
$domaine_nom  = '';
if ( $domaines && ! is_wp_error( $domaines ) ) {
	$domaine_slug = $domaines[0]->slug;
	$domaine_nom  = $domaines[0]->name;
}

// Outils (badges), plafonnés pour garder la carte compacte.
$outils = get_the_terms( $post_id, IAM_CERT_TAX_OUTIL );
$outils = ( $outils && ! is_wp_error( $outils ) ) ? $outils : array();

$max_tools = isset( $atts['max_tools'] ) ? (int) $atts['max_tools'] : 6;
$reste     = 0;
if ( $max_tools > 0 && count( $outils ) > $max_tools ) {
	$reste  = count( $outils ) - $max_tools;
	$outils = array_slice( $outils, 0, $max_tools );
}
?>
<article class="iam-cert-card iam-cert-card--<?php echo esc_attr( $type ); ?>" data-type="<?php echo esc_attr( $type ); ?>" data-domaine="<?php echo esc_attr( $domaine_slug ); ?>">

	<a class="iam-cert-card__media" href="<?php echo esc_url( $permalink ); ?>"
		aria-label="<?php echo esc_attr( get_the_title( $post_id ) ); ?>">
		<?php if ( has_post_thumbnail( $post_id ) ) : ?>
			<?php echo get_the_post_thumbnail( $post_id, 'large', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title( $post_id ) ) ) ); ?>
		<?php else : ?>
			<span class="iam-cert-card__media-fallback" aria-hidden="true"></span>
		<?php endif; ?>

		<span class="iam-cert-card__type"><?php echo esc_html( 'seminaire' === $type ? __( 'Séminaire', 'iam-certifications' ) : __( 'Certificat', 'iam-certifications' ) ); ?></span>

		<?php if ( $domaine_nom ) : ?>
			<span class="iam-cert-card__badge"><?php echo esc_html( $domaine_nom ); ?></span>
		<?php endif; ?>

		<?php if ( $duree ) : ?>
			<span class="iam-cert-card__duree"><?php echo esc_html( $duree ); ?></span>
		<?php endif; ?>
	</a>

	<div class="iam-cert-card__body">
		<h3 class="iam-cert-card__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
		</h3>

		<?php if ( has_excerpt( $post_id ) ) : ?>
			<p class="iam-cert-card__excerpt"><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
		<?php endif; ?>

		<?php if ( $public ) : ?>
			<p class="iam-cert-card__meta">
				<span class="iam-cert-card__icon"><?php echo iam_cert_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span><strong><?php esc_html_e( 'Pour :', 'iam-certifications' ); ?></strong> <?php echo esc_html( $public ); ?></span>
			</p>
		<?php endif; ?>

		<?php if ( $outils ) : ?>
			<ul class="iam-cert-card__tools">
				<?php foreach ( $outils as $outil ) : ?>
					<li class="iam-cert-card__tool"><?php echo esc_html( $outil->name ); ?></li>
				<?php endforeach; ?>
				<?php if ( $reste > 0 ) : ?>
					<li class="iam-cert-card__tool iam-cert-card__tool--plus"><?php echo esc_html( sprintf( '+%d', $reste ) ); ?></li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>

		<div class="iam-cert-card__footer">
			<a class="iam-cert-btn" href="<?php echo esc_url( $permalink ); ?>">
				<?php echo esc_html( $cta ); ?>
				<span class="iam-cert-btn__icon"><?php echo iam_cert_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			</a>
		</div>
	</div>
</article>

