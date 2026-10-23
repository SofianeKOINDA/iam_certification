# IAM Certifications

Plugin WordPress de gestion du **catalogue de formations** du Groupe IAM — **certificats professionnels** (parcours de 6 à 12 semaines) et **séminaires intensifs** (3 jours en présentiel ou intra) : custom post type, taxonomies, champs, shortcode de liste, page de détail (landing) et **intégration avec le plugin IAM Leads**.

- **Version** : 1.1.0
- **Prérequis** : WordPress 6.0+, PHP 7.4+
- **Optionnel** : plugin **IAM Leads** (pour le formulaire de candidature embarqué)

---

## Installation

1. Copier le dossier `iam-certifications` dans `wp-content/plugins/`.
2. Dans **Extensions**, activer « IAM Certifications ».
3. Au premier chargement de l'admin, le plugin **crée automatiquement les 8 certifications** du site (sans prix ni partenaire) et, si IAM Leads est actif, **le formulaire pro multi-étapes**.

> Le seed est **idempotent** : il ne s'exécute qu'une fois (protégé par des options). Si IAM Leads est activé *après* IAM Certifications, le formulaire est créé au chargement d'admin suivant.

---

## Utilisation

### Afficher la liste — shortcode `[iam_certifications]`

À coller dans un widget *Shortcode* d'Elementor ou dans une page.

| Attribut | Rôle | Défaut |
|---|---|---|
| `type` | `certificat` ou `seminaire` (vide = tous) | — |
| `columns` | colonnes grand écran (1–4) | `3` |
| `domaine` | slug(s) de domaine, séparés par des virgules | — |
| `outil` | slug(s) d'outil | — |
| `ids` | formations précises (ID séparés par des virgules), affichées dans cet ordre | — |
| `featured` | `true` = mises en avant uniquement | — |
| `limit` | nombre maximum | `-1` |
| `max_tools` | outils affichés avant « +N » | `6` |
| `group_by_domaine` | `true` = sections titrées par domaine | — |
| `filters` | `true` = barre de filtre cliquable | — |

Exemples :
```
[iam_certifications columns="3" filters="true"]
[iam_certifications group_by_domaine="true"]
[iam_certifications domaine="marketing-digital" columns="4"]
[iam_certifications featured="true" limit="3"]
[iam_certifications type="seminaire" group_by_domaine="true"]
[iam_certifications type="certificat" filters="true"]
```

### Deux types de formation

Chaque formation est soit un **certificat**, soit un **séminaire**. Le type se choisit en
première étape de l'assistant (cartes de choix) ; les champs, les libellés et la page de
détail s'adaptent. Les fiches créées avant la v1.2 sont des certificats.

| | Certificat professionnel | Séminaire intensif |
|---|---|---|
| Chiffres clés du hero | heures, semaines, modules, leçons | jours, heures, % de pratique, participants max |
| Programme | **frise de modules** qui se remplit au scroll, leçons dépliables, projet final mis en avant | **jour par jour** (J1, J2, J3…), matin / après-midi |
| Format | format, durée, rythme, niveau | jauge animée « % de pratique », groupe, horaires, suivi eLearning, infos pratiques |
| Fin de parcours | débouchés (métiers) + certificat délivré | livrables + attestation de réussite |
| Spécifique | — | **sessions datées** en cartes « billet » (+ carte intra) ; la session cliquée est transmise avec le lead |
| Bouton principal | « Candidater » | « Réserver ma place » |

### Page de détail

Chaque formation a sa page (`/certifications/<slug>/`) :

1. **Hero** plein écran : fil d'Ariane, type, titre révélé mot à mot (un titre « X — sous-titre »
   est séparé en deux), accroche, boutons, fiche « En bref » en verre dépoli (inclinaison 3D au
   survol), chiffres clés avec compteurs animés.
2. **Bandeau d'outils** défilant (taxonomie Outils).
3. **Navigation collante** : section active surlignée, barre de progression de lecture,
   bouton d'action affiché une fois la barre collée.
