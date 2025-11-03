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
        <button class="btn btn-primary" id="btnNuevo" data-bs-toggle="modal" data-bs-target="#modalAlmacen"><i class="fa-solid fa-plus me-2"></i>Nuevo</button>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <table class="table table-vcenter table-mobile-sm card-table">
        <thead>
          <tr>
            <th>N°</th>
            <th>Nombre</th>
            <th style="max-width: 180px;">Dirección</th>
            <th>Estado</th>
            <th class="w-1 text-sm-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($datos['almacenes']): ?>
          <?php foreach ($datos['almacenes'] as $key => $almacen): ?>
            <tr>
              <td data-label="N°"><?=$key + 1?></td>
              <td data-label="Nombre"><?=$almacen->nombre_almacen?></td>
              <td data-label="Dirección"><?=$almacen->direccion?></td>
              <td data-label="Estado">
                <span class="badge badge-outline <?=ObtenerEstadoRegistro($almacen->codigo)?>">
                  <?=$almacen->nombre?>
                </span>
              </td>
              <td data-label="Acciones">
                <div class="dropdown">
                  <button class="btn btn-tabla btn-outline-secondary btn-acciones" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-sm-none me-2">Acciones</span>
                      <i class="fa-solid fa-ellipsis-vertical"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">
                  <?php if (EsEditable($almacen->codigo)): ?>
                    <button class="dropdown-item btnEditar" data-id="<?=$almacen->id_almacen?>">
                      <i class="fa-solid fa-edit me-2"></i>
                        Editar almacen
                    </button>
                  <?php endif ?>
                    <button class="dropdown-item btnAccion" data-id="<?=$almacen->id_almacen?>" data-accion="<?=$almacen->TextoAccion().' '.$almacen->nombre_almacen?>" data-estado="<?=NuevoEstado($almacen->codigo)?>">
                      <i class="fa-solid fa-exclamation me-2"></i>
                        <?=$almacen->TextoAccion()?>
                    </button>
                  </div>
                </div>                
              </td>
            </tr>
          <?php endforeach ?>
        <?php endif ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modalAlmacen" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titulo"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAlmacen">
        <input type="hidden" name="idAlmacen">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="txtNombreAlmacen">Nombre *</label>
            <div class="input-icon">
              <span class="input-icon-addon"><i class="fa-solid fa-file-signature"></i></span>
              <input type="text" class="form-control" name="txtNombreAlmacen" id="txtNombreAlmacen" maxlength="150" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="txtDireccionAlmacen" class="form-label">Dirección</label>
            <div class="input-icon">
              <span class="input-icon-addon"><i class="fa-solid fa-street-view"></i></span>
              <input type="text" class="form-control" name="txtDireccionAlmacen" id="txtDireccionAlmacen" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 ,.\-#/()°]{3,150}" maxlength="150" required>
            </div>
          </div>          
        </div>
        <div class="modal-footer ">
          <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-outline-primary">
            <i class="fa-solid fa-floppy-disk me-2"></i>
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include_once __DIR__.'/../templates/modal-accion.php'; ?>