<?php
    session_start();

    // Como el formulario usa method="POST", debemos capturar desde $_POST
    $form = (object)$_POST;

    // Validación básica para verificar que los campos obligatorios no estén vacíos
    if (empty($form->nmID) || empty($form->nmANIO) || empty($form->nmMATERIA)) {
        $_SESSION["error"] = "Faltan datos obligatorios para registrar la cursada.";
        header("location:../index.php");
        exit;
    }

    try {
        $db_path = $_SERVER['DOCUMENT_ROOT'] . '/../CURSADAS.db';
        $base = new PDO("sqlite:" . $db_path);
        $base->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Consulta SQL usando parámetros seguros (?) para evitar errores de sintaxis y SQL injection
        $reglaSQL = "INSERT INTO T_CURSADAS2 (ID, ANIO, C_MATERIA, C_PROFESOR, ACTIVO) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $base->prepare($reglaSQL);
        $accion = $stmt->execute([
            $form->nmID,
            $form->nmANIO,
            $form->nmMATERIA,
            !empty($form->nmPROFESOR) ? $form->nmPROFESOR : null,
            $form->nmACTIVO
        ]);

        if ($accion) {
            $_SESSION["mensaje"] = "Cursada agregada exitosamente";
        }
    } catch (PDOException $e) {
        $_SESSION["error"] = "Error al guardar: " . $e->getMessage();
    }

    header("location:../index.php");
    exit;
?>