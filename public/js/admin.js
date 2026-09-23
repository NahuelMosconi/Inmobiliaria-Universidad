/*
 * JAVASCRIPT DEL PANEL DE ADMINISTRACIÓN
 * ---------------------------------------------------------------
 * - Menú lateral en celulares
 * - Confirmación antes de eliminar (data-confirmar)
 * - Selects que se envían solos (data-autoenviar)
 * - Vista previa de las fotos antes de subirlas (data-vista-previa)
 * - Pegar coordenadas "lat, lng" de Google Maps en un solo campo
 * - Contador de caracteres (data-contador)
 * - Aviso si se sale de un formulario con cambios sin guardar
 */
(function () {
    'use strict';

    const MAX_MB = 4; // tiene que coincidir con la validación de PropiedadRequest

    function iniciarMenuLateral() {
        const boton = document.querySelector('.admin-menu-boton');
        const lateral = document.querySelector('.admin-lateral');
        if (!boton || !lateral) {
            return;
        }

        boton.addEventListener('click', function (evento) {
            evento.stopPropagation();
            lateral.classList.toggle('abierto');
        });

        // Clic fuera del menú lo cierra
        document.addEventListener('click', function (evento) {
            if (lateral.classList.contains('abierto') && !lateral.contains(evento.target)) {
                lateral.classList.remove('abierto');
            }
        });
    }

    function iniciarConfirmaciones() {
        document.querySelectorAll('form[data-confirmar]').forEach(function (formulario) {
            formulario.addEventListener('submit', function (evento) {
                if (!window.confirm(formulario.dataset.confirmar)) {
                    evento.preventDefault();
                }
            });
        });
    }

    function iniciarAutoenvio() {
        document.querySelectorAll('[data-autoenviar]').forEach(function (campo) {
            campo.addEventListener('change', function () {
                campo.form.submit();
            });
        });
    }

    // Muestra miniaturas de las fotos elegidas, con el nombre y un aviso si pesan demasiado.
    function iniciarVistaPrevia() {
        document.querySelectorAll('input[type="file"][data-vista-previa]').forEach(function (input) {
            const contenedor = document.querySelector(input.dataset.vistaPrevia);

            input.addEventListener('change', function () {
                contenedor.innerHTML = '';

                Array.from(input.files).forEach(function (archivo) {
                    const foto = document.createElement('div');
                    foto.className = 'foto foto-nueva';

                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(archivo);
                    img.alt = archivo.name;
                    img.onload = function () {
                        URL.revokeObjectURL(img.src); // libera memoria
                    };

                    const texto = document.createElement('p');
                    const megas = archivo.size / 1024 / 1024;
                    texto.textContent = archivo.name + ' (' + megas.toFixed(1) + ' MB)';

                    if (megas > MAX_MB) {
                        texto.textContent = 'Pesa más de ' + MAX_MB + ' MB: ' + archivo.name;
                        texto.style.color = '#c0392b';
                    }

                    foto.append(img, texto);
                    contenedor.appendChild(foto);
                });
            });
        });
    }

    // Si en "latitud" se pega "-32.889, -68.845" (como lo copia Google Maps)
    // separo los dos números y completo latitud y longitud.
    function iniciarCoordenadas() {
        const latitud = document.getElementById('latitud');
        const longitud = document.getElementById('longitud');
        if (!latitud || !longitud) {
            return;
        }

        latitud.addEventListener('input', function () {
            const partes = latitud.value.split(',').map(function (p) { return p.trim(); });
            if (partes.length === 2 && !isNaN(partes[0]) && !isNaN(partes[1]) && partes[1] !== '') {
                latitud.value = partes[0];
                longitud.value = partes[1];
            }
        });
    }

    function iniciarContadores() {
        document.querySelectorAll('[data-contador][maxlength]').forEach(function (campo) {
            const contador = document.createElement('small');
            contador.className = 'contador-caracteres';
            campo.insertAdjacentElement('afterend', contador);

            function actualizar() {
                contador.textContent = campo.value.length + ' / ' + campo.maxLength;
            }

            campo.addEventListener('input', actualizar);
            actualizar();
        });
    }

    // Evita perder lo escrito si se toca otro link sin guardar.
    function iniciarAvisoCambios() {
        document.querySelectorAll('form.form-admin').forEach(function (formulario) {
            let hayCambios = false;
            let enviando = false;

            formulario.addEventListener('input', function () { hayCambios = true; });
            formulario.addEventListener('change', function () { hayCambios = true; });
            formulario.addEventListener('submit', function () { enviando = true; });

            window.addEventListener('beforeunload', function (evento) {
                if (hayCambios && !enviando) {
                    evento.preventDefault();
                    evento.returnValue = ''; // necesario para que Chrome muestre el aviso
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        iniciarMenuLateral();
        iniciarConfirmaciones();
        iniciarAutoenvio();
        iniciarVistaPrevia();
        iniciarCoordenadas();
        iniciarContadores();
        iniciarAvisoCambios();
    });
})();
