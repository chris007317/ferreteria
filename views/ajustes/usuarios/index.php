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
        <button class="btn btn-primary" id="btnNuevo" data-bs-toggle="modal" data-bs-target="#modalUsuario"><i class="fa-solid fa-plus me-2"></i>Nuevo</button>
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
            <th>Tipo doc</th>
            <th>Documento</th>
            <th>Datos</th>
            <th>Usuario</th>
            <th>Estado</th>
            <th>Tipo</th>
            <th class="w-1 text-sm-center">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($datos['usuarios']): ?>
            <?php foreach ($datos['usuarios'] as $key => $usuario): ?>
              <tr>
                <td data-label="Tipo de documento"><?=$usuario->nombre_tipo_doc?></td>
                <td data-label="Número de documento"><?=$usuario->num_documento?></td>
                <td data-label="Nombres y apellidos"><?=$usuario->ObtenerDatos()?></td>
                <td data-label="Usuario"><?=$usuario->username?></td>
                <td data-label="Estado">
                  <span class="badge badge-outline <?=ObtenerEstadoRegistro($usuario->codigo_estado)?>">
                    <?=$usuario->nombre_estado?>
                  </span>
                </td>
                <td data-label="Rol"><?=$usuario->nombre_rol?></td>
                <td data-label="Acciones">
                  <div class="d-flex justify-content-center justify-content-sm-end">
                    <div class="dropdown">
                      <button class="btn btn-tabla btn-outline-secondary btn-acciones" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-sm-none me-2">Acciones</span>
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end">
                        <?php if (!$usuario->EsEditable()): ?>
                          <button class="dropdown-item btnEditar" data-id="<?=$usuario->id_usuario?>">
                            <i class="fa-solid fa-edit me-2"></i>
                            Editar usuario
                          </button>
                          <button class="dropdown-item btnAccion" data-id="<?=$usuario->id_usuario?>" data-accion="<?=$usuario->TextoAccion().' '.$usuario->username?>" data-estado="<?=NuevoEstado($usuario->codigo_estado)?>">
                            <i class="fa-solid fa-exclamation me-2"></i>
                            <?=$usuario->TextoAccion()?>
                          </button>    
                        <?php endif ?>
                        <button class="dropdown-item btnNuevaContra" data-id="<?=$usuario->id_usuario?>" data-nombre="<?=$usuario->username?>">
                          <i class="fa-solid fa-key me-2"></i>
                          Generar contraseña
                        </button>
                      </div>
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

