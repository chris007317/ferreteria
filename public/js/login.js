document.addEventListener("DOMContentLoaded", function () {
	const formulario = document.getElementById("formLogin");
	validarInputs(formulario);
	formulario.addEventListener("submit", function(event) {
		if(!validarFormulario(this)) event.preventDefault();
    });
});