4. Sections (masquées si vides) : objectifs en cartes numérotées, programme, format, pour qui
   (public en puces + prérequis), sessions, débouchés, certificat / livrables (diplôme 3D).
5. Téléchargements, **candidature** (formulaire IAM Leads + contact direct), formations proches.
6. Mobile : barre d'action flottante en bas d'écran.

Animations GSAP + ScrollTrigger (bundlés en local). Avec `prefers-reduced-motion` ou sans
JavaScript, tout le contenu reste visible et statique.

### Interface d'administration dédiée

Menu **Formations** : **Catalogue** (liste filtrable Tout / Certificats / Séminaires, boutons
« Nouveau certificat » et « Nouveau séminaire »), **Ajouter**, **Domaines**, **Outils**.

L'assistant de saisie (inspiré des formulaires de création de ticket-place) compte 6 étapes,
avec stepper, barre de progression et « Ensuite : … » :

1. **Identité** : type (cartes), titre, accroche, domaine / collection, visuel (zone de dépôt).
2. **Format & durée** / **Format & sessions** : chiffres clés ; pour un séminaire, horaires, suivi, sessions (« dates | lieu », une par ligne) et infos pratiques.
3. **Contenu & public** : objectifs, public, prérequis, puis débouchés + projet final (certificat) ou livrables (séminaire), évaluation, coût.
4. **Modules** (répéteur titre + leçons) ou **Jour par jour** (répéteur J1, J2… matin / après-midi, 3 jours préparés).
5. **Médias & liens** : vidéo, image secondaire, brochure, dossier / bulletin, rendez-vous, texte du bouton.
6. **Publication** : outils, formulaire IAM Leads, mise en avant, check-list de vérification.

Seul le titre est obligatoire. « Brouillon » enregistre à tout moment ; après
enregistrement, l'assistant rouvre l'étape où l'on était.

---

## Intégration IAM Leads

- La landing embarque le formulaire via `[iam_form id="X"]`. L'ID vient du réglage
  de la certification, sinon du **formulaire « certifications » par défaut** créé au seed.
- Le formulaire par défaut est **multi-étapes**, pensé pour les professionnels :
  1. Profil professionnel (nom, poste, entreprise, secteur, taille)
  2. Projet (objectif, financement, disponibilité)
  3. Coordonnées (e-mail pro, téléphone, contact préféré, consentement)
