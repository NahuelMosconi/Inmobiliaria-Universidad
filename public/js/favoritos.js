/*
 * FAVORITOS
 * ---------------------------------------------------------------
 * Guarda los ids de las propiedades favoritas en el localStorage del
 * navegador, así el visitante no necesita crear una cuenta.
 *  - Todos los botones con data-favorito="ID" funcionan como "corazón".
 *  - En el header se muestra cuántas propiedades hay guardadas.
 *  - En la página /favoritos se piden los datos al servidor y se arman las tarjetas.
 */
(function () {
    'use strict';

    const CLAVE = 'triangulo_favoritos';

    // Lee la lista guardada. Si no hay nada (o el navegador bloquea el
    // localStorage, como en algunos modos incógnito) devuelve una lista vacía.
    function obtener() {
        try {
            const guardado = JSON.parse(localStorage.getItem(CLAVE));
            return Array.isArray(guardado) ? guardado.map(Number).filter(Boolean) : [];
        } catch (e) {
            return [];
        }
    }

    function guardar(ids) {
        try {
            localStorage.setItem(CLAVE, JSON.stringify(ids));
        } catch (e) {
            // Si no se puede guardar no rompo la página, simplemente no se recuerda.
        }
        actualizarPantalla();
    }

    // Agrega el id si no estaba o lo saca si ya estaba.
    function alternar(id) {
        const ids = obtener();
        const posicion = ids.indexOf(id);

        if (posicion === -1) {
            ids.push(id);
        } else {
            ids.splice(posicion, 1);
        }

        guardar(ids);
        return posicion === -1; // true si quedó agregado
    }

    // Pinta los corazones y el contador según lo que haya guardado.
    function actualizarPantalla() {
        const ids = obtener();

        document.querySelectorAll('[data-favorito]').forEach(function (boton) {
            const activo = ids.includes(Number(boton.dataset.favorito));
            boton.classList.toggle('activo', activo);
            boton.setAttribute('aria-pressed', activo ? 'true' : 'false');
            boton.title = activo ? 'Quitar de favoritos' : 'Agregar a favoritos';

            // El botón del detalle tiene texto además del corazón
            const texto = boton.querySelector('span');
            if (texto) {
                texto.textContent = activo ? 'Guardada' : 'Guardar';
            }
        });

        document.querySelectorAll('.contador-favoritos').forEach(function (contador) {
            contador.textContent = ids.length;
            contador.hidden = ids.length === 0;
        });
    }

    // Un solo "escuchador" para todos los corazones (también sirve para los
    // que se crean después con JavaScript en la página de favoritos).
    document.addEventListener('click', function (evento) {
        const boton = evento.target.closest('[data-favorito]');
        if (!boton) {
            return;
        }

        evento.preventDefault();
        const id = Number(boton.dataset.favorito);
        const agregado = alternar(id);

        // En la página de favoritos, si se quita uno se saca la tarjeta.
        const lista = document.getElementById('lista-favoritos');
        if (!agregado && lista && lista.contains(boton)) {
            boton.closest('.tarjeta').remove();
            mostrarVacioSiCorresponde();
        }
    });

    // Si el usuario tiene el sitio abierto en otra pestaña, se sincroniza.
    window.addEventListener('storage', function (evento) {
        if (evento.key === CLAVE) {
            actualizarPantalla();
        }
    });

    /* ---------------- Página "Mis favoritos" ---------------- */

    function mostrarVacioSiCorresponde() {
        const lista = document.getElementById('lista-favoritos');
        document.getElementById('favoritos-vacio').hidden = lista.children.length > 0;
    }

    // Arma una tarjeta igual a la de partials/tarjeta-propiedad.blade.php.
    // Uso textContent en vez de innerHTML para los datos, así no se puede
    // inyectar HTML aunque un título tenga caracteres raros.
    function crearTarjeta(propiedad) {
        const tarjeta = document.createElement('article');
        tarjeta.className = 'tarjeta';

        const enlaceImagen = document.createElement('a');
        enlaceImagen.href = propiedad.url;
        enlaceImagen.className = 'tarjeta-imagen';

        const imagen = document.createElement('img');
        imagen.src = propiedad.imagen;
        imagen.alt = propiedad.titulo;
        imagen.loading = 'lazy';

        const etiqueta = document.createElement('span');
        etiqueta.className = 'etiqueta etiqueta-' + propiedad.operacion.toLowerCase();
        etiqueta.textContent = propiedad.operacion;

        enlaceImagen.append(imagen, etiqueta);

        const corazon = document.createElement('button');
        corazon.type = 'button';
        corazon.className = 'boton-favorito activo';
        corazon.dataset.favorito = propiedad.id;
        corazon.innerHTML = '&#9829;';
        corazon.title = 'Quitar de favoritos';

        const info = document.createElement('div');
        info.className = 'tarjeta-info';

        const tipo = document.createElement('p');
        tipo.className = 'tarjeta-tipo';
        tipo.textContent = propiedad.tipo + ' · ' + propiedad.localidad;

        const titulo = document.createElement('h3');
        const enlaceTitulo = document.createElement('a');
        enlaceTitulo.href = propiedad.url;
        enlaceTitulo.textContent = propiedad.titulo;
        titulo.appendChild(enlaceTitulo);

        const resumen = document.createElement('p');
        resumen.className = 'tarjeta-resumen';
        resumen.textContent = propiedad.resumen;

        const precio = document.createElement('p');
        precio.className = 'tarjeta-precio';
        precio.textContent = propiedad.precio;

        info.append(tipo, titulo, resumen, precio);
        tarjeta.append(enlaceImagen, corazon, info);

        return tarjeta;
    }

    function cargarPaginaFavoritos() {
        const lista = document.getElementById('lista-favoritos');
        if (!lista) {
            return; // no estoy en la página de favoritos
        }

        const cargando = document.getElementById('favoritos-cargando');
        const ids = obtener();

        if (ids.length === 0) {
            cargando.hidden = true;
            mostrarVacioSiCorresponde();
            return;
        }

        // Armo la URL: /favoritos/datos?ids[]=1&ids[]=5...
        const parametros = new URLSearchParams();
        ids.forEach(function (id) {
            parametros.append('ids[]', id);
        });

        fetch(lista.dataset.url + '?' + parametros.toString(), { headers: { 'Accept': 'application/json' } })
            .then(function (respuesta) {
                if (!respuesta.ok) {
                    throw new Error('Error ' + respuesta.status);
                }
                return respuesta.json();
            })
            .then(function (propiedades) {
                propiedades.forEach(function (propiedad) {
                    lista.appendChild(crearTarjeta(propiedad));
                });

                // Si alguna propiedad guardada ya no existe (se vendió o se borró) la saco de la lista.
                const existentes = propiedades.map(function (p) { return p.id; });
                if (existentes.length !== ids.length) {
                    guardar(ids.filter(function (id) { return existentes.includes(id); }));
                }
            })
            .catch(function () {
                lista.innerHTML = '<p class="sin-resultados">No se pudieron cargar tus favoritos. Probá recargar la página.</p>';
            })
            .finally(function () {
                cargando.hidden = true;
                mostrarVacioSiCorresponde();
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        actualizarPantalla();
        cargarPaginaFavoritos();
    });
})();
