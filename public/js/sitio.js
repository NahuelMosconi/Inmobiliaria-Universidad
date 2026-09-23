/*
 * JAVASCRIPT GENERAL DEL SITIO
 * ---------------------------------------------------------------
 * - Menú hamburguesa en celulares
 * - Filtros desplegables en el listado (celulares)
 * - Selects que se envían solos al cambiar (data-autoenviar)
 * - Cerrar los mensajes de aviso
 * - Contador de caracteres (data-contador)
 * - Envío del formulario de consulta sin recargar la página (data-ajax)
 * - Botón "Compartir" (data-compartir)
 */
(function () {
    'use strict';

    /* ---------- Menú hamburguesa ---------- */
    function iniciarMenu() {
        const boton = document.querySelector('.menu-boton');
        const menu = document.getElementById('menu-principal');
        if (!boton || !menu) {
            return;
        }

        boton.addEventListener('click', function () {
            const abierto = menu.classList.toggle('abierto');
            boton.setAttribute('aria-expanded', abierto ? 'true' : 'false');
            boton.setAttribute('aria-label', abierto ? 'Cerrar menú' : 'Abrir menú');
        });

        // Si se agranda la ventana con el menú abierto lo cierro para que no quede raro.
        window.addEventListener('resize', function () {
            if (window.innerWidth > 900 && menu.classList.contains('abierto')) {
                menu.classList.remove('abierto');
                boton.setAttribute('aria-expanded', 'false');
            }
        });
    }

    /* ---------- Filtros del listado en celulares ---------- */
    function iniciarFiltros() {
        const boton = document.querySelector('.filtros-toggle');
        const formulario = document.getElementById('form-filtros');
        if (!boton || !formulario) {
            return;
        }

        function alternar(abrir) {
            formulario.classList.toggle('abierto', abrir);
            boton.setAttribute('aria-expanded', abrir ? 'true' : 'false');
            boton.textContent = abrir ? 'Ocultar filtros' : 'Filtrar búsqueda';
        }

        boton.addEventListener('click', function () {
            alternar(!formulario.classList.contains('abierto'));
        });

        // Si ya hay algún filtro aplicado los muestro abiertos para que se vea qué se filtró.
        const hayFiltros = Array.from(formulario.elements).some(function (campo) {
            return campo.type !== 'hidden' && campo.name && campo.name !== 'moneda' && campo.value !== '';
        });
        if (hayFiltros) {
            alternar(true);
        }
    }

    /* ---------- Selects que se envían solos ---------- */
    function iniciarAutoenvio() {
        document.querySelectorAll('[data-autoenviar]').forEach(function (campo) {
            campo.addEventListener('change', function () {
                campo.form.submit();
            });
        });
    }

    /* ---------- Mensajes de aviso ---------- */
    function iniciarAlertas() {
        document.querySelectorAll('.alerta').forEach(function (alerta) {
            const cerrar = alerta.querySelector('.alerta-cerrar');
            if (cerrar) {
                cerrar.addEventListener('click', function () {
                    alerta.remove();
                });
            }

            // Los mensajes de éxito se van solos a los 8 segundos
            if (alerta.classList.contains('alerta-exito')) {
                setTimeout(function () {
                    alerta.remove();
                }, 8000);
            }
        });
    }

    /* ---------- Contador de caracteres ---------- */
    function iniciarContadores() {
        document.querySelectorAll('[data-contador][maxlength]').forEach(function (campo) {
            const contador = document.createElement('small');
            contador.className = 'contador-caracteres';
            campo.insertAdjacentElement('afterend', contador);

            function actualizar() {
                contador.textContent = campo.value.length + ' / ' + campo.maxLength;
            }

            campo.addEventListener('input', actualizar);
            campo.form && campo.form.addEventListener('reset', function () {
                setTimeout(actualizar, 0); // espero a que el navegador limpie el campo
            });
            actualizar();
        });
    }

    /* ---------- Formulario de consulta ---------- */

    // Mensajes en español para la validación del navegador.
    function mensajeDeError(campo) {
        const v = campo.validity;
        if (v.valueMissing) return 'Este campo es obligatorio.';
        if (v.typeMismatch && campo.type === 'email') return 'Ingresá un email válido (ej: nombre@mail.com).';
        if (v.tooShort) return 'Tiene que tener al menos ' + campo.minLength + ' caracteres.';
        if (v.tooLong) return 'No puede tener más de ' + campo.maxLength + ' caracteres.';
        if (v.patternMismatch && campo.type === 'tel') return 'El teléfono solo puede tener números, espacios y guiones.';
        return 'Revisá este campo.';
    }

    function mostrarError(campo, mensaje) {
        const grupo = campo.closest('.form-grupo');
        if (!grupo) {
            return; // campos ocultos (propiedad_id) no tienen dónde mostrar el error
        }

        limpiarError(campo);
        campo.classList.add('invalido');

        const error = document.createElement('p');
        error.className = 'error-campo';
        error.textContent = mensaje;
        grupo.appendChild(error);
    }

    function limpiarError(campo) {
        campo.classList.remove('invalido');
        const grupo = campo.closest('.form-grupo');
        const anterior = grupo && grupo.querySelector('.error-campo');
        if (anterior) {
            anterior.remove();
        }
    }

    // Valida todos los campos y devuelve true si está todo bien.
    function validarFormulario(formulario) {
        let valido = true;

        formulario.querySelectorAll('.form-grupo input, .form-grupo textarea').forEach(function (campo) {
            // minlength no se chequea si el usuario no escribió nada, así que lo reviso a mano
            const muyCorto = campo.minLength > 0 && campo.value.trim().length > 0 && campo.value.trim().length < campo.minLength;

            if (!campo.checkValidity() || muyCorto) {
                mostrarError(campo, muyCorto ? 'Tiene que tener al menos ' + campo.minLength + ' caracteres.' : mensajeDeError(campo));
                valido = false;
            } else {
                limpiarError(campo);
            }
        });

        // Llevo el foco al primer campo con error
        const primero = formulario.querySelector('.invalido');
        if (primero) {
            primero.focus();
        }

        return valido;
    }

    function mostrarResultado(formulario, tipo, texto) {
        const caja = formulario.querySelector('.form-resultado');
        caja.className = 'form-resultado ' + tipo;
        caja.textContent = texto;
        caja.hidden = false;
    }

    function iniciarFormulariosAjax() {
        document.querySelectorAll('form[data-ajax]').forEach(function (formulario) {
            const boton = formulario.querySelector('[type="submit"]');
            const textoBoton = boton.textContent;

            // Al corregir un campo se le saca el error
            formulario.addEventListener('input', function (evento) {
                if (evento.target.classList.contains('invalido') && evento.target.checkValidity()) {
                    limpiarError(evento.target);
                }
            });

            formulario.addEventListener('submit', function (evento) {
                evento.preventDefault();

                if (!validarFormulario(formulario)) {
                    return;
                }

                // Deshabilito el botón para que no se envíe dos veces
                boton.disabled = true;
                boton.textContent = 'Enviando...';

                fetch(formulario.action, {
                    method: 'POST',
                    body: new FormData(formulario), // incluye el token CSRF (_token)
                    headers: { 'Accept': 'application/json' }
                })
                    .then(function (respuesta) {
                        return respuesta.json().catch(function () {
                            return {};
                        }).then(function (datos) {
                            return { estado: respuesta.status, datos: datos };
                        });
                    })
                    .then(function (resultado) {
                        if (resultado.estado === 200) {
                            formulario.reset();
                            mostrarResultado(formulario, 'exito', resultado.datos.mensaje);
                        } else if (resultado.estado === 422) {
                            // Errores de validación que devolvió Laravel
                            Object.keys(resultado.datos.errors || {}).forEach(function (nombre) {
                                const campo = formulario.elements[nombre];
                                if (campo) {
                                    mostrarError(campo, resultado.datos.errors[nombre][0]);
                                }
                            });
                            mostrarResultado(formulario, 'error', 'Revisá los datos marcados en rojo.');
                        } else if (resultado.estado === 429) {
                            mostrarResultado(formulario, 'error', 'Enviaste varias consultas seguidas. Esperá un minuto y probá de nuevo.');
                        } else if (resultado.estado === 419) {
                            mostrarResultado(formulario, 'error', 'La página estuvo abierta mucho tiempo. Recargala y volvé a intentar.');
                        } else {
                            throw new Error('Error ' + resultado.estado);
                        }
                    })
                    .catch(function () {
                        mostrarResultado(formulario, 'error', 'No se pudo enviar la consulta. Revisá tu conexión o escribinos por WhatsApp.');
                    })
                    .finally(function () {
                        boton.disabled = false;
                        boton.textContent = textoBoton;
                    });
            });
        });
    }

    /* ---------- Compartir ---------- */
    function iniciarCompartir() {
        document.querySelectorAll('[data-compartir]').forEach(function (boton) {
            boton.addEventListener('click', function () {
                const datos = { title: boton.dataset.titulo || document.title, url: window.location.href };

                // En celulares abre el menú nativo para compartir (WhatsApp, etc.)
                if (navigator.share) {
                    navigator.share(datos).catch(function () { /* el usuario canceló */ });
                    return;
                }

                // En la compu copio el link al portapapeles
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(datos.url).then(function () {
                        const original = boton.textContent;
                        boton.textContent = '¡Link copiado!';
                        setTimeout(function () {
                            boton.textContent = original;
                        }, 2000);
                    });
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        iniciarMenu();
        iniciarFiltros();
        iniciarAutoenvio();
        iniciarAlertas();
        iniciarContadores();
        iniciarFormulariosAjax();
        iniciarCompartir();
    });
})();