- Un **champ caché `certification`** (mappé sur la colonne `programme` d'IAM Leads) est
  rempli automatiquement par la landing : chaque lead est ainsi rattaché à SA certification
  (visible dans le dashboard, l'export et l'e-mail de notification). En complément,
  `utm_campaign` reçoit le slug **s'il est vide** (ne remplace jamais une vraie campagne).
- Au seed, **banel@groupeiam.com** est ajouté aux destinataires internes d'IAM Leads
  (Réglages → Destinataires internes), sans écraser les adresses existantes.

> ⚠️ Pour que les e-mails partent réellement, le **SMTP doit être configuré** dans
> IAM Leads → Réglages (l'image Docker WordPress n'a pas de service d'envoi). Sans SMTP,
> **les leads restent enregistrés en base** et visibles dans le dashboard.

Si IAM Leads est absent, la landing affiche un **repli contact direct** (e-mail + téléphone).

### Contact

Par défaut : **Banel — banel@groupeiam.com — +221 77 714 39 91**. Personnalisable via le filtre :
```php
add_filter( 'iam_cert_contact', function ( $c ) {
	$c['email'] = 'autre@groupeiam.com';
	return $c;
} );
```

---

## Surcharge des templates par le thème

Copier un template dans le thème pour le personnaliser :

- `votre-theme/iam-certifications/card.php` (carte de liste)
- `votre-theme/single-certification.php` *(ou)* `votre-theme/iam-certifications/single-certification.php` (page de détail)

---

## Contenu pré-rempli

**v1.2** ajoute les **8 séminaires** du catalogue 2026-2027 (collections « Talents × IA » et
« Marketing Digital & IA » : programme J1–J3, objectifs, public, prérequis, livrables) et le
certificat **CEDLA** (`includes/data/seminaires-seed.php`), sans prix ni partenaire. Les
sessions ne sont pas datées (à saisir dans l'admin). Import idempotent (option
`iam_cert_seed_v12`) ; les fiches existantes reçoivent le type « certificat ».

Les 8 certifications sont **pré-remplies à partir du contenu réel** des fiches
`/certification/` de groupeiam.com (accroche, modules, leçons, niveau, format, public,
prérequis, évaluation, outils), **sans le prix ni le partenaire**.

À faire après activation :
- **Ajouter les images** mises en avant (le seed n'importe pas les visuels).
- Relire les textes dans l'admin si besoin.

> Note : sur le site, les slugs d'URL des fiches sont mélangés, mais le contenu correspond
> bien aux 8 formations ; le plugin les recrée avec des slugs propres.

---

## Structure

```
iam-certifications/
├── iam-certifications.php          Amorçage, constantes, activation/flush
├── uninstall.php                   Nettoyage des options (conserve les données)
├── includes/
│   ├── iam-cert-helpers.php        Templates surchargeables, données, modules, contact, rendu formulaire
│   ├── class-iam-cpt.php           CPT « certification »
│   ├── class-iam-taxonomies.php    Taxonomies domaine + outil
│   ├── class-iam-metaboxes.php     Metabox : infos, programme (répéteur modules), formulaire
│   ├── class-iam-admin.php         Colonnes, tri, filtre par domaine
│   ├── class-iam-assets.php        Chargement CSS/JS
│   ├── class-iam-shortcode.php     [iam_certifications]
│   ├── class-iam-template.php      Route de la page de détail
│   ├── class-iam-seeder.php        Seed : 8 certifs + formulaire pro
│   └── data/
│       └── certifications-seed.php Données scrapées des 8 fiches
├── templates/
│   ├── card.php                    Carte de liste
│   └── single-certification.php    Page de détail (landing)
└── assets/
    ├── css/iam-front.css           Design --ckd-* responsive
    ├── css/iam-admin.css
    ├── js/iam-front.js             Filtre + pont formulaire
    └── js/iam-admin.js             Répéteur de modules
```

---

## Mises à jour automatiques (GitHub)

Le plugin se met à jour depuis les **Releases** du dépôt
[SofianeKOINDA/iam_certification](https://github.com/SofianeKOINDA/iam_certification)
grâce à la bibliothèque *Plugin Update Checker* (`lib/plugin-update-checker`, licence MIT).

**Publier une version**
1. Monter la version dans `iam-certifications.php` (en-tête `Version:` **et** constante `IAM_CERT_VERSION`).
2. Commiter, pousser, puis créer le tag correspondant :
   ```
   git tag v1.4.1
   git push origin v1.4.1
   ```
3. La GitHub Action `.github/workflows/release.yml` vérifie que tag et version concordent,
   contrôle la syntaxe PHP, construit `iam-certifications.zip` et publie la Release.

**Côté WordPress**
- Une fois la 1.4.0 installée (dernière installation manuelle), la mise à jour apparaît dans
  *Extensions* comme pour tout plugin (vérification toutes les 12 h, ou « Vérifier les mises à jour »).
- Activer « Activer les mises à jour auto » sur la ligne du plugin pour qu'elles s'installent seules.
- Après une mise à jour, les imports de données éventuels s'exécutent au premier passage d'un
  administrateur dans l'admin.
- Dépôt passé en privé : ajouter dans `wp-config.php`
  `define( 'IAM_CERT_GITHUB_TOKEN', 'ghp_…' );` (jeton en lecture seule).

## Journal des versions

### 1.7.3
- **Mise à jour automatique en 30 minutes au lieu de plusieurs heures** : une tâche planifiée (`iam_cert_maj_auto`, toutes les 30 minutes) interroge GitHub et, si une version plus récente existe et que « mises à jour auto » est activé pour IAM Certifications dans Extensions, l'installe aussitôt avec l'outil de mise à jour automatique de WordPress. Auparavant, WordPress n'installait les mises à jour automatiques que deux fois par jour. La tâche s'arrête à la désactivation du plugin. WP-Cron s'exécutant au passage des visiteurs, le délai réel dépend aussi du trafic du site.

### 1.7.2
- **Tarif de tous les certificats** (fourchette 350 000 à 450 000 FCFA, fixée selon la durée, le volume horaire et la valeur métier) : 450 000 pour CEDLA, CRM Social & E-CRM et Consultant fonctionnel Odoo ; 400 000 pour Content Marketing, No Code, Marketing Automation et ADAPT-IA ; 350 000 pour Social Media Marketing et Knowledge Management RH (formats les plus courts). Les 19 certificats BBA restent à 400 000. Données : `includes/data/tarifs-v172.php`, posées au premier passage dans l'admin (option `iam_cert_seed_v172`) uniquement si le tarif est vide. La section « Investissement » et le tarif de l'encadré « En bref » apparaissent donc sur tous les certificats.

### 1.7.1
- **Section « Investissement » sur la page de détail** : le petit encadré « Coût et financement » devient un bandeau vert profond pleine largeur, avec le montant en très grand (compteur animé de 0 au prix, séparateurs de milliers), la phrase qui l'accompagne, la carte « Ce qui est inclus » (durée, format, nombre de modules ou de jours, eLearning, certificat), un bouton « Candidater » pulsé et le lien vers la brochure. Lien « Tarif » ajouté à la barre de navigation de la fiche. Animations désactivées si le visiteur a demandé à réduire les mouvements ; le montant final s'affiche quoi qu'il arrive.

### 1.7.0
- **Catalogue « Certificats professionnels 2025-2026 » (IAM E-Learning Center)** : les 19 certificats du parcours diplômant BBA, 100 % en ligne, 3 mois chacun. Prix du catalogue majoré de 50 000 FCFA : **400 000 FCFA**. Repris du catalogue : titres et cours (codes GES). Rédigé au niveau des autres certificats : accroche, un module par cours avec ses leçons, objectifs, débouchés, projet final, évaluation, public, prérequis (niveau BAC), rythme, outils. Niveau déduit du code des cours (1xxx débutant, 2xxx intermédiaire, 3xxx avancé). Domaine « Gestion & Management (BBA) ». Données dans `includes/data/certificats-bba-seed.php`, import au premier passage dans l'admin (option `iam_cert_seed_v17`).
- **Tarif dans l'encadré « En bref »** de la page de détail : le montant (FCFA, XOF ou €) est repris du champ « Coût et financement ».
- **19 visuels** (Unsplash, licence libre) montrant des professionnels et étudiants noirs, cœur de cible du Groupe IAM, posés seulement sur les fiches sans visuel.

### 1.6.0
- **Catalogue IAM Executive — Data & Business Intelligence** : 3 formations courtes au format séminaire, hybrides (présentiel Dakar ou intra, et à distance) : *Power BI : concevoir des tableaux de bord décisionnels* (3 jours), *Analyse de données pour managers* (2 jours), *Data visualisation & storytelling* (2 jours). Repris du catalogue : accroche, objectifs, compétences, public ; complété de façon cohérente avec les séminaires existants : durée, programme jour par jour, prérequis, livrables, évaluation, infos pratiques. Données dans `includes/data/executive-seed.php`, import au premier passage dans l'admin (option `iam_cert_seed_v16`), avec leurs visuels.
- **Générateur de shortcode** (Formations → Shortcodes) : blocs à glisser-déposer ou à ajouter d'un clic (type, mises en avant, domaines, outils, formations précises, colonnes, nombre, tri, filtres, regroupement), shortcode écrit en direct, compteur de formations affichées, aperçu réel et shortcodes enregistrés (option `iam_cert_shortcodes`).
- Shortcode : nouvel attribut `ids="12,5,9"` pour afficher des formations précises, dans cet ordre.

### 1.5.0
- **Visuels des formations** : une photo par certificat et par séminaire (17), livrée dans `assets/img/formations/<slug>.jpg` (Unsplash, licence libre d'usage commercial). Au premier passage dans l'admin (option `iam_cert_seed_v15`), chaque photo est ajoutée à la médiathèque et posée comme image mise en avant, **uniquement si la fiche n'en a pas** : un visuel choisi à la main n'est jamais remplacé. Elle apparaît dans le hero de la page, sur les cartes du catalogue et dans l'admin.

### 1.4.2
- Version de test de la chaîne de mises à jour automatiques (aucun changement fonctionnel).

### 1.4.1
- La page de détail s'ouvre sur le hero : le formulaire IAM Leads multi-étapes (< 1.5.3) faisait défiler la page jusqu'à lui et y plaçait le focus dès le chargement. Garde-fou côté plugin (sans effet avec IAM Leads 1.5.3, qui corrige la cause).

### 1.4.0
- Mises à jour automatiques depuis les Releases GitHub (Plugin Update Checker 5.7) et publication automatisée par GitHub Action.

### 1.3.1
- **Réparation des données** (option `iam_cert_seed_v131`) : les fiches créées avant les imports restaient incomplètes en production (accroche, modules, format, niveau, domaine, outils…). Chaque champ **vide** est rempli depuis les catalogues, l'extrait des cartes suit l'accroche, puis les corrections 1.3 sont réappliquées. Rien de renseigné n'est modifié. S'exécute au premier chargement d'une page d'admin par un administrateur.
- **Compatibilité thème Univet / Elementor** : titre du hero forcé en blanc, icônes SVG protégées du remplissage imposé par le thème, barre de navigation de la page placée sous le header du site quand il réapparaît au scroll.
- **Animations** : utilisation de nos propres instances GSAP / ScrollTrigger (le thème charge d'autres versions de GSAP), filets de sécurité (hero, compteurs et apparitions au scroll toujours affichés dans leur état final).

### 1.3.0
- Page de détail : direction « sobre + temps forts verts » (hero, programme, sessions / débouchés, candidature en vert ; accent `#EDA900`), formulaire IAM Leads aux couleurs IAM (surcharge CSS, IAM Leads inchangé), logo du Groupe IAM sur le diplôme (filtre `iam_cert_logo_url`).
- Admin : écrans centrés, écrans maison **Domaines** et **Outils** (les écrans natifs redirigent), champs obligatoires pour publier (contrôle navigateur + serveur ; une fiche déjà en ligne n'est jamais dépubliée).
- Données : compléments tirés des catalogues PDF (`includes/data/complements-v13.php`, option `iam_cert_seed_v13`) — objectifs et projet final des 9 certificats, rythme, corrections Knowledge Management RH (format atelier, mention tarifaire retirée), ADAPT-IA (blended, 10 à 12 semaines). Non destructif : seuls les champs vides ou encore identiques à l'import d'origine sont touchés.

### 1.2.0
- Deux types de formation : **certificat** et **séminaire** (méta `_iam_type`), champs propres à chaque type.
- Nouvelle page de détail (hero animé, compteurs, frise de modules, programme jour par jour, sessions, diplôme 3D, navigation collante).
- Nouvel assistant d'administration en 6 étapes et nouvelle liste du catalogue.
- Import des 8 séminaires et du certificat CEDLA ; shortcode : attribut `type`.

### 1.1.0
- Nouvelle page de détail en bandes pleine largeur (hero sur image, blocs dépliables colorés, bande débouchés, bande modalités, bandeau de téléchargement).
- Nouveaux champs : objectifs, débouchés, rythme, coût et scolarité, vidéo, image secondaire, brochure, dossier de candidature, lien de RDV.
- Fenêtre vidéo (chargement au clic), barre « Candidater » sur mobile.
- Admin : nouvelle étape « Médias & liens », sélecteur Médiathèque pour les fichiers, lien « Voir la page ».
- Texte du bouton par défaut : « Candidater ».
- Animations : une seule entrée orchestrée sur le hero (suppression des apparitions à chaque section).

### 1.0.0
- Version initiale.
