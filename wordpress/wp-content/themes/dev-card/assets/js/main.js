/**
 * Dev Card — visual effects.
 *
 * Vanilla JS, no dependencies. Every effect is progressive enhancement:
 * the site is fully usable without it, and it respects prefers-reduced-motion.
 */
( function () {
	'use strict';

	const root = document.documentElement;
	const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	const finePointer = window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches;

	// Tell CSS that JS is running, so the "reveal" failsafe can be switched off.
	root.classList.add( 'motion-ok' );

	/* ------------------------------------------------------------------
	 * 1. Scroll reveal: elements fade/slide in when they enter the viewport.
	 * ------------------------------------------------------------------ */
	const revealItems = document.querySelectorAll( '.reveal' );

	if ( reducedMotion || ! ( 'IntersectionObserver' in window ) ) {
		revealItems.forEach( ( el ) => el.classList.add( 'is-visible' ) );
	} else {
		// Stagger siblings inside a [data-stagger] container.
		document.querySelectorAll( '[data-stagger]' ).forEach( ( group ) => {
			group.querySelectorAll( '.reveal' ).forEach( ( el, index ) => {
				el.style.setProperty( '--reveal-delay', `${ index * 80 }ms` );
			} );
		} );

		const observer = new IntersectionObserver(
			( entries ) => {
				entries.forEach( ( entry ) => {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						observer.unobserve( entry.target );
					}
				} );
			},
			// No negative bottom margin: elements at the very end of the page must
			// still be able to intersect when the page can't scroll any further.
			{ rootMargin: '0px', threshold: 0.12 }
		);

		revealItems.forEach( ( el ) => observer.observe( el ) );
	}

	/* ------------------------------------------------------------------
	 * 2. Scroll-driven effects: progress bar, header state, parallax.
	 *    One rAF loop for all of them — never do heavy work in the scroll event itself.
	 * ------------------------------------------------------------------ */
	const progress = document.querySelector( '.scroll-progress' );
	const header = document.querySelector( '.site-header' );
	const parallaxItems = reducedMotion ? [] : Array.from( document.querySelectorAll( '[data-parallax]' ) );
	let ticking = false;

	function onScroll() {
		const scrollY = window.scrollY;
		const maxScroll = root.scrollHeight - window.innerHeight;

		if ( progress ) {
			progress.style.transform = `scaleX(${ maxScroll > 0 ? scrollY / maxScroll : 0 })`;
		}

		if ( header ) {
			header.classList.toggle( 'is-scrolled', scrollY > 24 );
		}

		parallaxItems.forEach( ( el ) => {
			const speed = parseFloat( el.dataset.parallax ) || 0;
			el.style.setProperty( '--parallax-y', `${ scrollY * speed }px` );
		} );

		ticking = false;
	}

	window.addEventListener(
		'scroll',
		() => {
			if ( ! ticking ) {
				window.requestAnimationFrame( onScroll );
				ticking = true;
			}
		},
		{ passive: true }
	);
	onScroll();

	// Effects below follow the mouse — skip them on touch devices and for reduced motion.
	if ( reducedMotion || ! finePointer ) {
		return;
	}

	/* ------------------------------------------------------------------
	 * 3. Cursor glow + mouse parallax of the background blobs.
	 * ------------------------------------------------------------------ */
	const glow = document.querySelector( '.cursor-glow' );

	window.addEventListener(
		'pointermove',
		( event ) => {
			const x = event.clientX;
			const y = event.clientY;

			if ( glow ) {
				glow.style.transform = `translate(${ x }px, ${ y }px)`;
			}

			// -0.5 … 0.5 relative to the viewport center.
			root.style.setProperty( '--mouse-x', ( x / window.innerWidth - 0.5 ).toFixed( 3 ) );
			root.style.setProperty( '--mouse-y', ( y / window.innerHeight - 0.5 ).toFixed( 3 ) );
		},
		{ passive: true }
	);

	/* ------------------------------------------------------------------
	 * 4. Spotlight: a radial light follows the cursor inside cards.
	 * ------------------------------------------------------------------ */
	document.querySelectorAll( '.spotlight' ).forEach( ( card ) => {
		card.addEventListener( 'pointermove', ( event ) => {
			const rect = card.getBoundingClientRect();
			card.style.setProperty( '--spot-x', `${ event.clientX - rect.left }px` );
			card.style.setProperty( '--spot-y', `${ event.clientY - rect.top }px` );
		} );
	} );

	/* ------------------------------------------------------------------
	 * 5. 3D tilt for [data-tilt] elements.
	 * ------------------------------------------------------------------ */
	document.querySelectorAll( '[data-tilt]' ).forEach( ( el ) => {
		const max = parseFloat( el.dataset.tilt ) || 8;

		el.addEventListener( 'pointermove', ( event ) => {
			const rect = el.getBoundingClientRect();
			const px = ( event.clientX - rect.left ) / rect.width - 0.5;
			const py = ( event.clientY - rect.top ) / rect.height - 0.5;
			el.style.setProperty( '--tilt-x', `${ ( -py * max ).toFixed( 2 ) }deg` );
			el.style.setProperty( '--tilt-y', `${ ( px * max ).toFixed( 2 ) }deg` );
		} );

		el.addEventListener( 'pointerleave', () => {
			el.style.setProperty( '--tilt-x', '0deg' );
			el.style.setProperty( '--tilt-y', '0deg' );
		} );
	} );

	/* ------------------------------------------------------------------
	 * 6. Magnetic buttons: slightly pulled towards the cursor.
	 * ------------------------------------------------------------------ */
	document.querySelectorAll( '[data-magnetic]' ).forEach( ( el ) => {
		el.addEventListener( 'pointermove', ( event ) => {
			const rect = el.getBoundingClientRect();
			const dx = event.clientX - ( rect.left + rect.width / 2 );
			const dy = event.clientY - ( rect.top + rect.height / 2 );
			el.style.transform = `translate(${ dx * 0.2 }px, ${ dy * 0.3 }px)`;
		} );

		el.addEventListener( 'pointerleave', () => {
			el.style.transform = '';
		} );
	} );
} )();
