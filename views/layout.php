<!doctype html>
<html lang="es">
	<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>REDECAMP - <?php echo $titulo; ?></title>
    <!-- CSS files -->
    <link href="/css/tabler/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
    <link href="/css/estilos.css" rel="stylesheet"/>
    <!-- <link href="./dist/css/tabler-flags.min.css?1692870487" rel="stylesheet"/>
    <link href="./dist/css/tabler-payments.min.css?1692870487" rel="stylesheet"/>
    <link href="./dist/css/tabler-vendors.min.css?1692870487" rel="stylesheet"/>
    <link href="/css/tabler/demo.min.css" rel="stylesheet"/> 
    -->

    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
      	--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
      }
    </style>
  </head>
  <body class="layout-fluid">
  	<div class="page">
  		<!-- Menú -->
      <?php 
	      include_once 'templates/menu.php';
	    ?>
      <!-- Navbar -->
      <?php 
	      include_once 'templates/header.php';
	    ?>
      <div class="page-wrapper">
        <?php 
        	echo $contenido;
	      	include_once 'templates/footer.php';
	    	?>
      </div>
  	</div>
  	<script src="/js/tabler/tabler.min.js" defer></script>
  	<script src="/js/fontawesome.all.min.js" defer></script>
    <script src="/js/funciones.js"></script>
    <script src="/js/<?php echo $script; ?>.js"></script>
    <?php 
      if ($script == 'estadisticas') {
    ?>    
    <script src="/js/apexcharts.min.js"></script>
    <?php 
      }
     ?>
  </body>
</html>