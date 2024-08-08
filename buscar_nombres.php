<?php
session_start();

// Include de los archivos necesarios
include("../server/BD/_config/conexionBD.php");
include("../server/BD/_config/insertaBD.php");
include("../server/BD/_config/consultaBD.php");
include("../server/BD/_config/actualizaBD.php");
include("_fecha.php");

try {
    $conexion = new ConexionBD();
    $con = $conexion->conectar();

    if (!$con) {
        die(json_encode(['error' => 'Error de conexión: ' . oci_error()]));
    }

    $nombre = isset($_GET['nombre']) ? trim($_GET['nombre']) : ''; // Obtener el término de búsqueda

    // Preparar la consulta para obtener nombres
    $par = "NOMBRE";
    $tb = "vtas_usuarios";
    $cadena = "WHERE ESTATUS != 'Baja' AND NOMBRE LIKE :nombre";

    // Ejecutar la consulta
    $consulta = new ConsultaBD();
    $resultado = $consulta->consultaDatos($con, $par, $tb, $cadena, [':nombre' => "%$nombre%"]);

    $nombres = [];
    if ($resultado) {
        while ($row = oci_fetch_assoc($resultado)) {
            $nombres[] = [
                'nombre' => htmlspecialchars($row['NOMBRE']),
            ];
        }
    }

    // Devolver los resultados en formato JSON
    header('Content-Type: application/json');
    echo json_encode($nombres);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Se produjo un error: ' . htmlspecialchars($e->getMessage())]);
} finally {
    if (isset($conexion)) {
        $conexion->cerrarConexion();
    }
}
