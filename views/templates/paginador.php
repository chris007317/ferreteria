<?php 
  $getParams = $_GET;
?>

<div class="card-footer d-flex align-items-center mt-3">
  <?php if ($datos['paginador']->totalRegistros > 0): ?>
  <p class="m-0 text-secondary">
    Mostrando <span><?= $datos['paginador']->ObtenerInicio() ?></span> – <span><?= $datos['paginador']->ObtenerFin() ?></span> de <span><?= $datos['paginador']->totalRegistros ?></span> resultados
  </p>
  <ul class="pagination m-0 ms-auto">
    <!-- Botón anterior -->
    <li class="page-item <?= !$datos['paginador']->tienePaginaPrevia ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= !$datos['paginador']->tienePaginaPrevia ? '#' : $datos['paginador']->urlConParametros($datos['paginador']->paginaPrevia, $getParams) ?>">
        <i class="fa-solid fa-angle-left"></i>
      </a>
    </li>

    <!-- Números -->
    <?php foreach($datos['paginador']->obtenerPaginas() as $pagina): ?>
      <?php if ($pagina === '...'): ?>
        <li class="page-item"><p class="page-link">...</p></li>
      <?php else: ?>
        <li class="page-item <?= $pagina == $datos['paginador']->paginaActual ? 'active' : '' ?>">
          <a class="page-link" href="<?= $datos['paginador']->urlConParametros($pagina, $getParams) ?>"><?= $pagina ?></a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>

    <!-- Botón siguiente -->
    <li class="page-item <?= !$datos['paginador']->tienePaginaSiguiente ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= !$datos['paginador']->tienePaginaSiguiente ? '#' : $datos['paginador']->urlConParametros($datos['paginador']->paginaSiguiente, $getParams) ?>">
        <i class="fa-solid fa-angle-right"></i>
      </a>
    </li>
  </ul>    
  <?php else: ?>
  <p class="m-0 text-secondary">No se encontraron resultados</p>
  <?php endif ?>
</div>     