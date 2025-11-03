<?php 

namespace Response;

Class PaginadorResponse {
    public $paginaActual;
    public $paginaSiguiente;
    public $paginaPrevia;
    public $totalPaginas;
    public $totalRegistros;
    public $porPagina;
    public $tienePaginaSiguiente;
    public $tienePaginaPrevia;

    public function __construct($paginaActual, $porPagina, $totalRegistros) {
        $this->paginaActual = max(1, (int)$paginaActual);
        $this->porPagina = (int)$porPagina;
        $this->totalRegistros = (int)$totalRegistros;
        $this->totalPaginas = (int)ceil($totalRegistros / $porPagina);

        $this->paginaSiguiente = $this->paginaActual < $this->totalPaginas ? $this->paginaActual + 1 : null;
        $this->paginaPrevia    = $this->paginaActual > 1 ? $this->paginaActual - 1 : null;

        $this->tienePaginaSiguiente = $this->paginaSiguiente !== null;
        $this->tienePaginaPrevia    = $this->paginaPrevia !== null;
    }

    public function obtenerPaginas() {
        $paginas = [];

        if ($this->totalPaginas <= 5) {
            // Mostrar todas
            for ($i = 1; $i <= $this->totalPaginas; $i++) {
                $paginas[] = $i;
            }
        } else {
            // Página actual cerca del inicio
            if ($this->paginaActual <= 3) {
                $paginas = [1, 2, 3, '...', $this->totalPaginas - 1, $this->totalPaginas];
            }
            // Página actual cerca del final
            elseif ($this->paginaActual >= $this->totalPaginas - 2) {
                $paginas = [1, 2, '...', $this->totalPaginas - 2, $this->totalPaginas - 1, $this->totalPaginas];
            }
            // Página en medio
            else {
                $paginas = [1, '...', $this->paginaActual - 1, $this->paginaActual, $this->paginaActual + 1, '...', $this->totalPaginas];
            }
        }
        return $paginas;
    }
    public function urlConParametros(int $destino, array $getParams): string{
        $params = $getParams;
        // Cambiar el número de página
        $params['pagina'] = $destino;
        // Reconstruir la query
        $query = http_build_query($params);
        return '?' . $query;
    }

    public function ObtenerInicio(){
        return (($this->paginaActual -1) * $this->porPagina) + 1;
    }

    public function ObtenerFin(){
        return $this->paginaActual == $this->totalPaginas ? $this->totalRegistros : $this->paginaActual * $this->porPagina;
    }
}