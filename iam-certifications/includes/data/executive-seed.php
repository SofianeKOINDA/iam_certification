<?php
/**
 * Données v1.6 : catalogue « Formations professionnelles — IAM Executive »,
 * domaine 01 Data & Business Intelligence (3 formations).
 *
 * Repris du catalogue : titre, accroche (« En bref »), objectifs, compétences
 * clés (outils), public cible. Complété pour rester cohérent avec les
 * séminaires existants : durée, format, programme jour par jour, prérequis,
 * livrables, évaluation, infos pratiques. Sans prix ni date de session.
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$infos_pratiques = 'Sessions inter-entreprises à Dakar, calendrier communiqué sur demande
Organisable en intra, dans vos locaux ou sur un campus IAM, à Dakar comme en région
Format hybride : chaque session se suit en salle ou à distance, en classe virtuelle synchronisée
Venir avec son ordinateur portable : les jeux de données d\'exercice sont fournis
Pauses café et déjeuner incluses (8h30 – 17h30)
Formation en français
Accès 3 mois à la plateforme eLearning et à la communauté d\'apprenants';

return array(
	array(
		'type'             => 'seminaire',
		'slug'             => 'power-bi-tableaux-de-bord-decisionnels',
		'titre'            => 'Power BI : concevoir des tableaux de bord décisionnels',
		'domaine'          => 'Data & Business Intelligence',
		'hero'             => 'Une formation pratique pour passer de données dispersées à des tableaux de bord fiables et partagés. Les participants construisent un rapport complet, de la connexion aux sources jusqu\'à la publication.',
		'heures'           => '24',
		'duree_texte'      => '3 jours',
		'niveau'           => 'intermédiaire',
		'format'           => 'Hybride : présentiel Dakar ou intra, et à distance',
		'participants_max' => '14',
		'pratique_pct'     => '75',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois à la plateforme eLearning',
		'public'           => 'Contrôleurs de gestion, analystes, responsables reporting et managers souhaitant piloter par les données.',
		'prerequis'        => 'Pratique courante d\'Excel (tableaux, filtres, tableaux croisés dynamiques). Aucune connaissance de Power BI n\'est requise.',
		'objectifs'        => 'Importer et préparer des données issues de sources multiples
Construire un modèle de données cohérent
Concevoir des visuels lisibles et interactifs
Publier et partager des rapports avec les équipes',
		'jours'            => array(
			array(
				'titre'      => 'Connecter et préparer les données',
				'matin'      => 'Panorama de Power BI (Desktop, Service, mobile) ; connexion aux sources : Excel, CSV, bases de données, SharePoint.',
				'apres_midi' => 'Power Query : nettoyer, transformer, fusionner et ajouter des tables ; requêtes réutilisables et actualisables (atelier sur données réelles anonymisées).',
			),
			array(
				'titre'      => 'Modéliser et calculer',
				'matin'      => 'Modèle en étoile : tables de faits et de dimensions, relations, table de dates ; erreurs de modélisation les plus fréquentes.',
				'apres_midi' => 'Mesures DAX essentielles : CALCULATE, filtres, cumuls, comparaisons N-1 ; colonnes calculées ou mesures, savoir choisir (atelier).',
			),
			array(
				'titre'      => 'Visualiser, publier, partager',
				'matin'      => 'Choisir le bon visuel ; navigation, segments, info-bulles et drill-down ; règles de lisibilité d\'un tableau de bord de pilotage.',
				'apres_midi' => 'Publication sur le service Power BI, espaces de travail, actualisation planifiée et droits d\'accès ; atelier final : rapport complet présenté au groupe.',
			),
		),
		'livrables'        => 'Rapport Power BI complet, de la source à la publication
Modèle de données et bibliothèque de mesures DAX réutilisables
Check-list de conception d\'un tableau de bord',
		'evaluation'       => 'Évaluation sur cas pratique : construction et présentation d\'un tableau de bord. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'Power BI',
			'Power Query',
			'DAX',
			'Excel',
		),
		'infos_pratiques'  => $infos_pratiques,
	),
	array(
		'type'             => 'seminaire',
		'slug'             => 'analyse-de-donnees-pour-managers',
		'titre'            => 'Analyse de données pour managers',
		'domaine'          => 'Data & Business Intelligence',
		'hero'             => 'Comprendre ce que disent réellement les chiffres pour décider plus vite et avec plus d\'assurance. Une approche sans jargon, centrée sur les usages du manager.',
		'heures'           => '16',
		'duree_texte'      => '2 jours',
		'niveau'           => 'débutant',
		'format'           => 'Hybride : présentiel Dakar ou intra, et à distance',
		'participants_max' => '16',
		'pratique_pct'     => '60',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois à la plateforme eLearning',
		'public'           => 'Managers, chefs de service, directeurs et cadres dirigeants.',
		'prerequis'        => 'Aucun prérequis technique ni statistique : la formation part des tableaux de bord que les participants reçoivent déjà.',
		'objectifs'        => 'Identifier les indicateurs utiles à son activité
Interpréter tendances, écarts et signaux faibles
Poser les bonnes questions aux équipes data',
		'jours'            => array(
			array(
				'titre'      => 'Lire et choisir ses indicateurs',
				'matin'      => 'Du chiffre à la décision : KPI, indicateurs de pilotage et indicateurs de vanité ; construire le tableau de bord utile à son activité (atelier).',
				'apres_midi' => 'Lire un tableau de bord avec esprit critique : moyennes trompeuses, effets d\'échelle, graphiques qui induisent en erreur ; exercices sur des rapports réels.',
			),
			array(
				'titre'      => 'Interpréter et décider',
				'matin'      => 'Tendances, saisonnalité, écarts au budget et signaux faibles ; corrélation n\'est pas causalité ; décider avec une donnée imparfaite.',
				'apres_midi' => 'Travailler avec les équipes data : formuler une demande claire, challenger une analyse, poser les bonnes questions ; atelier final : plan d\'indicateurs de son service.',
			),
		),
		'livrables'        => 'Plan d\'indicateurs de son service
Grille de lecture critique d\'un tableau de bord
Modèle de demande d\'analyse aux équipes data',
		'evaluation'       => 'Évaluation par quiz final et présentation du plan d\'indicateurs. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'Excel',
			'Power BI',
		),
		'infos_pratiques'  => $infos_pratiques,
	),
	array(
		'type'             => 'seminaire',
		'slug'             => 'data-visualisation-storytelling',
		'titre'            => 'Data visualisation & storytelling',
		'domaine'          => 'Data & Business Intelligence',
		'hero'             => 'Présenter les données de façon claire, juste et convaincante : choisir le bon graphique, construire le message et embarquer son auditoire.',
		'heures'           => '16',
		'duree_texte'      => '2 jours',
		'niveau'           => 'débutant',
		'format'           => 'Hybride : présentiel Dakar ou intra, et à distance',
		'participants_max' => '16',
		'pratique_pct'     => '70',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois à la plateforme eLearning',
		'public'           => 'Managers, contrôleurs de gestion, analystes, responsables d\'activité et équipes reporting.',
		'prerequis'        => 'Pratique courante d\'Excel ou de PowerPoint. Venir si possible avec un rapport ou une présentation chiffrée de son activité.',
		'objectifs'        => 'Choisir le graphique adapté à chaque message
Concevoir des visuels clairs, justes et accessibles
Construire un récit de données qui mène à la décision
Présenter des résultats chiffrés avec impact',
		'jours'            => array(
			array(
				'titre'      => 'Concevoir des visuels justes',
				'matin'      => 'Perception visuelle et principes de clarté : hiérarchie, couleur, désencombrement ; quel graphique pour quelle question.',
				'apres_midi' => 'Atelier « avant / après » : refondre des graphiques réels dans Excel ou Power BI ; erreurs qui trompent le lecteur et comment les éviter.',
			),
			array(
				'titre'      => 'Raconter et convaincre',
				'matin'      => 'Storytelling de données : du constat au message, structure d\'un récit, titres qui disent la conclusion, adapter le niveau de détail à l\'auditoire.',
				'apres_midi' => 'Atelier final : chaque participant présente une histoire de données tirée de son activité, avec retours du groupe et de l\'intervenant.',
			),
		),
		'livrables'        => 'Présentation chiffrée refondue, prête à l\'emploi
Guide de choix des graphiques
Trame de récit de données',
		'evaluation'       => 'Évaluation sur la présentation finale. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'Excel',
			'Power BI',
			'PowerPoint',
		),
		'infos_pratiques'  => $infos_pratiques,
	),
);
