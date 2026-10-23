<?php
/**
 * Compléments v1.3 tirés des catalogues (certificats 2026, séminaires 2026-2027) :
 * - « fill » : renseigné seulement si le champ est vide ;
 * - « fix »  : [ancienne valeur, nouvelle] — corrigé seulement si la fiche porte
 *   encore la valeur de l'import d'origine (une saisie manuelle n'est jamais écrasée).
 * Sans prix ni partenaire.
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	'social-media-marketing-social-selling'     => array(
		'fill' => array(
			'objectifs'    => 'Faire des réseaux sociaux un levier de notoriété et de vente
Bâtir une stratégie de contenu IA-first (formats TikTok, Reels, LinkedIn, UGC)
Prospecter et vendre directement sur les réseaux (social selling)
Piloter la publicité sociale et le social commerce
Mesurer le ROI de sa présence sociale',
			'projet_final' => 'Audit complet d\'une présence sociale et simulation de campagne budgétée, restitués en pitch devant jury.',
			'rythme'       => 'Compatible avec une activité professionnelle, avec sessions de coaching',
		),
	),
	'marketing-automation-growth-hacking'       => array(
		'fill' => array(
			'objectifs'    => 'Concevoir une machine d\'acquisition automatisée autour du tunnel AARRR
Mettre en place un nurturing multicanal personnalisé à grande échelle
Installer une démarche d\'expérimentation systématique (A/B tests, priorisation ICE/PIE)
Piloter le scaling par la donnée : LTV, churn, attribution',
			'projet_final' => 'Growth machine complète, modélisée et soutenue devant jury.',
			'rythme'       => 'Compatible avec une activité professionnelle, avec sessions de coaching',
		),
		'fix'  => array(
			'evaluation' => array(
				'QCM + travaux pratiques + session de coaching à chaque module ; projet final avec répétition de soutenance et défense devant jury Le certificat est délivré après évaluation, jamais par simple complétion du parcours.',
				'QCM, travaux pratiques et session de coaching à chaque module ; projet final avec répétition de soutenance et défense devant jury. Le certificat est délivré après évaluation, jamais par simple complétion du parcours.',
			),
		),
	),
	'no-code-marketing-salestech'               => array(
		'fill' => array(
			'objectifs'    => 'Construire des landing pages et des sites marketing sans code
Concevoir un CRM sur mesure et des portails clients sécurisés
Créer des applications mobiles sans code
Automatiser la prospection et les workflows commerciaux',
			'projet_final' => 'MVP marketing & sales opérationnel, documenté et pitché avec un argumentaire de valeur métier.',
			'rythme'       => 'Compatible avec une activité professionnelle, avec sessions de coaching',
		),
	),
	'content-marketing-strategie-editoriale-ia' => array(
		'fill' => array(
			'objectifs'    => 'Bâtir une stratégie de contenu qui convertit
Combiner IA générative et valeur ajoutée humaine (human in the loop)
Maîtriser le SEO sémantique et la production multi-format
Personnaliser les contenus à l\'échelle en restant conforme (droit d\'auteur, RGPD)',
			'projet_final' => 'Stratégie éditoriale sur 90 jours, trois productions IA et pitch final avec démonstration.',
			'rythme'       => 'Compatible avec une activité professionnelle, avec sessions de coaching',
		),
	),
	'crm-social-e-crm'                          => array(
		'fill' => array(
			'objectifs'    => 'Maîtriser la relation client omnicanale et les données first-party
Animer la relation client sur les réseaux sociaux (Social CRM)
Mettre en place la segmentation et la personnalisation prédictive
Déployer des parcours automatisés et un portail self-care conformes au RGPD',
			'projet_final' => 'Architecture CRM, prototype no code et trois parcours automatisés, avec tableau de bord, soutenus devant jury.',
			'rythme'       => 'Compatible avec une activité professionnelle, avec sessions de coaching',
		),
	),
	'consultant-fonctionnel-odoo-junior'        => array(
		'fill' => array(
			'objectifs'    => 'Paramétrer les cycles commerciaux, logistiques, financiers (SYSCOHADA) et RH d\'Odoo
Personnaliser Odoo en low-code avec Studio
Adopter la posture et la méthodologie du consultant : cadrage, cahier des charges, recette
Mener un projet de déploiement de bout en bout',
			'projet_final' => 'Projet tutoré complet sur le cas Sénégal Distribution SA, puis certification finale Consultant Fonctionnel Odoo Junior.',
			'rythme'       => 'Compatible avec une activité professionnelle, avec sessions de coaching',
		),
	),
	'adapt-ia-adopter-lia-en-entreprise'        => array(
		'fill' => array(
			'objectifs'    => 'Intégrer l\'IA de façon utile, souveraine et conforme
Maîtriser le prompting, l\'automatisation et les agents
Déployer une IA locale et un RAG sur ses documents internes
Conduire le changement, ou exercer le métier de consultant IA (parcours B)',
			'projet_final' => 'Évaluation finale sur un cas réel d\'entreprise, après un livrable d\'application à chaque module.',
			'rythme'       => 'Blended learning avec coaching, livrables appliqués en entreprise',
		),
		'fix'  => array(
			'format'      => array(
				'100 % en ligne',
				'Blended learning et coaching',
			),
			'duree_texte' => array(
				'12 semaines',
				'10 à 12 semaines',
			),
		),
	),
	'knowledge-management-rh'                   => array(
		'fill' => array(
			'objectifs'    => 'Faire des RH les architectes du savoir de l\'organisation
Capitaliser les connaissances critiques avant qu\'elles ne partent
Réduire le coût de l\'ignorance
Intégrer le Knowledge Management au cycle de vie du collaborateur (RH 4.0)',
			'projet_final' => 'Cartographie des connaissances critiques et plan d\'action KM de sa propre organisation.',
		),
		'fix'  => array(
			'format'     => array(
				'100 % en ligne',
				'Ateliers collaboratifs, intra-entreprise possible',
			),
			'public'     => array(
				'RRH, HR Business Partners, responsables formation, talent managers',
				'RRH, HR Business Partners, responsables formation, talent managers, responsables RSE/QVCT',
			),
			'evaluation' => array(
				'Exercices et ateliers évalués en continu ; livrable final : la cartographie des connaissances critiques et le plan d\'action KM de sa propre organisation. Le certificat est délivré après évaluation, jamais par simple complétion du parcours. Nous consulter Format atelier, déployable en intra-entreprise. Tarification sur devis.',
				'Exercices et ateliers évalués en continu (études de cas, World Café, diagnostics flash) ; livrable final : la cartographie des connaissances critiques et le plan d\'action KM de sa propre organisation. Le certificat est délivré après évaluation, jamais par simple complétion du parcours.',
			),
		),
	),
	'cedla-edtech-digital-learning-afrique'     => array(
		'fill' => array(
			'objectifs' => 'Concevoir des dispositifs de formation digitale adaptés aux réalités africaines
Déployer et administrer une plateforme LMS, y compris hors ligne
Produire des contenus multimédia mobile-first, accessibles et légers
Piloter la qualité, les données d\'apprentissage et le modèle économique',
			'rythme'    => 'Parcours long adossable à un semestre, avec mission terrain',
		),
	),
);
