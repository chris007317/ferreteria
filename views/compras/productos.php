<?php 
  $compra = $datos['compra'];
  $proveedor = $datos['proveedor'];
 ?>
<div class="page-header d-print-none px-3">
  <div class="container-xl">
    <div class="mb-4">
      <ol class="breadcrumb" aria-label="breadcrumbs">
        <li class="breadcrumb-item">
          <a href="inicio">Inicio</a>
        </li>
        <li class="breadcrumb-item">
          <a href="compras">Compras</a>
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
    </div>
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="row">
      <div class="col-12 col-sm-6 col-lg-4 col-xxl-2">
        <div class="dropdown-menu dropdown-menu-demo">
          <h6 class="dropdown-header">Datos compra</h6>
          <div class="dropdown-item justify-content-between">
            <p class="m-0 fw-bold">Número de compra</p>
            <p class="m-0 fs-5"><?=$compra->getNumeroCompra()?></p>            
          </div>
          <div class="dropdown-item justify-content-between flex-wrap">
            <p class="m-0 fw-bold">Proveedor</p>
            <p class="m-0 fs-6"><?=$proveedor->getRazonSocial()?></p>
          </div>
          <div class="dropdown-item justify-content-between">
            <p class="m-0 fw-bold">Sub total</p>
            <p class="m-0 fs-5" id="subTotal">S/. <?=$compra->getSubTotal()?></p>            
          </div>
          <div class="dropdown-item justify-content-between">
            <p class="m-0 fw-bold">Igv</p>
            <p class="m-0 fs-5"><?=$compra->getIgv()?></p>
          </div>
          <div class="dropdown-item justify-content-between">
            <p class="m-0 fw-bold">Total</p>
            <p class="m-0 fs-5" id="total">S/. <?=$compra->getTotal()?></p>            
          </div>
          <div class="dropdown-item justify-content-between">
            <p class="m-0 fw-bold">Estado</p>
            <p class="m-0 fs-5" id="total"><?=$datos['estado']->getNombre()?></p>            
          </div>
          <div class="dropdown-divider"></div>
          <h6 class="dropdown-header">Buscar producto</h6>
          <form id="formBuscarProducto" class="px-2">
            <div class="mb-3">
              <label class="form-label" for="cmbAlmacen">Almacén</label>
              <select class="form-control" name="cmbAlmacen" id="cmbAlmacen" required>
                <option value="" selected disabled>Seleccione una opción</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Categoría</label>
              <select class="form-control" required>
                <option value="" selected disabled>Seleccione una opción</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Producto</label>
              <select class="form-control" required>
                <option value="" selected disabled>Seleccione una opción</option>
              </select>
            </div>
            <div class="d-flex justify-content-end">
              <button class="btn btn-primary" type="submit"><i class="fa-solid fa-check me-2"></i>Seleccionar</button>
            </div>
          </form>
          <div class="dropdown-divider"></div>
          <div class="d-flex justify-content-end px-2 pb-3">
            <button class="btn btn-outline-primary" id="btnNuevo" data-bs-toggle="modal" data-bs-target="#modalProducto"><i class="fa-solid fa-plus me-2"></i>Producto</button>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-lg-8 col-xxl-10 card p-3">
        <table class="table table-vcenter table-mobile-sm card-table">
          <thead>
            <tr>
              <th>N°</th>
              <th>Categoría</th>
              <th>Almacén</th>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>P. unitario</th>
              <th>Total</th>
              <th>Estado</th>
              <th class="w-1 text-sm-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($datos['productos']): ?>
              <?php foreach ($datos['productos'] as $key => $producto): ?>
                <tr>
                  <td data-label="Nro"><?=$key + 1?></td>
                  <td data-label="Categoría"><?=$producto->nombre_categoria?></td>
                  <td data-label="Almacén"><?=$producto->nombre_almacen?></td>
                  <td data-label="Producto"><?=$producto->nombre?></td>
                  <td data-label="Cantidad"><?=$producto->cantidad?></td>
                  <td data-label="P. unitario"><?=$producto->precio_unitario?></td>
                  <td data-label="Total">S/. <?=$producto->TotalProducto()?></td>
                  <td data-label="Estado">
                    <span class="badge badge-outline <?=$producto->ObtenerBadgeEstado()?>">
                      <?=$producto->estado_producto?>
                    </span>
                  </td>
                  <td data-label="Acciones"></td>
                </tr>
              <?php endforeach ?>
            <?php endif ?>
          </tbody>
        </table>
        <div class="mt-3 d-flex justify-content-end">
          <button type="button" class="btn btn-outline-primary" id="btnAprobarCompra">
            Aprobar compra
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modalProducto" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear producto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formCompra">
        <input type="hidden" name="idCompra" value="<?=$compra->getIdCompra()?>">
        <input type="hidden" name="idProducto" value="">
        <div class="modal-body">
          <div class="row mb-3 row-gap-3">
            <div class="col-12 col-sm-7">
              <label class="form-label" for="cmbAlmacenProducto">Proveedor *</label>
              <select name="cmbAlmacenProducto" id="cmbAlmacenProducto" required>
                <option value="" selected disabled>Seleccione una opción</option>
              <?php if ($datos['almacenes']): ?>
                <?php foreach ($datos['almacenes'] as $key => $proveedor): ?>
                  <option value="<?=$proveedor->getIdAlmacen()?>"><?=$proveedor->getNombreAlmacen()?></option>
                <?php endforeach ?>
              <?php endif ?>
              </select>
            </div>
            <div class="col-12 col-sm-5">
              <label class="form-label" for="cmbCategoriaProducto">Categoría *</label>
              <select placeholder="Seleccione categoría" id="cmbCategoriaProducto" name="cmbCategoriaProducto" required>
                <option value="" disabled selected>Seleccione categoría</option>
              <?php if ($datos['categorias']): ?>
                <?php foreach ($datos['categorias'] as $key => $categoria): ?>
                  <option value="<?=$categoria->getIdCategoria()?>"><?=$categoria->getNombre()?></option>
                <?php endforeach ?>
              <?php endif ?>                
              </select>
            </div>
            <div class="col-12 col-sm-6">
              <label class="form-label" for="txtNombreProducto">Nombre *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-file-signature"></i>
                </span>
                <input type="text" id="txtNombreProducto" name="txtNombreProducto" value="" class="form-control" placeholder="Ingrese nombre del producto" maxlength="150" required>
              </div>
            </div>
            <div class="col-4 col-sm-3">
              <label class="form-label" for="txtPrecioCompraProducto">Precio compra *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  S/.   
                </span>
                <input type="number" id="txtPrecioCompraProducto" name="txtPrecioCompraProducto" value="0" class="form-control" placeholder="Precio compra" step="0.01" min="0" required>
              </div>
            </div>
            <div class="col-4 col-sm-3">
              <label class="form-label" for="txtPrecioVentaProducto">Precio venta *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  S/.   
                </span>
                <input type="number" id="txtPrecioVentaProducto" name="txtPrecioVentaProducto" value="0" class="form-control" placeholder="Precio venta" step="0.01" min="0" required>
              </div>
            </div>
            <div class="col-4 col-sm-3">
              <label class="form-label" for="txtCantidadProducto">Cantidad *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-boxes-stacked"></i>
                </span>
                <input type="number" id="txtCantidadProducto" name="txtCantidadProducto" value="0" class="form-control" placeholder="Precio venta" step="1" min="0" required>
              </div>
            </div>
          </div>          
          <div>
            <label class="form-label" for="txtDescripcionProducto">Observaciones</label>
            <textarea class="form-control" id="txtDescripcionProducto" name="txtDescripcionProducto" rows="4"></textarea>
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

<div class="modal modal-blur fade" id="modalAprobar" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-status bg-info"></div>
      <div class="modal-body text-center py-4">
        <!-- Download SVG icon from http://tabler-icons.io/i/alert-triangle -->
        <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-info icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.24 3.957l-8.422 14.06a1.989 1.989 0 0 0 1.7 2.983h16.845a1.989 1.989 0 0 0 1.7 -2.983l-8.423 -14.06a1.989 1.989 0 0 0 -3.4 0z" /><path d="M12 9v4" /><path d="M12 17h.01" /></svg>
        <h3 id="textoAccion">Aprobar compra</h3>
        <div class="text-secondary">¿Está seguro de aprobar la compra?</div>
      </div>
      <div class="modal-footer">
        <div class="w-100">
          <input type="hidden" name="idAccion">
          <div class="row">
            <div class="col"><button type="button" class="btn w-100" data-bs-dismiss="modal">
                Cancelar
              </button></div>
            <div class="col"><button type="button" class="btn btn-info w-100" id="btnAprobarModal">
                Aceptar
              </button></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>