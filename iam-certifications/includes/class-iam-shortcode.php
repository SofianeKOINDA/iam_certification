<?php
/**
 * Shortcode [iam_certifications] : liste des certifications en grille de cartes.
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enregistre et rend le shortcode de liste.
 */
class IAM_Cert_Shortcode {

	/**
	 * Enregistre les hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_shortcode( 'iam_certifications', array( $this, 'render' ) );
	}

	/**
	 * Rend le shortcode.
	 *
	 * @param array|string $atts Attributs.
	 * @return string
	 */
	public function render( $atts ) {
		$atts = shortcode_atts(
			array(
				'type'             => '',      // « certificat » ou « seminaire » (vide = tous).
				'domaine'          => '',      // Slug(s) de domaine, séparés par des virgules.
				'outil'            => '',      // Slug(s) d'outil.
				'ids'              => '',      // Formations précises (ID séparés par des virgules), dans cet ordre.
				'limit'            => -1,      // Nombre de certifications (-1 = toutes).
				'columns'          => 3,       // Colonnes sur grand écran (1 à 4).
				'featured'         => '',      // "true" pour ne montrer que les mises en avant.
				'orderby'          => 'menu_order title',
				'order'            => 'ASC',
				'group_by_domaine' => '',      // "true" : titres de section par domaine (façon site actuel).
				'filters'          => '',      // "true" : barre de filtre par domaine (côté client).
				'max_tools'        => 6,       // Nombre d'outils affichés sur la carte avant « +N ».
			),
			$atts,
			'iam_certifications'
		);

		// Chargement des assets à la demande.
		wp_enqueue_style( 'iam-cert-front' );
		wp_enqueue_script( 'iam-cert-front' );
		wp_enqueue_script( 'iam-cert-animations' );

		$query = $this->build_query( $atts );

		if ( ! $query->have_posts() ) {
			return '<p class="iam-cert-empty">' . esc_html__( 'Aucune certification disponible pour le moment.', 'iam-certifications' ) . '</p>';
		}

		$group = ( 'true' === $atts['group_by_domaine'] );

		ob_start();
		echo '<div class="iam-cert">';

		if ( 'true' === $atts['filters'] && ! $group ) {
			$this->render_filter_bar( $query );
		}

		if ( $group ) {
			$this->render_grouped( $query, $atts );
		} else {
			$this->render_grid( $query, $atts );
		}

		echo '</div>';
		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Construit la WP_Query à partir des attributs.
	 *
	 * @param array $atts Attributs nettoyés.
	 * @return WP_Query
	 */
	private function build_query( $atts ) {
		$args = array(
			'post_type'      => IAM_CERT_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => (int) $atts['limit'],
			'orderby'        => sanitize_text_field( $atts['orderby'] ),
			'order'          => 'DESC' === strtoupper( $atts['order'] ) ? 'DESC' : 'ASC',
			'no_found_rows'  => true,
		);

		// Formations choisies une à une : affichées dans l'ordre donné.
		$ids = array_filter( array_map( 'absint', explode( ',', (string) $atts['ids'] ) ) );
		if ( $ids ) {
			$args['post__in'] = $ids;
			$args['orderby']  = 'post__in';
		}

		$tax_query = array();

		if ( '' !== $atts['domaine'] ) {
			$tax_query[] = array(
				'taxonomy' => IAM_CERT_TAX_DOMAINE,
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', explode( ',', $atts['domaine'] ) ),
			);
		}

		if ( '' !== $atts['outil'] ) {
			$tax_query[] = array(
				'taxonomy' => IAM_CERT_TAX_OUTIL,
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', explode( ',', $atts['outil'] ) ),
			);
		}

		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}

		if ( $tax_query ) {
			$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		}

		$meta_query = array();

		if ( 'true' === $atts['featured'] ) {
			$meta_query[] = array(
				'key'   => '_iam_featured',
				'value' => '1',
			);
		}

		if ( 'seminaire' === $atts['type'] ) {
			$meta_query[] = array(
				'key'   => '_iam_type',
				'value' => 'seminaire',
			);
		} elseif ( 'certificat' === $atts['type'] ) {
			// Les fiches antérieures à la v1.2 n'ont pas de type : ce sont des certificats.
			$meta_query[] = array(
				'relation' => 'OR',
				array(
					'key'   => '_iam_type',
					'value' => 'certificat',
				),
				array(
					'key'     => '_iam_type',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		if ( $meta_query ) {
			$args['meta_query'] = $meta_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
		}

		return new WP_Query( $args );
	}

	/**
	 * Grille simple de cartes.
	 *
	 * @param WP_Query $query Résultats.
	 * @param array    $atts  Attributs.
	 * @return void
	 */
	private function render_grid( $query, $atts ) {
		$cols = max( 1, min( 4, (int) $atts['columns'] ) );
		printf( '<div class="iam-cert-grid" style="--iam-cols:%d;">', $cols );
		while ( $query->have_posts() ) {
			$query->the_post();
			$this->render_card( get_post(), $atts );
		}
		echo '</div>';
	}

	/**
	 * Cartes regroupées par domaine, avec un titre de section par domaine.
	 *
	 * @param WP_Query $query Résultats.
	 * @param array    $atts  Attributs.
	 * @return void
	 */
	private function render_grouped( $query, $atts ) {
		$cols     = max( 1, min( 4, (int) $atts['columns'] ) );
		$buckets  = array();
		$sans_dom = array();

		while ( $query->have_posts() ) {
			$query->the_post();
			$post  = get_post();
			$terms = get_the_terms( $post->ID, IAM_CERT_TAX_DOMAINE );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$term = $terms[0];
				if ( ! isset( $buckets[ $term->term_id ] ) ) {
					$buckets[ $term->term_id ] = array(
						'term'  => $term,
						'posts' => array(),
					);
				}
				$buckets[ $term->term_id ]['posts'][] = $post;
			} else {
				$sans_dom[] = $post;
			}
		}

		foreach ( $buckets as $bucket ) {
			printf(
				'<section class="iam-cert-section"><h2 class="iam-cert-section__title">%s</h2>',
				esc_html( $bucket['term']->name )
			);
			$count = count( $bucket['posts'] );
			printf(
				'<p class="iam-cert-section__count">%s</p>',
				esc_html( sprintf( _n( '%d programme', '%d programmes', $count, 'iam-certifications' ), $count ) )
			);
			printf( '<div class="iam-cert-grid" style="--iam-cols:%d;">', $cols );
			foreach ( $bucket['posts'] as $post ) {
				setup_postdata( $GLOBALS['post'] = $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
				$this->render_card( $post, $atts );
			}
			echo '</div></section>';
		}

		if ( $sans_dom ) {
			printf( '<section class="iam-cert-section"><div class="iam-cert-grid" style="--iam-cols:%d;">', $cols );
			foreach ( $sans_dom as $post ) {
				setup_postdata( $GLOBALS['post'] = $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found
				$this->render_card( $post, $atts );
			}
			echo '</div></section>';
		}
	}

	/**
	 * Barre de filtre par domaine (filtrage côté client via data-attributs).
	 *
	 * @param WP_Query $query Résultats (pour lister les domaines présents).
	 * @return void
	 */
	private function render_filter_bar( $query ) {
		$terms = array();
		foreach ( $query->posts as $post ) {
			$t = get_the_terms( $post->ID, IAM_CERT_TAX_DOMAINE );
			if ( $t && ! is_wp_error( $t ) ) {
				foreach ( $t as $term ) {
					$terms[ $term->slug ] = $term->name;
				}
			}
		}

		if ( count( $terms ) < 2 ) {
			return;
		}

		echo '<div class="iam-cert-filters" role="tablist">';
		printf(
			'<button type="button" class="iam-cert-filter is-active" data-domaine="">%s</button>',
			esc_html__( 'Tous', 'iam-certifications' )
		);
		foreach ( $terms as $slug => $name ) {
			printf(
				'<button type="button" class="iam-cert-filter" data-domaine="%s">%s</button>',
				esc_attr( $slug ),
				esc_html( $name )
			);
		}
		echo '</div>';
	}

	/**
	 * Rend une carte via le template (surchargeable par le thème).
	 *
	 * @param WP_Post $post Certification.
	 * @param array   $atts Attributs du shortcode.
	 * @return void
	 */
	private function render_card( $post, $atts ) {
		iam_cert_get_template(
			'card.php',
			array(
				'post' => $post,
				'atts' => $atts,
			)
		);
	}
}
