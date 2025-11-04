<?php 

namespace Request;

Class BuscarCompraRequest{
    public ?string $proveedor = null;
    public ?string $ruc = null;
    public ?string $fecha = null;

    public function __construct(array $get = []) {
        $this->proveedor = !empty($get['txtBuscarProveedor']) ? trim($get['txtBuscarProveedor']) : null;
        $this->ruc = !empty($get['txtBuscarRuc']) ? trim($get['txtBuscarRuc']) : null;
        $this->fecha = !empty($get['txtBuscarFecha']) ? trim($get['txtBuscarFecha']) : null;
    }

    public function hayFiltros(): bool {
        return $this->proveedor !== null || $this->ruc !== null || $this->fecha !== null;
    }	
}