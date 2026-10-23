/**
 * IAM Certifications — animations GSAP (front).
 * Amélioration progressive : si GSAP est absent ou si l'utilisateur préfère
 * moins d'animations, la page reste parfaitement lisible (états au repos visibles).
 */
(function () {
	'use strict';

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	// Nos instances (mémorisées au chargement) plutôt que les globales, que le thème peut écraser.
	var gsap = window.iamGsap || window.gsap;
	var ST = window.iamScrollTrigger || window.ScrollTrigger;
	if (reduce || !gsap) {
		return;
	}

	var hasST = !!ST;
	if (hasST) {
		gsap.registerPlugin(ST);
	}
	var fine = window.matchMedia && window.matchMedia('(pointer: fine)').matches;

	function st(trigger, start) {
		return hasST ? { trigger: trigger, start: start || 'top 85%', once: true } : undefined;
	}

	function $$(sel, ctx) {
		return Array.prototype.slice.call((ctx || document).querySelectorAll(sel));
	}

	/* Liste : apparition en cascade des cartes. */
	function animateList() {
		$$('.iam-cert-grid').forEach(function (grid) {
			var cards = grid.querySelectorAll('.iam-cert-card');
			if (!cards.length) {
				return;
			}
			gsap.from(cards, {
				opacity: 0,
				y: 26,
				duration: 0.6,
				ease: 'power2.out',
				stagger: 0.07,
				scrollTrigger: st(grid)
			});
		});
	}

	/* Compteur de 0 à la valeur finale. */
	function countUp(el, delay) {
		var end = parseInt(el.getAttribute('data-iam-count'), 10) || 0;
		var obj = { v: 0 };
		el.textContent = '0';
		var tween = gsap.to(obj, {
			v: end,
			duration: 1.6,
			delay: delay || 0,
			ease: 'power3.out',
			onUpdate: function () {
				el.textContent = Math.round(obj.v);
			}
		});
		// Filet de sécurité : la valeur finale s'affiche quoi qu'il arrive.
		window.setTimeout(function () {
			if (tween.progress() < 1) {
				tween.progress(1);
			}
			el.textContent = end;
		}, ((delay || 0) + 3) * 1000);
		return tween;
	}

	/* ------------------------------------------------------------------
	 * Page de détail
	 * ---------------------------------------------------------------- */
	function animateLanding(page) {
		page.classList.add('is-animated');

		/* Hero : entrée orchestrée. */
		var hero = page.querySelector('.iam-lp-hero');
		if (hero) {
			var q = function (sel) {
				return hero.querySelectorAll(sel);
			};
			var tl = gsap.timeline({
				defaults: { ease: 'expo.out' },
				// En fin d'entrée, on rend la main au CSS (survols, responsive).
				onComplete: function () {
					gsap.set(hero.querySelectorAll('.iam-lp-kpi, .iam-lp-kpi__icon, .iam-lp-kpi__title, .iam-lp-fiche, .iam-lp-fiche__row'), { clearProps: 'transform,opacity' });
				}
			});
			// Filet de sécurité : si l'animation n'a pas pu se jouer (onglet en arrière-plan,
			// conflit de scripts), le hero est affiché dans son état final.
			window.setTimeout(function () {
				if (tl.progress() < 1) {
					tl.progress(1);
				}
			}, 4500);
			if (q('.iam-lp-hero__img').length) {
				tl.from(q('.iam-lp-hero__img'), { scale: 1.18, duration: 2.4, ease: 'power2.out' }, 0);
			}
			tl.from(q('.iam-lp-crumbs, .iam-lp-type'), { opacity: 0, y: 16, duration: 0.8, stagger: 0.08 }, 0.15)
				.from(q('.iam-w > span'), { yPercent: 110, rotate: 4, duration: 1.1, stagger: 0.06 }, 0.25)
				.from(q('.iam-lp-hero__sub'), { opacity: 0, y: 20, duration: 0.9 }, 0.55)
				.from(q('.iam-lp-hero__lead'), { opacity: 0, y: 22, duration: 0.9 }, 0.6)
				.from(q('.iam-lp-hero__ctas > *'), { opacity: 0, y: 18, duration: 0.8, stagger: 0.1 }, 0.75)
				.from(q('.iam-lp-fiche'), { opacity: 0, y: 50, rotateX: -12, duration: 1.2 }, 0.5)
				.from(q('.iam-lp-fiche__row'), { opacity: 0, x: 18, duration: 0.6, stagger: 0.06 }, 0.85)
				.from(q('.iam-lp-kpi'), { opacity: 0, y: 46, scale: 0.96, duration: 0.9, stagger: 0.12 }, 0.85)
				.from(q('.iam-lp-kpi__icon'), { scale: 0, rotate: -60, duration: 0.8, ease: 'back.out(2.2)', stagger: 0.12 }, 1.05)
				.from(q('.iam-lp-kpi__title'), { opacity: 0, x: -14, duration: 0.6, stagger: 0.12 }, 1.15)
				.fromTo(q('.iam-lp-kpi'), { '--stripes': '60px' }, { '--stripes': '0px', duration: 1.1, stagger: 0.12 }, 1.1);

			$$('.iam-lp-kpi [data-iam-count]', hero).forEach(function (el, i) {
				countUp(el, 1.1 + i * 0.12);
			});

			// Parallaxe douce du fond pendant le défilement.
			if (hasST && q('.iam-lp-hero__img').length) {
				gsap.to(q('.iam-lp-hero__img'), {
					yPercent: 12,
					ease: 'none',
					scrollTrigger: { trigger: hero, start: 'top top', end: 'bottom top', scrub: true }
				});
			}
		}

		/* Titres de section et groupes en cascade. */
		$$('[data-iam-reveal]', page).forEach(function (el) {
			gsap.fromTo(el, { opacity: 0, y: 36 }, { opacity: 1, y: 0, duration: 1, ease: 'expo.out', scrollTrigger: st(el) });
		});
		$$('[data-iam-stagger]', page).forEach(function (group) {
			gsap.fromTo(group.children, { opacity: 0, y: 40 }, {
				opacity: 1,
				y: 0,
				duration: 0.9,
				ease: 'expo.out',
				stagger: 0.08,
				scrollTrigger: st(group, 'top 88%')
			});
		});

		/* Parcours : la frise se remplit au fil du défilement. */
		var path = page.querySelector('[data-iam-path]');
		var fill = page.querySelector('[data-iam-path-fill]');
		if (path && fill && hasST) {
			gsap.to(fill, {
				scaleY: 1,
				ease: 'none',
				scrollTrigger: { trigger: path, start: 'top 65%', end: 'bottom 65%', scrub: 0.6 }
			});
			$$('.iam-lp-step', path).forEach(function (step) {
				ST.create({
					trigger: step,
					start: 'top 65%',
					onEnter: function () {
						step.classList.add('is-reached');
					},
					onLeaveBack: function () {
						step.classList.remove('is-reached');
					}
				});
				gsap.from(step, { opacity: 0, x: 40, duration: 0.9, ease: 'expo.out', scrollTrigger: st(step, 'top 90%') });
			});
		}

		/* Jauge de pratique + compteur associé. */
		$$('[data-iam-ring]', page).forEach(function (ring) {
			var tile = ring.closest('.iam-lp-tile');
			var counter = tile ? tile.querySelector('[data-iam-count]') : null;
			var p = parseFloat(getComputedStyle(ring).getPropertyValue('--p')) || 0;
			gsap.fromTo(ring, { strokeDasharray: '0 100' }, {
				strokeDasharray: p + ' 100',
				duration: 1.8,
				ease: 'power3.out',
				scrollTrigger: st(ring, 'top 80%'),
				onStart: function () {
					if (counter) {
						countUp(counter);
					}
				}
			});
		});

		/* Tarif : le montant défile de 0 à sa valeur, séparateurs de milliers
		 * compris, quand la section entre à l'écran. */
		$$('[data-iam-price]', page).forEach(function (el) {
			var end = parseInt(el.getAttribute('data-iam-price'), 10) || 0;
			var fmt = function (v) {
				return Math.round(v).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '\u202f');
			};
			var obj = { v: 0 };
			el.style.setProperty('--w', el.offsetWidth + 'px');
			el.classList.add('is-counting');
			var tween = gsap.to(obj, {
				v: end,
				duration: 2.2,
				ease: 'power3.out',
				scrollTrigger: st(el, 'top 85%'),
				onStart: function () {
					el.textContent = fmt(0);
				},
				onUpdate: function () {
					el.textContent = fmt(obj.v);
				},
				onComplete: function () {
					el.textContent = fmt(end);
					el.classList.remove('is-counting');
				}
			});
			gsap.from(el, { scale: 0.85, opacity: 0, duration: 1.2, ease: 'back.out(1.6)', scrollTrigger: st(el, 'top 85%') });
			// Filet de sécurité : le montant final s'affiche quoi qu'il arrive.
			if ('IntersectionObserver' in window) {
				var io = new IntersectionObserver(function (entries) {
					if (entries[0].isIntersecting) {
						io.disconnect();
						window.setTimeout(function () {
							if (tween.progress() < 1) {
								tween.progress(1);
							}
							el.textContent = fmt(end);
							gsap.set(el, { opacity: 1, scale: 1 });
						}, 4000);
					}
				});
				io.observe(el);
			}
		});

		/* Carte « Ce qui est inclus » : arrive en se redressant. */
		var priceCard = page.querySelector('.iam-lp-price__card');
		if (priceCard) {
			gsap.from(priceCard, { rotateY: 18, rotateX: 8, x: 60, opacity: 0, duration: 1.3, ease: 'expo.out', scrollTrigger: st(priceCard, 'top 85%') });
		}

		/* Image « Pour qui » : parallaxe interne. */
		$$('[data-iam-parallax] img', page).forEach(function (img) {
			if (!hasST) {
				return;
			}
			gsap.fromTo(img, { yPercent: -8 }, {
				yPercent: 0,
				ease: 'none',
				scrollTrigger: { trigger: img.parentNode, start: 'top bottom', end: 'bottom top', scrub: true }
			});
		});

		/* Diplôme : se redresse à l'arrivée, reflet qui balaie. */
		var diploma = page.querySelector('.iam-lp-diploma');
		if (diploma) {
			gsap.from(diploma, { rotateY: -40, rotateX: 20, y: 60, opacity: 0, duration: 1.4, ease: 'expo.out', scrollTrigger: st(diploma, 'top 85%') });
		}

		if (fine) {
			initTilt(page);
			initMagnetic(page);
		}

		revealSafetyNet(page);
	}

	/* Filet de sécurité des apparitions au scroll : un élément visible à l'écran
	 * mais resté transparent (déclencheur qui n'a pas fonctionné) est affiché. */
	function revealSafetyNet(page) {
		if (!('IntersectionObserver' in window)) {
			page.classList.remove('is-animated');
			return;
		}
		var items = $$('[data-iam-reveal], [data-iam-stagger] > *, .iam-lp-price__card', page);
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) {
					return;
				}
				var el = entry.target;
				io.unobserve(el);
				window.setTimeout(function () {
					if (parseFloat(getComputedStyle(el).opacity) < 0.05) {
						gsap.set(el, { opacity: 1, clearProps: 'transform' });
					}
				}, 1600);
			});
		}, { threshold: 0.1 });
		items.forEach(function (el) {
			io.observe(el);
		});
	}

	/* Inclinaison 3D au survol (fiche du hero, diplôme). */
	function initTilt(page) {
		$$('[data-iam-tilt]', page).forEach(function (el) {
			var base = el.classList.contains('iam-lp-diploma') ? { rx: 6, ry: -14 } : { rx: 0, ry: 0 };
			var toX = gsap.quickTo(el, 'rotationX', { duration: 0.6, ease: 'power3.out' });
			var toY = gsap.quickTo(el, 'rotationY', { duration: 0.6, ease: 'power3.out' });
			gsap.set(el, { transformPerspective: 1000 });
			el.addEventListener('pointermove', function (e) {
				var r = el.getBoundingClientRect();
				var px = (e.clientX - r.left) / r.width - 0.5;
				var py = (e.clientY - r.top) / r.height - 0.5;
				toX(base.rx - py * 10);
				toY(base.ry + px * 12);
				el.style.setProperty('--shine', (px * 120) + '%');
			});
			el.addEventListener('pointerleave', function () {
				toX(base.rx);
				toY(base.ry);
				el.style.setProperty('--shine', '-60%');
			});
		});
	}

	/* Bouton principal légèrement attiré par le curseur. */
	function initMagnetic(page) {
		$$('[data-iam-magnetic]', page).forEach(function (btn) {
			var toX = gsap.quickTo(btn, 'x', { duration: 0.5, ease: 'power3.out' });
			var toY = gsap.quickTo(btn, 'y', { duration: 0.5, ease: 'power3.out' });
			btn.addEventListener('pointermove', function (e) {
				var r = btn.getBoundingClientRect();
				toX((e.clientX - r.left - r.width / 2) * 0.25);
				toY((e.clientY - r.top - r.height / 2) * 0.35);
			});
			btn.addEventListener('pointerleave', function () {
				toX(0);
				toY(0);
			});
		});
	}

	function init() {
		animateList();
		$$('.iam-lp').forEach(animateLanding);
		if (hasST) {
			ST.refresh();
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
