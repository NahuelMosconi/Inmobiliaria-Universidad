/*
 * GALERÍA DE FOTOS (detalle de propiedad)
 * ---------------------------------------------------------------
 * - Al tocar una miniatura cambia la foto grande.
 * - Al tocar la foto grande se abre un visor a pantalla completa.
 * - En el visor se puede pasar de foto con las flechas del teclado,
 *   con los botones o deslizando el dedo en el celular. Esc cierra.
 */
(function () {
    'use strict';

    function iniciarGaleria(galeria) {
        const principal = galeria.querySelector('.galeria-principal');
        const imagenPrincipal = principal.querySelector('img');
        const miniaturas = Array.from(galeria.querySelectorAll('.galeria-miniaturas button'));
        const fotos = miniaturas.map(function (boton) {
            return { src: boton.dataset.src, alt: boton.querySelector('img').alt };
        });

        let actual = 0;   // foto que se está mostrando
        let visor = null; // el visor se crea recién cuando se abre la primera vez

        function mostrarEnPrincipal(indice) {
            actual = indice;
            imagenPrincipal.src = fotos[indice].src;
            imagenPrincipal.alt = fotos[indice].alt;
            miniaturas.forEach(function (boton, i) {
                boton.classList.toggle('activa', i === indice);
            });
        }

        miniaturas.forEach(function (boton, indice) {
            boton.addEventListener('click', function () {
                mostrarEnPrincipal(indice);
            });
        });

        /* ----- Visor a pantalla completa ----- */

        function crearVisor() {
            visor = document.createElement('div');
            visor.className = 'visor';
            visor.setAttribute('role', 'dialog');
            visor.setAttribute('aria-modal', 'true');
            visor.setAttribute('aria-label', 'Fotos de la propiedad');
            visor.innerHTML =
                '<img src="" alt="">' +
                '<button type="button" class="visor-cerrar" aria-label="Cerrar">&times;</button>' +
                '<button type="button" class="visor-anterior" aria-label="Foto anterior">&#8249;</button>' +
                '<button type="button" class="visor-siguiente" aria-label="Foto siguiente">&#8250;</button>' +
                '<p class="visor-contador"></p>';

            visor.querySelector('.visor-cerrar').addEventListener('click', cerrar);
            visor.querySelector('.visor-anterior').addEventListener('click', function () { mover(-1); });
            visor.querySelector('.visor-siguiente').addEventListener('click', function () { mover(1); });

            // Clic en el fondo oscuro (fuera de la foto) también cierra
            visor.addEventListener('click', function (evento) {
                if (evento.target === visor) {
                    cerrar();
                }
            });

            // Deslizar con el dedo en celulares
            let inicioX = null;
            visor.addEventListener('touchstart', function (evento) {
                inicioX = evento.touches[0].clientX;
            }, { passive: true });
            visor.addEventListener('touchend', function (evento) {
                if (inicioX === null) return;
                const diferencia = evento.changedTouches[0].clientX - inicioX;
                if (Math.abs(diferencia) > 50) {
                    mover(diferencia < 0 ? 1 : -1);
                }
                inicioX = null;
            });

            // Con una sola foto no tiene sentido mostrar las flechas
            if (fotos.length < 2) {
                visor.querySelector('.visor-anterior').hidden = true;
                visor.querySelector('.visor-siguiente').hidden = true;
            }

            document.body.appendChild(visor);
        }

        function pintarVisor() {
            const img = visor.querySelector('img');
            img.src = fotos[actual].src;
            img.alt = fotos[actual].alt;
            visor.querySelector('.visor-contador').textContent = (actual + 1) + ' / ' + fotos.length;
        }

        function abrir() {
            if (!visor) {
                crearVisor();
            }
            visor.hidden = false;
            document.body.style.overflow = 'hidden'; // evita que la página de atrás haga scroll
            pintarVisor();
            document.addEventListener('keydown', teclado);
            visor.querySelector('.visor-cerrar').focus();
        }

        function cerrar() {
            visor.hidden = true;
            document.body.style.overflow = '';
            document.removeEventListener('keydown', teclado);
            mostrarEnPrincipal(actual); // la foto grande queda en la última que se vio
            principal.focus();
        }

        // Avanza o retrocede. El % hace que después de la última vuelva a la primera.
        function mover(paso) {
            actual = (actual + paso + fotos.length) % fotos.length;
            pintarVisor();
        }

        function teclado(evento) {
            if (evento.key === 'Escape') cerrar();
            if (evento.key === 'ArrowRight') mover(1);
            if (evento.key === 'ArrowLeft') mover(-1);
        }

        principal.addEventListener('click', abrir);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-galeria]').forEach(iniciarGaleria);
    });
})();
