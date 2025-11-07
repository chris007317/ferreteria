<div class="page-header d-print-none px-3">
  <div class="container-xl">
    <div class="mb-4">
      <ol class="breadcrumb" aria-label="breadcrumbs">
        <li class="breadcrumb-item">
          <a href="inicio">Inicio</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
          <a href="#"><?php echo $titulo; ?></a>
        </li>
      </ol>
    </div>
    <div class="d-flex flex-column gap-2 flex-sm-row align-items-sm-center justify-content-sm-between">
      <h2 class="page-title">
        <?php echo $titulo; ?>
      </h2>
      <div>
        <button class="btn btn-primary" id="btnNuevo" data-bs-toggle="modal" data-bs-target="#modalCompra"><i class="fa-solid fa-plus me-2"></i>Nuevo</button>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card p-3 mb-3">
      <form id="formBuscar" method="GET">
      </form>
    </div>
    <div class="card">
      <table class="table table-vcenter table-mobile-sm card-table">
        <thead>
          <tr>
            <th>N°</th>
            <th>Fecha</th>
            <th>N° venta</th>
            <th>Cliente</th>
            <th>Documento</th>
            <th>T. pago</th>
            <th class="text-sm-center">Descuento</th>
            <th class="text-sm-center">Total</th>
            <th class="text-sm-center">Igv</th>
            <th class="text-sm-center">Sub total</th>
            <th class="text-sm-center">T. con desc.</th>
            <th class="w-1 text-sm-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($datos['ventas']): ?>
          <?php foreach ($datos['ventas'] as $key => $venta): ?>
            <tr>
              <td data-label="Nro"><?=$key + 1?></td>
              <td data-label="Fecha venta"><?=$venta->fechaVenta?></td>
              <td data-label="Número de venta"><?=$venta->numero_venta?></td>
              <td data-label="Datos cliente"><?=$venta->ObtenerCliente()?></td>
              <td data-label="Datos cliente"><?=$venta->num_documento?></td>
              <td data-label="Datos cliente"><?=$venta->nombre_pago?></td>
              <td data-label="Descuento aplicado" class="text-sm-center">S/. <?=$venta->descuento?></td>
              <td data-label="Total" class="text-sm-center">S/. <?=$venta->total?></td>
              <td data-label="Igv" class="text-sm-center"><?=$venta->igv?></td>
              <td data-label="Sub total" class="text-sm-center">S/. <?=$venta->subtotal?></td>
              <td data-label="Total con descuento" class="text-sm-center">S/. <?=$venta->total_descuento ?? "0.00"?></td>
              <td data-label="Acciones"></td>
            </tr>
          <?php endforeach ?>
          
        <?php endif ?>
        </tbody>
      </table>
    </div>
    <?php include_once __DIR__.'/../templates/paginador.php'; ?>
  </div>
</div>
