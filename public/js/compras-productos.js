document.addEventListener("DOMContentLoaded", function(){
    var urlRuta = '/compras/productos/';
    const proveedorSelect = new TomSelect("#cmbAlmacenProducto", {
        create: false,
        maxItems: 1,
        sortField: {field: "text"},
    });

    proveedorSelect.on("item_add", function() {
        this.blur(); // quita el foco del input
    });

    const categoriaSelect = new TomSelect("#cmbCategoriaProducto", {
        create: false,
        maxItems: 1,
        sortField: {field: "text"},
    });

    categoriaSelect.on("item_add", function() {
        this.blur();
    });

    const nombreSelect = new TomSelect("#cmbNombreProducto", {
        create: true,
        maxItems: 1,
        sortField: {field: "text"},
    });

    nombreSelect.on("item_add", function() {
        this.blur();
    });

    const formCompra = document.getElementById('formCompra');
    validarInputs(formCompra);

    const cmbAlmacenProducto = formCompra.querySelector('#cmbAlmacenProducto');
    const cmbCategoriaProducto = formCompra.querySelector('#cmbCategoriaProducto');

    if(typeof btnNuevo !== 'undefined'){
        btnNuevo.addEventListener('click', function(event){
            nombreSelect.clear(); 
            nombreSelect.clearOptions();
            formCompra.reset();
            proveedorSelect.setValue('');
            categoriaSelect.setValue('');
        });
    }


    formCompra.addEventListener("submit", function(event) {
        event.preventDefault();
        const producto = nombreSelect.getValue();
        if(!validarFormulario(this)) return;
        let accion = 'crear';
        if (/^\d+$/.test(producto)) {
            accion = 'agregar';
        }
        const idDetalle = this.querySelector('input[name="idDetalle"]').value;
        if(idDetalle != ''){
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
                    cerrarModal('modalProducto');
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

    cmbAlmacenProducto.addEventListener('change', function(event){
        ListarProductos(cmbCategoriaProducto.value, this.value);
    });

    cmbCategoriaProducto.addEventListener('change', function(event){
        ListarProductos(this.value, cmbAlmacenProducto.value);
    });

    const btnAprobarCompra = document.getElementById('btnAprobarCompra');
    if(btnAprobarCompra){
        btnAprobarCompra.addEventListener('click', function(event){
            const nuevoEstado = this.getAttribute('data-estado');
            const idCompra = document.querySelector('input[name="idCompra"]').value;
            if(!idCompra){
                mostrarAlerta("warning", "ID de compra no válido.");
                return;
            }
            const modal = document.getElementById("modalAprobar");
            modal.querySelector('input[name="idAccion"]').value = idCompra;
            const modalAccion = new bootstrap.Modal(modal);
            modalAccion.show();          
        });

        const btnAprobarModal = document.getElementById('btnAprobarModal');
        btnAprobarModal.addEventListener('click', async function(event){
            const modal = document.getElementById("modalAprobar");
            const idCompra = modal.querySelector('input[name="idAccion"]').value;
            let formData = new FormData;
            formData.append('idAccion', idCompra);
            try {
                const respuesta = await fetch(urlRuta + 'aprobar', {method: 'POST', body: formData})
                if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);
                const data = await respuesta.json();
                if (data.alerta.tipo === "error") {
                    mostrarAlerta("warning", data.alerta.mensaje);
                    return;
                }
                if (data.alerta.tipo === "ok") {
                    mostrarAlerta("ok", data.alerta.mensaje);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            }catch(error){
                console.error("Error en la petición:", error);
                mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");     
            }
        });  
    }

    const btnRecibirCompra = document.getElementById('btnRecibirCompra');
    if(btnRecibirCompra){
        btnRecibirCompra.addEventListener('click', function(event){
            const nuevoEstado = this.getAttribute('data-estado');
            const idCompra = document.querySelector('input[name="idCompra"]').value;
            if(!idCompra){
                mostrarAlerta("warning", "ID de compra no válido.");
                return;
            }
            const modal = document.getElementById("modalRecibirCompra");
            modal.querySelector('input[name="idAccion"]').value = idCompra;
            const modalAccion = new bootstrap.Modal(modal);
            modalAccion.show();          
        });

        const btnRecibirCompraModal = document.getElementById('btnRecibirCompraModal');
        btnRecibirCompraModal.addEventListener('click', async function(event){
            const modal = document.getElementById("modalRecibirCompra");
            const idCompra = modal.querySelector('input[name="idAccion"]').value;
            let formData = new FormData;
            formData.append('idAccion', idCompra);
            try {
                const respuesta = await fetch(urlRuta + 'recibir', {method: 'POST', body: formData})
                if (!respuesta.ok) throw new Error(`Error HTTP: ${respuesta.status}`);
                const data = await respuesta.json();
                if (data.alerta.tipo === "error") {
                    mostrarAlerta("warning", data.alerta.mensaje);
                    return;
                }
                if (data.alerta.tipo === "ok") {
                    mostrarAlerta("ok", data.alerta.mensaje);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            }catch(error){
                console.error("Error en la petición:", error);
                mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");     
            }
        });      
    }

    async function ListarProductos(idCategoria, idAlmacen){
        if (!idCategoria || !idAlmacen) {
            return;
        }
        try {
            const response = await fetch(urlRuta + `por-almacen-categoria?idCategoria=${idCategoria}&idAlmacen=${idAlmacen}`, { method: "GET" });
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const data = await response.json();
            if (!data.alerta) return;
            if (data.alerta.tipo === "error") {
                mostrarAlerta("warning", data.alerta.mensaje);
                return;
            }
            if (data.alerta.tipo === "ok" && data.datos) {
                const datos = data.datos;
                nombreSelect.clearOptions();
                datos.forEach(p => {
                    nombreSelect.addOption({
                        value: p.id_producto,
                        text: p.nombre
                    });
                });
                nombreSelect.refreshOptions(false);
            }
        } catch (error) {
            console.error("Error en la petición:", error);
            mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");
        }
    }
});