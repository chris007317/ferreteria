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

    const formCompra = document.getElementById('formCompra');
    validarInputs(formCompra);

    formCompra.addEventListener("submit", function(event) {
        event.preventDefault();
        if(!validarFormulario(this)) return;
        const idProducto = this.querySelector('input[name="idProducto"]').value;
        let accion = 'crear';
        if(idProducto != ''){
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

    const btnAprobarCompra = document.getElementById('btnAprobarCompra');
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
});