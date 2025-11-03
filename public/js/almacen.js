document.addEventListener("DOMContentLoaded", function(){
	var urlRuta = '/almacenes/';
	
	activarLink('/almacenes');

	const formAlmacen = document.getElementById('formAlmacen');
	validarInputs(formAlmacen);

    btnNuevo.addEventListener('click', function(event){
        formAlmacen.reset();
        formAlmacen.querySelector('input[name="idAlmacen"]').value = '';
        document.getElementById('titulo').textContent = 'Nuevo almacen';
    });

	formAlmacen.addEventListener("submit", function(event) {
        event.preventDefault();
        if(!validarFormulario(this)) return;
        const idAlmacen = this.querySelector('input[name="idAlmacen"]').value;
        let accion = 'crear';
        if(idAlmacen != ''){
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
                    cerrarModal('modalAlmacen');
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
            formAlmacen.reset();
            formAlmacen.querySelector('input[name="idAlmacen"]').value = '';
            const idAlmacen = this.getAttribute("data-id");
            if (!idAlmacen) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            document.getElementById('titulo').textContent = 'Editar categoria';
            try {
                const response = await fetch(urlRuta + `seleccionar?idAlmacen=${idAlmacen}`, { method: "GET" });
                if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                const data = await response.json();
                if (!data.alerta) return;
                if (data.alerta.tipo === "error") {
                    mostrarAlerta("warning", data.alerta.mensaje);
                    return;
                }
                if (data.alerta.tipo === "ok" && data.datos) {
                    const datos = data.datos;
                    formAlmacen.querySelector('input[name="idAlmacen"]').value = datos.id_almacen || "";
                    formAlmacen.querySelector('input[name="txtNombreAlmacen"]').value = datos.nombre_almacen || '';
                    formAlmacen.querySelector('input[name="txtDireccionAlmacen"]').value = datos.direccion || '';
                    const modalAlmacen = new bootstrap.Modal(document.getElementById("modalAlmacen"));
                    modalAlmacen.show();
                }
            } catch (error) {
                console.error("Error en la petición:", error);
                mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");
            }
        });
    });

    document.querySelectorAll(".btnAccion").forEach(button => {
        button.addEventListener("click", async function (event) {
            const idAlmacen = this.getAttribute("data-id");
            const textoModal = this.getAttribute("data-accion");
            console.log("textoModal", textoModal);
            const nuevoEstado = this.getAttribute("data-estado");
            if (!idAlmacen) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            const modal = document.getElementById("modalAccion");
            document.getElementById('textoCambioEstado').textContent = textoModal;
            modal.querySelector('input[name="idAccion"]').value = idAlmacen;
            modal.querySelector('input[name="nuevoEstado"]').value = nuevoEstado;
            const modalAccion = new bootstrap.Modal(modal);
            modalAccion.show();     
        });
    });

    const btnEnviarAccion = document.getElementById('btnEnviarAccion');
    btnEnviarAccion.addEventListener('click', async function(event){
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

});