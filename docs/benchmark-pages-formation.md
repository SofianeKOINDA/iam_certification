# Benchmark — pages de détail de formations

Objectif : concevoir la page `/certifications/<slug>/` du Groupe IAM pour deux produits
différents, les **certificats** (6 à 12 semaines, en ligne) et les **séminaires** (3 jours,
présentiel ou intra).

## Acteurs observés

| Acteur | Format comparable | Ce qu'il fait bien | Ce qu'on reprend |
|---|---|---|---|
| **Cegos** | Séminaires inter / intra de 2-3 jours | Fiche très factuelle : durée, référence, programme **jour par jour**, **calendrier des sessions** avec ville et bouton « S'inscrire », option intra sur devis | Programme J1/J2/J3, sessions datées cliquables, carte « intra-entreprise » |
| **HEC Paris Executive Education** (programmes Afrique, COFEB Dakar) | Programmes courts (4 jours) et certificats | Hero éditorial, fiche « en bref » (durée, format, prochaine date), brochure téléchargeable | Hero typographique + fiche « En bref » à droite, bouton brochure |
| **Coursera** (Professional Certificates) | Certificats en ligne de quelques semaines | Rangée de chiffres clés (niveau, durée, rythme), compétences en puces, liste de cours dépliables, **aperçu du certificat** | Compteurs animés, modules dépliables, diplôme mis en scène |
| **OpenClassrooms** | Parcours certifiants en ligne | Projets professionnalisants, mentorat, soutenance, débouchés métiers | Projet final mis en avant, section « Débouchés » |
| **Simplon / Orange Digital Center (Sénégal)** | Formations courtes et qualifiantes | Ancrage local, public éloigné de l'emploi, gratuité | Formulations simples, « Pour qui » très concret |

## Ce que les pages efficaces ont en commun

1. **Tout ce qui décide est au-dessus de la ligne de flottaison** : type, durée, format, prochaine
   session et un seul bouton d'action.
2. **Le programme est le cœur de la page**, découpé en unités visibles d'un coup d'œil
   (modules ou jours), détail au clic.
3. **Navigation dans la page** collante sur les fiches longues (Coursera, HEC).
4. **La preuve de valeur** : certificat montré, livrables concrets, métiers visés.
5. **La conversion arrive après la preuve** : formulaire court, étapes annoncées
   (« 3 étapes »), contact humain nommé.

## Écarts qu'on exploite

- Les sites concurrents traitent séminaire et certificat avec le même gabarit. Ici la page
  **change de mise en scène selon le type** (frise de modules vs. programme horaire, débouchés
  vs. livrables, rentrée vs. sessions).
- Peu de pages formation en Afrique de l'Ouest soignent le mouvement : révélation du titre,
  compteurs, frise qui se remplit au scroll, jauge de pratique, diplôme en 3D, toujours désactivés
  si l'utilisateur préfère moins d'animations.
- Les sessions sont des « billets » cliquables : la session choisie est transmise au lead
  IAM Leads (champ `certification` → `programme`).

## Sources

- [Offre Cegos (catalogue 2020, formats inter / intra, calendrier des sessions)](https://static3.cegos.fr/content/uploads/2020/10/14120427/Offre-Formation-Resah-l-Selection-Cegos-2020.pdf)
- [HEC Paris Executive Education — programmes Afrique](https://www.hec.edu/fr/executive-education/nos-programmes-pour-les-particuliers/par-domaine-de-formation/afrique)
- [BCEAO / COFEB × HEC Paris — certificats à Dakar](https://www.bceao.int/fr/communique-presse/partenariat-bceao-cofeb-et-hec-paris-lancement-dune-nouvelle-gamme-de-programmes)
- [Coursera — exemple de Professional Certificate](https://www.coursera.org/professional-certificates/canva-essentials)
- [OpenClassrooms — fonctionnement des parcours](https://openclassrooms.com/fr/how-does-it-work)
- [Simplon au Sénégal](https://www.osiris.sn/capacit-4-africa-simplon-booste-la-formation-numerique-pour-un-developpement.html)
- [Orange Digital Center Dakar](https://osiris.sn/senegal-orange-inaugure-un-centre-hi-tech-de-formation-aux-metiers-du-digital.html)
