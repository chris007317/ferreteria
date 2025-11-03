document.addEventListener("DOMContentLoaded", function(){
	var urlRuta = '/proveedores/';
	var urlPersona = '/ajustes/personas/';
	
	activarLink('/proveedores');

	const formProveedor = document.getElementById('formProveedor');
	validarInputs(formProveedor);

    btnNuevo.addEventListener('click', function(event){
        formProveedor.reset();
        formProveedor.querySelector('input[name="idProveedor"]').value = '';
        document.getElementById('titulo').textContent = 'Nuevo proveedor';
    });

    const inputRuc = formProveedor.querySelector('input[name="txtDocumentoProveedor"]');
    inputRuc.addEventListener('input', function(event){
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    const btnBuscar = formProveedor.querySelector('#btnBuscar');
	btnBuscar.addEventListener('click', async function(event){
        const documento = inputRuc.value;
        if(documento.length != 11){
            mostrarAlerta("warning", "El RUC debe tener 11 digitos");
            return;
        }
        mostrarLoaderBoton(this);
        try {
            const response = await fetch(urlPersona + `buscarRuc?documento=${documento}`, { method: "GET" });
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const data = await response.json();
            if (!data) return;
            if (data.alerta.tipo === "ok" && data.datos) {
                const datos = data.datos;
		        formProveedor.querySelector('input[name="txtRazonSocialProveedor"]').value = datos.razon_social ?? '';
		        formProveedor.querySelector('input[name="txtDireccionProveedor"]').value = datos.direccion ?? '';
		        formProveedor.querySelector('input[name="txtCorreoProveedor"]').focus();
            }else{
                limpiarDatosProveedor();
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

	formProveedor.addEventListener("submit", function(event) {
        event.preventDefault();
        if(!validarFormulario(this)) return;
        const idProveedor = this.querySelector('input[name="idProveedor"]').value;
        let accion = 'crear';
        if(idProveedor != ''){
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
                    cerrarModal('modalProveedor');
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
            formProveedor.reset();
            formProveedor.querySelector('input[name="idProveedor"]').value = '';
            const idProveedor = this.getAttribute("data-id");
            console.log("idProveedor", idProveedor);
            if (!idProveedor) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            document.getElementById('titulo').textContent = 'Editar categoria';
            try {
                const response = await fetch(urlRuta + `seleccionar?idProveedor=${idProveedor}`, { method: "GET" });
                if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                const data = await response.json();
                if (!data.alerta) return;
                if (data.alerta.tipo === "error") {
                    mostrarAlerta("warning", data.alerta.mensaje);
                    return;
                }
                if (data.alerta.tipo === "ok" && data.datos) {
                    const datos = data.datos;
                    formProveedor.querySelector('input[name="idProveedor"]').value = datos.id_proveedor || "";
                    formProveedor.querySelector('input[name="txtDocumentoProveedor"]').value = datos.num_documento || '';
                    formProveedor.querySelector('input[name="txtRazonSocialProveedor"]').value = datos.razon_social || '';
                    formProveedor.querySelector('input[name="txtDireccionProveedor"]').value = datos.direccion || '';
                    formProveedor.querySelector('input[name="txtCorreoProveedor"]').value = datos.email || '';
                    formProveedor.querySelector('input[name="txtCelular"]').value = datos.telefono || '';
                    const modalProveedor = new bootstrap.Modal(document.getElementById("modalProveedor"));
                    modalProveedor.show();
                }
            } catch (error) {
                console.error("Error en la petición:", error);
                mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");
            }
        });
    });

    document.querySelectorAll(".btnAccion").forEach(button => {
        button.addEventListener("click", async function (event) {
            const idProveedor = this.getAttribute("data-id");
            const textoModal = this.getAttribute("data-accion");
            const nuevoEstado = this.getAttribute("data-estado");
            if (!idProveedor) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            const modal = document.getElementById("modalAccion");
            document.getElementById('textoCambioEstado').textContent = textoModal;
            modal.querySelector('input[name="idAccion"]').value = idProveedor;
            modal.querySelector('input[name="nuevoEstado"]').value = nuevoEstado;
            const modalAccion = new bootstrap.Modal(modal);
            modalAccion.show();     
        });
    });

    const btnEliminarModal = document.getElementById('btnEnviarAccion');
    btnEliminarModal.addEventListener('click', async function(event){
        const modal = document.getElementById("modalAccion");
        const idCategoria = modal.querySelector('input[name="idAccion"]').value;
        const nuevoEstado = modal.querySelector('input[name="nuevoEstado"]').value;
        let formData = new FormData;
        formData.append('idAccion', idCategoria);
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
                cerrarModal('modalAccion');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        }catch(error){
            cerrarModal('modalAccion');
            console.error("Error en la petición:", error);
            mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");     
        }
    });    


	function limpiarDatosProveedor(){
        formProveedor.querySelector('input[name="txtRazonSocialProveedor"]').focus();
        formProveedor.querySelector('input[name="txtRazonSocialProveedor"]').value = '';
        formProveedor.querySelector('input[name="txtDireccionProveedor"]').value = '';
    }
});