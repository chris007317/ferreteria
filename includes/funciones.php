<?php 
use Enum\EstadoRegistro;

    function isAuth() : bool{
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION['idAdministrativo']) && !empty($_SESSION);
    }

    function calcularEdad($fechaNacimiento){
        $fechaActual = date('Y-m-d');
        $fecha_nacimiento = new DateTime($fechaNacimiento);
        $hoy = new DateTime();
        $edad = $hoy->diff($fecha_nacimiento);
        $dias = $edad->d;
        $meses = $edad->m;
        $year = $edad->y;
        $tiempo = '';
        if ($edad->m == 0 && $edad->d == 0) {
            $tiempo = $edad->y.' AÑOS';
        }else if($edad->y == 0 && $edad->d == 0){
            $tiempo = $edad->d.' MESES';
        }else if($edad->y == 0 && $edad->m == 0){
            $tiempo = $edad->d.' DÍAS';
        }else if ($edad->d == 0) {
            $tiempo = $edad->y.' AÑOS '.$edad->m.' MESES';
        }else if($edad->m == 0){
            $tiempo = $edad->y.' AÑOS '.$edad->d.' DÍAS';
        }else if ($edad->y == 0) {
            $tiempo = $edad->m.' MESES '.$edad->d.' DÍAS';
        }else{
            $tiempo = $edad->y.' AÑOS '.$edad->m.' MESES '.$edad->d.' DÍAS';
        }
        return $tiempo;
    }

    function validarFecha($fecha){
        $valores = explode('-', $fecha);
        if(count($valores) == 3 && checkdate($valores[1], $valores[2], $valores[0])){
            return true;
        }
        return false;
    }

    function buscarPersona($dni){
        // Datos
        $token = $_ENV['TOKEN_DNI'];
        // Iniciar llamada a API
        $curl = curl_init();

        // Buscar dni
        curl_setopt_array($curl, array(
          // para user api versión 1
          CURLOPT_URL => $_ENV['URL_DNI'] . $dni,
          // para user api versión 2
          // CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $dni,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 2,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Referer: https://apis.net.pe/consulta-dni-api',
            'Authorization: Bearer ' . $token
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        // Datos listos para usar
        return $response;        
    }

    function buscarPersonaDni($dni) {
        $url = $_ENV['URL_DNI'] . $dni;
        $token = $_ENV['TOKEN_DNI'];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$token}"
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        // Si hubo error de conexión
        if ($error) {
            $alerta['tipo'] = 'error';
            $alerta['mensaje'] = "Error en cURL: " . $error;
            return $alerta;
        }

        // Decodificar respuesta
        $result = json_decode($response, true);

        // Si la API devuelve error o el JSON es inválido
        if (json_last_error() !== JSON_ERROR_NONE || empty($result) || isset($result['message'])) {
            $alerta['tipo'] = 'error';
            $alerta['mensaje'] = "No se encontró información para el DNI {$dni}";
            return $alerta;
        }
        $alerta['tipo'] = 'ok';
        $alerta['dato'] = $result;
        return $alerta;
    }
        
    function buscarRucEmpresa($ruc) {
        $url = $_ENV['URL_RUC'] . $ruc;
        $token = $_ENV['TOKEN_DNI'];

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$token}"
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);

        // Si hubo error de conexión
        if ($error) {
            $alerta['tipo'] = 'error';
            $alerta['mensaje'] = "Error en cURL: " . $error;
            return $alerta;
        }

        // Decodificar respuesta
        $result = json_decode($response, true);

        // Si la API devuelve error o el JSON es inválido
        if (json_last_error() !== JSON_ERROR_NONE || empty($result) || isset($result['message'])) {
            $alerta['tipo'] = 'error';
            $alerta['mensaje'] = "No se encontró información para el RUC {$ruc}";
            return $alerta;
        }
        $alerta['tipo'] = 'ok';
        $alerta['dato'] = $result;
        return $alerta;
    }    

    function insertarImagen($nombreImg, $porcentaje, $carpeta, $imagenActual, $nombreImagen, $calidad = 80) {
        $directorio =  $_ENV['RUTA_IMAGEN']."/img/" . $carpeta;
        $rutaImagenActual = $directorio . "/" . $imagenActual;
        $rutaNueva = $directorio . "/" . $nombreImagen . ".webp"; // Siempre guardaremos en WebP
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        // Verificar si se ha subido un archivo válido
        if (isset($_FILES[$nombreImg]['tmp_name']) && is_uploaded_file($_FILES[$nombreImg]['tmp_name'])) {
            $tipo = $_FILES[$nombreImg]['type'];
            // Si la imagen ya es WebP, la movemos sin procesarla
            if ($tipo === "image/webp") {
                move_uploaded_file($_FILES[$nombreImg]['tmp_name'], $rutaNueva);
                // Eliminar la imagen anterior si existe
                if (file_exists($rutaImagenActual)) {
                    unlink($rutaImagenActual);
                }
                return $nombreImagen . '.webp';
            }
            // Crear la imagen según el formato original
            switch ($tipo) {
                case "image/jpeg":
                    $origen = imagecreatefromjpeg($_FILES[$nombreImg]['tmp_name']);
                    break;
                case "image/png":
                    $origen = imagecreatefrompng($_FILES[$nombreImg]['tmp_name']);
                    imagealphablending($origen, false);
                    imagesavealpha($origen, true);
                    break;
                case "image/gif":
                    $origen = imagecreatefromgif($_FILES[$nombreImg]['tmp_name']);
                    break;
                case "image/bmp":
                    $origen = imagecreatefrombmp($_FILES[$nombreImg]['tmp_name']);
                    break;
                default:
                    return ''; // Si no es un formato soportado, no hacemos nada
            }
            // Leer metadatos de orientación para imágenes JPEG
            if ($tipo === "image/jpeg") {
                $exif = @exif_read_data($_FILES[$nombreImg]['tmp_name']);
                if ($exif !== false && isset($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $origen = imagerotate($origen, 180, 0);
                            break;
                        case 6:
                            $origen = imagerotate($origen, -90, 0);
                            break;
                        case 8:
                            $origen = imagerotate($origen, 90, 0);
                            break;
                    }
                }
            }
            // Obtener dimensiones y redimensionar
            list($ancho, $alto) = getimagesize($_FILES[$nombreImg]['tmp_name']);
            $nuevoAncho = $ancho * $porcentaje;
            $nuevoAlto = $alto * $porcentaje;
            $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
            // Manejo de transparencia para PNG y GIF
            if ($tipo === "image/png" || $tipo === "image/gif") {
                imagealphablending($destino, false);
                imagesavealpha($destino, true);
            }            
            imagecopyresampled($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
            // Guardar en formato WebP con calidad 80 (puedes ajustarla)
            imagewebp($destino, $rutaNueva, $calidad);
            // Eliminar la imagen anterior si existe
            if ($imagenActual != 'imagen.png' && file_exists($rutaImagenActual)) {
                unlink($rutaImagenActual);
            }
            // Liberar memoria
            imagedestroy($origen);
            imagedestroy($destino);
            return $nombreImagen . '.webp';
        }
        return '';
    }


function eliminarImagen($carpeta, $imagen){
    $carpetaImagen = $carpeta.'/'.$imagen;
    if($imagen!='imagen.png'){
        $ruta = 'views/recursos/img/'.$carpetaImagen;
        unlink($ruta);
        return true;
    }
    return false;
}

function sustraerCelulares($celTotal){
    $celulares = '';
    if ($celTotal != '[]' && !empty($celTotal)) {
        $celularPersonal = json_decode($celTotal, true);
        for ($i=0; $i < count($celularPersonal) ; $i++) { 
            $celulares .= $celularPersonal[$i].', ';
        }
        $celulares = substr($celulares, 0, -2);     
    }
    return $celulares;
}

function ObtenerDias(){
    $dias = [
        ['nombre' => 'Lunes', 'valor' => 1],
        ['nombre' => 'Martes', 'valor' => 2],
        ['nombre' => 'Miercoles', 'valor' => 3],
        ['nombre' => 'Jueves', 'valor' => 4],
        ['nombre' => 'Viernes', 'valor' => 5],
        ['nombre' => 'Sábado', 'valor' => 6],
        ['nombre' => 'Domingo', 'valor' => 7],
    ];
    return $dias;
}

function ObtenerDia($valor) {
    $dias = ObtenerDias();
    foreach ($dias as $dia) {
        if ($dia['valor'] === $valor) {
            return $dia['nombre'];
        }
    }
    return '';
}

function obtenerHoraMinuto($hora){
    $formato_hora = date("H:i", strtotime($hora));
    return $formato_hora;
}

function generarNombreImagen($codigo): string{
    $fecha = date("Ymd_His");
    return $fecha . "_" .$codigo;
}

function ObtenerEstadoRegistro(string $estado): string {
    return match ($estado) {
        EstadoRegistro::ACTIVO->value  => 'text-cyan',
        EstadoRegistro::INACTIVO->value => 'text-dark',
        default => 'badge-success',
    };
}

function NuevoEstado($estado) : string{
    return $estado == EstadoRegistro::ACTIVO->value ? EstadoRegistro::INACTIVO->value : EstadoRegistro::ACTIVO->value;
}

function EsEditable($estado) : bool{
    return $estado == EstadoRegistro::ACTIVO->value;
}

function mapearAObjeto($clase, $data) {
    $obj = new $clase();
    foreach ($data as $key => $value) {
        if (property_exists($obj, $key)) {
            $obj->$key = $value;
        }
    }
    return $obj;
}

function enviarCorreo($para, $asunto, $plantilla, $de) {
    $cabeceras  = "MIME-Version: 1.0" . "\r\n";
    $cabeceras .= "Content-type: text/html; charset=UTF-8" . "\r\n";
    $cabeceras .= "From: " . $de . "\r\n";
    $cabeceras .= "X-Mailer: PHP/" . phpversion();

    return mail($para, $asunto, $plantilla, $cabeceras);
}

function tipoMonedaPeru(float $monto): string {
    return number_format($monto, 2, '.', ',');
}