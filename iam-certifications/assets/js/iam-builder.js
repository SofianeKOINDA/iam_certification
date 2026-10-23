/*
 * Générateur de shortcode [iam_certifications].
 *
 * Un état unique (la liste des blocs posés, dans l'ordre) d'où tout est
 * recalculé : zone de construction, palette, shortcode, compteur, notes et
 * aperçu. Les blocs s'ajoutent par glisser-déposer ou par simple clic
 * (clavier et mobile).
 */
( function () {
	'use strict';

	var D = window.iamBuilder;
	var racine = document.querySelector( '.iam-builder' );

	if ( ! D || ! racine ) {
		return;
	}

	var $ = function ( sel ) {
		return racine.querySelector( sel );
	};

	var el = {
		palette: $( '[data-iam-palette]' ),
		recherche: $( '[data-iam-search]' ),
		zone: $( '[data-iam-zone]' ),
		vide: $( '[data-iam-empty]' ),
		notes: $( '[data-iam-notes]' ),
		compte: $( '[data-iam-count]' ),
		code: $( '[data-iam-code]' ),
		copier: $( '[data-iam-copy]' ),
		copierLibelle: $( '[data-iam-copy-label]' ),
		saveCode: $( '[data-iam-save-code]' ),
		saveBlocs: $( '[data-iam-save-blocs]' ),
		apercu: $( '[data-iam-preview]' )
	};

	/* ── Catalogue des blocs ────────────────────────────────────── */

	var BLOCS = {};
	var GROUPES = [];

	function groupe( titre, icone, couleur, blocs ) {
		GROUPES.push( { titre: titre, icone: icone, couleur: couleur, cles: blocs.map( function ( b ) {
			BLOCS[ b.cle ] = Object.assign( { couleur: couleur }, b );
			return b.cle;
		} ) } );
	}

	groupe( 'Contenu', 'category', 'vert', [
		{ cle: 'type:certificat', libelle: 'Certificats seulement', icone: 'awards', exclusif: 'type' },
		{ cle: 'type:seminaire', libelle: 'Séminaires seulement', icone: 'groups', exclusif: 'type' },
		{ cle: 'featured', libelle: 'Mises en avant seulement', icone: 'star-filled' }
	] );

	groupe( 'Mise en page', 'layout', 'encre', [
		{ cle: 'columns', libelle: 'Colonnes', icone: 'columns', defaut: '3', choix: [ [ '1', '1' ], [ '2', '2' ], [ '3', '3' ], [ '4', '4' ] ] },
		{ cle: 'limit', libelle: 'Nombre maximum', icone: 'editor-ol', defaut: '6', nombre: [ 1, 50 ] },
		{ cle: 'orderby', libelle: 'Tri', icone: 'sort', defaut: 'titre', choix: [ [ 'titre', 'Titre A → Z' ], [ 'recent', 'Plus récentes' ], [ 'manuel', 'Ordre du catalogue' ] ] },
		{ cle: 'filters', libelle: 'Barre de filtres par domaine', icone: 'filter' },
		{ cle: 'group', libelle: 'Grouper par domaine', icone: 'excerpt-view' },
		{ cle: 'max_tools', libelle: 'Outils affichés par carte', icone: 'admin-tools', defaut: '4', nombre: [ 0, 12 ] }
	] );

	if ( D.domaines.length ) {
		groupe( 'Domaines', 'tag', 'or', D.domaines.map( function ( d ) {
			return { cle: 'd:' + d.slug, libelle: d.nom, icone: 'tag', slug: d.slug };
		} ) );
	}

	if ( D.outils.length ) {
		groupe( 'Outils', 'admin-tools', 'bleu', D.outils.map( function ( o ) {
			return { cle: 'o:' + o.slug, libelle: o.nom, icone: 'admin-tools', slug: o.slug };
		} ) );
	}

	groupe( 'Formations précises', 'welcome-learn-more', 'terre', D.formations.map( function ( f ) {
		return {
			cle: 'f:' + f.id,
			libelle: f.titre,
			icone: 'seminaire' === f.type ? 'groups' : 'awards',
			image: f.image,
			id: f.id,
			ordonnable: true
		};
	} ) );

	/* ── État ───────────────────────────────────────────────────── */

	// [ { cle, valeur } ] dans l'ordre de la zone.
	var etat = [];

	function present( cle ) {
		return etat.some( function ( b ) {
			return b.cle === cle;
		} );
	}

	function ajouter( cle, index ) {
		var def = BLOCS[ cle ];

		if ( ! def || present( cle ) ) {
			return;
		}

		// Un seul bloc de type à la fois : le nouveau remplace l'ancien.
		if ( def.exclusif ) {
			etat = etat.filter( function ( b ) {
				return BLOCS[ b.cle ].exclusif !== def.exclusif;
			} );
		}

		var bloc = { cle: cle, valeur: def.defaut || '' };

		if ( 'number' === typeof index && index >= 0 && index <= etat.length ) {
			etat.splice( index, 0, bloc );
		} else {
			etat.push( bloc );
		}

		dernierAjout = cle;
		rafraichir();
	}

	function retirer( cle ) {
		etat = etat.filter( function ( b ) {
			return b.cle !== cle;
		} );
		rafraichir();
	}

	function deplacer( cle, index ) {
		var depart = etat.findIndex( function ( b ) {
			return b.cle === cle;
		} );

		if ( depart < 0 ) {
			return;
		}

		var bloc = etat.splice( depart, 1 )[ 0 ];
		if ( index > depart ) {
			index--;
		}
		etat.splice( Math.max( 0, Math.min( index, etat.length ) ), 0, bloc );
		rafraichir();
	}

	/* ── Shortcode ──────────────────────────────────────────────── */

	function attributs() {
		var a = {};
		var domaines = [];
		var outils = [];
		var ids = [];

		etat.forEach( function ( b ) {
			var def = BLOCS[ b.cle ];

			if ( 0 === b.cle.indexOf( 'type:' ) ) {
				a.type = b.cle.slice( 5 );
			} else if ( 'featured' === b.cle ) {
				a.featured = 'true';
			} else if ( 'columns' === b.cle && '3' !== b.valeur ) {
				a.columns = b.valeur;
			} else if ( 'limit' === b.cle && b.valeur ) {
				a.limit = b.valeur;
			} else if ( 'orderby' === b.cle ) {
				if ( 'titre' === b.valeur ) {
					a.orderby = 'title';
				} else if ( 'recent' === b.valeur ) {
					a.orderby = 'date';
					a.order = 'DESC';
				}
			} else if ( 'filters' === b.cle ) {
				a.filters = 'true';
			} else if ( 'group' === b.cle ) {
				a.group_by_domaine = 'true';
			} else if ( 'max_tools' === b.cle && '6' !== b.valeur ) {
				a.max_tools = b.valeur;
			} else if ( 0 === b.cle.indexOf( 'd:' ) ) {
				domaines.push( def.slug );
			} else if ( 0 === b.cle.indexOf( 'o:' ) ) {
				outils.push( def.slug );
			} else if ( 0 === b.cle.indexOf( 'f:' ) ) {
				ids.push( def.id );
			}
		} );

		if ( domaines.length ) {
			a.domaine = domaines.join( ',' );
		}
		if ( outils.length ) {
			a.outil = outils.join( ',' );
		}
		if ( ids.length ) {
			a.ids = ids.join( ',' );
			// Les formations choisies gardent leur ordre : le tri ne s'applique pas.
			delete a.orderby;
			delete a.order;
		}

		return a;
	}

	var ORDRE = [ 'type', 'ids', 'domaine', 'outil', 'featured', 'limit', 'columns', 'orderby', 'order', 'group_by_domaine', 'filters', 'max_tools' ];

	function shortcode( a ) {
		return '[iam_certifications' + ORDRE.filter( function ( k ) {
			return undefined !== a[ k ];
		} ).map( function ( k ) {
			return ' ' + k + '="' + a[ k ] + '"';
		} ).join( '' ) + ']';
	}

	/* ── Compteur : formations que le shortcode affichera ───────── */

	function correspondances( a ) {
		var dom = a.domaine ? a.domaine.split( ',' ) : [];
		var out = a.outil ? a.outil.split( ',' ) : [];
		var ids = a.ids ? a.ids.split( ',' ).map( Number ) : [];

		var liste = D.formations.filter( function ( f ) {
			if ( a.type && a.type !== f.type ) {
				return false;
			}
			if ( a.featured && ! f.featured ) {
				return false;
			}
			if ( dom.length && ! dom.some( function ( s ) {
				return f.domaines.indexOf( s ) > -1;
			} ) ) {
				return false;
			}
			if ( out.length && ! out.some( function ( s ) {
				return f.outils.indexOf( s ) > -1;
			} ) ) {
				return false;
			}
			if ( ids.length && ids.indexOf( f.id ) < 0 ) {
				return false;
			}
			return true;
		} );

		return a.limit ? Math.min( liste.length, +a.limit ) : liste.length;
	}

	/* ── Rendu ──────────────────────────────────────────────────── */

	var dernierAjout = null;

	function icone( nom ) {
		return '<span class="dashicons dashicons-' + nom + '" aria-hidden="true"></span>';
	}

	function echapper( texte ) {
		var d = document.createElement( 'div' );
		d.textContent = texte;
		return d.innerHTML;
	}

	function rendrePalette() {
		var filtre = ( el.recherche.value || '' ).trim().toLowerCase();

		el.palette.innerHTML = GROUPES.map( function ( g ) {
			var cles = g.cles.filter( function ( cle ) {
				return ! filtre || BLOCS[ cle ].libelle.toLowerCase().indexOf( filtre ) > -1;
			} );

			if ( ! cles.length ) {
				return '';
			}

			return '<details class="iam-pal" open><summary>' + icone( g.icone ) + echapper( g.titre ) + ' <small>' + cles.length + '</small></summary>' +
				'<div class="iam-pal__items">' + cles.map( function ( cle ) {
					var b = BLOCS[ cle ];
					var pris = present( cle );
					var vignette = b.image ? '<img src="' + b.image + '" alt="" loading="lazy">' : icone( b.icone );

					return '<button type="button" class="iam-chip iam-chip--' + b.couleur + ( pris ? ' is-used' : '' ) + '" draggable="' + ( pris ? 'false' : 'true' ) + '" data-cle="' + echapper( cle ) + '"' + ( pris ? ' aria-disabled="true"' : '' ) + ' title="' + echapper( b.libelle ) + '">' +
						vignette + '<span>' + echapper( b.libelle ) + '</span>' + ( pris ? icone( 'yes' ) : icone( 'plus-alt2' ) ) + '</button>';
				} ).join( '' ) + '</div></details>';
		} ).join( '' );
	}

	function controle( b, def ) {
		if ( def.choix ) {
			return '<select data-valeur aria-label="' + echapper( def.libelle ) + '">' + def.choix.map( function ( c ) {
				return '<option value="' + c[ 0 ] + '"' + ( c[ 0 ] === b.valeur ? ' selected' : '' ) + '>' + echapper( c[ 1 ] ) + '</option>';
			} ).join( '' ) + '</select>';
		}
		if ( def.nombre ) {
			return '<input type="number" data-valeur min="' + def.nombre[ 0 ] + '" max="' + def.nombre[ 1 ] + '" value="' + echapper( b.valeur ) + '" aria-label="' + echapper( def.libelle ) + '">';
		}
		return '';
	}

	function rendreZone() {
		Array.prototype.slice.call( el.zone.querySelectorAll( '.iam-bloc' ) ).forEach( function ( n ) {
			n.remove();
		} );

		el.vide.hidden = etat.length > 0;

		etat.forEach( function ( b ) {
			var def = BLOCS[ b.cle ];
			var n = document.createElement( 'div' );
			var vignette = def.image ? '<img src="' + def.image + '" alt="">' : icone( def.icone );

			n.className = 'iam-bloc iam-chip--' + def.couleur + ( dernierAjout === b.cle ? ' is-new' : '' );
			n.draggable = true;
			n.dataset.cle = b.cle;
			n.innerHTML =
				'<span class="iam-bloc__poignee" aria-hidden="true">' + icone( 'menu' ) + '</span>' +
				'<span class="iam-bloc__icone">' + vignette + '</span>' +
				'<span class="iam-bloc__libelle">' + echapper( def.libelle ) + '</span>' +
				controle( b, def ) +
				( def.ordonnable ? '<button type="button" class="iam-bloc__btn" data-monter title="Monter">' + icone( 'arrow-up-alt2' ) + '</button><button type="button" class="iam-bloc__btn" data-descendre title="Descendre">' + icone( 'arrow-down-alt2' ) + '</button>' : '' ) +
				'<button type="button" class="iam-bloc__btn iam-bloc__btn--retirer" data-retirer title="Retirer">' + icone( 'no-alt' ) + '</button>';
			el.zone.appendChild( n );
		} );

		dernierAjout = null;
	}

	function rendreNotes( a ) {
		var notes = [];

		if ( a.group_by_domaine && a.filters ) {
			notes.push( 'La barre de filtres n\'apparaît pas quand les formations sont groupées par domaine.' );
		}
		if ( a.ids && ( a.type || a.domaine || a.outil || a.featured ) ) {
			notes.push( 'Les formations choisies sont aussi filtrées par les autres blocs : une formation qui ne correspond pas n\'apparaîtra pas.' );
		}
		if ( a.ids && present( 'orderby' ) ) {
			notes.push( 'Le tri est ignoré : les formations choisies s\'affichent dans l\'ordre de la zone.' );
		}

		el.notes.innerHTML = notes.map( function ( t ) {
			return '<li>' + icone( 'info-outline' ) + echapper( t ) + '</li>';
		} ).join( '' );
	}

	var minuterie = null;

	function rafraichirApercu( a ) {
		clearTimeout( minuterie );
		minuterie = setTimeout( function () {
			el.apercu.src = D.apercu + '&a=' + encodeURIComponent( JSON.stringify( a ) );
		}, 350 );
	}

	function rafraichir() {
		var a = attributs();
		var code = shortcode( a );
		var n = correspondances( a );

		rendrePalette();
		rendreZone();
		rendreNotes( a );

		el.code.textContent = code;
		el.saveCode.value = code;
		el.saveBlocs.value = JSON.stringify( etat );
		el.compte.innerHTML = '<b>' + n + '</b> ' + ( n > 1 ? 'formations' : 'formation' );
		el.compte.classList.toggle( 'is-zero', 0 === n );

		rafraichirApercu( a );
	}

	/* ── Glisser-déposer ────────────────────────────────────────── */

	var glisse = null; // { cle, depuis: 'palette' | 'zone' }
	var repere = document.createElement( 'div' );
	repere.className = 'iam-builder__repere';

	function indexDepot( y ) {
		var blocs = Array.prototype.slice.call( el.zone.querySelectorAll( '.iam-bloc:not(.is-dragging)' ) );

		for ( var i = 0; i < blocs.length; i++ ) {
			var r = blocs[ i ].getBoundingClientRect();
			if ( y < r.top + r.height / 2 ) {
				return { index: etat.findIndex( function ( b ) {
					return b.cle === blocs[ i ].dataset.cle;
				} ), avant: blocs[ i ] };
			}
		}
		return { index: etat.length, avant: null };
	}

	racine.addEventListener( 'dragstart', function ( e ) {
		var cible = e.target.closest( '.iam-chip[draggable="true"], .iam-bloc' );

		if ( ! cible ) {
			return;
		}

		glisse = { cle: cible.dataset.cle, depuis: cible.classList.contains( 'iam-bloc' ) ? 'zone' : 'palette' };
		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData( 'text/plain', glisse.cle );
		cible.classList.add( 'is-dragging' );
		el.zone.classList.add( 'is-waiting' );
	} );

	racine.addEventListener( 'dragend', function ( e ) {
		var cible = e.target.closest( '.iam-chip, .iam-bloc' );

		if ( cible ) {
			cible.classList.remove( 'is-dragging' );
		}
		glisse = null;
		repere.remove();
		el.zone.classList.remove( 'is-waiting', 'is-over' );
	} );

	el.zone.addEventListener( 'dragover', function ( e ) {
		if ( ! glisse ) {
			return;
		}
		e.preventDefault();
		el.zone.classList.add( 'is-over' );

		var pos = indexDepot( e.clientY );
		if ( pos.avant ) {
			el.zone.insertBefore( repere, pos.avant );
		} else {
			el.zone.appendChild( repere );
		}
	} );

	el.zone.addEventListener( 'dragleave', function ( e ) {
		if ( ! el.zone.contains( e.relatedTarget ) ) {
			el.zone.classList.remove( 'is-over' );
			repere.remove();
		}
	} );

	el.zone.addEventListener( 'drop', function ( e ) {
		if ( ! glisse ) {
			return;
		}
		e.preventDefault();

		var index = indexDepot( e.clientY ).index;
		if ( 'zone' === glisse.depuis ) {
			deplacer( glisse.cle, index );
		} else {
			ajouter( glisse.cle, index );
		}
	} );

	// Un bloc de la zone reposé sur la palette est retiré.
	el.palette.addEventListener( 'dragover', function ( e ) {
		if ( glisse && 'zone' === glisse.depuis ) {
			e.preventDefault();
		}
	} );
	el.palette.addEventListener( 'drop', function ( e ) {
		if ( glisse && 'zone' === glisse.depuis ) {
			e.preventDefault();
			retirer( glisse.cle );
		}
	} );

	/* ── Clics et saisies ───────────────────────────────────────── */

	el.palette.addEventListener( 'click', function ( e ) {
		var chip = e.target.closest( '.iam-chip' );

		if ( chip && ! chip.classList.contains( 'is-used' ) ) {
			ajouter( chip.dataset.cle );
		}
	} );

	el.zone.addEventListener( 'click', function ( e ) {
		var bloc = e.target.closest( '.iam-bloc' );

		if ( ! bloc ) {
			return;
		}

		var cle = bloc.dataset.cle;
		var i = etat.findIndex( function ( b ) {
			return b.cle === cle;
		} );

		if ( e.target.closest( '[data-retirer]' ) ) {
			retirer( cle );
		} else if ( e.target.closest( '[data-monter]' ) && i > 0 ) {
			deplacer( cle, i - 1 );
		} else if ( e.target.closest( '[data-descendre]' ) && i < etat.length - 1 ) {
			deplacer( cle, i + 2 );
		}
	} );

	el.zone.addEventListener( 'change', function ( e ) {
		var champ = e.target.closest( '[data-valeur]' );

		if ( ! champ ) {
			return;
		}

		var cle = champ.closest( '.iam-bloc' ).dataset.cle;
		etat.forEach( function ( b ) {
			if ( b.cle === cle ) {
				b.valeur = champ.value;
			}
		} );
		rafraichir();
	} );

	// Un champ de bloc ne déclenche pas le glisser de son bloc.
	el.zone.addEventListener( 'mousedown', function ( e ) {
		var bloc = e.target.closest( '.iam-bloc' );
		if ( bloc ) {
			bloc.draggable = ! e.target.closest( 'input, select, button' );
		}
	} );

	el.recherche.addEventListener( 'input', rendrePalette );

	function copier( texte, bouton, libelle ) {
		if ( ! navigator.clipboard ) {
			return;
		}
		navigator.clipboard.writeText( texte ).then( function () {
			bouton.classList.add( 'is-copied' );
			if ( libelle ) {
				libelle.textContent = 'Copié !';
			}
			setTimeout( function () {
				bouton.classList.remove( 'is-copied' );
				if ( libelle ) {
					libelle.textContent = 'Copier';
				}
			}, 1600 );
		} );
	}

	el.copier.addEventListener( 'click', function () {
		copier( el.code.textContent, el.copier, el.copierLibelle );
	} );

	racine.addEventListener( 'click', function ( e ) {
		var copie = e.target.closest( '[data-iam-copy-text]' );
		var charge = e.target.closest( '[data-iam-load]' );

		if ( copie ) {
			copier( copie.getAttribute( 'data-iam-copy-text' ), copie );
		}

		if ( charge ) {
			try {
				etat = JSON.parse( charge.getAttribute( 'data-iam-load' ) ).filter( function ( b ) {
					return BLOCS[ b.cle ];
				} );
			} catch ( err ) {
				etat = [];
			}
			rafraichir();
			el.zone.scrollIntoView( { behavior: 'smooth', block: 'center' } );
		}
	} );

	// Hauteur de l'aperçu ajustée à son contenu.
	window.addEventListener( 'message', function ( e ) {
		if ( e.source === el.apercu.contentWindow && e.data && e.data.iamApercu ) {
			el.apercu.style.height = Math.min( 2400, Math.max( 240, e.data.iamApercu ) ) + 'px';
		}
	} );

	rafraichir();
}() );
