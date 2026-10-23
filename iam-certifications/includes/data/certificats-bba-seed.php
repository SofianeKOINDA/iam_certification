<?php
/**
 * Données v1.7 : catalogue « Certificats professionnels 2025-2026 » de
 * l'IAM E-Learning Center — parcours diplômant BBA (19 certificats).
 *
 * Repris du catalogue : titre, cours (codes GES), durée (3 mois), format
 * (100 % en ligne). Prix : tarif du catalogue + 50 000 FCFA (demande du
 * Groupe IAM), soit 400 000 FCFA. Le reste (accroche, leçons, objectifs,
 * débouchés, projet, évaluation) est rédigé pour être au niveau des autres
 * certificats. Niveau déduit du code des cours : 1xxx débutant, 2xxx
 * intermédiaire, 3xxx avancé.
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$domaine    = 'Gestion & Management (BBA)';
$format     = '100 % en ligne';
$duree      = '3 mois';
$cout       = '400 000 FCFA pour le certificat complet (3 mois, 100 % en ligne).';
$rythme     = 'À votre rythme, environ 6 h par semaine';
$public     = 'Titulaires du BAC, étudiants, salariés et entrepreneurs qui veulent valider des compétences en gestion, ou engager pas à pas un parcours BBA';
$prerequis  = 'Niveau BAC. Un ordinateur ou un smartphone avec une connexion internet suffit : tous les cours sont en ligne.';
$evaluation = 'Quiz à la fin de chaque chapitre, devoirs notés par cours et examen final en ligne. Le certificat est délivré après évaluation, jamais par simple complétion du parcours. Il valide une partie du parcours diplômant BBA de l\'IAM.';

$cert = static function ( $c ) use ( $domaine, $format, $duree, $cout, $rythme, $public, $prerequis, $evaluation ) {
	return array_merge(
		array(
			'type'        => 'certificat',
			'domaine'     => $domaine,
			'heures'      => '',
			'duree_texte' => $duree,
			'format'      => $format,
			'rythme'      => $rythme,
			'public'      => $public,
			'prerequis'   => $prerequis,
			'evaluation'  => $evaluation,
			'cout'        => $cout,
			'outils'      => array(),
		),
		$c
	);
};

return array(
	$cert(
		array(
			'slug'         => 'certificat-fondamentaux-en-gestion',
			'titre'        => 'Fondamentaux en gestion',
			'niveau'       => 'débutant',
			'hero'         => 'Les trois piliers de toute entreprise en un seul certificat : comprendre comment on dirige, comment on compte et comment on vend.',
			'objectifs'    => 'Comprendre les fonctions du manager : planifier, organiser, diriger, contrôler
Lire et passer les écritures comptables de base selon le SYSCOHADA
Établir un bilan et un compte de résultat simples
Analyser un marché et construire un mix marketing cohérent',
			'debouches'    => 'Assistant(e) de gestion
Assistant(e) comptable
Chargé(e) de clientèle
Création ou reprise d\'une petite entreprise',
			'projet_final' => 'Dossier de synthèse sur une PME réelle : organisation, premiers comptes de l\'exercice et plan marketing simplifié.',
			'outils'       => array( 'SYSCOHADA', 'Excel' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 111 — Principes du management',
					'lecons' => array( 'Rôle et fonctions du manager', 'Planifier et fixer des objectifs', 'Organiser le travail et les équipes', 'Diriger, motiver, contrôler' ),
				),
				array(
					'titre'  => 'GES 112 — Comptabilité générale',
					'lecons' => array( 'Le plan comptable SYSCOHADA', 'Journal, grand livre et balance', 'Opérations courantes : achats, ventes, trésorerie', 'Bilan et compte de résultat' ),
				),
				array(
					'titre'  => 'GES 113 — Introduction au marketing',
					'lecons' => array( 'Le marché et ses acteurs', 'Segmentation, ciblage, positionnement', 'Le mix marketing (4P)', 'Comprendre le comportement du consommateur' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-pratiques-d-entreprise',
			'titre'        => 'Pratiques d\'entreprise',
			'niveau'       => 'débutant',
			'hero'         => 'De l\'idée au projet, et de l\'étudiant au professionnel : les réflexes concrets pour entreprendre et réussir son insertion.',
			'objectifs'    => 'Passer d\'une idée à un projet d\'entreprise structuré
Connaître les étapes et les formalités de création au Sénégal et en zone OHADA
Construire son projet professionnel et son réseau
Se présenter avec impact : CV, entretien, présence en ligne',
			'debouches'    => 'Création d\'entreprise
Chargé(e) de projet junior
Insertion en stage ou en premier emploi
Intrapreneuriat en entreprise',
			'projet_final' => 'Fiche projet d\'entreprise validée (idée, cible, modèle économique) et plan de développement professionnel personnel.',
			'outils'       => array( 'LinkedIn' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 1221 — Parcours de l\'entrepreneuriat',
					'lecons' => array( 'De l\'idée à l\'opportunité', 'Le modèle économique (Business Model Canvas)', 'Formes juridiques et formalités de création', 'Financer son démarrage' ),
				),
				array(
					'titre'  => 'GES 1223 — Développement professionnel',
					'lecons' => array( 'Bilan de compétences et projet professionnel', 'CV, lettre de motivation et profil LinkedIn', 'Réussir un entretien', 'Construire et entretenir son réseau' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-management-strategique-et-operationnel',
			'titre'        => 'Management stratégique et opérationnel d\'entreprise',
			'niveau'       => 'intermédiaire',
			'hero'         => 'Relier la stratégie au terrain : faire circuler les produits efficacement et bâtir une stratégie marketing qui tient la route.',
			'objectifs'    => 'Comprendre le fonctionnement d\'une chaîne logistique de bout en bout
Optimiser stocks, approvisionnements et délais
Élaborer une stratégie marketing à partir d\'un diagnostic
Traduire la stratégie en plan d\'actions mesurable',
			'debouches'    => 'Assistant(e) supply chain ou logistique
Chargé(e) de marketing
Responsable des achats junior
Chef de produit junior',
			'projet_final' => 'Étude de cas : diagnostic de la chaîne logistique et plan marketing d\'une entreprise de distribution.',
			'outils'       => array( 'Excel' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 2422 — Introduction à la supply chain',
					'lecons' => array( 'Les maillons de la chaîne logistique', 'Gestion des stocks et des approvisionnements', 'Transport et distribution', 'Indicateurs de performance logistique' ),
				),
				array(
					'titre'  => 'GES 2423 — Stratégie marketing',
					'lecons' => array( 'Diagnostic interne et externe (SWOT, PESTEL)', 'Choix stratégiques et avantage concurrentiel', 'Stratégie de marque et de prix', 'Plan marketing opérationnel' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-management-strategique',
			'titre'        => 'Management stratégique',
			'niveau'       => 'avancé',
			'hero'         => 'Penser en dirigeant : définir la stratégie de l\'entreprise et faire travailler ensemble des équipes de cultures différentes.',
			'objectifs'    => 'Analyser l\'environnement concurrentiel d\'une entreprise
Formuler et évaluer des options stratégiques
Comprendre et faire évoluer la culture d\'une organisation
Manager des équipes multiculturelles',
			'debouches'    => 'Chargé(e) de mission stratégie
Manager d\'équipe
Consultant(e) junior en organisation
Responsable de développement',
			'projet_final' => 'Recommandation stratégique argumentée pour une entreprise africaine, présentée sous forme de note de direction.',
			'modules'      => array(
				array(
					'titre'  => 'GES 3611 — Stratégie d\'entreprise',
					'lecons' => array( 'Analyse stratégique : Porter, chaîne de valeur', 'Stratégies de croissance et de diversification', 'Alliances, fusions et partenariats', 'Pilotage et déploiement de la stratégie' ),
				),
				array(
					'titre'  => 'GES 2422 — Culture d\'entreprise et management interculturel',
					'lecons' => array( 'Les composantes de la culture d\'entreprise', 'Dimensions culturelles et management', 'Communiquer entre cultures', 'Conduire le changement culturel' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-environnement-juridique-et-international',
			'titre'        => 'Environnement juridique et international de l\'entreprise',
			'niveau'       => 'avancé',
			'hero'         => 'Les règles qui encadrent l\'entreprise, du contrat de travail aux échanges internationaux.',
			'objectifs'    => 'Appliquer les règles essentielles du droit du travail sénégalais et OHADA
Gérer un contrat de travail de l\'embauche à la rupture
Comprendre les mécanismes de l\'économie internationale
Analyser l\'impact des échanges mondiaux sur l\'entreprise',
			'debouches'    => 'Assistant(e) ressources humaines
Chargé(e) d\'administration du personnel
Assistant(e) juridique
Chargé(e) d\'études économiques junior',
			'projet_final' => 'Dossier : cas pratique de droit du travail et note d\'analyse sur l\'exposition internationale d\'une entreprise.',
			'modules'      => array(
				array(
					'titre'  => 'GES 3511 — Droit du travail',
					'lecons' => array( 'Sources du droit du travail', 'Le contrat de travail : formation et exécution', 'Suspension et rupture du contrat', 'Représentation du personnel et conflits' ),
				),
				array(
					'titre'  => 'GES 3512 — Économie internationale',
					'lecons' => array( 'Théories du commerce international', 'Balance des paiements et taux de change', 'Intégration régionale : UEMOA, CEDEAO, ZLECAf', 'Mondialisation et développement' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-management-et-entreprenariat',
			'titre'        => 'Management & entrepreneuriat',
			'niveau'       => 'avancé',
			'hero'         => 'Les outils financiers de l\'entrepreneur : diagnostiquer la santé d\'une entreprise et bâtir un business plan crédible.',
			'objectifs'    => 'Réaliser le diagnostic financier d\'une entreprise
Calculer et interpréter les principaux ratios
Construire un business plan complet
Présenter son projet à des financeurs',
			'debouches'    => 'Création d\'entreprise
Analyste financier(ère) junior
Chargé(e) d\'affaires en banque ou microfinance
Conseiller(ère) en création d\'entreprise',
			'projet_final' => 'Business plan complet, avec prévisions financières sur 3 ans, défendu devant un jury.',
			'outils'       => array( 'Excel' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 3521 — Analyse financière et diagnostic financier',
					'lecons' => array( 'Du bilan comptable au bilan financier', 'Soldes intermédiaires de gestion et CAF', 'Ratios de rentabilité, de solvabilité et de liquidité', 'Tableau de flux de trésorerie' ),
				),
				array(
					'titre'  => 'GES 3522 — Business plan',
					'lecons' => array( 'Étude de marché et positionnement', 'Plan opérationnel et organisation', 'Prévisions financières', 'Pitch et recherche de financement' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-dimension-internationale-de-l-entreprise',
			'titre'        => 'Dimension internationale de l\'entreprise',
			'niveau'       => 'intermédiaire',
			'hero'         => 'Comprendre les nouveaux équilibres du monde et maîtriser les opérations du commerce international.',
			'objectifs'    => 'Analyser la place des puissances émergentes dans l\'économie mondiale
Comprendre les enjeux géopolitiques pour les entreprises africaines
Maîtriser les techniques d\'import-export et les Incoterms
Sécuriser une opération internationale : paiement, douane, transport',
			'debouches'    => 'Assistant(e) import-export
Agent(e) de transit ou de déclaration en douane
Chargé(e) de développement international
Assistant(e) commercial(e) export',
			'projet_final' => 'Montage complet d\'une opération d\'export depuis le Sénégal : choix du marché, Incoterm, mode de paiement et documents.',
			'outils'       => array( 'Incoterms' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 2432 — Puissances émergentes et géopolitique',
					'lecons' => array( 'Les nouveaux pôles de l\'économie mondiale', 'Chine, Inde, Brésil, pays du Golfe : stratégies en Afrique', 'Ressources, routes commerciales et rivalités', 'Lire l\'actualité géopolitique pour décider' ),
				),
				array(
					'titre'  => 'GES 2431 — Commerce international',
					'lecons' => array( 'Les opérations d\'import-export', 'Incoterms 2020', 'Moyens de paiement internationaux', 'Douane, transport et assurance' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-comptabilite-et-aspect-juridique',
			'titre'        => 'Comptabilité et aspect juridique de l\'entreprise',
			'niveau'       => 'intermédiaire',
			'hero'         => 'Calculer les coûts, respecter ses obligations fiscales et sécuriser ses actes de commerce.',
			'objectifs'    => 'Calculer les coûts de revient et les marges par produit
Utiliser la comptabilité analytique pour décider
Appliquer les principaux impôts et taxes des entreprises
Connaître le droit commercial OHADA : commerçant, fonds de commerce, contrats',
			'debouches'    => 'Comptable
Contrôleur(se) de gestion junior
Assistant(e) fiscal(e)
Assistant(e) juridique',
			'projet_final' => 'Étude de cas : calcul des coûts d\'une PME, déclaration fiscale simulée et analyse juridique d\'un contrat commercial.',
			'outils'       => array( 'SYSCOHADA', 'Excel' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 2411 — Comptabilité analytique',
					'lecons' => array( 'Charges directes et indirectes', 'Méthode des coûts complets', 'Coûts partiels et seuil de rentabilité', 'Analyse des écarts' ),
				),
				array(
					'titre'  => 'GES 2412 — Fiscalité des entreprises',
					'lecons' => array( 'Le système fiscal sénégalais', 'Impôt sur les sociétés', 'TVA : mécanisme et déclarations', 'Obligations et contrôle fiscal' ),
				),
				array(
					'titre'  => 'GES 2413 — Droit commercial',
					'lecons' => array( 'Le commerçant et les actes de commerce', 'Le fonds de commerce', 'Les contrats commerciaux', 'Les sociétés commerciales (OHADA)' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-aptitudes-manageriales-et-langue',
			'titre'        => 'Aptitudes managériales et langue',
			'niveau'       => 'intermédiaire',
			'hero'         => 'Agir avec éthique, comprendre les comportements au travail et communiquer en anglais professionnel.',
			'objectifs'    => 'Prendre des décisions éthiques et responsables
Comprendre la motivation, le leadership et la dynamique des groupes
Gérer les conflits et le stress en équipe
Communiquer en anglais dans un contexte professionnel',
			'debouches'    => 'Chef d\'équipe
Assistant(e) de direction
Chargé(e) de clientèle bilingue
Superviseur(se)',
			'projet_final' => 'Étude d\'un cas managérial réel présentée en anglais : diagnostic du comportement de l\'équipe et plan d\'action éthique.',
			'modules'      => array(
				array(
					'titre'  => 'GES 2341 — Éthique et déontologie',
					'lecons' => array( 'Éthique, morale et déontologie', 'Responsabilité sociale des entreprises (RSE)', 'Dilemmes éthiques en entreprise', 'Codes de conduite et lutte contre la corruption' ),
				),
				array(
					'titre'  => 'GES 2342 — Comportement organisationnel',
					'lecons' => array( 'Personnalité, perception et attitudes', 'Motivation au travail', 'Leadership et pouvoir', 'Groupes, conflits et stress' ),
				),
				array(
					'titre'  => 'GES 2343 — Anglais des affaires',
					'lecons' => array( 'Présentations et réunions', 'E-mails et écrits professionnels', 'Téléphoner et négocier', 'Vocabulaire du management' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-outils-d-aide-a-la-prise-de-decision',
			'titre'        => 'Outils d\'aide à la prise de décision',
			'niveau'       => 'intermédiaire',
			'hero'         => 'Décider sur des faits : étudier un marché, piloter la logistique et raisonner face au risque.',
			'objectifs'    => 'Conduire une étude de marché, du questionnaire à l\'analyse
Organiser et optimiser les opérations logistiques et de transport
Évaluer des décisions en situation de risque et d\'incertitude
Présenter une recommandation chiffrée',
			'debouches'    => 'Chargé(e) d\'études de marché
Assistant(e) logistique et transport
Analyste junior
Chargé(e) de planification',
			'projet_final' => 'Étude de marché complète pour un produit local, avec plan logistique de lancement et analyse des risques.',
			'outils'       => array( 'Excel', 'Google Forms' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 2331 — Étude et analyse de marché',
					'lecons' => array( 'Définir la problématique et les objectifs', 'Études qualitatives et quantitatives', 'Construire un questionnaire et un échantillon', 'Analyser et restituer les résultats' ),
				),
				array(
					'titre'  => 'GES 2332 — Gestion des opérations, de la logistique et du transport',
					'lecons' => array( 'Planification de la production', 'Gestion des entrepôts', 'Organisation du transport', 'Amélioration continue des opérations' ),
				),
				array(
					'titre'  => 'GES 2333 — Risque, décision et incertitude',
					'lecons' => array( 'Décider en avenir certain, risqué ou incertain', 'Arbres de décision et espérance', 'Critères de décision en incertitude', 'Gestion des risques en entreprise' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-fonctions-support-de-l-entreprise',
			'titre'        => 'Fonctions support de l\'entreprise',
			'niveau'       => 'intermédiaire',
			'hero'         => 'Qualité, ressources humaines et communication : les fonctions qui font tourner l\'entreprise au quotidien.',
			'objectifs'    => 'Mettre en place une démarche qualité et ses outils
Gérer le cycle de vie du salarié : recrutement, intégration, carrière, départ
Concevoir un plan de communication interne et externe
Améliorer l\'image et le fonctionnement de l\'organisation',
			'debouches'    => 'Assistant(e) qualité
Chargé(e) des ressources humaines
Chargé(e) de communication
Assistant(e) de direction',
			'projet_final' => 'Plan d\'amélioration des fonctions support d\'une PME : démarche qualité, processus RH et plan de communication.',
			'outils'       => array( 'Canva' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 2321 — Introduction au management de la qualité',
					'lecons' => array( 'Enjeux et principes de la qualité', 'Normes ISO 9001', 'Outils de la qualité : PDCA, 5 pourquoi, Ishikawa', 'Mesurer la satisfaction client' ),
				),
				array(
					'titre'  => 'GES 2322 — GRH : cycle de vie de l\'employé',
					'lecons' => array( 'Recrutement et sélection', 'Intégration et formation', 'Évaluation et gestion des carrières', 'Rémunération et départs' ),
				),
				array(
					'titre'  => 'GES 2323 — Communication d\'entreprise',
					'lecons' => array( 'Communication interne', 'Communication externe et image de marque', 'Relations presse et réseaux sociaux', 'Communication de crise' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-comptabilite-et-finance-d-entreprise',
			'titre'        => 'Comptabilité et finance d\'entreprise',
			'niveau'       => 'intermédiaire',
			'hero'         => 'Tenir les comptes des sociétés, choisir les bons investissements et piloter la santé financière de l\'entreprise.',
			'objectifs'    => 'Comptabiliser les opérations propres aux sociétés : capital, résultat, dividendes
Évaluer la rentabilité d\'un investissement (VAN, TRI, délai de récupération)
Choisir le financement le plus adapté
Analyser la performance financière d\'une entreprise',
			'debouches'    => 'Comptable
Assistant(e) financier(ère)
Analyste crédit junior
Chargé(e) de trésorerie',
			'projet_final' => 'Dossier d\'investissement : choix d\'un projet, plan de financement et analyse financière de l\'entreprise porteuse.',
			'outils'       => array( 'SYSCOHADA', 'Excel' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 2311 — Comptabilité des sociétés',
					'lecons' => array( 'Constitution et apports', 'Augmentation et réduction du capital', 'Affectation du résultat', 'Dissolution et liquidation' ),
				),
				array(
					'titre'  => 'GES 2312 — Stratégies d\'investissement et choix de financement',
					'lecons' => array( 'Les critères de choix d\'investissement', 'Actualisation, VAN et TRI', 'Sources de financement', 'Plan de financement' ),
				),
				array(
					'titre'  => 'GES 2313 — Finance d\'entreprise et analyse financière',
					'lecons' => array( 'Équilibre financier et fonds de roulement', 'Ratios d\'analyse', 'Rentabilité et effet de levier', 'Gestion de la trésorerie' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-langue-et-cultures',
			'titre'        => 'Langue et cultures',
			'niveau'       => 'débutant',
			'hero'         => 'Travailler en anglais et en arabe des affaires : deux langues clés des échanges de l\'Afrique de l\'Ouest.',
			'objectifs'    => 'Progresser en anglais professionnel selon son niveau
Échanger à l\'écrit et à l\'oral avec des partenaires anglophones
Acquérir les bases de l\'arabe des affaires
Comprendre les codes culturels des partenaires commerciaux',
			'debouches'    => 'Assistant(e) bilingue
Chargé(e) de clientèle internationale
Assistant(e) commercial(e) export
Accueil et relations partenaires',
			'projet_final' => 'Présentation orale enregistrée en anglais et échange écrit simulé en arabe des affaires avec un partenaire.',
			'modules'      => array(
				array(
					'titre'  => 'GES 1251 — Anglais des affaires (cours par niveau)',
					'lecons' => array( 'Test de positionnement', 'Se présenter et présenter son entreprise', 'Correspondance professionnelle', 'Réunions et prises de parole' ),
				),
				array(
					'titre'  => 'GES 1252 — Arabe des affaires',
					'lecons' => array( 'Alphabet, lecture et écriture', 'Formules de politesse et présentations', 'Vocabulaire du commerce', 'Codes culturels dans les affaires' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-droit-et-psychosociologie',
			'titre'        => 'Droit et psychosociologie',
			'niveau'       => 'débutant',
			'hero'         => 'Comprendre les règles de droit et les relations humaines qui structurent toute organisation.',
			'objectifs'    => 'Connaître l\'organisation judiciaire et les sources du droit
Distinguer les grandes branches du droit et leurs règles de base
Comprendre le fonctionnement des groupes et des organisations
Découvrir les missions de la gestion des ressources humaines',
			'debouches'    => 'Assistant(e) juridique
Assistant(e) ressources humaines
Agent(e) administratif(ve)
Poursuite vers les certificats RH et droit du parcours BBA',
			'projet_final' => 'Commentaire d\'un cas juridique simple et analyse du climat social d\'une organisation.',
			'modules'      => array(
				array(
					'titre'  => 'GES 1241 — Introduction au droit',
					'lecons' => array( 'La règle de droit et ses sources', 'Organisation judiciaire', 'Personnes et biens', 'Contrats et responsabilité' ),
				),
				array(
					'titre'  => 'GES 1242 — Psychosociologie des organisations et introduction à la GRH',
					'lecons' => array( 'L\'individu dans l\'organisation', 'Dynamique des groupes', 'Pouvoir, autorité et conflits', 'Les missions de la fonction RH' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-bureautique-et-techniques-d-expression',
			'titre'        => 'Bureautique et techniques d\'expression',
			'niveau'       => 'débutant',
			'hero'         => 'Bien écrire, bien parler et maîtriser Excel : les compétences que tout recruteur attend.',
			'objectifs'    => 'Rédiger des écrits professionnels clairs : courriers, comptes rendus, notes
Prendre la parole avec aisance en réunion et en public
Utiliser Excel pour la gestion : formules, tableaux, graphiques
Construire des tableaux de bord simples',
			'debouches'    => 'Assistant(e) administratif(ve)
Assistant(e) de gestion
Secrétaire de direction
Tous postes de bureau',
			'projet_final' => 'Rapport professionnel rédigé, accompagné d\'un tableau de bord Excel et d\'une présentation orale enregistrée.',
			'outils'       => array( 'Excel', 'Word', 'PowerPoint' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 1231 — Techniques d\'expression orale et écrite',
					'lecons' => array( 'Les règles de l\'écrit professionnel', 'Courrier, note et compte rendu', 'Prise de parole en public', 'Argumenter et convaincre' ),
				),
				array(
					'titre'  => 'GES 1232 — Logiciels de bureautique appliqués à la gestion (Excel)',
					'lecons' => array( 'Prise en main et mise en forme', 'Formules et fonctions de gestion', 'Tableaux croisés dynamiques', 'Graphiques et tableaux de bord' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-fondamentaux-en-economie-et-gestion',
			'titre'        => 'Les fondamentaux en économie et gestion',
			'niveau'       => 'débutant',
			'hero'         => 'Comprendre l\'économie qui entoure l\'entreprise et consolider sa comptabilité.',
			'objectifs'    => 'Comprendre les grands mécanismes de l\'économie : offre, demande, prix
Analyser le comportement des consommateurs et des entreprises
Lire les grands indicateurs économiques
Approfondir la comptabilité générale : inventaire et fin d\'exercice',
			'debouches'    => 'Assistant(e) de gestion
Assistant(e) comptable
Chargé(e) d\'études junior
Poursuite vers les certificats de niveau 2 du parcours BBA',
			'projet_final' => 'Note d\'analyse économique d\'un secteur et travaux d\'inventaire complets sur un cas d\'entreprise.',
			'outils'       => array( 'SYSCOHADA', 'Excel' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 1211 — Analyse économique (principes de l\'économie)',
					'lecons' => array( 'Les grandes questions économiques', 'Production, croissance et emploi', 'Monnaie, inflation et politique économique', 'Les indicateurs économiques' ),
				),
				array(
					'titre'  => 'GES 1212 — Microéconomie',
					'lecons' => array( 'Le comportement du consommateur', 'Le comportement du producteur', 'L\'équilibre du marché', 'Les structures de marché' ),
				),
				array(
					'titre'  => 'GES 1213 — Comptabilité générale niveau 2',
					'lecons' => array( 'Amortissements', 'Provisions et dépréciations', 'Régularisations de fin d\'exercice', 'États financiers SYSCOHADA' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-bureautique-et-langues',
			'titre'        => 'Bureautique et langues',
			'niveau'       => 'débutant',
			'hero'         => 'Produire des documents professionnels impeccables sous Word et communiquer en anglais des affaires.',
			'objectifs'    => 'Créer des documents professionnels structurés avec Word
Utiliser les modèles, styles, tableaux et le publipostage
Communiquer en anglais dans les situations courantes du travail
Rédiger des e-mails professionnels en anglais',
			'debouches'    => 'Assistant(e) administratif(ve)
Assistant(e) bilingue
Agent(e) d\'accueil
Secrétaire',
			'projet_final' => 'Dossier professionnel complet mis en page sous Word et présentation de son entreprise en anglais.',
			'outils'       => array( 'Word' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 1141 — Logiciels de bureautique appliqués à la gestion (Word)',
					'lecons' => array( 'Mise en forme et styles', 'Tableaux, images et modèles', 'Documents longs : sommaire, en-têtes', 'Publipostage' ),
				),
				array(
					'titre'  => 'GES 1142 — Anglais des affaires',
					'lecons' => array( 'Se présenter au travail', 'Accueil et téléphone', 'E-mails professionnels', 'Vocabulaire de l\'entreprise' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-culture-entrepreneuriale',
			'titre'        => 'Culture entrepreneuriale',
			'niveau'       => 'débutant',
			'hero'         => 'Développer l\'état d\'esprit de l\'entrepreneur : confiance, esprit critique et compréhension de son environnement économique.',
			'objectifs'    => 'Mieux se connaître et développer son efficacité personnelle
Raisonner avec méthode et exercer son esprit critique
Comprendre les liens entre culture, société et entrepreneuriat en Afrique
Identifier des opportunités d\'entreprendre',
			'debouches'    => 'Création d\'entreprise
Entrepreneuriat social
Intrapreneuriat
Poursuite vers « Pratiques d\'entreprise » et « Management & entrepreneuriat »',
			'projet_final' => 'Portrait d\'entrepreneur africain analysé et carnet de route de son propre projet d\'entreprendre.',
			'modules'      => array(
				array(
					'titre'  => 'GES 1131 — Développement personnel',
					'lecons' => array( 'Connaissance de soi', 'Gestion du temps et des priorités', 'Confiance et affirmation de soi', 'Fixer et atteindre ses objectifs' ),
				),
				array(
					'titre'  => 'GES 1132 — Raisonnement et pensée critique',
					'lecons' => array( 'Les bases de l\'argumentation', 'Identifier les biais et les erreurs de raisonnement', 'Analyser une information', 'Résoudre un problème avec méthode' ),
				),
				array(
					'titre'  => 'GES 1133 — Anthropologie économique et entrepreneuriat',
					'lecons' => array( 'Économie, culture et société', 'Les formes d\'entrepreneuriat en Afrique', 'Économie informelle et tontines', 'Réseaux et confiance dans les affaires' ),
				),
			),
		)
	),
	$cert(
		array(
			'slug'         => 'certificat-techniques-quantitatives-de-gestion',
			'titre'        => 'Techniques quantitatives de gestion',
			'niveau'       => 'débutant',
			'hero'         => 'Les outils mathématiques et statistiques indispensables pour compter, prévoir et décider en gestion.',
			'objectifs'    => 'Calculer intérêts, actualisation et remboursements d\'emprunts
Décrire et résumer une série de données statistiques
Utiliser les fonctions mathématiques utiles au gestionnaire
Appuyer ses décisions sur des calculs fiables',
			'debouches'    => 'Assistant(e) de gestion
Agent(e) de crédit ou de microfinance
Assistant(e) statisticien(ne)
Poursuite vers les certificats de finance du parcours BBA',
			'projet_final' => 'Étude chiffrée : tableau d\'emprunt, analyse statistique des ventes et prévisions simples pour une entreprise.',
			'outils'       => array( 'Excel' ),
			'modules'      => array(
				array(
					'titre'  => 'GES 1121 — Mathématiques financières',
					'lecons' => array( 'Intérêts simples et composés', 'Actualisation et capitalisation', 'Annuités', 'Emprunts indivis et tableaux d\'amortissement' ),
				),
				array(
					'titre'  => 'GES 1122 — Statistiques descriptives',
					'lecons' => array( 'Séries et représentations graphiques', 'Moyenne, médiane, mode', 'Dispersion : variance et écart-type', 'Corrélation et régression simple' ),
				),
				array(
					'titre'  => 'GES 1123 — Mathématiques pour gestionnaires',
					'lecons' => array( 'Fonctions et équations', 'Dérivées et optimisation', 'Calcul matriciel de base', 'Applications à la gestion' ),
				),
			),
		)
	),
);
