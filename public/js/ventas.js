document.addEventListener("DOMContentLoaded", function () {
    var urlRuta = '/ventas/';

    const contenedorMenu = document.querySelector("#menuVentas");
    if (contenedorMenu) {
        contenedorMenu.click();
        contenedorMenu.blur();
    }

    activarLinkMenu('/ventas');

});