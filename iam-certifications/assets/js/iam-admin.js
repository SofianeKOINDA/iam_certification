/**
 * IAM Certifications — admin.
 * - Bascule certificat / séminaire (champs, libellés et étapes s'adaptent).
 * - Assistant à étapes avec barre de progression.
 * - Répéteurs (modules, journées), visuel principal, champs médiathèque.
 * - Liste : filtre par type, confirmation de suppression.
 */
(function () {
	'use strict';

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function $(sel, ctx) {
		return (ctx || document).querySelector(sel);
	}

	function $$(sel, ctx) {
		return Array.prototype.slice.call((ctx || document).querySelectorAll(sel));
	}

	/* ------------------------------------------------------------------
	 * Assistant
	 * ---------------------------------------------------------------- */
	function initWizard() {
		var form = document.getElementById('iam-cert-wizard');
		if (!form) {
			return;
		}

		var steps = $$('.iam-step', form);
		var dots = $$('.iam-stepper__step', form);
		var prev = $('[data-iam-prev]', form);
		var next = $('[data-iam-next]', form);
		var publish = $('[data-iam-submit="publish"]', form);
		var title = $('[data-iam-step-title]', form);
		var hint = $('[data-iam-step-next]', form);
		var fill = $('[data-iam-step-fill]', form);
		var titre = document.getElementById('iam_titre');
		var storeKey = 'iamCertStep:' + (form.querySelector('[name="id"]').value || 'new');
		var current = 0;

		function kind() {
			var checked = $('input[name="_iam_type"]:checked', form);
			return checked ? checked.value : 'certificat';
		}

		function label(i) {
			return dots[i] ? dots[i].getAttribute('data-label-' + kind()) : '';
		}

		function validTitle() {
			var ok = titre && titre.value.trim() !== '';
			var err = $('[data-iam-error-for="iam_titre"]', form);
			if (err) {
				err.hidden = ok;
			}
			if (titre) {
				titre.classList.toggle('is-invalid', !ok);
			}
			return ok;
		}

		/* Champs obligatoires (data-required) et groupes (domaine, modules,
		 * jours) : seuls ceux du type choisi comptent. */
		function inScope(el) {
			var holder = el.closest('[data-scope]');
			var scope = holder ? holder.getAttribute('data-scope') : 'both';
			return scope === 'both' || scope === kind();
		}

		function groupFilled(group, sec) {
			if (group === 'domaine') {
				var sel = sec.querySelector('select');
				var nouv = sec.querySelector('[name="domaine_new"]');
				return (sel && sel.value && sel.value !== '0') || (nouv && nouv.value.trim() !== '');
			}
			if (group === 'modules') {
				return $$('.iam-module__titre', sec).some(function (i) {
					return i.value.trim() !== '';
				});
			}
			if (group === 'jours') {
				return $$('.iam-day textarea', sec).some(function (i) {
					return i.value.trim() !== '';
				});
			}
			return true;
		}

		function setError(holder, on, msg) {
			holder.classList.toggle('is-invalid', on);
			var err = holder.querySelector(':scope > .iam-field__error, :scope [data-iam-group-error]');
			if (!err && on && msg) {
				err = document.createElement('p');
				err.className = 'iam-field__error';
				err.textContent = msg;
				holder.appendChild(err);
			}
			if (err) {
				err.hidden = !on;
			}
		}

		// Renvoie le premier élément en erreur de l'étape (ou null).
		function validateStep(idx, silent) {
			var step = steps[idx];
			var first = null;
			if (idx === 0 && !validTitle()) {
				first = titre;
			}
			$$('[data-required]', step).forEach(function (el) {
				if (!inScope(el)) {
					return;
				}
				var ok = el.value.trim() !== '';
				if (!silent) {
					setError(el.closest('.iam-field'), !ok, 'Ce champ est obligatoire pour publier.');
				}
				if (!ok && !first) {
					first = el;
				}
			});
			$$('[data-required-group]', step).forEach(function (sec) {
				if (!inScope(sec)) {
					return;
				}
				var ok = groupFilled(sec.getAttribute('data-required-group'), sec);
				if (!silent) {
					setError(sec, !ok);
				}
				if (!ok && !first) {
					first = sec.querySelector('input, select, textarea') || sec;
				}
			});
			return first;
		}

		function focusError(el) {
			if (el && el.focus) {
				el.focus({ preventScroll: true });
				el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'center' });
			}
		}

		// Effacement de l'erreur dès la saisie.
		form.addEventListener('input', function (e) {
			var holder = e.target.closest('.is-invalid');
			if (holder && (e.target.value || '').trim() !== '') {
				setError(holder, false);
			}
		});
		form.addEventListener('change', function (e) {
			var holder = e.target.closest('.is-invalid');
			if (holder && e.target.value && e.target.value !== '0') {
				setError(holder, false);
			}
		});

		function go(i, dir) {
			// On n'avance qu'une fois les étapes précédentes complètes.
			for (var k = current; k < i; k++) {
				var bad = validateStep(k);
				if (bad) {
					if (k !== current) {
						i = k;
						break;
					}
					focusError(bad);
					return;
				}
			}
			current = Math.max(0, Math.min(steps.length - 1, i));
			var last = current === steps.length - 1;

			steps.forEach(function (s, idx) {
				s.classList.toggle('is-active', idx === current);
			});
			dots.forEach(function (d, idx) {
				d.classList.toggle('is-active', idx === current);
				d.classList.toggle('is-done', idx < current);
				var btn = d.querySelector('button');
				if (btn) {
					btn.setAttribute('aria-current', idx === current ? 'step' : 'false');
				}
			});

			if (title) {
				title.textContent = 'Étape ' + (current + 1) + ' sur ' + steps.length + ' — ' + label(current);
			}
			if (hint) {
				hint.textContent = last ? 'Dernière ligne droite avant publication' : 'Ensuite : ' + label(current + 1);
			}
			if (fill) {
				fill.style.width = Math.round(((current + 1) / steps.length) * 100) + '%';
			}
			if (prev) {
				prev.hidden = current === 0;
			}
			if (next) {
				next.hidden = last;
			}
			if (publish) {
				publish.classList.toggle('is-primary', last);
			}

			try {
				window.sessionStorage.setItem(storeKey, String(current));
			} catch (e) {}

			if (dir && !reduce && window.gsap) {
				window.gsap.fromTo(steps[current], { opacity: 0, x: dir * 24 }, { opacity: 1, x: 0, duration: 0.35, ease: 'power2.out' });
			}
			if (dir) {
				var top = form.getBoundingClientRect().top + window.scrollY - 40;
				if (window.scrollY > top) {
					window.scrollTo({ top: top, behavior: reduce ? 'auto' : 'smooth' });
				}
			}
			refreshChecklist();
		}

		/* Bascule de type : classe sur le formulaire (le CSS masque les champs
		 * de l'autre type), placeholders et libellés du stepper. */
		function applyKind(animate) {
			var k = kind();
			form.classList.toggle('iam-kind--certificat', k === 'certificat');
			form.classList.toggle('iam-kind--seminaire', k === 'seminaire');
			$$('[data-ph-' + k + ']', form).forEach(function (el) {
				el.setAttribute('placeholder', el.getAttribute('data-ph-' + k));
			});
			go(current);
			if (animate && !reduce && window.gsap) {
				window.gsap.from($$('.iam-kind input:checked + .iam-kind__icon', form), { scale: 0.6, rotate: -12, duration: 0.45, ease: 'back.out(2)' });
			}
		}

		$$('input[name="_iam_type"]', form).forEach(function (r) {
			r.addEventListener('change', function () {
				applyKind(true);
			});
		});

		if (prev) {
			prev.addEventListener('click', function () {
				go(current - 1, -1);
			});
		}
		if (next) {
			next.addEventListener('click', function () {
				go(current + 1, 1);
			});
		}
		dots.forEach(function (d, idx) {
			var btn = d.querySelector('button');
			if (btn) {
				btn.addEventListener('click', function () {
					go(idx, idx < current ? -1 : 1);
				});
			}
		});

		// Entrée dans un champ texte : étape suivante plutôt qu'un envoi involontaire.
		form.addEventListener('keydown', function (e) {
			if (e.key !== 'Enter' || e.target.tagName === 'TEXTAREA' || e.target.type === 'submit') {
				return;
			}
			e.preventDefault();
			if (current < steps.length - 1) {
				go(current + 1, 1);
			}
		});

		// Brouillon : seul le titre est exigé. Publication : tous les champs requis.
		form.addEventListener('submit', function (e) {
			var submitter = e.submitter || document.activeElement;
			var publishing = !submitter || submitter.value !== 'draft';
			if (!validTitle()) {
				e.preventDefault();
				current = 0;
				go(0);
				titre.focus();
				return;
			}
			if (!publishing) {
				return;
			}
			for (var k = 0; k < steps.length; k++) {
				var bad = validateStep(k);
				if (bad) {
					e.preventDefault();
					current = k;
					go(k);
					focusError(bad);
					return;
				}
			}
		});
		if (titre) {
			titre.addEventListener('input', function () {
				if (titre.classList.contains('is-invalid')) {
					validTitle();
				}
			});
		}

		/* Checklist de l'étape « Publication ». */
		function filled(name) {
			if (name === 'modules') {
				return $$('.iam-module__titre', form).some(function (i) {
					return i.value.trim() !== '';
				});
			}
			if (name === 'jours') {
				return $$('#iam-days textarea', form).some(function (i) {
					return i.value.trim() !== '';
				});
			}
			if (name === 'domaine') {
				var sec = form.querySelector('[data-required-group="domaine"]');
				return !!sec && groupFilled('domaine', sec);
			}
			var el = form.querySelector('[name="' + name + '"]');
			return !!(el && el.value && el.value.trim() !== '' && el.value !== '0');
		}

		function refreshChecklist() {
			$$('[data-iam-checklist] li', form).forEach(function (li) {
				li.classList.toggle('is-ok', filled(li.getAttribute('data-check')));
			});
		}
		form.addEventListener('input', refreshChecklist);

		// Reprise à l'étape où l'on était avant l'enregistrement.
		var start = 0;
		try {
			start = parseInt(window.sessionStorage.getItem(storeKey) || '0', 10) || 0;
		} catch (e) {}
		if (form.getAttribute('data-edit') !== '1') {
			start = 0;
		}
		current = start;
		applyKind(false);
	}

	/* ------------------------------------------------------------------
	 * Répéteurs (modules / journées)
	 * ---------------------------------------------------------------- */
	function initRepeaters() {
		$$('.iam-repeater').forEach(function (root) {
			var list = $('.iam-repeater__list', root);
			var add = $('.iam-repeater__add', root);
			var counter = list ? list.children.length : 0;

			if (add && list) {
				add.addEventListener('click', function () {
					var tpl = document.getElementById(add.getAttribute('data-template'));
					if (!tpl) {
						return;
					}
					var wrap = document.createElement('div');
					wrap.innerHTML = tpl.innerHTML.replace(/__index__/g, 'new_' + counter++).trim();
					var node = wrap.firstElementChild;
					list.appendChild(node);
					var first = node.querySelector('input, textarea');
					if (first) {
						first.focus();
					}
					if (!reduce && window.gsap) {
						window.gsap.from(node, { opacity: 0, y: 14, duration: 0.3, ease: 'power2.out' });
					}
				});
			}

			root.addEventListener('click', function (e) {
				var btn = e.target.closest('.iam-row-remove');
				if (!btn) {
					return;
				}
				var row = btn.closest('.iam-module, .iam-day');
				if (!row) {
					return;
				}
				var hasContent = $$('input, textarea', row).some(function (i) {
					return i.value.trim() !== '';
				});
				if (hasContent && !window.confirm('Supprimer ce bloc et son contenu ?')) {
					return;
				}
				row.parentNode.removeChild(row);
				row.dispatchEvent(new Event('input', { bubbles: true }));
				var form = document.getElementById('iam-cert-wizard');
				if (form) {
					form.dispatchEvent(new Event('input'));
				}
			});
		});
	}

	/* ------------------------------------------------------------------
	 * Visuel principal (médiathèque WordPress)
	 * ---------------------------------------------------------------- */
	function initImage() {
		var drop = document.getElementById('iam_thumb_drop');
		if (!drop || typeof wp === 'undefined' || !wp.media) {
			return;
		}
		var input = document.getElementById('iam_thumb_id');
		var preview = document.getElementById('iam_thumb_preview');
		var frame;

		function pick(e) {
			e.preventDefault();
			if (!frame) {
				frame = wp.media({ title: 'Visuel principal', multiple: false, library: { type: 'image' } });
				frame.on('select', function () {
					var att = frame.state().get('selection').first().toJSON();
					var url = att.sizes && att.sizes.large ? att.sizes.large.url : att.url;
					input.value = att.id;
					preview.innerHTML = '<img src="' + url + '" alt="">';
					drop.classList.add('has-image');
					input.dispatchEvent(new Event('input', { bubbles: true }));
				});
			}
			frame.open();
		}

		$('#iam_thumb_btn', drop).addEventListener('click', pick);
		$('[data-iam-thumb-replace]', drop).addEventListener('click', pick);
		$('#iam_thumb_remove', drop).addEventListener('click', function (e) {
			e.preventDefault();
			input.value = '';
			preview.innerHTML = '';
			drop.classList.remove('has-image');
			input.dispatchEvent(new Event('input', { bubbles: true }));
		});
	}

	/* Champs « Médiathèque » (brochure, dossier, image secondaire). */
	function initMediaFields() {
		if (typeof wp === 'undefined' || !wp.media) {
			return;
		}
		document.addEventListener('click', function (e) {
			var btn = e.target.closest('.iam-media-pick');
			if (!btn) {
				return;
			}
			e.preventDefault();
			var input = document.getElementById(btn.getAttribute('data-target'));
			var lib = btn.getAttribute('data-library');
			var opts = { title: btn.textContent.trim(), multiple: false };
			if (lib) {
				opts.library = { type: lib };
			}
			var frame = wp.media(opts);
			frame.on('select', function () {
				var att = frame.state().get('selection').first().toJSON();
				if (input) {
					input.value = att.url;
					input.dispatchEvent(new Event('input', { bubbles: true }));
				}
			});
			frame.open();
		});
	}

	/* ------------------------------------------------------------------
	 * Liste
	 * ---------------------------------------------------------------- */
	function initList() {
		var tabs = $('[data-iam-list-tabs]');
		if (tabs) {
			tabs.addEventListener('click', function (e) {
				var btn = e.target.closest('button');
				if (!btn) {
					return;
				}
				var type = btn.getAttribute('data-type');
				$$('button', tabs).forEach(function (b) {
					b.classList.toggle('is-active', b === btn);
				});
				$$('.iam-adm-row').forEach(function (row) {
					row.hidden = !!type && row.getAttribute('data-type') !== type;
				});
			});
		}

		document.addEventListener('click', function (e) {
			var del = e.target.closest('.iam-cert-del');
			if (del && !window.confirm('Mettre cette formation à la corbeille ?')) {
				e.preventDefault();
			}
			var delTerm = e.target.closest('.iam-term-del');
			if (delTerm) {
				var n = parseInt(delTerm.getAttribute('data-count'), 10) || 0;
				var msg = n ? 'Supprimer ? ' + n + ' formation(s) perdront ce classement (elles ne sont pas supprimées).' : 'Supprimer définitivement ?';
				if (!window.confirm(msg)) {
					e.preventDefault();
				}
			}
		});

		if (!reduce && window.gsap && $$('.iam-adm-row').length) {
			window.gsap.from('.iam-adm-row', { opacity: 0, y: 12, duration: 0.4, stagger: 0.035, ease: 'power2.out' });
		}
	}

	function boot() {
		initWizard();
		initRepeaters();
		initImage();
		initMediaFields();
		initList();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
