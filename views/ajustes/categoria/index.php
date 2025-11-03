<?php 
  $filtros = $datos['filtros'];
 ?>
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
        <button class="btn btn-primary" id="btnNuevo" data-bs-toggle="modal" data-bs-target="#modalCategoria"><i class="fa-solid fa-plus me-2"></i>Nuevo</button>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card p-3 mb-3">
      <form id="formBuscar" method="GET">
        <div class="row row-gap-3 justify-content-xxl-end">
          <div class="col-12 col-sm-4 col-md-6 col-xl-3">
            <label class="form-label" for="txtBuscarNombre">Categoria </label>
            <input type="text" id="txtBuscarNombre" name="txtBuscarNombre" class="form-control" placeholder="Ingrese categoría" value="<?=$filtros->nombre?>">
          </div>
          <div class="col-12 col-sm-4 col-md-12 col-xl-4 col-xxl-2 d-flex justify-content-end align-items-end gap-2">
            <button type="button" class="btn btn-secondary" id="btnLimpiarFiltros">
              <i class="fa-solid fa-xmark me-1"></i>
              <span>Limpiar</span>
            </button>
            <button type="submit" class="btn btn-outline-primary">
              <i class="fa-solid fa-magnifying-glass me-1"></i>
              <span>Buscar</span>
            </button>
          </div>
        </div>
      </form>
    </div>    
    <div class="card">
      <table class="table table-vcenter table-mobile-sm card-table">
        <thead>
          <tr>
            <th>N°</th>
            <th>Nombre</th>
            <th style="max-width: 250px;">Descripción</th>
            <th>Estado</th>
            <th class="w-1 text-sm-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($datos['categorias']): ?>
          <?php foreach ($datos['categorias'] as $key => $categoria): ?>
            <tr>
              <td data-label="N°"><?=$key + 1?></td>
              <td data-label="Nombre"><?=$categoria->nombre?></td>
              <td data-label="Descripción" style="max-width: 250px;"><?=$categoria->descripcion?></td>
              <td data-label="Estado">
                <span class="badge badge-outline <?=ObtenerEstadoRegistro($categoria->codigo_estado)?>">
                  <?=$categoria->nombre_estado?>
                </span>
              </td>
              <td data-label="Acciones">
                <div class="dropdown">
                  <button class="btn btn-tabla btn-outline-secondary btn-acciones" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-sm-none me-2">Acciones</span>
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">
                  <?php if (EsEditable($categoria->codigo_estado)): ?>
                    <button class="dropdown-item btnEditar" data-id="<?=$categoria->id_categoria?>">
                      <i class="fa-solid fa-edit me-2"></i>
                      Editar categoria
                    </button>
                  <?php endif ?>
                    <button class="dropdown-item btnAccion" data-id="<?=$categoria->id_categoria?>" data-accion="<?=$categoria->TextoAccion().' '.$categoria->nombre?>" data-estado="<?=NuevoEstado($categoria->codigo_estado)?>">
                      <i class="fa-solid fa-exclamation me-2"></i>
                      <?=$categoria->TextoAccion()?>
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
    <?php include_once __DIR__.'/../../templates/paginador.php'; ?>
  </div>
</div>

<div class="modal modal-blur fade" id="modalCategoria" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titulo"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formCategoria">
        <input type="hidden" name="idCategoria">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label" for="txtNombreCategoria">Nombre:</label>
            <div class="input-icon">
              <span class="input-icon-addon"><i class="fa-solid fa-file-signature"></i></span>
              <input type="text" class="form-control" name="txtNombreCategoria" id="txtNombreCategoria" maxlength="100" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="txtDescripcionCategoria" class="form-label">Descripción</label>
            <textarea class="form-control" id="txtDescripcionCategoria" name="txtDescripcionCategoria" rows="4" maxlength="255"></textarea>
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

<div class="modal modal-blur fade" id="modalAccion" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-info"></div>
      <div class="modal-body text-center py-4">
        <!-- Download SVG icon from http://tabler-icons.io/i/alert-triangle -->
        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-info icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z" /><path d="M12 9v4" /><path d="M12 17h.01" /></svg>
        <h3 id="textoAccion"></h3>
        <div class="text-secondary" id="textoEliminar"></div>
      </div>
      <div class="modal-footer">
        <div class="w-100">
          <input type="hidden" name="idAccion">
          <input type="hidden" name="nuevoEstado">
          <div class="row">
            <div class="col"><button type="button" class="btn w-100" data-bs-dismiss="modal">
                Cancelar
              </button></div>
            <div class="col"><button type="button" class="btn btn-info w-100" id="btnEnviarAccion">
                Aceptar
              </button></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>