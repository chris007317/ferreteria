<div class="container container-tight py-4">
<div class="text-center mb-4">
  <a href="." class="navbar-brand navbar-brand-autodark">
    <img src="img/logo.png" width="110" height="32" alt="Tabler" class="navbar-brand-image">
  </a>
</div>
<div class="card card-md">
  <div class="card-body">
    <h2 class="h2 text-center mb-4">Ingresa al sistema punto venta</h2>
    <form autocomplete="off" action="login" method="POST" id="formLogin">
      <div class="mb-3">
        <label class="form-label">Usuario</label>
        <input type="text" name="txtUsuarioLogin" class="form-control" placeholder="Ingrese usuario" autocomplete="off" minlength="5" maxlength="20" required>
      </div>
      <div class="mb-2">
        <label class="form-label">
          Contraseña
          <!-- <span class="form-label-description">
            <a href="./forgot-password.html">Olvidé mi contraseña</a>
          </span> -->
        </label>
        <div class="input-group input-group-flat">
          <input type="password" name="txtContraLogin" class="form-control"  placeholder="Ingrese contraseña"  autocomplete="off" minlength="6" maxlength="30" required>
        </div>
      </div>
      <?php 
      if(isset($alertas) && $alertas){
       ?>
        <div class="alert alert-danger" role="alert">
          <?php echo $alertas['mensaje'] ?>
        </div>
       <?php 
      }
        ?> 
      <div class="form-footer">
        <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
      </div>
    </form>
  </div>
</div>
<!-- <div class="text-center text-white mt-3">
  ¿No tienes una cuenta? <a href="./sign-up.html" class="fw-bold" tabindex="-1">Crea una aquí</a>
</div> -->
</div>