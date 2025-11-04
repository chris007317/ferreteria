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
      <div class="col-sm-6 col-lg-4 col-xxl-2">
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
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modalProducto" role="dialog" aria-hidden="true">
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
            <div class="col-12 col-sm-7">
              <label class="form-label" for="cmbProveedor">Proveedor *</label>
              <select name="cmbProveedor" id="cmbProveedor" required>
                <option value="" selected disabled>Seleccione una opción</option>
              <?php if ($datos['proveedores']): ?>
                <?php foreach ($datos['proveedores'] as $key => $proveedor): ?>
                  <option value="<?=$proveedor->getIdProveedor()?>"><?=$proveedor->getRazonSocial()?></option>
                <?php endforeach ?>
              <?php endif ?>
              </select>
            </div>
            <div class="col-12 col-sm-5">
              <label class="form-label" for="cmbCategoria">Categoría *</label>
              <select placeholder="Seleccione categoría" id="cmbCategoria" name="cmbCategoria" required>
                <option value="" disabled selected>Seleccione categoría</option>
              <?php if ($datos['categorias']): ?>
                <?php foreach ($datos['categorias'] as $key => $categoria): ?>
                  <option value="<?=$categoria->getIdCategoria()?>"><?=$categoria->getNombre()?></option>
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