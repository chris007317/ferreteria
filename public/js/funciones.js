function activarLinkMenu(link){
   const linkActivo = document.querySelector('a[href="'+link+'"]');
    linkActivo.classList.add('active');
}

function activarLink(link){
   const linkActivo = document.querySelector('a[href="'+link+'"]');
    linkActivo.classList.add('active');
    linkActivo.classList.add('link-activo')
}

function agregarQuitarClase(elemento, clase) {
   if (elemento.classList.contains(clase)) {
        elemento.classList.remove(clase);
    } else {
        elemento.classList.add(clase);
    }
}

function agregarClase(elemento, clase) {
    if (!elemento.classList.contains(clase)) {
        elemento.classList.add(clase);
    }
}

function quitarClase(elemento, clase) {
    if (elemento.classList.contains(clase)) {
        elemento.classList.remove(clase);
    }
}

function validarFormulario(form) {
    if (!form) return true; // Retorna true si no hay formulario
    
    let esValido = true;
    const inputs = form.querySelectorAll("input, textarea, select");
    
    inputs.forEach(input => {
        input.classList.remove("is-invalid");
        if (!input.checkValidity()) { 
            esValido = false;
            input.classList.add("is-invalid");
        }
    });

    return esValido;
}

function validarInputs(formulario){
    formulario.querySelectorAll("input, textarea, select").forEach(input => {
        input.addEventListener("invalid", function () {
            this.classList.add("is-invalid");
        });
        input.addEventListener("input", function () {
             if (!this.checkValidity()) { 
                input.classList.add("is-invalid");
                return;
            }
            this.classList.remove("is-invalid"); // Quitar error al escribir
        });
    });
}

function alertaSucces(mensaje){
    const alerta = `<div class="alerta">
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="d-flex gap-3">
                        <div class="alerta-icono">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            ${mensaje}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                </div>
            `;
    return alerta;
}

function alertaError(mensaje){
    const alerta = `<div class="alerta">
                <div class="alert alert-danger alert-dismissible" role="alert">
                      <div class="d-flex gap-3">
                        <div class="alerta-icono">
                          <i class="fa-solid fa-circle-exclamation"></i>
                        </div>
                        <div>
                          ${mensaje}
                        </div>
                      </div>
                      <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            </div>`;
    return alerta;
}


function alertaWarning(mensaje){
    const alerta = `<div class="alerta">
                <div class="alert alert-warning alert-dismissible" role="alert">
                      <div class="d-flex gap-3">
                        <div class="alerta-icono">
                          <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                          ${mensaje}
                        </div>
                      </div>
                      <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                </div>
            </div>`;
    return alerta;
}

function mostrarAlerta(tipo, mensaje){
    let body = document.body;
    if (tipo === 'ok') {
        body.insertAdjacentHTML("beforeend", alertaSucces(mensaje));
    }
    if(tipo == 'error'){
        body.insertAdjacentHTML("beforeend", alertaError(mensaje));
    }
    if(tipo == 'warning'){
        body.insertAdjacentHTML("beforeend", alertaWarning(mensaje));
    }
    let alertaDiv = body.querySelector(".alerta");
    if(!alertaDiv)return;
    setTimeout(() => {
        alertaDiv.classList.add("mostrar-alerta");
    }, 500);
    setTimeout(() => {
        alertaDiv.classList.remove("mostrar-alerta");
        setTimeout(() => {
            alertaDiv.remove();
        }, 500);
    }, 3500);    
}

function validarTipoImagen(file) {
    const tiposPermitidos = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    if (!file) {
        return false;
    }
    if (!tiposPermitidos.includes(file.type)) {
        console.error("Tipo de archivo no permitido.");
        return false;
    }
    return true;
}

async function subirImagen(formData, ruta, carpeta, nombreElemento, hostImagen) {
    try {
        const response = await fetch(ruta, {
            method: "POST",
            body: formData,
        });
        // Si el servidor responde con un error (código HTTP diferente de 2xx)
        if (!response.ok) {
            throw new Error(`Error ${response.status}: ${response.statusText}`);
        }
        const data = await response.json();
        if (data.alerta) {
            mostrarAlerta(data.alerta.tipo, data.alerta.mensaje);
        }
        if (data.imagen) {
            document.getElementById(nombreElemento).src = `${hostImagen}/img/${carpeta}/${data.imagen}`;
        }
        return true;
    } catch (error) {
        console.error("Error en la solicitud:", error);
        mostrarAlerta("error", `No se pudo subir la imagen: ${error.message}`);
        return false;
    }
}

function sumarHora(hora, adicional) {
    let [hours, minutes] = hora.split(":").map(Number);
    hours = (hours + adicional) % 24;
    return `${hours.toString().padStart(2, "0")}:${minutes.toString().padStart(2, "0")}`;
}

function cerrarModal(idModal){
    const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById(idModal));
    modal.hide();
}

