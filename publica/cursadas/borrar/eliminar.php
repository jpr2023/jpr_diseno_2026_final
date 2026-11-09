<?php
session_start();

// Verificar si se recibió el ID por POST
if (isset($_POST['id']) && !empty($_POST['id'])) {
    $id = trim($_POST['id']);

    try {
        // Conectar a la base de datos SQLite
        $base = new PDO("sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db");
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Preparar y ejecutar la consulta de eliminación por seguridad (evita SQL Injection)
        $sql = "DELETE FROM T_CURSADAS WHERE ID = ?";
        $stmt = $base->prepare($sql);
        $stmt->execute([$id]);

        // Redirigir de regreso al listado principal una vez eliminado
        header("Location: /cursadas/");
        exit();

    } catch (PDOException $e) {
        // En caso de error en la base de datos
        echo "Error al eliminar el registro: " . htmlspecialchars($e->getMessage());
        echo '<br><br><a href="/cursadas/">Volver</a>';
    }
} else {
    // Si se accede sin ID, redirigir al listado
    header("Location: /cursadas/");
    exit();
}
?>