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
        <button class="btn btn-primary" id="btnNuevo" data-bs-toggle="modal" data-bs-target="#modalCompra"><i class="fa-solid fa-plus me-2"></i>Nuevo</button>
      </div>
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card p-3 mb-3">
      <form id="formBuscar" method="GET">
        <div class="row row-gap-3 justify-content-xxl-end">
          <div class="col-12 col-sm-4 col-md-6 col-xl-2">
            <label class="form-label" for="txtBuscarProveedor">Proveedor </label>
            <input type="text" id="txtBuscarProveedor" name="txtBuscarProveedor" class="form-control" placeholder="Ingrese razón social" value="<?=$filtros->proveedor?>">
          </div>
          <div class="col-12 col-sm-4 col-md-6 col-xl-2">
            <label class="form-label" for="txtBuscarRuc">RUC</label>
            <input type="text" id="txtBuscarRuc" name="txtBuscarRuc" class="form-control" placeholder="Ingrese RUC" minlength="11" maxlength="11" pattern="^[0-9]+" value="<?=$filtros->ruc?>">
          </div>
          <div class="col-12 col-sm-4 col-md-6 col-xl-2">
            <label class="form-label" for="txtBuscarFecha">Fecha</label>
            <input type="date" id="txtBuscarFecha" name="txtBuscarFecha" class="form-control" placeholder="Ingrese categoría" value="<?=$filtros->fecha?>">
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
            <th>Fecha</th>
            <th>N° de compra</th>
            <th>Proveedor</th>
            <th>Ruc</th>
            <th>Sub total</th>
            <th>Igv</th>
            <th>Total</th>
            <th>Estado</th>
            <th class="w-1 text-sm-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($datos['compras']): ?>
          <?php foreach ($datos['compras'] as $key => $compra): ?>
            <tr>
              <td data-label="Nro"><?=$key + 1?></td>
              <td data-label="Fecha de compra"><?=$compra->fecha?></td>
              <td data-label="Nro de compra"><?=$compra->numero_compra?></td>
              <td data-label="Proveedor"><?=$compra->razon_social?></td>
              <td data-label="RUC"><?=$compra->num_documento?></td>
              <td data-label="Sub total"><?=$compra->subtotal?></td>
              <td data-label="Igv"><?=$compra->igv?></td>
              <td data-label="Total"><?=$compra->total?></td>
              <td data-label="Estado">
                <span class="badge badge-outline <?=$compra->ObtenerBadgeEstado()?>">
                  <?=$compra->nombre_estado?>
                </span>
              </td>              
              <td data-label="Acciones">
                <div class="dropdown">
                  <button class="btn btn-tabla btn-outline-secondary btn-acciones" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="d-sm-none me-2">Acciones</span>
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item btnAgregarProductos" href="/compras/productos?numero_compra=<?=$compra->numero_compra?>">
                      <i class="fa-solid fa-plus me-2"></i>
                      Agregar productos
                    </a>
                    <button class="dropdown-item btnEditar" data-id="<?=$compra->id_compra?>">
                      <i class="fa-solid fa-edit me-2"></i>
                      Editar compra
                    </button>
                    <button class="dropdown-item btnEliminar" data-id="<?=$compra->id_compra?>" data-accion="Eliminar compra con número : <?=$compra->numero_compra?>">
                      <i class="fa-solid fa-xmark me-2"></i>
                      Eliminar compra
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
    <?php include_once __DIR__.'/../templates/paginador.php'; ?>
  </div>
</div>

<div class="modal modal-blur fade" id="modalCompra" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titulo"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formCompra">
        <input type="hidden" name="idCompra">     
        <div class="modal-body">
          <div class="row mb-3 row-gap-3">
            <div class="col-12 col-sm-6">
              <label class="form-label" for="cmbProveedor">Proveedor *</label>
              <select name="cmbProveedor" id="cmbProveedor" class="form-select" required>
                <option value="" selected disabled>Seleccione una opción</option>
              <?php if ($datos['proveedores']): ?>
                <?php foreach ($datos['proveedores'] as $key => $proveedor): ?>
                  <option value="<?=$proveedor->getIdProveedor()?>"><?=$proveedor->getRazonSocial()?></option>
                <?php endforeach ?>
                
              <?php endif ?>
              </select>
            </div>            
            <div class="col-12 col-sm-6">
              <label class="form-label" for="txtNumeroCompra">Número compra *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-hashtag"></i>
                </span>
                <input type="text" id="txtNumeroCompra" name="txtNumeroCompra" value="" class="form-control" placeholder="Ingrese número de compra" maxlength="20" pattern="^[0-9]+"required>
              </div>
            </div>
          </div>
          <div class="row mb-3 row-gap-3">
            <div class="col-12 col-sm-6 col-md-4">
              <label class="form-label" for="txtFechaCompra">Fecha de compra *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-calendar-days"></i>
                </span>
                <input type="datetime-local" id="txtFechaCompra" name="txtFechaCompra" class="form-control" value="" required>
              </div>
            </div>
            <div class="col-4 col-sm-3">
              <label class="form-label" for="txtTotalCompra">Total *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  S/.   
                </span>
                <input type="number" id="txtTotalCompra" name="txtTotalCompra" value="0" class="form-control" placeholder="Total" step="0.01" min="0" required>
              </div>
            </div>
            <div class="col-4 col-sm-3 col-md-2">
              <label class="form-label" for="txtIgvCompra">Igv *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-coins"></i>
                </span>
                <input type="number" id="txtIgvCompra" name="txtIgvCompra" class="form-control" value="0.18" step="0.01" min="0" required>
              </div>
            </div>
            <div class="col-4 col-sm-3">
              <label class="form-label" for="txtSubTotalCompra">Sub total *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  S/.  
                </span>
                <input type="number" id="txtSubTotalCompra" name="txtSubTotalCompra" class="form-control" placeholder="Sub total" step="0.01" disabled>
              </div>
            </div>
          </div>          
          <div>
            <label class="form-label" for="txtObservacionesCompra">Observaciones</label>
            <textarea class="form-control" id="txtObservacionesCompra" name="txtObservacionesCompra" rows="4"></textarea>
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

<div class="modal modal-blur fade" id="modalEliminar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-danger"></div>
      <div class="modal-body text-center py-4">
        <!-- Download SVG icon from http://tabler-icons.io/i/alert-triangle -->
        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z" /><path d="M12 9v4" /><path d="M12 17h.01" /></svg>
        <h3 id="textoAccion">Eliminar registro</h3>
        <div class="text-secondary" id="textoCambioEstado"></div>
      </div>
      <div class="modal-footer">
        <div class="w-100">
          <input type="hidden" name="idEliminar">
          <div class="row">
            <div class="col"><button type="button" class="btn w-100" data-bs-dismiss="modal">
                Cancelar
              </button></div>
            <div class="col"><button type="button" class="btn btn-danger w-100" id="bntEliminar">
                Aceptar
              </button></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>