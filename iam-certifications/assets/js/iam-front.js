/**
 * IAM Certifications — interactions front.
 * - Filtre par domaine (barre de boutons) : masque/affiche les cartes côté client.
 * - Page de détail : navigation collante, sessions, fenêtre vidéo, barre mobile.
 */
(function () {
	'use strict';

	function initFilters(root) {
		var bar = root.querySelector('.iam-cert-filters');
		if (!bar) {
			return;
		}

		var buttons = bar.querySelectorAll('.iam-cert-filter');
		var cards = root.querySelectorAll('.iam-cert-card');

		bar.addEventListener('click', function (event) {
			var btn = event.target.closest('.iam-cert-filter');
			if (!btn) {
				return;
			}

			var domaine = btn.getAttribute('data-domaine') || '';

			buttons.forEach(function (b) {
				b.classList.toggle('is-active', b === btn);
			});

			cards.forEach(function (card) {
				var match = !domaine || card.getAttribute('data-domaine') === domaine;
				card.classList.toggle('is-hidden', !match);
			});
		});
	}

	/**
	 * Injecte le nom de la formation dans le formulaire IAM Leads embarqué,
	 * pour qu'un lead soit rattaché à SA formation (et à sa session).
	 * - remplit le champ caché « certification » (mappé côté IAM Leads sur « programme ») ;
	 * - renseigne utm_campaign avec le slug uniquement s'il est vide
	 *   (on ne remplace jamais une campagne réelle d'où viendrait le visiteur).
	 */
	function setFormProgramme(session) {
		document.querySelectorAll('.iam-cert-form[data-cert-title]').forEach(function (wrap) {
			var title = wrap.getAttribute('data-cert-title') || '';
			var form = wrap.querySelector('.iam-form');
			if (!form) {
				return;
			}
			var champ = form.querySelector('[name="certification"]');
			if (champ) {
				champ.value = session ? title + ' — ' + session : title;
			}
		});
	}

	function initFormBridge() {
		setFormProgramme('');
		document.querySelectorAll('.iam-cert-form[data-cert-slug]').forEach(function (wrap) {
			var utm = wrap.querySelector('.iam-form [name="utm_campaign"]');
			if (utm && !utm.value) {
				utm.value = wrap.getAttribute('data-cert-slug') || '';
			}
		});
	}

	/**
	 * Séminaire : un clic sur une session la retient pour l'inscription
	 * (affichée au-dessus du formulaire et transmise avec le lead).
	 */
	function initSessions() {
		var label = document.querySelector('[data-iam-session-label]');
		document.addEventListener('click', function (e) {
			var ticket = e.target.closest('[data-iam-session]');
			if (!ticket) {
				return;
			}
			var session = ticket.getAttribute('data-iam-session') || '';
			setFormProgramme(session);
			if (label) {
				label.textContent = 'Session choisie : ' + session;
				label.hidden = false;
			}
		});
	}

	/**
	 * Navigation collante : lien actif selon la section visible, barre de
	 * progression de lecture, bouton d'action affiché une fois la barre collée.
	 */
	function initPageNav() {
		var nav = document.querySelector('[data-iam-nav]');
		if (!nav) {
			return;
		}
		var links = Array.prototype.slice.call(nav.querySelectorAll('a[href^="#"]:not(.iam-lp-btn)'));
		var progress = nav.querySelector('[data-iam-progress]');
		var page = nav.closest('.iam-lp');
		var ticking = false;

		var targets = links.map(function (a) {
			return document.getElementById(a.getAttribute('href').slice(1));
		});

		/* Header du thème fixé en haut (ex. réapparition au scroll) : on décale
		 * la barre pour qu'elle reste visible juste en dessous. */
		function siteHeaderBottom() {
			var x = Math.round(window.innerWidth / 2);
			// Sous la barre d'admin WordPress si elle est là (déjà prise en compte en CSS).
			var bar = document.getElementById('wpadminbar');
			var top = bar && getComputedStyle(bar).position === 'fixed' ? bar.offsetHeight : 0;
			var el = document.elementFromPoint(x, top + 2);
			while (el && el !== document.body) {
				if (page && page.contains(el)) {
					return 0;
				}
				var pos = getComputedStyle(el).position;
				if (pos === 'fixed' || pos === 'sticky') {
					var r = el.getBoundingClientRect();
					return r.top <= top + 1 && r.bottom > top && r.bottom < window.innerHeight / 3 ? Math.round(r.bottom - top) : 0;
				}
				el = el.parentElement;
			}
			return 0;
		}

		function update() {
			ticking = false;
			if (page) {
				var offset = 0;
				nav.style.visibility = 'hidden';
				offset = siteHeaderBottom();
				nav.style.visibility = '';
				page.style.setProperty('--site-header', offset + 'px');
			}
			var navRect = nav.getBoundingClientRect();
			var stuck = navRect.top <= parseFloat(getComputedStyle(nav).top) + 1;
			nav.classList.toggle('is-stuck', stuck);

			var line = navRect.bottom + 80;
			var current = -1;
			targets.forEach(function (t, i) {
				if (t && t.getBoundingClientRect().top <= line) {
					current = i;
				}
			});
			links.forEach(function (a, i) {
				var on = i === current;
				if (on && !a.classList.contains('is-active') && a.scrollIntoView) {
					var list = a.parentNode.parentNode;
					list.scrollTo({ left: a.offsetLeft - 16, behavior: 'smooth' });
				}
				a.classList.toggle('is-active', on);
			});

			if (progress && page) {
				var r = page.getBoundingClientRect();
				var total = r.height - window.innerHeight;
				var ratio = total > 0 ? Math.min(1, Math.max(0, -r.top / total)) : 0;
				progress.style.transform = 'scaleX(' + ratio + ')';
			}
		}

		window.addEventListener('scroll', function () {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		}, { passive: true });
		window.addEventListener('resize', update);
		update();
	}

	/**
	 * Fenêtre vidéo : l'iframe n'est créée qu'à l'ouverture (page plus légère)
	 * et détruite à la fermeture (la vidéo s'arrête).
	 */
	function initVideo() {
		var dialog = document.querySelector('.iam-cert-video');
		var openBtn = document.querySelector('[data-iam-video-open]');
		if (!dialog || !openBtn) {
			return;
		}
		var frame = dialog.querySelector('.iam-cert-video__frame');
		var tpl = dialog.querySelector('.iam-cert-video__tpl');
		var closeBtn = dialog.querySelector('[data-iam-video-close]');

		function open() {
			frame.innerHTML = tpl ? tpl.innerHTML : '';
			var video = frame.querySelector('video');
			if (typeof dialog.showModal === 'function') {
				dialog.showModal();
			} else {
				dialog.setAttribute('open', '');
			}
			if (video) {
				video.play().catch(function () {});
			}
		}

		function close() {
			if (typeof dialog.close === 'function') {
				dialog.close();
			} else {
				dialog.removeAttribute('open');
			}
		}

		openBtn.addEventListener('click', open);
		if (closeBtn) {
			closeBtn.addEventListener('click', close);
		}
		// Clic sur le fond sombre = fermeture.
		dialog.addEventListener('click', function (e) {
			if (e.target === dialog) {
				close();
			}
		});
		dialog.addEventListener('close', function () {
			frame.innerHTML = '';
			openBtn.focus();
		});
	}

	/**
	 * Barre d'action fixée en bas sur mobile : visible une fois le hero
	 * dépassé, masquée quand le formulaire est à l'écran.
	 */
	function initStickyBar() {
		var bar = document.querySelector('.iam-lp-mbar');
		var hero = document.querySelector('.iam-lp-hero');
		var form = document.getElementById('iam-candidature');
		if (!bar || !hero || !form || !('IntersectionObserver' in window)) {
			return;
		}
		bar.hidden = false;

		var heroVisible = true;
		var formVisible = false;

		function update() {
			bar.classList.toggle('is-shown', !heroVisible && !formVisible);
		}

		new IntersectionObserver(function (entries) {
			heroVisible = entries[0].isIntersecting;
			update();
		}).observe(hero);

		new IntersectionObserver(function (entries) {
			formVisible = entries[0].isIntersecting;
			update();
		}, { rootMargin: '0px 0px -20% 0px' }).observe(form);
	}

	/**
	 * Les formulaires multi-étapes d'IAM Leads (< 1.5.3) font défiler la page
	 * jusqu'au formulaire et y placent le focus dès le chargement. Sur la page
	 * de détail, le visiteur doit arriver sur le hero : si le focus a été posé
	 * dans le formulaire sans action de sa part (et sans ancre dans l'URL), on
	 * le retire et on revient en haut.
	 */
	function guardInitialScroll() {
		if (window.location.hash || !document.querySelector('.iam-lp')) {
			return;
		}
		var touched = false;
		['wheel', 'touchstart', 'keydown', 'mousedown'].forEach(function (type) {
			window.addEventListener(type, function () {
				touched = true;
			}, { once: true, passive: true });
		});

		function restore() {
			var active = document.activeElement;
			if (touched || !active || !active.closest || !active.closest('.iam-form-wrap')) {
				return;
			}
			active.blur();
			window.scrollTo({ top: 0, behavior: 'instant' });
		}

		[0, 150, 500].forEach(function (delay) {
			window.setTimeout(restore, delay);
		});
	}

	function init() {
		document.querySelectorAll('.iam-cert').forEach(initFilters);
		guardInitialScroll();
		initFormBridge();
		initSessions();
		initPageNav();
		initVideo();
		initStickyBar();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
