<?php
/**
 * Tarifs v1.7.2 des certificats hors parcours BBA (les 19 BBA sont à
 * 400 000 FCFA depuis la v1.7). Fourchette fixée par le Groupe IAM :
 * 350 000 à 450 000 FCFA, 350 000 réservé aux parcours les plus courts.
 *
 * Posés seulement si « Coût et financement » est vide : un tarif saisi à
 * la main n'est jamais remplacé.
 *
 * @package IAM_Certifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(
	// 450 000 : parcours les plus longs ou les plus directement employables.
	'cedla-edtech-digital-learning-afrique'     => '450 000 FCFA pour le certificat complet (1 semestre, avec mission terrain et mémoire soutenu devant jury).',
	'crm-social-e-crm'                          => '450 000 FCFA pour le certificat complet (12 semaines, 100 % en ligne).',
	'consultant-fonctionnel-odoo-junior'        => '450 000 FCFA pour le certificat complet (10 semaines, 100 % en ligne).',

	// 400 000 : parcours complets de 9 à 12 semaines.
	'content-marketing-strategie-editoriale-ia' => '400 000 FCFA pour le certificat complet (11 semaines, 100 % en ligne).',
	'no-code-marketing-salestech'               => '400 000 FCFA pour le certificat complet (10 semaines, 100 % en ligne).',
	'marketing-automation-growth-hacking'       => '400 000 FCFA pour le certificat complet (9 semaines, 100 % en ligne).',
	'adapt-ia-adopter-lia-en-entreprise'        => '400 000 FCFA pour le certificat complet (12 semaines, 100 % en ligne).',

	// 350 000 : formats les plus courts.
	'social-media-marketing-social-selling'     => '350 000 FCFA pour le certificat complet (8 semaines, 100 % en ligne).',
	'knowledge-management-rh'                   => '350 000 FCFA pour le certificat complet (3 jours en format atelier).',
);
