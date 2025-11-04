}<div class="page-header d-print-none px-3">
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
        <button class="btn btn-primary" id="btnNuevo" data-bs-toggle="modal" data-bs-target="#modalProveedor"><i class="fa-solid fa-plus me-2"></i>Nuevo</button>
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
            <th>RUC</th>
            <th>Razón social</th>
            <th style="max-width: 150px;">Dirección</th>
            <th>Celular</th>
            <th>Correo</th>
            <th>Estado</th>
            <th class="w-1 text-sm-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($datos['proveedores']): ?>
            <?php foreach ($datos['proveedores'] as $key => $proveedor): ?>
              <tr>
                <td data-label="N°"><?=$key + 1?></td>
                <td data-label="RUC"><?=$proveedor->num_documento?></td>
                <td data-label="Razón social"><?=$proveedor->razon_social?></td>
                <td data-label="Dirección"><?=$proveedor->direccion?></td>
                <td data-label="Celular"><?=$proveedor->telefono?></td>
                <td data-label="Correo"><?=$proveedor->email?></td>
                <td data-label="Estado">
                  <span class="badge badge-outline <?=ObtenerEstadoRegistro($proveedor->codigo)?>">
                  <?=$proveedor->nombre?>
                </span>
                </td>
                <td data-label="Acciones">
                  <div class="dropdown">
                    <button class="btn btn-tabla btn-outline-secondary btn-acciones" data-bs-toggle="dropdown" aria-expanded="false">
                      <span class="d-sm-none me-2">Acciones</span>
                      <i class="fa-solid fa-ellipsis-vertical"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                    <?php if (EsEditable($proveedor->codigo)): ?>
                      <button class="dropdown-item btnEditar" data-id="<?=$proveedor->id_proveedor?>">
                        <i class="fa-solid fa-edit me-2"></i>
                        Editar proveedor
                      </button>
                    <?php endif ?>
                      <button class="dropdown-item btnAccion" data-id="<?=$proveedor->id_proveedor?>" data-accion="<?=$proveedor->TextoAccion().' '.$proveedor->razon_social?>" data-estado="<?=NuevoEstado($proveedor->codigo)?>">
                        <i class="fa-solid fa-exclamation me-2"></i>
                        <?=$proveedor->TextoAccion()?>
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

<div class="modal modal-blur fade" id="modalProveedor" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titulo"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formProveedor">
        <input type="hidden" name="idProveedor">
        <div class="modal-body">
          <div class="row mb-3 row-gap-3">
            <div class="col-12 col-sm-5 col-md-4">
              <label class="form-label" for="txtDocumentoProveedor">RUC *</label>
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar documento" id="txtDocumentoProveedor" minlength="11" maxlength="11" pattern="^[0-9]+" name="txtDocumentoProveedor" required>
                <button class="btn" type="button" id="btnBuscar" data-icono='<i class="fas fa-search"></i>'><i class="fas fa-search"></i></button>
              </div>
            </div>
            <div class="col-12 col-sm-7 col-md-8">
              <label class="form-label" for="txtRazonSocialProveedor">Razón social*</label>
              <div class="input-icon">
                <span class="input-icon-addon"><i class="fa-solid fa-address-card"></i></span>
                <input type="text" class="form-control" name="txtRazonSocialProveedor" id="txtRazonSocialProveedor" required>
              </div>
            </div>
          </div>
          <div class="col-12 mb-3">
            <label class="form-label" for="txtDireccionProveedor">Dirección*</label>
            <div class="input-icon">
              <span class="input-icon-addon"><i class="fa-solid fa-street-view"></i></span>
              <input type="text" class="form-control" name="txtDireccionProveedor" id="txtDireccionProveedor" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 ,.\-#/()°]{3,200}" maxlength="200" required>
            </div>
          </div>
          <div class="row mb-3 row-gap-3">
            <div class="col-12 col-sm-7 col-md-8">
              <label class="form-label" for="txtCorreoProveedor">Correo</label>
              <div class="input-icon">
                <span class="input-icon-addon"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" class="form-control" name="txtCorreoProveedor" id="txtCorreoProveedor">
              </div>
            </div>
            <div class="col-12 col-sm-5 col-md-4">
              <label class="form-label" for="txtCelular">Celular</label>
              <div class="input-icon">
                <span class="input-icon-addon"><i class="fa-solid fa-phone"></i></span>
                <input type="text" class="form-control" name="txtCelular" id="txtCelular" minlength="9" maxlength="9" pattern="^[0-9]+">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer ">
          <button type="button" class="btn btn-dark" data-bs-dismiss="modal">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary btn-outline-primary">
            <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>
            Enviar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include_once __DIR__.'/../templates/modal-accion.php'; ?>