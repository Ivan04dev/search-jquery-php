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

    // Preparar la consulta para obtener usuarios, buscando en NOMBRE y USUARIO
    $par = "NOMBRE, USUARIO, PUESTO, ESTATUS";
    $tb = "vtas_usuarios";
    $cadena = "WHERE ESTATUS != 'Baja' AND (NOMBRE LIKE :nombre OR NOMBRE_COMPLETO LIKE :nombre OR USUARIO LIKE :usuario)";
    
    // Ejecutar la consulta
    $consulta = new ConsultaBD();
    $resultado = $consulta->consultaDatos($con, $par, $tb, $cadena, [
        ':nombre' => "%$nombre%",
        ':usuario' => "%$nombre%"  // Agregamos este parámetro
    ]);

    $usuarios = [];
    if ($resultado) {
        while ($row = oci_fetch_assoc($resultado)) {
            $usuarios[] = [
                'nombre' => htmlspecialchars($row['NOMBRE']),
                'usuario' => htmlspecialchars($row['USUARIO']),
                'puesto' => htmlspecialchars($row['PUESTO']),
                'estado' => htmlspecialchars($row['ESTATUS'])
            ];
        }
    }

    // Devolver los resultados en formato JSON
    header('Content-Type: application/json');
    echo json_encode($usuarios);

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Se produjo un error: ' . htmlspecialchars($e->getMessage())]);
} finally {
    if (isset($conexion)) {
        $conexion->cerrarConexion();
    }
}