function mostrarModalAlerta(tipo, mensaje, actualizar = false){
    let clase = 'danger';
    let svg = `<svg
          xmlns="http://www.w3.org/2000/svg"
          class="icon mb-2 text-danger icon-lg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          stroke-width="2"
          stroke="currentColor"
          fill="none"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
          <path d="M12 9v2m0 4v.01" />
          <path
            d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"
          />
        </svg>`;
    if(tipo == 'ok'){
        clase = 'success';
        svg = `<svg
          xmlns="http://www.w3.org/2000/svg"
          class="icon mb-2 text-green icon-lg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          stroke-width="2"
          stroke="currentColor"
          fill="none"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path stroke="none" d="M0 0h24v24H0z" fill="none" />
          <circle cx="12" cy="12" r="9" />
          <path d="M9 12l2 2l4 -4" />
        </svg>`;
    }
    const modal = `<div class="modal modal-blur fade" id="alertaModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <div class="modal-status bg-${clase}"></div>
          <div class="modal-body text-center py-4">
            <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
            ${svg}
            <div class="text-secondary">${mensaje}</div>
          </div>
          <div class="modal-footer">
            <div class="w-100">
              <div class="row">
                <div class="col"><a href="#" id="btnAlerta" class="btn btn-${clase} w-100" data-bs-dismiss="modal">
                    Aceptar
                  </a></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>`;
    let body = document.body;
    body.insertAdjacentHTML("beforeend", modal);
    const myModal = new bootstrap.Modal(document.getElementById('alertaModal'));
    myModal.show();
    const btnAlerta = document.getElementById('btnAlerta');
    if(btnAlerta && tipo == 'ok' && actualizar){
        btnAlerta.addEventListener('click', function(event){
            location.reload();
        });
    }else{
        btnAlerta.addEventListener('click', function(event){
            let alerta = document.getElementById('alertaModal');
            alerta.remove();
        });
    }
}

function generarContra(longitud) {
  const caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  let contraseña = '';
  for (let i = 0; i < longitud; i++) {
    contraseña += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
  }
  return contraseña;
}

function calcularHoraFin(horaInicio, horasReserva) {
    const cantidadHoras = parseInt(horasReserva);

    if (!horaInicio || isNaN(cantidadHoras)) {
        return '';
    }
    // Crear objeto Date con una fecha cualquiera y la hora de inicio
    const [horas, minutos] = horaInicio.split(':');
    const date = new Date();
    date.setHours(parseInt(horas));
    date.setMinutes(parseInt(minutos));
    date.setSeconds(0);
    // Sumar las horas seleccionadas
    date.setHours(date.getHours() + cantidadHoras);
    // Formatear hora de salida (HH:mm)
    const horaFin = date.toTimeString().substring(0, 5);
    return horaFin;
}

