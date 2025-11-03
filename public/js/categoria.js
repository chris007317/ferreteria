document.addEventListener("DOMContentLoaded", function () {
    var urlRuta = '/ajustes/categoria/';
    const contenedorMenu = document.querySelector("#menuAjustes");
    if (contenedorMenu) {
        contenedorMenu.click();
        contenedorMenu.blur();
    }

    activarLinkMenu('/ajustes/categoria');

    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    btnLimpiarFiltros.addEventListener('click', function(event){
        const formBuscar = document.getElementById('formBuscar');
        formBuscar.querySelector('input[name="txtBuscarNombre"]').value = '';
        const action = formBuscar.getAttribute('action') || window.location.pathname;
        formBuscar.setAttribute('action', action);
        formBuscar.submit();
    });

    const formCategoria = document.getElementById('formCategoria');
    validarInputs(formCategoria);

    btnNuevo.addEventListener('click', function(event){
        formCategoria.reset();
        formCategoria.querySelector('input[name="idCategoria"]').value = '';
        document.getElementById('titulo').textContent = 'Nueva categoría';
    });

    formCategoria.addEventListener("submit", function(event) {
        event.preventDefault();
        if(!validarFormulario(this)) return;
        const idCategoria = this.querySelector('input[name="idCategoria"]').value;
        let accion = 'crear';
        if(idCategoria != ''){
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
                    cerrarModal('modalCategoria');
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
            formCategoria.reset();
            formCategoria.querySelector('input[name="idCategoria"]').value = '';
            const idCategoria = this.getAttribute("data-id");
            if (!idCategoria) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            document.getElementById('titulo').textContent = 'Editar categoria';
            try {
                const response = await fetch(urlRuta + `seleccionar?idCategoria=${idCategoria}`, { method: "GET" });
                if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                const data = await response.json();
                if (!data.alerta) return;
                if (data.alerta.tipo === "error") {
                    mostrarAlerta("warning", data.alerta.mensaje);
                    return;
                }
                if (data.alerta.tipo === "ok" && data.datos) {
                    const datos = data.datos;
                    formCategoria.querySelector('input[name="idCategoria"]').value = datos.id_categoria || "";
                    formCategoria.querySelector('input[name="txtNombreCategoria"]').value = datos.nombre || '';
                    formCategoria.querySelector('input[name="txtNombreCategoria"]').focus();
                    formCategoria.querySelector('textarea[name="txtDescripcionCategoria"]').value = datos.descripcion || '';                    
                    const modalCategoria = new bootstrap.Modal(document.getElementById("modalCategoria"));
                    modalCategoria.show();
                }
            } catch (error) {
                console.error("Error en la petición:", error);
                mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");
            }
        });
    });

    document.querySelectorAll(".btnAccion").forEach(button => {
        button.addEventListener("click", async function (event) {
            const idCategoria = this.getAttribute("data-id");
            const textoModal = this.getAttribute("data-accion");
            const nuevoEstado = this.getAttribute("data-estado");
            if (!idCategoria) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            const modal = document.getElementById("modalAccion");
            document.getElementById('textoAccion').textContent = textoModal;
            modal.querySelector('input[name="idAccion"]').value = idCategoria;
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
});