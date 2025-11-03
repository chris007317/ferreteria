<header class="navbar navbar-expand-md d-none d-lg-flex sticky-top d-print-none" >
  <div class="container-xl justify-content-end">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav flex-row order-md-last">
      <div class="d-none d-md-flex">
        <div class="nav-item dropdown d-none d-md-flex me-2">
          <!-- <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1" aria-label="Show notifications">
            <i class="fa-solid fa-bell"></i>
            <span class="badge bg-red"></span>
          </a> -->
          <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
            <div class="card">
              <div class="list-group list-group-flush list-group-hoverable">
                <div class="list-group-item">
                  <div class="row align-items-center">
                    <div class="col-auto"><span class="status-dot d-block"></span></div>
                    <div class="col text-truncate">
                      <a href="#" class="text-body d-block">Example 2</a>
                      <div class="d-block text-secondary text-truncate mt-n1">
                        justify-content:between ⇒ justify-content:space-between (#29734)
                      </div>
                    </div>
                    <div class="col-auto">
                      <a href="#" class="list-group-item-actions show">
                        <!-- Download SVG icon from http://tabler-icons.io/i/star -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-yellow" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="nav-item dropdown">
        <a href="#" class="nav-link d-flex lh-1 text-reset p-0 gap-3" data-bs-toggle="dropdown" aria-label="Open user menu">
          <div class="d-none d-xl-block ps-2">
            <div><?php echo isset($_SESSION['persona_datos']) ? $_SESSION['persona_datos'] : ''; ?></div>
            <!-- <div class="mt-1 small text-secondary">Administrador</div> -->
          </div>
          <span class="avatar avatar-sm"><i class="fa-solid fa-user"></i></span>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
          <!-- <a href="./profile.html" class="dropdown-item"><i class="fa-solid fa-address-card me-2"></i>Perfil</a>
          <div class="dropdown-divider m-0"></div> -->
          <a href="/cerrar-sesion" class="dropdown-item"><i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesión</a>
        </div>
      </div>
    </div>
  </div>
</header>