<div class="modal modal-blur fade" id="modalUsuario" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="titulo">Nuevo usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formUsuario">
        <input type="hidden" name="idUsuario">     
        <div class="modal-body">
          <div class="row mb-3 row-gap-3">
            <div class="col-12 col-sm-6">
              <label class="form-label" for="cmbTipoDocumentoUsuario">Tipo de documento *</label>
              <select name="cmbTipoDocumentoUsuario" id="cmbTipoDocumentoUsuario" class="form-select" required>
                <option value="" selected disabled>Seleccione una opción</option>
                <?php if ($datos['tiposDocumentos']): ?>
                  <?php foreach ($datos['tiposDocumentos'] as $key => $tipoDocumento): ?>
                    <option value="<?=$tipoDocumento->getCodigo()?>"><?=$tipoDocumento->getNombre()?></option>
                  <?php endforeach ?>
                <?php endif ?>
              </select>
            </div>            
            <div class="col-12 col-sm-6">
              <label class="form-label" for="txtDocumentoUsuario">Documento *</label>
              <div class="input-group">
                <input type="text" class="form-control" placeholder="Buscar documento" id="txtDocumentoUsuario" minlength="0" maxlength="20" name="txtDocumentoUsuario" disabled required>
                <button class="btn" type="button" id="btnBuscar" data-icono='<i class="fas fa-search"></i>'><i class="fas fa-search"></i></button>
              </div>
            </div>          
          </div>
          <div class="row mb-3 row-gap-3">
            <div class="col-12 col-sm-6">
              <label class="form-label" for="txtNombresPersona">Nombres *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-address-card"></i>       
                </span>
                <input type="text" id="txtNombresPersona" name="txtNombresPersona" value="" class="form-control" placeholder="Ingrese nombres" maxlength="60" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$" required>
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <label class="form-label" for="txtApellidos">Apellidos *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-address-card"></i> 
                </span>
                <input type="text" id="txtApellidos" name="txtApellidos" value="" class="form-control" placeholder="Ingrese apellidos" maxlength="150" pattern="^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$" required>
              </div>
            </div>
          </div>
          <div class="row mb-3 row-gap-3">
            <div class="col-12 col-sm-6">
              <label class="form-label" for="txtCorreoPersona">Correo</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-envelope"></i>   
                </span>
                <input type="email" id="txtCorreoPersona" name="txtCorreoPersona" value="" class="form-control" placeholder="Ingrese correo" maxlength="150">
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <label class="form-label" for="txtCelularPersona">Celular</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-brands fa-whatsapp"></i>     
                </span>
                <input type="text" id="txtCelularPersona" name="txtCelularPersona" value="" class="form-control" placeholder="Ingrese celular" minlength="9" maxlength="9" pattern="^[0-9]+">
              </div>
            </div>
          </div>
          <div class="row mb-3 row-gap-3">
            <div class="col-12 col-sm-6">
              <label class="form-label" for="txtNombreUsuario">Nombre de usuario *</label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-user"></i>
                </span>
                <input type="text" id="txtNombreUsuario" name="txtNombreUsuario" value="" class="form-control" placeholder="Ingrese usuario" maxlength="20" required>
              </div>
            </div>
            <div class="col-12 col-sm-6" id="generarContra">
              <label class="form-check">
                <input class="form-check-input" type="checkbox" id="chkGenerarContra" name="chkGenerarContra">
                <span class="form-check-label">Generar contraseña</span>
              </label>
              <div class="input-icon">
                <span class="input-icon-addon">
                  <i class="fa-solid fa-unlock-keyhole"></i>
                </span>
                <input type="text" id="txtContraUsuario" name="txtContraUsuario" value="" class="form-control" placeholder="Ingrese contraseña" maxlength="50">
              </div>
            </div>
            <div class="col-12 col-sm-6">
              <label class="form-label" for="cmbTipoUsuario">Tipo de usuario *</label>
              <select name="cmbTipoUsuario" id="cmbTipoUsuario" class="form-select" required>
                <option value="" selected disabled>Seleccione una opción</option>
                <?php if ($datos['tiposUsuarios']): ?>
                  <?php foreach ($datos['tiposUsuarios'] as $key => $tipoUsuario): ?>
                    <option value="<?=$tipoUsuario->getCodigo()?>"><?=$tipoUsuario->getNombre()?></option>
                  <?php endforeach ?>
                <?php endif ?>                
              </select>
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


<div class="modal modal-blur fade" id="modalEliminar" tabindex="-1" role="dialog" aria-hidden="true">
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
            <div class="col"><button type="button" class="btn btn-info w-100" id="btnEliminarModal">
                Aceptar
              </button></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="modal modal-blur fade" id="modalNuevaContra" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <input type="hidden" name="idActualizar">
        <div class="modal-title">Usuario: <span id="usuarioDatos"></span></div>
        <div class="col-12" id="nuevaContra">
          <label class="form-check">
            <input class="form-check-input" type="checkbox" id="chkNuevaContra" name="chkNuevaContra">
            <span class="form-check-label">Generar contraseña</span>
          </label>
          <div class="input-icon">
            <span class="input-icon-addon">
              <i class="fa-solid fa-unlock-keyhole"></i>
            </span>
            <input type="text" id="txtNuevaContraUsuario" name="txtNuevaContraUsuario" value="" class="form-control" placeholder="Ingrese contraseña" maxlength="50">
          </div>
        </div>          
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary btn-outline-primary" id="btnActualizarContra">Si, cambiar</button>
      </div>
    </div>
  </div>
</div>