function calcularTotalesReserva({ 
    horaInicioStr, 
    horasReserva, 
    datosEspacio, 
    txtTotalPagar, 
    txtAdelanto, 
    txtHoraSalida,
}, editar = false) {
    if (!horaInicioStr || isNaN(horasReserva)) return;

    const [hora, minuto] = horaInicioStr.split(':').map(Number);
    const horaInicio = new Date();
    horaInicio.setHours(hora, minuto, 0, 0);

    const horaFin = new Date(horaInicio);
    horaFin.setHours(horaFin.getHours() + horasReserva);

    const [hs, ms] = datosEspacio.horaSalida.split(':').map(Number);
    const horaLimite = new Date();
    horaLimite.setHours(hs, ms, 0, 0);

    if (horaFin > horaLimite) {
        mostrarAlerta('warning', 'La reserva excede la hora de cierre del local');
        txtTotalPagar.value = '';
        txtAdelanto.value = '';
        txtHoraSalida.value = '';
        return;
    }

    let totalPagar = 0;
    for (let i = 0; i < horasReserva; i++) {
        const horaActual = hora + i;
        const esNoche = horaActual >= 18;
        const precio = esNoche ? datosEspacio.precioNocheCancha : datosEspacio.precioDiaCancha;
        totalPagar += parseFloat(precio);
    }

    txtTotalPagar.value = totalPagar.toFixed(2);

    const porcentaje = parseFloat(datosEspacio.porcentajeReservaLocal);
    if(!editar) txtAdelanto.value = ((totalPagar * porcentaje / 100).toFixed(2));

    // Pintar la hora de salida
     txtHoraSalida.value = horaFin.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function formatearFecha(fechaISO) {
    // Dividir la fecha en partes y usar UTC
    const [anio, mes, dia] = fechaISO.split('-');
    const fecha = new Date(Date.UTC(anio, mes - 1, dia));
    
    const diaFormateado = String(fecha.getUTCDate()).padStart(2, '0');
    const mesFormateado = String(fecha.getUTCMonth() + 1).padStart(2, '0');
    const anioFormateado = fecha.getUTCFullYear();
    
    return `${diaFormateado}/${mesFormateado}/${anioFormateado}`;
}

function mostrarModalConFocus(modalId, inputSelector) {
    const modal = document.getElementById(modalId);
    const modalMostrar = new bootstrap.Modal(modal);
    // Antes de abrir, garantizamos que solo tenga un listener
    modal.addEventListener('shown.bs.modal', function () {
        const input = modal.querySelector(inputSelector);
        if (input) {
            input.focus();
        }
    }, { once: true });
    modalMostrar.show();
}

function calcularTotalesReservaFrecuente({ 
    horaInicioStr, 
    horasReserva, 
    datosEspacio, 
    txtTotalPagar, 
    txtHoraSalida,
}) {
    if (!horaInicioStr || isNaN(horasReserva)) return;

    const [hora, minuto] = horaInicioStr.split(':').map(Number);
    const horaInicio = new Date();
    horaInicio.setHours(hora, minuto, 0, 0);

    const horaFin = new Date(horaInicio);
    horaFin.setHours(horaFin.getHours() + horasReserva);

    const [hs, ms] = datosEspacio.horaSalida.split(':').map(Number);
    const horaLimite = new Date();
    horaLimite.setHours(hs, ms, 0, 0);

    if (horaFin > horaLimite) {
        mostrarAlerta('warning', 'La reserva excede la hora de cierre del local');
        txtTotalPagar.value = '';
        txtHoraSalida.value = '';
        return;
    }
    let totalPagar = 0;
    for (let i = 0; i < horasReserva; i++) {
        const horaActual = hora + i;
        const esNoche = horaActual >= 18;
        const precio = esNoche ? datosEspacio.precioNocheCancha : datosEspacio.precioDiaCancha;
        totalPagar += parseFloat(precio);
    }

    txtTotalPagar.value = totalPagar.toFixed(2);

    const porcentaje = parseFloat(datosEspacio.porcentajeReservaLocal);
    // Pintar la hora de salida
     txtHoraSalida.value = horaFin.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function formatearHora(hora) {
  if (!hora) return "";
  const [hh, mm] = hora.split(":"); 
  return `${hh}:${mm}`;
}

function pintarHorasLibreEditar(txtHoraSalidaEditar, txtTotalPagarEditar, txtHorasReservaEditar){
    txtHoraSalidaEditar.value = '';
    // txtAdelantoReservaEditar.value = '0.00';
    txtTotalPagarEditar.value = '0.00';
    txtHorasReservaEditar.value = '1';
}

function pintarHoraLibreCrear(txtHoraSalida, txtAdelanto, txtTotalPagar, txtHorasReserva, txtHorasReserva){
    txtHoraSalida.value = '';
    txtAdelanto.value = '0.00';
    txtTotalPagar.value = '0.00';
    txtHorasReserva.value = '1';
    txtHorasReserva.disabled = true;    
}

// Función reusable para ajustar días
function ajustarDia(fechaSeleccionada, dias) {
    const fecha = new Date(fechaSeleccionada);
    fecha.setDate(fecha.getDate() + dias);
    return fecha.toISOString().split('T')[0];
}

function agregarLoaderBotonSubmit(form){
    const submitBtn = form.querySelector('button[type="submit"]');
    if(submitBtn){
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Procesando...
        `; 
        submitBtn.disabled = true;
    }
}

function setupFormSpinner(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    
    if (submitBtn && !submitBtn.hasAttribute('data-spinner-setup')) {
        submitBtn.setAttribute('data-spinner-setup', 'true');
        
        // Guardar contenido original
        submitBtn.dataset.originalContent = submitBtn.innerHTML;
    }
}

function showFormSpinner(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn && submitBtn.dataset.originalContent) {
        submitBtn.classList.add('text-white');
        submitBtn.innerHTML = `
            <span class="spinner-border spinner-border-sm text-light me-2" aria-hidden="true"></span>
            ${submitBtn.textContent || 'Procesando...'}
        `;
        submitBtn.disabled = true;
    }
}

function hideFormSpinner(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn && submitBtn.dataset.originalContent) {
        submitBtn.classList.remove('text-white');
        submitBtn.innerHTML = submitBtn.dataset.originalContent;
        submitBtn.disabled = false;
    }
}

function mostrarLoaderBoton(button){
    button.innerHTML = `<span class="spinner-border spinner-border-sm text-dark" aria-hidden="true"></span>`;
    button.disabled = true;
}

function ocultarLoaderBoton(button){
    button.innerHTML = button.dataset.icono ?? '';
    button.disabled = false;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal form').forEach(form => {
        setupFormSpinner(form);
    });
});

function validarLongitudDocumento(tipo, numero) {
    switch (tipo) {
        case '000010': // DNI
            return /^\d{8}$/.test(numero);

        case '000011': // Pasaporte
            return /^[A-Za-z0-9]{8,12}$/.test(numero);

        case '000012': // Carné de extranjería
            return /^[A-Za-z0-9]{9,12}$/.test(numero);

        default:
            return false;
    }
}