const proveedorSelect = new TomSelect("#cmbProveedor", {
    create: false,
    maxItems: 1,
    sortField: {field: "text"},
});

proveedorSelect.on("item_add", function() {
    this.blur(); // quita el foco del input
});

const categoriaSelect = new TomSelect("#cmbCategoria", {
    create: false,
    maxItems: 1,
    sortField: {field: "text"},
});

categoriaSelect.on("item_add", function() {
    this.blur();
});