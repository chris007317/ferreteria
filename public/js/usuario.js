document.addEventListener("DOMContentLoaded", function () {
    var urlRuta = '/ajustes/usuarios/';
    var urlPersona = '/ajustes/personas/';
    const contenedorMenu = document.querySelector("#menuAjustes");
    if (contenedorMenu) {
        contenedorMenu.click();
        contenedorMenu.blur();
    }

    activarLinkMenu('/ajustes/usuarios');

    const formUsuario = document.getElementById('formUsuario');
    validarInputs(formUsuario);

    btnNuevo.addEventListener('click', function(event){
        formUsuario.reset();
        const inputContraUsuario = formUsuario.querySelector('input[name="txtContraUsuario"]');
        inputContraUsuario.value = '';
        inputContraUsuario.readOnly = false;
        formUsuario.querySelector('input[name="idUsuario"]').value = '';
        formUsuario.querySelector('#chkGenerarContra').checked = false;
        formUsuario.querySelector('#generarContra').classList.remove('d-none');
        document.getElementById('titulo').textContent = 'Nuevo usuario';
    });

    const inputDocumentoUsuario = formUsuario.querySelector('input[name="txtDocumentoUsuario"]');
    const cmbTipoDocumentoUsuario = formUsuario.querySelector('#cmbTipoDocumentoUsuario');
    cmbTipoDocumentoUsuario.addEventListener('change', function(event){
        inputDocumentoUsuario.disabled = false;
        inputDocumentoUsuario.value = '';
        const codigo = this.value;
        switch (codigo) {
            case '000010':
                inputDocumentoUsuario.maxLength = 8;
                inputDocumentoUsuario.minLength = 8;
                break;
            case '000011':
                inputDocumentoUsuario.maxLength = 12;
                inputDocumentoUsuario.minLength = 8;
                break;
            case '000012':
                inputDocumentoUsuario.maxLength = 12;
                inputDocumentoUsuario.minLength = 9;
                break;
            default:
                inputDocumentoUsuario.disabled = true;                
                break;
        }
    });

    inputDocumentoUsuario.addEventListener('input', function(event){
        if(cmbTipoDocumentoUsuario.value == '000010'){
            this.value = this.value.replace(/[^0-9]/g, '');
        }
    });

    const btnBuscar = document.getElementById('btnBuscar');

    btnBuscar.addEventListener('click', async function(event){
        const tipo = cmbTipoDocumentoUsuario.value;
        const documento = inputDocumentoUsuario.value;
        if (!validarLongitudDocumento(tipo, documento)) {
            mostrarAlerta("warning", "El número de documento no es válido");
            return;
        }
        mostrarLoaderBoton(this);
        try {
            const response = await fetch(urlPersona + `buscarDocumento?documento=${documento}&&tipo=${tipo}`, { method: "GET" });
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const data = await response.json();
            if (!data) return;
            if (data.alerta.tipo === "ok" && data.datos) {
                const persona = data.datos;                
                formUsuario.querySelector('input[name="txtNombresPersona"]').value = persona.nombres || '';
                formUsuario.querySelector('input[name="txtApellidos"]').value = persona.apellidos || '';
                formUsuario.querySelector('input[name="txtCelularPersona"]').value = persona.telefono || '';
                formUsuario.querySelector('input[name="txtCorreoPersona"]').focus();
            }else{
                limpiarDatosPersona();
                mostrarAlerta(data.alerta.tipo, data.alerta.mensaje);
                return;
            }
        }catch(error){
            console.error("Error en la petición:", error);
            mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");
        }finally{
            ocultarLoaderBoton(this);
        }
    });

    const chkGenerarContra = formUsuario.querySelector('input[name="chkGenerarContra"]');

    chkGenerarContra.addEventListener('change', function() {
        const inputContraUsuario = document.querySelector('input[name="txtContraUsuario"]')
        if (this.checked) {
            inputContraUsuario.readOnly = true;
            inputContraUsuario.value = generarContra(8);
        } else {
            inputContraUsuario.readOnly = false;
            inputContraUsuario.value = '';
            inputContraUsuario.focus();
        }
    });

    formUsuario.addEventListener("submit", function(event) {
        event.preventDefault();
        if(!validarFormulario(this)) return;
        const idUsuario = this.querySelector('input[name="idUsuario"]').value;
        let accion = 'crear';
        if(idUsuario != ''){
            accion = 'editar'
        }
        let formData = new FormData(this);
        fetch(urlRuta + accion, {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.alerta){
                if(data.alerta.tipo == 'ok') {
                    cerrarModal('modalUsuario');
                    mostrarModalAlerta(data.alerta.tipo, data.alerta.mensaje, true);
                    return;
                }
                mostrarModalAlerta(data.alerta.tipo, data.alerta.mensaje);
            }
        })
        .catch(error => {
            mostrarModalAlerta('error', 'No se pudo realizar la acción')
        });
    });

    document.querySelectorAll(".btnEditar").forEach(button => {
        button.addEventListener("click", async function (event) {
            formUsuario.reset();
            const idUsuario = this.getAttribute("data-id");
            if (!idUsuario) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            document.getElementById('titulo').textContent = 'Editar usuario';
            try {
                const response = await fetch(urlRuta + `seleccionar?idUsuario=${idUsuario}`, { method: "GET" });
                if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                const data = await response.json();
                if (!data.alerta) return;
                if (data.alerta.tipo === "error") {
                    mostrarAlerta("warning", data.alerta.mensaje);
                    return;
                }
                if (data.alerta.tipo === "ok" && data.datos) {
                    const usuario = data.datos;
                    console.log("usuario", usuario);
                    formUsuario.querySelector('#cmbTipoDocumentoUsuario').value = usuario.codigo_tipo_doc || '';
                    formUsuario.querySelector('input[name="idUsuario"]').value = usuario.id_usuario || "";
                    formUsuario.querySelector('input[name="txtDocumentoUsuario"]').value = usuario.num_documento || '';
                    formUsuario.querySelector('input[name="txtNombresPersona"]').value = usuario.nombres || '';
                    formUsuario.querySelector('input[name="txtApellidos"]').value = usuario.apellidos || '';
                    formUsuario.querySelector('input[name="txtCelularPersona"]').value = usuario.telefono || '';
                    formUsuario.querySelector('input[name="txtCorreoPersona"]').value = usuario.email || '';
                    formUsuario.querySelector('input[name="txtNombreUsuario"]').value = usuario.username || '';
                    formUsuario.querySelector('#cmbTipoUsuario').value = usuario.codigo_rol || '';
                    formUsuario.querySelector('#generarContra').classList.add('d-none');
                    const modalUsuario = new bootstrap.Modal(document.getElementById("modalUsuario"));
                    modalUsuario.show();
                }
            } catch (error) {
                console.error("Error en la petición:", error);
                mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");
            }
        });
    });

    document.querySelectorAll(".btnNuevaContra").forEach(button => {
        button.addEventListener("click", async function (event) {
            let inputContra = document.querySelector('input[name="txtNuevaContraUsuario"]');
            inputContra.value = '';
            inputContra.readOnly = false;
            document.querySelector('input[name="chkNuevaContra"]').checked  = false;
            const idUsuario = this.getAttribute("data-id");
            const nombreUsuario = this.getAttribute("data-nombre");
            document.getElementById("usuarioDatos").textContent = nombreUsuario;
            if (!idUsuario) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            const modal = document.getElementById("modalNuevaContra");
            modal.querySelector('input[name="idActualizar"]').value = idUsuario;
            const modalNuevaContra = new bootstrap.Modal(modal);
            modal.addEventListener('shown.bs.modal', () => {
                inputContra.focus();
            }, { once: true })            
            modalNuevaContra.show();
        });
    });
    
    const chkNuevaContra = document.getElementById('chkNuevaContra');

    chkNuevaContra.addEventListener('change', function() {
        const inputContraUsuario = document.querySelector('input[name="txtNuevaContraUsuario"]')
        if (this.checked) {
            inputContraUsuario.readOnly = true;
            inputContraUsuario.value = generarContra(8);
        } else {
            inputContraUsuario.readOnly = false;
            inputContraUsuario.value = '';
            inputContraUsuario.focus();
        }
    });

    const btnNuevaContra = document.getElementById('btnActualizarContra');
    btnNuevaContra.addEventListener('click', async function(event){
        const modal = document.getElementById("modalNuevaContra");
        const idUsuario = modal.querySelector('input[name="idActualizar"]').value;
        const nuevaContra = modal.querySelector('input[name="txtNuevaContraUsuario"]').value;
        let formData = new FormData;
        formData.append('idActualizar', idUsuario);
        formData.append('txtNuevaContraUsuario', nuevaContra);
        try {
            const respuesta = await fetch(urlRuta + 'nueva-contra', {method: 'POST', body: formData})
            if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);
            const data = await respuesta.json();
            if (data.alerta.tipo === "error") {
                mostrarAlerta("warning", data.alerta.mensaje);
                return;
            }
            if (data.alerta.tipo === "ok") {
                mostrarAlerta("ok", data.alerta.mensaje);
                cerrarModal('modalNuevaContra');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        }catch(error){
            cerrarModal('modalNuevaContra');
            console.error("Error en la petición:", error);
            mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");     
        }
    });

    document.querySelectorAll(".btnAccion").forEach(button => {
        button.addEventListener("click", async function (event) {
            const idUsuario = this.getAttribute("data-id");
            const textoModal = this.getAttribute("data-accion");
            const nuevoEstado = this.getAttribute("data-estado");
            console.log("textoModal", textoModal);
            if (!idUsuario) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            const modal = document.getElementById("modalEliminar");
            document.getElementById('textoAccion').textContent = textoModal;
            modal.querySelector('input[name="idAccion"]').value = idUsuario;
            modal.querySelector('input[name="nuevoEstado"]').value = nuevoEstado;
            const modalEliminar = new bootstrap.Modal(modal);
            modalEliminar.show();     
        });
    });

    const btnEliminarModal = document.getElementById('btnEliminarModal');
    btnEliminarModal.addEventListener('click', async function(event){
        const modal = document.getElementById("modalEliminar");
        const idUsuario = modal.querySelector('input[name="idAccion"]').value;
        const nuevoEstado = modal.querySelector('input[name="nuevoEstado"]').value;
        let formData = new FormData;
        formData.append('idAccion', idUsuario);
        formData.append('estado', nuevoEstado);
        try {
            const respuesta = await fetch(urlRuta + 'cambiar-estado', {method: 'POST', body: formData})
            if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);
            const data = await respuesta.json();
            if (data.alerta.tipo === "error") {
                mostrarAlerta("warning", data.alerta.mensaje);
                return;
            }
            if (data.alerta.tipo === "ok") {
                mostrarAlerta("ok", data.alerta.mensaje);
                cerrarModal('modalEliminar');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        }catch(error){
            cerrarModal('modalEliminar');
            console.error("Error en la petición:", error);
            mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");     
        }
    });    

    function limpiarDatosPersona(){
        formUsuario.querySelector('input[name="txtNombresPersona"]').focus();
        formUsuario.querySelector('input[name="txtNombresPersona"]').value = '';
        formUsuario.querySelector('input[name="txtApellidos"]').value = '';
        formUsuario.querySelector('input[name="txtCelularPersona"]').value = '';        
    }
});