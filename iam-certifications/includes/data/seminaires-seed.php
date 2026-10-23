<?php
/**
 * Données de pré-remplissage v1.2 : les 8 séminaires de 3 jours (collections
 * « Talents × IA » et « Marketing Digital & IA ») et le certificat CEDLA.
 * Sans prix ni partenaire.
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	array(
		'type'             => 'seminaire',
		'slug'             => 'seminaire-km-ia-knowledge-management-rh',
		'titre'            => 'KM × IA — Knowledge Management RH augmenté par l’IA',
		'domaine'          => 'Talents × IA',
		'hero'             => 'Transformer le savoir dispersé de l\'organisation en capital exploitable, grâce aux outils d\'IA générative.',
		'heures'           => '24',
		'duree_texte'      => '3 jours',
		'format'           => 'Présentiel Dakar ou intra',
		'participants_max' => '16',
		'pratique_pct'     => '70',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois à la plateforme eLearning',
		'public'           => 'DRH, responsables formation, knowledge managers, responsables SIRH, directions générales',
		'prerequis'        => 'Aucun prérequis technique ; une pratique courante des outils bureautiques suffit.',
		'objectifs'        => 'Cartographier les connaissances critiques de son organisation et les risques de perte de savoir
Capturer et structurer le savoir avec les outils d\'IA (transcription, synthèse, documentation automatisée)
Concevoir une base de connaissances interrogeable par un assistant IA interne
Piloter la gouvernance, la qualité et l\'adoption du dispositif',
		'jours'            => array(
			array(
				'titre'      => 'Cartographier et capturer le savoir',
				'matin'      => 'Enjeux du knowledge management en 2026 ; cartographie des savoirs critiques ; diagnostic de maturité de son organisation (atelier).',
				'apres_midi' => 'Outils IA de capture du savoir : transcription et synthèse de réunions, entretiens de départ documentés, fiches de capitalisation générées (atelier outillé).',
			),
			array(
				'titre'      => 'Structurer et interroger',
				'matin'      => 'Structurer la base de connaissances : taxonomies, référentiels, qualité documentaire ; principes des assistants documentaires (RAG) expliqués sans jargon.',
				'apres_midi' => 'Atelier : construire un prototype d\'assistant de connaissances RH sur un corpus réel anonymisé ; limites, hallucinations et garde-fous.',
			),
			array(
				'titre'      => 'Gouverner et déployer',
				'matin'      => 'Gouvernance et conduite du changement : rôles, droits d\'accès, confidentialité, rituels de contribution ; mesurer la valeur du dispositif.',
				'apres_midi' => 'Atelier final : feuille de route KM × IA de son organisation à 6 mois, présentée et challengée en groupe.',
			),
		),
		'livrables'        => 'Feuille de route KM × IA personnalisée
Prototype d\'assistant documentaire',
		'evaluation'       => 'Évaluation par quiz final et présentation du plan d\'action. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'ChatGPT',
			'Claude',
			'NotebookLM',
			'Notion',
		),
		'infos_pratiques'  => 'Sessions inter-entreprises à Dakar de novembre 2026 à mars 2027, une session toutes les deux semaines environ
Organisable en intra, dans vos locaux ou sur un campus IAM, à Dakar comme en région
Venir avec son ordinateur portable : les accès aux outils sont fournis
Pauses café et déjeuner incluses (8h30 – 17h30)
Formation en français, supports partiellement en anglais selon les outils
Accès 3 mois à la plateforme eLearning et à la communauté d\'apprenants',
	),
	array(
		'type'             => 'seminaire',
		'slug'             => 'seminaire-acquisition-ia-recruter-avec-lia',
		'titre'            => 'Acquisition × IA — Acquisition de talents augmentée par l’IA',
		'domaine'          => 'Talents × IA',
		'hero'             => 'Diviser par deux le temps de recrutement tout en améliorant la qualité et l\'équité des embauches.',
		'heures'           => '24',
		'duree_texte'      => '3 jours',
		'format'           => 'Présentiel Dakar ou intra',
		'participants_max' => '16',
		'pratique_pct'     => '70',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois à la plateforme eLearning',
		'public'           => 'Recruteurs, chargés de recrutement, cabinets RH, managers recruteurs, responsables marque employeur',
		'prerequis'        => 'Expérience d\'au moins un cycle de recrutement ; aucun prérequis technique.',
		'objectifs'        => 'Rédiger et diffuser des offres optimisées avec l\'IA générative
Outiller le sourcing et la présélection (screening assisté, entretiens structurés, scorecards)
Identifier et corriger les biais des outils d\'IA de recrutement
Automatiser l\'expérience candidat sans la déshumaniser, dans le respect des données personnelles',
		'jours'            => array(
			array(
				'titre'      => 'Attirer',
				'matin'      => 'Le recrutement augmenté : panorama des outils 2026 ; marque employeur et rédaction d\'offres assistée (atelier prompts).',
				'apres_midi' => 'Sourcing augmenté : ciblage, multidiffusion, approche directe assistée par IA ; atelier sur les canaux pertinents au Sénégal (LinkedIn, jobboards, WhatsApp).',
			),
			array(
				'titre'      => 'Sélectionner',
				'matin'      => 'Présélection assistée : screening de CV, matching, tests et entretiens structurés ; scorecards et prise de décision collégiale.',
				'apres_midi' => 'Biais, équité et conformité : auditer ses outils, protéger les données des candidats ; simulation d\'un pipeline complet sur un poste réel.',
			),
			array(
				'titre'      => 'Engager',
				'matin'      => 'Expérience candidat : communication automatisée, relances, feedback systématique ; CRM candidats et viviers de talents.',
				'apres_midi' => 'Atelier final : refonte outillée de son processus de recrutement, du besoin au contrat, avec plan de déploiement.',
			),
		),
		'livrables'        => 'Processus de recrutement refondu et outillé
Bibliothèque de prompts recruteur',
		'evaluation'       => 'Évaluation par mise en situation finale. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'ChatGPT',
			'LinkedIn Recruiter',
			'WhatsApp Business',
		),
		'infos_pratiques'  => 'Sessions inter-entreprises à Dakar de novembre 2026 à mars 2027, une session toutes les deux semaines environ
Organisable en intra, dans vos locaux ou sur un campus IAM, à Dakar comme en région
Venir avec son ordinateur portable : les accès aux outils sont fournis
Pauses café et déjeuner incluses (8h30 – 17h30)
Formation en français, supports partiellement en anglais selon les outils
Accès 3 mois à la plateforme eLearning et à la communauté d\'apprenants',
	),
	array(
		'type'             => 'seminaire',
		'slug'             => 'seminaire-gestion-ia-gerer-developper-talents',
		'titre'            => 'Gestion × IA — Gestion des talents augmentée par l’IA',
		'domaine'          => 'Talents × IA',
		'hero'             => 'Passer d\'une gestion RH administrative à un pilotage des talents éclairé par la donnée et l\'IA.',
		'heures'           => '24',
		'duree_texte'      => '3 jours',
		'format'           => 'Présentiel Dakar ou intra',
		'participants_max' => '16',
		'pratique_pct'     => '70',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois à la plateforme eLearning',
		'public'           => 'DRH, responsables talents, people managers, responsables formation et mobilité',
		'prerequis'        => 'Fonction RH ou managériale en exercice ; aucun prérequis technique.',
		'objectifs'        => 'Concevoir des parcours d\'onboarding et de développement augmentés par l\'IA
Cartographier les compétences et anticiper les besoins avec l\'analytique RH
Outiller les entretiens (feedback, performance, développement) avec l\'IA générative
Construire des tableaux de bord de rétention et d\'engagement',
		'jours'            => array(
			array(
				'titre'      => 'Intégrer et cartographier',
				'matin'      => 'Le cycle de vie collaborateur augmenté ; onboarding : parcours, contenus et suivi générés et personnalisés (atelier).',
				'apres_midi' => 'Cartographie des compétences assistée par IA : référentiels, auto-évaluations, détection des écarts (atelier sur cas réel).',
			),
			array(
				'titre'      => 'Développer',
				'matin'      => 'Performance et développement : préparation d\'entretiens assistée, synthèses de feedback, plans de développement individualisés.',
				'apres_midi' => 'Mobilité interne et succession : détecter les potentiels, construire des viviers internes ; ateliers entretiens augmentés.',
			),
			array(
				'titre'      => 'Fidéliser',
				'matin'      => 'Rétention et engagement : signaux faibles, enquêtes analysées par IA, plans d\'action ciblés ; tableaux de bord RH.',
				'apres_midi' => 'Atelier final : feuille de route People × IA de son organisation, priorisée et budgétée, présentée en groupe.',
			),
		),
		'livrables'        => 'Feuille de route People × IA
Kit d\'entretiens augmentés',
		'evaluation'       => 'Évaluation par quiz et présentation finale. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'ChatGPT',
			'Claude',
			'Looker Studio',
			'Excel',
		),
		'infos_pratiques'  => 'Sessions inter-entreprises à Dakar de novembre 2026 à mars 2027, une session toutes les deux semaines environ
Organisable en intra, dans vos locaux ou sur un campus IAM, à Dakar comme en région
Venir avec son ordinateur portable : les accès aux outils sont fournis
Pauses café et déjeuner incluses (8h30 – 17h30)
Formation en français, supports partiellement en anglais selon les outils
Accès 3 mois à la plateforme eLearning et à la communauté d\'apprenants',
	),
	array(
		'type'             => 'seminaire',
		'slug'             => 'seminaire-fiscalite-ia-piloter-ia-sans-risque',
		'titre'            => 'Fiscalité × IA — Piloter l’IA sans risque déontologique',
		'domaine'          => 'Talents × IA',
		'hero'             => 'Adopter l\'IA dans les métiers du chiffre et du droit en maîtrisant totalement la confidentialité et la responsabilité professionnelle.',
		'heures'           => '24',
		'duree_texte'      => '3 jours',
		'format'           => 'Présentiel Dakar ou intra',
		'participants_max' => '16',
		'pratique_pct'     => '70',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois à la plateforme eLearning',
		'public'           => 'Experts-comptables, fiscalistes, DAF, auditeurs, juristes d\'entreprise, cabinets de conseil',
		'prerequis'        => 'Exercice d\'un métier du chiffre, du droit ou de la finance.',
		'objectifs'        => 'Identifier les cas d\'usage de l\'IA à forte valeur et à faible risque pour son cabinet ou sa direction
Maîtriser les règles de confidentialité, de secret professionnel et de responsabilité face aux outils d\'IA
Outiller les travaux récurrents : veille, synthèse documentaire, revue de conformité, documentation
Rédiger et déployer une charte IA opposable dans son organisation',
		'jours'            => array(
			array(
				'titre'      => 'Comprendre le cadre',
				'matin'      => 'Panorama de l\'IA pour les métiers du chiffre et du droit ; ce que l\'IA sait et ne sait pas faire ; cadre déontologique, secret professionnel et responsabilité.',
				'apres_midi' => 'Cas d\'usage sécurisés : veille fiscale et réglementaire, synthèse de textes, préparation de notes ; atelier sur documents publics.',
			),
			array(
				'titre'      => 'Outiller les travaux',
				'matin'      => 'IA appliquée aux travaux fiscaux et comptables : revue de cohérence, préparation déclarative, documentation des dossiers ; confidentialité des données clients (anonymisation, environnements maîtrisés).',
				'apres_midi' => 'Ateliers outillés sur cas pratiques sénégalais (CGI, obligations déclaratives) avec données fictives ; vérification systématique des sorties d\'IA.',
			),
			array(
				'titre'      => 'Encadrer les usages',
				'matin'      => 'Construire la charte IA de son cabinet ou de sa direction : usages autorisés, outils validés, revue humaine obligatoire, traçabilité.',
				'apres_midi' => 'Atelier final : rédaction de sa charte IA et de son plan de déploiement (formation des équipes, contrôles) ; restitution croisée.',
			),
		),
		'livrables'        => 'Charte IA personnalisée
Catalogue de cas d\'usage sécurisés',
		'evaluation'       => 'Évaluation par étude de cas finale. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'ChatGPT',
			'Claude',
			'Ollama',
		),
		'infos_pratiques'  => 'Sessions inter-entreprises à Dakar de novembre 2026 à mars 2027, une session toutes les deux semaines environ
Organisable en intra, dans vos locaux ou sur un campus IAM, à Dakar comme en région
Venir avec son ordinateur portable : les accès aux outils sont fournis
Pauses café et déjeuner incluses (8h30 – 17h30)
Formation en français, supports partiellement en anglais selon les outils
Accès 3 mois à la plateforme eLearning et à la communauté d\'apprenants',
	),
	array(
		'type'             => 'seminaire',
		'slug'             => 'seminaire-social-media-marketing-social-selling',
		'titre'            => 'Social Media Marketing & Social Selling',
		'domaine'          => 'Marketing Digital & IA',
		'hero'             => 'Construire une présence sociale qui vend, en combinant contenu IA-first, publicité sociale et vente directe sur les réseaux.',
		'heures'           => '24',
		'duree_texte'      => '3 jours',
		'format'           => 'Présentiel Dakar ou intra',
		'participants_max' => '16',
		'pratique_pct'     => '70',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois au parcours eLearning (29 contenus)',
		'public'           => 'Community managers, responsables marketing et communication, commerciaux B2B, entrepreneurs',
		'prerequis'        => 'Pratique personnelle des réseaux sociaux ; aucun prérequis publicitaire.',
		'objectifs'        => 'Bâtir une stratégie social media alignée sur les objectifs business
Produire un calendrier de contenus assisté par l\'IA (textes, visuels, vidéos courtes)
Pratiquer le social selling : prospection et conversion directement sur les réseaux
Piloter la publicité sociale et mesurer le ROI de sa présence',
		'jours'            => array(
			array(
				'titre'      => 'Stratégie & contenu',
				'matin'      => 'Fondamentaux du social media marketing 2026 : plateformes, algorithmes, audiences au Sénégal et en Afrique de l\'Ouest.',
				'apres_midi' => 'Stratégie de contenu IA-first : ligne éditoriale, calendrier, production assistée (textes, visuels, vidéos) — atelier création.',
			),
			array(
				'titre'      => 'Vendre sur les réseaux',
				'matin'      => 'Social selling : profil vendeur, prospection sociale, séquences d\'approche, conversation commerciale (LinkedIn, WhatsApp Business).',
				'apres_midi' => 'Publicité sociale & social commerce : campagnes Meta/TikTok, ciblage, boutiques sociales — atelier campagne réelle.',
			),
			array(
				'titre'      => 'Automatiser & mesurer',
				'matin'      => 'Automation & no code pour les réseaux : programmation, veille, réponses assistées ; analytics, ROI et social intelligence.',
				'apres_midi' => 'Mise en situation finale : présentation de sa stratégie sociale complète (contenu + vente + publicité) devant le groupe.',
			),
		),
		'livrables'        => 'Stratégie social media complète
Calendrier éditorial sur 90 jours',
		'evaluation'       => 'Évaluation par mise en situation finale. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'LinkedIn Sales Navigator',
			'Meta Ads',
			'TikTok Ads',
			'WhatsApp Business',
			'Metricool',
		),
		'infos_pratiques'  => 'Collection ouverte à la programmation : premières sessions calées avec les entreprises partenaires
Organisable en intra, dans vos locaux ou sur un campus IAM, à Dakar comme en région
Venir avec son ordinateur portable : les accès aux outils sont fournis
Pauses café et déjeuner incluses (8h30 – 17h30)
Formation en français, supports partiellement en anglais selon les outils
Accès 3 mois à la plateforme eLearning et à la communauté d\'apprenants',
	),
	array(
		'type'             => 'seminaire',
		'slug'             => 'seminaire-marketing-automation-growth-hacking',
		'titre'            => 'Marketing Automation & Growth Hacking',
		'domaine'          => 'Marketing Digital & IA',
		'hero'             => 'Mettre en place une machine d\'acquisition et de fidélisation qui travaille 24 h/24, sans écrire une ligne de code.',
		'heures'           => '24',
		'duree_texte'      => '3 jours',
		'format'           => 'Présentiel Dakar ou intra',
		'participants_max' => '16',
		'pratique_pct'     => '70',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois au parcours eLearning (63 contenus)',
		'public'           => 'Responsables marketing, growth marketers, chargés CRM, fondateurs de startups et PME',
		'prerequis'        => 'Bases de marketing digital (campagnes email ou réseaux sociaux déjà pratiquées).',
		'objectifs'        => 'Comprendre le framework growth (acquisition, activation, rétention, referral, revenu)
Automatiser l\'acquisition et l\'activation : scénarios, scoring, déclencheurs
Déployer le lead nurturing et la personnalisation à grande échelle
Installer une culture d\'expérimentation : tests systématiques, analytics et scaling',
		'jours'            => array(
			array(
				'titre'      => 'Cartographier son funnel',
				'matin'      => 'Fondamentaux du marketing automation : parcours client, scénarios, outils ; fondamentaux du growth hacking et framework AARRR.',
				'apres_midi' => 'Atelier : cartographier son funnel et identifier les 3 leviers prioritaires ; premiers scénarios automatisés (bienvenue, relance).',
			),
			array(
				'titre'      => 'Automatiser',
				'matin'      => 'Acquisition et activation automatisées : capture, scoring, séquences multicanales (email, WhatsApp, SMS).',
				'apres_midi' => 'Lead nurturing et personnalisation à grande échelle ; growth hacking par l\'automatisation no code — ateliers outillés.',
			),
			array(
				'titre'      => 'Expérimenter & scaler',
				'matin'      => 'Expérimentation et tests systématiques : hypothèses, A/B tests, mesure ; rétention, parrainage et boucles virales ; analytics, ROI et scaling.',
				'apres_midi' => 'Projet final : concevoir sa « growth machine » automatisée de bout en bout et la présenter au groupe.',
			),
		),
		'livrables'        => 'Growth machine opérationnelle (scénarios + tableaux de bord)',
		'evaluation'       => 'Évaluation par projet final. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'HubSpot',
			'Make',
			'Zapier',
			'n8n',
			'Google Analytics',
		),
		'infos_pratiques'  => 'Collection ouverte à la programmation : premières sessions calées avec les entreprises partenaires
Organisable en intra, dans vos locaux ou sur un campus IAM, à Dakar comme en région
Venir avec son ordinateur portable : les accès aux outils sont fournis
Pauses café et déjeuner incluses (8h30 – 17h30)
Formation en français, supports partiellement en anglais selon les outils
Accès 3 mois à la plateforme eLearning et à la communauté d\'apprenants',
	),
	array(
		'type'             => 'seminaire',
		'slug'             => 'seminaire-no-code-marketing-salestech',
		'titre'            => 'No Code Marketing & SalesTech',
		'domaine'          => 'Marketing Digital & IA',
		'hero'             => 'Construire sites, CRM, automatisations et applications marketing sans développeur : l\'autonomie technologique des équipes marketing et commerciales.',
		'heures'           => '24',
		'duree_texte'      => '3 jours',
		'format'           => 'Présentiel Dakar ou intra',
		'participants_max' => '16',
		'pratique_pct'     => '70',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois au parcours eLearning (39 contenus)',
		'public'           => 'Marketeurs, commerciaux, chefs de projet digitaux, entrepreneurs, assistants de direction',
		'prerequis'        => 'Aisance avec les outils bureautiques et le web ; aucune compétence en programmation requise.',
		'objectifs'        => 'Créer des landing pages et sites marketing sans code
Structurer des bases de données marketing et un CRM no code
Automatiser les workflows marketing et commerciaux entre les outils
Livrer un MVP marketing & sales complet en fin de séminaire',
		'jours'            => array(
			array(
				'titre'      => 'Sites & données',
				'matin'      => 'Introduction au no code pour le marketing et la vente : panorama des outils, cas d\'usage, limites.',
				'apres_midi' => 'Atelier : création de landing pages et d\'un mini-site marketing sans code ; bases de données marketing (Airtable ou équivalent).',
			),
			array(
				'titre'      => 'CRM & workflows',
				'matin'      => 'CRM & SalesTech no code : pipeline, fiches contacts, suivi commercial ; automatisation des workflows entre outils (formulaires → CRM → messages).',
				'apres_midi' => 'Portails clients et espaces sécurisés no code ; atelier d\'intégration complète du parcours prospect.',
			),
			array(
				'titre'      => 'Apps & MVP',
				'matin'      => 'Applications mobiles marketing sans code ; prospection et closing outillés ; analytique et reporting sans code.',
				'apres_midi' => 'Projet final : livrer son MVP marketing & sales no code fonctionnel et le présenter au groupe.',
			),
		),
		'livrables'        => 'MVP no code fonctionnel (site + CRM + automatisations)',
		'evaluation'       => 'Évaluation par projet final. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'Airtable',
			'Webflow',
			'Make',
			'Zapier',
			'Glide',
		),
		'infos_pratiques'  => 'Collection ouverte à la programmation : premières sessions calées avec les entreprises partenaires
Organisable en intra, dans vos locaux ou sur un campus IAM, à Dakar comme en région
Venir avec son ordinateur portable : les accès aux outils sont fournis
Pauses café et déjeuner incluses (8h30 – 17h30)
Formation en français, supports partiellement en anglais selon les outils
Accès 3 mois à la plateforme eLearning et à la communauté d\'apprenants',
	),
	array(
		'type'             => 'seminaire',
		'slug'             => 'seminaire-content-marketing-strategie-editoriale-ia',
		'titre'            => 'Content Marketing & Stratégie éditoriale IA',
		'domaine'          => 'Marketing Digital & IA',
		'hero'             => 'Produire dix fois plus de contenu utile, sans sacrifier la qualité ni l\'authenticité de la marque.',
		'heures'           => '24',
		'duree_texte'      => '3 jours',
		'format'           => 'Présentiel Dakar ou intra',
		'participants_max' => '16',
		'pratique_pct'     => '70',
		'horaires'         => '8h30 – 12h30 · 14h – 17h30',
		'elearning'        => 'Accès 3 mois au parcours eLearning (48 contenus)',
		'public'           => 'Responsables communication et contenus, rédacteurs, community managers, agences, directions marketing',
		'prerequis'        => 'Pratique de la rédaction professionnelle (articles, posts, newsletters).',
		'objectifs'        => 'Bâtir une stratégie éditoriale augmentée par l\'IA, alignée sur le SEO sémantique
Industrialiser la production de contenu avec une revue humaine systématique (human in the loop)
Décliner les contenus en formats avancés et les distribuer cross-canal
Mesurer la performance et respecter l\'éthique, la transparence et la conformité',
		'jours'            => array(
			array(
				'titre'      => 'Stratégie éditoriale',
				'matin'      => 'Fondamentaux du content marketing à l\'ère de l\'IA ; stratégie éditoriale augmentée : personas, piliers de contenu, ton de marque.',
				'apres_midi' => 'Recherche de sujets et SEO sémantique assistés par IA — atelier de construction du plan éditorial.',
			),
			array(
				'titre'      => 'Production',
				'matin'      => 'Production de contenu assistée par IA : articles, newsletters, scripts vidéo ; human in the loop : révision, fact-checking, valeur ajoutée humaine.',
				'apres_midi' => 'Formats avancés et distribution cross-canal ; personnalisation du contenu à l\'échelle — ateliers de production.',
			),
			array(
				'titre'      => 'Mesure & conformité',
				'matin'      => 'Mesure de performance et optimisation continue ; workflows no code pour le content marketing ; éthique, transparence et conformité (mentions IA, droits).',
				'apres_midi' => 'Projet final : stratégie éditoriale IA complète de sa marque, du calendrier aux workflows, présentée au groupe.',
			),
		),
		'livrables'        => 'Stratégie éditoriale IA complète
Workflows de production',
		'evaluation'       => 'Évaluation par projet final. Attestation de réussite délivrée après évaluation finale.',
		'outils'           => array(
			'ChatGPT',
			'Claude',
			'Midjourney',
			'HeyGen',
			'Semrush',
		),
		'infos_pratiques'  => 'Collection ouverte à la programmation : premières sessions calées avec les entreprises partenaires
Organisable en intra, dans vos locaux ou sur un campus IAM, à Dakar comme en région
Venir avec son ordinateur portable : les accès aux outils sont fournis
Pauses café et déjeuner incluses (8h30 – 17h30)
Formation en français, supports partiellement en anglais selon les outils
Accès 3 mois à la plateforme eLearning et à la communauté d\'apprenants',
	),
	array(
		'type'         => 'certificat',
		'slug'         => 'cedla-edtech-digital-learning-afrique',
		'titre'        => 'CEDLA — EdTech & Digital Learning, contexte africain',
		'domaine'      => 'Ingénierie pédagogique',
		'hero'         => 'Concevoir, déployer et piloter des dispositifs de formation digitale adaptés aux réalités africaines : bande passante, mobile-first, modèles économiques.',
		'heures'       => '',
		'duree_texte'  => '1 semestre',
		'niveau'       => 'intermédiaire',
		'format'       => 'Parcours long, mission terrain',
		'public'       => 'Enseignants, responsables pédagogiques, chefs de projet formation, entrepreneurs EdTech',
		'prerequis'    => 'Expérience en enseignement, en formation ou en gestion de projet éducatif.',
		'evaluation'   => 'Quiz par bloc, mission terrain encadrée, mémoire professionnel et soutenance devant jury. Le certificat est délivré après évaluation, jamais par simple complétion du parcours.',
		'projet_final' => 'Projet fil rouge, mission terrain en école ou en entreprise, mémoire professionnel et soutenance devant jury.',
		'outils'       => array(
			'Moodle',
			'SCORM',
			'xAPI',
			'H5P',
		),
		'modules'      => array(
			array(
				'titre'  => 'Bloc 1 — Fondamentaux EdTech & écosystème africain',
				'lecons' => array(
					'Cadre juridique',
					'Financement',
				),
			),
			array(
				'titre'  => 'Bloc 2 — Ingénierie pédagogique & sciences de l\'apprentissage',
				'lecons' => array(
					'Méthode ADDIE',
					'Scénarisation',
				),
			),
			array(
				'titre'  => 'Bloc 3 — Plateformes LMS : déploiement & administration',
				'lecons' => array(
					'Moodle',
					'SCORM / xAPI',
					'Usage hors-ligne',
				),
			),
			array(
				'titre'  => 'Bloc 4 — Production de contenus multimédia mobile-first',
				'lecons' => array(
					'Outils auteurs',
					'Vidéo',
					'Accessibilité',
				),
			),
			array(
				'titre'  => 'Bloc 5 — Gestion de projet digital learning',
				'lecons' => array(
					'Méthodes agiles',
					'Qualité',
					'Conduite du changement',
				),
			),
			array(
				'titre'  => 'Bloc 6 — Data, évaluation & démarche qualité',
				'lecons' => array(
					'Learning analytics',
					'Modèle de Kirkpatrick',
					'Qualiopi / ISO 21001',
				),
			),
			array(
				'titre'  => 'Bloc 7 — Posture professionnelle, éthique & entrepreneuriat',
				'lecons' => array(
					'IA responsable',
					'Insertion',
				),
			),
			array(
				'titre'  => 'Bloc 8 — Capstone',
				'lecons' => array(
					'Projet fil rouge',
					'Mission terrain en école ou entreprise',
					'Mémoire et soutenance devant jury',
				),
			),
		),
	),
);
