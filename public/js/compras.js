document.addEventListener("DOMContentLoaded", function(){
	var urlRuta = '/compras/';
	
	activarLink('/compras');

    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');
    btnLimpiarFiltros.addEventListener('click', function(event){
        const formBuscar = document.getElementById('formBuscar');
        formBuscar.querySelector('input[name="txtBuscarProveedor"]').value = '';
        formBuscar.querySelector('input[name="txtBuscarRuc"]').value = '';
        formBuscar.querySelector('input[name="txtBuscarFecha"]').value = '';
        const action = formBuscar.getAttribute('action') || window.location.pathname;
        formBuscar.setAttribute('action', action);
        formBuscar.submit();
    });

	const formCompra = document.getElementById('formCompra');
	validarInputs(formCompra);

    btnNuevo.addEventListener('click', function(event){
        formCompra.reset();
        formCompra.querySelector('input[name="idCompra"]').value = '';
        document.getElementById('titulo').textContent = 'Nueva compra';
    });

	formCompra.addEventListener("submit", function(event) {
        event.preventDefault();
        if(!validarFormulario(this)) return;
        const idCompra = this.querySelector('input[name="idCompra"]').value;
        let accion = 'crear';
        if(idCompra != ''){
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
                    cerrarModal('modalCompra');
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

    const inputTotal = formCompra.querySelector('input[name="txtTotalCompra"]');
    const inputSubTotal = formCompra.querySelector('input[name="txtSubTotalCompra"]');
    const inputIgv = formCompra.querySelector('input[name="txtIgvCompra"]');

    inputTotal.addEventListener('keyup', function(evet){
    	const total = parseFloat(this.value);
    	const igv = parseFloat(inputIgv.value);
    	if (total <= 0) return;
    	inputSubTotal.value  = (total / (1 + igv)).toFixed(2);
    });

    inputIgv.addEventListener('keyup', function(evet){
    	const total = parseFloat(inputTotal.value);
    	const igv = parseFloat(this.value);
    	if (igv <= 0) return;
    	inputSubTotal.value  = (total / (1 + igv)).toFixed(2);
    });

    document.querySelectorAll(".btnEditar").forEach(button => {
        button.addEventListener("click", async function (event) {
            formCompra.reset();
            formCompra.querySelector('input[name="idCompra"]').value = '';
            const idCompra = this.getAttribute("data-id");
            console.log("idCompra", idCompra);
            if (!idCompra) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            document.getElementById('titulo').textContent = 'Editar categoria';
            try {
                const response = await fetch(urlRuta + `seleccionar?idCompra=${idCompra}`, { method: "GET" });
                if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                const data = await response.json();
                if (!data.alerta) return;
                if (data.alerta.tipo === "error") {
                    mostrarAlerta("warning", data.alerta.mensaje);
                    return;
                }
                if (data.alerta.tipo === "ok" && data.datos) {
                    const datos = data.datos;
                    formCompra.querySelector('input[name="idCompra"]').value = datos.id_compra || "";
                    formCompra.querySelector('select[name="cmbProveedor"]').value = datos.id_proveedor || '';
                    formCompra.querySelector('input[name="txtNumeroCompra"]').value = datos.numero_compra || '';
                    formCompra.querySelector('input[name="txtFechaCompra"]').value = datos.fecha || '';
                    formCompra.querySelector('input[name="txtTotalCompra"]').value = datos.total || '';
                    formCompra.querySelector('input[name="txtIgvCompra"]').value = datos.igv || '';
                    formCompra.querySelector('input[name="txtSubTotalCompra"]').value = datos.subtotal || '';
                    formCompra.querySelector('textarea[name="txtObservacionesCompra"]').value = datos.observaciones || '';
                    const modalCompra = new bootstrap.Modal(document.getElementById("modalCompra"));
                    modalCompra.show();
                }
            } catch (error) {
                console.error("Error en la petición:", error);
                mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");
            }
        });
    });

    document.querySelectorAll(".btnEliminar").forEach(button => {
        button.addEventListener("click", async function (event) {
            const idCompra = this.getAttribute("data-id");
            const textoModal = this.getAttribute("data-accion");
            console.log("textoModal", textoModal);
            if (!idCompra) {
                mostrarAlerta("warning", "ID de usuario no válido.");
                return;
            }
            const modal = document.getElementById("modalEliminar");
            document.getElementById('textoCambioEstado').textContent = textoModal;
            modal.querySelector('input[name="idEliminar"]').value = idCompra;
            const modalEliminar = new bootstrap.Modal(modal);
            modalEliminar.show();     
        });
    });

    const btnEliminarModal = document.getElementById('bntEliminar');
    btnEliminarModal.addEventListener('click', async function(event){
        const modal = document.getElementById("modalEliminar");
        const idCompra = modal.querySelector('input[name="idEliminar"]').value;
        let formData = new FormData;
        formData.append('idEliminar', idCompra);
        try {
            const respuesta = await fetch(urlRuta + 'eliminar', {method: 'POST', body: formData})
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

});
