document.addEventListener("DOMContentLoaded", function () {
    var urlRuta = '/ventas/';
    var urlRutaProducto = '/productos/';
    var urlPersona = '/ajustes/personas/';

    const contenedorMenu = document.querySelector("#menuVentas");
    if (contenedorMenu) {
        contenedorMenu.click();
        contenedorMenu.blur();
    }

    activarLinkMenu('/ventas');

    const productoSelect = new TomSelect("#cmbProductoVenta", {
        create: false,
        maxItems: 1,
        sortField: {field: "text"},
    });

    productoSelect.on("item_add", function() {
        this.blur();
    });

    const formAgregarProducto = document.getElementById('formAgregarProducto');
    validarInputs(formAgregarProducto);

    const cmbProductoVenta = formAgregarProducto.querySelector('#cmbProductoVenta');
    const inputPrecioUnidadProducto = formAgregarProducto.querySelector('input[name="txtPrecioUnidadaProducto"]');
    const inputCantidadProducto = formAgregarProducto.querySelector('input[name="txtCantidadProducto"]');
    const inputDescuentoProducto = formAgregarProducto.querySelector('input[name="txtDescuentoProducto"]');

    inputDescuentoProducto.addEventListener('change', function(event){
        const descuento = this.value;
        if(descuento < 0){
            this.value = 0;
            mostrarAlerta("warning", "El descuento no debe ser menor a cero");
            return;
        }
        const precio = inputPrecioUnidadProducto.value;
        if(descuento >= precio){
            this.value = 0;
            mostrarAlerta("warning", "El descuento no debe ser menor a cero");
            return;
        }
    });

    cmbProductoVenta.addEventListener('change', async function(event){
        const idProducto = this.value;
        if(!idProducto){
            return;
        }
        try {
            const response = await fetch(urlRutaProducto + `seleccionar?idProducto=${idProducto}`, { method: "GET" });
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const data = await response.json();
            if (!data.alerta) return;
            if (data.alerta.tipo === "error") {
                mostrarAlerta("warning", data.alerta.mensaje);
                return;
            }
            if (data.alerta.tipo === "ok" && data.datos) {
                const datos = data.datos;
                inputPrecioUnidadProducto.value = datos.precio_venta ?? '';
                inputCantidadProducto.max = datos.stock ?? 0;
                inputCantidadProducto.focus();
                formAgregarProducto.querySelector('input[name="stockProducto"]').value = datos.stock ?? 0;
            }
        } catch (error) {
            console.error("Error en la petición:", error);
            mostrarAlerta("error", "Ocurrió un error, comunícate con un administrador");
        }           
    });
    let productosAgregados = [];
    const tablaBody = document.querySelector("#tablaVenta tbody");
    const tablafoot = document.querySelector("#tablaVenta tfoot");

    formAgregarProducto.addEventListener("submit", function (event) {
        event.preventDefault();
        if (!validarFormulario(this)) return;
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        // Normalizar datos
        data.idProducto = data.cmbProductoVenta;
        data.textoProducto = cmbProductoVenta.options[cmbProductoVenta.selectedIndex].text;
        data.precioUnit = parseFloat(data.txtPrecioUnidadaProducto);
        data.cantidad = parseInt(data.txtCantidadProducto);
        data.descuento = parseFloat(data.txtDescuentoProducto);
        data.stock = parseInt(data.stockProducto);
        data.total = (data.precioUnit - data.descuento) * data.cantidad;
        // Validaciones
        if (!data.idProducto) {
            mostrarAlerta("warning", "Debe seleccionar un producto");
            return;
        }
        if (data.cantidad <= 0) {
            mostrarAlerta("warning", "La cantidad debe ser mayor a cero");
            return;
        }
        if (data.cantidad > data.stock) {
            mostrarAlerta("warning", "No cuenta con stock suficiente");
            return;
        }
        // Agregar o actualizar producto
        if (agregarProducto(data)) {
            renderTabla(tablaBody, tablafoot);
            formAgregarProducto.reset();
            productoSelect.setValue('');
            productoSelect.onFocus();
            formAgregarProducto.querySelector('input[name="stockProducto"]').value = "";
        }
    });

    tablaBody.addEventListener("click", function (event) {
        if (event.target.closest(".eliminarProducto")) {
            const index = event.target.closest(".eliminarProducto").dataset.index;
            eliminarProducto(index);
        }
    });

    formVenta = document.getElementById('formVenta');
    validarInputs(formVenta);

    const inputSubTotalVenta = formVenta.querySelector('input[name="txtSubTotal"]');
    const inputTotalVenta = formVenta.querySelector('input[name="txtTotalVenta"]');
    const cmbTipoDocumento = formVenta.querySelector('select[name="cmbTipoCliente"]');
    const inputNumeroDocumento = formVenta.querySelector('input[name="txtNumeroDocumento"]');
    const inputDescuentoTotal = formVenta.querySelector('input[name="txtDescuento"]');

    cmbTipoDocumento.addEventListener('change', function(event){
        inputNumeroDocumento.disabled = false;
        inputNumeroDocumento.value = '';
        const codigo = this.value;
        switch (codigo) {
            case '000010':
                inputNumeroDocumento.maxLength = 8;
                inputNumeroDocumento.minLength = 8;
                break;
            case '000011':
                inputNumeroDocumento.maxLength = 12;
                inputNumeroDocumento.minLength = 8;
                break;
            case '000012':
                inputNumeroDocumento.maxLength = 12;
                inputNumeroDocumento.minLength = 9;
                break;
            default:
                inputNumeroDocumento.disabled = true;                
                break;
        }
    });

    inputNumeroDocumento.addEventListener('input', function(event){
        if(cmbTipoDocumento.value == '000010'){
            this.value = this.value.replace(/[^0-9]/g, '');
        }
    });

    const btnBuscar = document.getElementById('btnBuscar');

    btnBuscar.addEventListener('click', async function(event){
        const tipo = cmbTipoDocumento.value;
        const documento = inputNumeroDocumento.value;
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
                formVenta.querySelector('input[name="txtNombresPersona"]').value = persona.nombres || '';
                formVenta.querySelector('input[name="txtApellidosPersona"]').value = persona.apellidos || '';
            }else{
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

    formVenta.addEventListener("submit", function(event) {
        event.preventDefault();
        if(!validarFormulario(this)) return;
        if(productosAgregados.length == 0){
            mostrarAlerta("warning", "Debe agregar por lo menos un producto a la venta");
            return;
        }
        let accion = 'crear';
        let formData = new FormData(this);
        formData.append("productos", JSON.stringify(productosAgregados));
        fetch(urlRuta + accion, {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.alerta){
                if(data.alerta.tipo == 'ok') {
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

    inputDescuentoTotal.addEventListener('change', function(event){
        const descuento = this.value;
        const total = inputTotalVenta.value;
        if(descuento < 0){
            mostrarAlerta("warning", "El descuento no puede ser menor a cero");
            this.value = 0;
            return;
        }
        if(descuento >= total) {
            mostrarAlerta("warning", "El descuento no puede ser mayor o igual que el total");
            this.value = 0;
            return;
        }
        formVenta.querySelector('#totalConDescuento').innerHTML = 'S/.' + (total - descuento).toFixed(2);
    });

    function renderTabla(tablaBody, tablaFoot) {
        tablaBody.innerHTML = ""; 
        let sumaTotal = 0;
        productosAgregados.forEach((item, index) => {
            const fila = document.createElement("tr");
            fila.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.textoProducto}</td>
                <td class="text-center">${item.cantidad}</td>
                <td class="text-center">S/. ${item.precioUnit.toFixed(2)}</td>
                <td class="text-center">S/. ${(item.descuento * item.cantidad).toFixed(2)}</td>
                <td class="text-center">S/. ${item.total.toFixed(2)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-dark eliminarProducto" data-index="${index}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tablaBody.appendChild(fila);
            sumaTotal += item.total;
        });
        // --- TFOOT ---
        tablaFoot.innerHTML = `
            <tr class="table-light">
                <td colspan="5" class="text-end fw-bold">TOTAL GENERAL</td>
                <td class="text-center fw-bold">S/. ${sumaTotal.toFixed(2)}</td>
                <td></td>
            </tr>
        `;
        inputTotalVenta.value = sumaTotal.toFixed(2);
        inputSubTotalVenta.value = (sumaTotal.toFixed(2) * 0.18).toFixed(2);
        formVenta.querySelector('#totalConDescuento').innerHTML = 'S/.' + sumaTotal.toFixed(2);
    }


    function eliminarProducto(index) {
        const tablaBody = document.querySelector("#tablaVenta tbody");
        productosAgregados.splice(index, 1);
        renderTabla(tablaBody, tablafoot);
    }

    function agregarProducto(data) {
        const indexExistente = productosAgregados.findIndex(
            p => p.idProducto == data.idProducto
        );
        if (indexExistente > -1) {
            // Ya existe → sumamos
            const existente = productosAgregados[indexExistente];
            const nuevaCantidad = existente.cantidad + data.cantidad;
            // Validar stock
            if (nuevaCantidad > data.stock) {
                mostrarAlerta("warning", "La cantidad total supera el stock disponible");
                inputCantidadProducto.focus();
                return false;
            }
            existente.cantidad = nuevaCantidad;
            existente.descuento += data.descuento; // si quieres sumar descuentos
            existente.total = (existente.precioUnit * existente.cantidad) - existente.descuento;
            return true;
        }
        // No existe → agregar nuevo
        productosAgregados.push(data);
        return true;
    }
});