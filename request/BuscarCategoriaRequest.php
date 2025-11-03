<?php 

namespace Request;

Class BuscarCategoriaRequest{
    public ?string $nombre = null;

    public function __construct(array $get = []) {
        $this->nombre = !empty($get['txtBuscarNombre']) ? trim($get['txtBuscarNombre']) : null;
    }

    public function hayFiltros(): bool {
        return $this->cliente !== null;
    }	
}