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
  </div>
</div>

<div class="page-body">
  <div class="container-xl">
    <div class="card">
      <div class="row g-0">
        <div class="col-12 col-sm-6 col-lg-8 col-xxl-9 p-3 border-end">
          <form class="row row-gap-3" id="formAgregarProducto">
            <input type="hidden" name="stockProducto" value="">
            <div class="">
              <label class="form-label" for="cmbProductoVenta">Producto *</label>
              <select placeholder="Seleccione categoría" id="cmbProductoVenta" name="cmbProductoVenta" required>
                <option value="" disabled selected>Seleccione categoría</option>
              <?php if ($datos['productos']): ?>
                  <?php foreach ($datos['productos'] as $key => $producto): ?>
                  <option value="<?=$producto->id_producto?>"><?=$producto->ObtenerTextoProducto()?></option>
                <?php endforeach ?>
              <?php endif ?>                
              </select>
            </div>          
            <div class="col-4 col-xl-3">
              <label class="form-label" for="txtPrecioUnidadaProducto">Precio unitario *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  S/.
                </span>
                <input type="number" id="txtPrecioUnidadaProducto" name="txtPrecioUnidadaProducto" value="0" class="form-control" placeholder="Precio unitario" step="0.01" min="0" required>
              </div>            
            </div>
            <div class="col-4 col-xl-3">
              <label class="form-label" for="txtCantidadProducto">Cantidad *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-dolly"></i>
                </span>
                <input type="number" id="txtCantidadProducto" name="txtCantidadProducto" value="" class="form-control" placeholder="Cantidad" step="1" min="1" required>
              </div>
            </div>
            <div class="col-4 col-xl-3">
              <label class="form-label" for="txtDescuentoProducto">Descuento *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  S/.
                </span>
                <input type="number" id="txtDescuentoProducto" name="txtDescuentoProducto" value="0" class="form-control" placeholder="Descuento" step="0.01" min="0" required>
              </div>
            </div>
            <div class="col-12 col-xxl-3 d-flex justify-content-end align-items-end">
              <div class="d-flex gap-3">
                <button type="button" class="btn btn-secondary">
                  <i class="fas fa-xmark me-1"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-outline-primary" id="btnAgregar">
                  <i class="fas fa-plus me-1"></i> Agregar
                </button>
              </div>
            </div>
          </form>
          <hr class="border border-dark border-2 opacity-50">
          <div class="overflow-x-scroll">
            <table class="table table-vcenter card-table" id="tablaVenta">
              <thead>
                <tr>
                  <th>N°</th>
                  <th style="min-width: 140px;">Producto</th>
                  <th class="text-center">Cantidad</th>
                  <th class="text-center">P. unitario</th>
                  <th class="text-center">Descuento</th>
                  <th class="text-center">Total</th>
                  <th class="w-1 text-sm-center">Acciones</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
          <div class="card-body">
            <h4 class="subheader">Emitir nueva venta</h4>
            <form id="formVenta" class="row row-gap-3">
              <div>
                <label class="form-label" for="cmbTipoCliente">Tipo de documento *</label>
                <select name="cmbTipoCliente" id="cmbTipoCliente" class="form-select" required>
                  <option value="" selected disabled>Seleccione una opción</option>
                  <?php if ($datos['tiposDocumentos']): ?>
                    <?php foreach ($datos['tiposDocumentos'] as $key => $tipoDocumento): ?>
                      <option value="<?=$tipoDocumento->getCodigo()?>"><?=$tipoDocumento->getNombre()?></option>
                    <?php endforeach ?>
                  <?php endif ?>
                </select>
              </div>
              <div>
                <label class="form-label" for="txtNumeroDocumento">Documento *</label>
                <div class="input-group">
                  <input type="text" class="form-control" placeholder="Buscar documento" id="txtNumeroDocumento" minlength="0" maxlength="20" name="txtNumeroDocumento" disabled required>
                  <button class="btn" type="button" id="btnBuscar" data-icono='<i class="fas fa-search"></i>'><i class="fas fa-search"></i></button>
                </div>
              </div>
              <div>
                <label class="form-label" for="txtNombresPersona">Nombres *</label>
                <div class="input-icon">
                  <span class="input-icon-addon">
                    <i class="fa-solid fa-address-card"></i>       
                  </span>
                  <input type="text" id="txtNombresPersona" name="txtNombresPersona" value="" class="form-control" placeholder="Ingrese nombres" maxlength="60" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$" required>
                </div>
              </div>
              <div>
                <label class="form-label" for="txtApellidosPersona">Apellidos *</label>
                <div class="input-icon">
                  <span class="input-icon-addon">
                    <i class="fa-solid fa-address-card"></i> 
                  </span>
                  <input type="text" id="txtApellidosPersona" name="txtApellidosPersona" value="" class="form-control" placeholder="Ingrese apellidos" maxlength="150" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$" required>
                </div>
              </div>
              <div class="col-8">
                <label class="form-label" for="cmbTipoPago">Tipo de pago *</label>
                <select name="cmbTipoPago" id="cmbTipoPago" class="form-select" required>
                  <option value="" selected disabled>Seleccione una opción</option>
                  <?php if ($datos['tiposDocumentos']): ?>
                    <?php foreach ($datos['tiposDocumentos'] as $key => $tipoDocumento): ?>
                      <option value="<?=$tipoDocumento->getCodigo()?>"><?=$tipoDocumento->getNombre()?></option>
                    <?php endforeach ?>
                  <?php endif ?>
                </select>
              </div>
              <div class="col-4">
                <label class="form-label" for="txtSubTotal">Sub total *</label>
                <div class="input-icon">
                  <span class="input-icon-addon">
                    S/.   
                  </span>
                  <input type="number" id="txtSubTotal" name="txtSubTotal" value="0" class="form-control" placeholder="Total" step="0.01" min="0" required>
                </div>
              </div>
              <div class="col-4">
                <label class="form-label" for="txtIgv">Igv</label>
                <div class="input-icon">
                  <span class="input-icon-addon">
                    <i class="fa-solid fa-percent"></i>
                  </span>
                  <input type="number" id="txtIgv" name="txtIgv" class="form-control" placeholder="Total" value="0.18" disabled>
                </div>
              </div>
              <div class="col-4">
                <label class="form-label" for="txtTotalVenta">Total *</label>
                <div class="input-icon">
                  <span class="input-icon-addon">
                    S/.   
                  </span>
                  <input type="number" id="txtTotalVenta" name="txtTotalVenta" value="0" class="form-control" placeholder="Total" step="0.01" min="0" required>
                </div>
              </div>
              <div class="col-4">
                <label class="form-label" for="txtDescuento">Descuento</label>
                <div class="input-icon">
                  <span class="input-icon-addon">
                    S/.   
                  </span>
                  <input type="number" id="txtDescuento" name="txtDescuento" value="0" class="form-control" placeholder="Total" step="0.01" min="0">
                </div>
              </div>
              <div class="d-flex justify-content-end">
                <button class="btn btn-primary"><i class="fa-solid fa-floppy-disk me-2"></i>Emitir venta</button>
              </div>      
            </form>
          </div>
        </div>      
      </div>
    </div>
  </div>
</div>