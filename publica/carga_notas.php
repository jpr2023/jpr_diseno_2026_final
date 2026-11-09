<?php
    session_start();
    $url = (object)$_GET;
    
    $base = new PDO("sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db");

    // Capturar filtros de búsqueda
    $filtroMateria = isset($_GET['materia']) ? trim($_GET['materia']) : '';
    $filtroAnio = isset($_GET['anio']) ? trim($_GET['anio']) : '';

    // Saber si hay filtros activos para mantener el buscador abierto por defecto si se está buscando
    $busquedaActiva = ($filtroMateria !== '' || $filtroAnio !== '');

    // Construir la consulta SQL con filtros dinámicos
    $reglaSQL = "SELECT C.*, M.NOMBRE AS MNOMBRE 
                 FROM T_CURSADAS2 AS C
                 LEFT JOIN T_MATERIAS AS M ON M.ID = C.C_MATERIA
                 WHERE C.ACTIVO IS TRUE";
    $params = [];

    if ($filtroMateria !== '') {
        $reglaSQL .= " AND M.NOMBRE LIKE ?";
        $params[] = "%$filtroMateria%";
    }
    if ($filtroAnio !== '') {
        $reglaSQL .= " AND C.ANIO = ?";
        $params[] = $filtroAnio;
    }
    $reglaSQL .= ";";

    $stmt = $base->prepare($reglaSQL);
    $stmt->execute($params);
    $todos = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<html>
<head>
    <title>Tu App</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css" />
    <link rel="stylesheet" href="../estilosuser.css">
    <style>
        [full]{ width: 100% }
        details.seccion-busqueda {
            margin-bottom: 2rem;
            max-width: 600px;
            border: 1px solid var(--border-color, #ccc);
            padding: 1rem;
            border-radius: 6px;
        }
        details.seccion-busqueda summary {
            cursor: pointer;
            font-weight: bold;
            user-select: none;
        }
        .form-busqueda {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }
        .campos-fila {
            display: flex;
            gap: 1rem;
        }
        .campos-fila > div {
            flex: 1;
        }
        .botones {
            display: flex;
            gap: 1rem;
        }
        /* Ajustes para la tabla y los botones en una sola línea */
        table {
            width: 100%;
        }
        .acciones-tabla {
            display: flex;
            gap: 0.4rem;
            flex-wrap: nowrap; 
            align-items: center;
            white-space: nowrap;
        }
        .acciones-tabla button {
            padding: 0.4rem 0.8rem; /* Botones un poco más compactos para que entren bien */
            font-size: 0.9rem;
            margin: 0;
        }
    </style>
</head>
<body>
    <header>
        <h1>Cursadas Activas</h1>
        <nav>
            <button onclick="location.href='/';">Volver</button>
        </nav>
    </header>

    <!-- Formulario de Búsqueda Colapsable -->
    <details class="seccion-busqueda" <?php echo $busquedaActiva ? 'open' : ''; ?>>
        <summary>🔍 Buscar / Filtrar cursadas</summary>
        <form method="GET" class="form-busqueda">
            <div class="campos-fila">
                <div>
                    <label for="materia">Materia:</label>
                    <input type="text" id="materia" name="materia" value="<?php echo htmlspecialchars($filtroMateria); ?>" placeholder="Buscar materia...">
                </div>
                <div>
                    <label for="anio">Año:</label>
                    <input type="text" id="anio" name="anio" value="<?php echo htmlspecialchars($filtroAnio); ?>" placeholder="Ej: 2026">
                </div>
            </div>
            <div class="botones">
                <button type="submit">Filtrar</button>
                <button type="button" onclick="location.href='?'" style="background: gray;">Limpiar</button>
            </div>
        </form>
    </details>

<?php
    if (empty($todos)):
        printf("<p class='error'>No se encontraron registros de cursadas</p>");
    else:
        printf("<p>En total hay %d cursadas</p>", count($todos));
?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Materia</th>
                <th>Año</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($todos as $cadaRegistro): ?>
                <tr>
                    <td><?php echo htmlspecialchars($cadaRegistro->ID); ?></td>
                    <td><?php echo htmlspecialchars($cadaRegistro->MNOMBRE ?? 'Sin materia'); ?></td>
                    <td><?php echo htmlspecialchars($cadaRegistro->ANIO ?? ''); ?></td>
                    <td>
                        <div class="acciones-tabla">

                            <form action="/cursadas/modulos/cargar.php" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('¿Desea cargar notas de los alumnos?');"> 
                                <input type="hidden" name="id" value="<?php echo $cadaRegistro->ID; ?>">
                                <button type="submit" style="background-color: #4f5ad9; color: white;">Cargar Notas</button>
                            </form> 

                            <form action="/cursadas/modulos/cargara.php" method="POST" style="display:inline; margin:0;" onsubmit="return confirm('¿Desea cargar asistencias de los alumnos?');"> 
                                <input type="hidden" name="id" value="<?php echo $cadaRegistro->ID; ?>">
                                <button type="submit" style="background-color: #4f5ad9; color: white;">Cargar Asistencias</button>
                            </form> 
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php
    endif;
?>
</body>
<footer data-role="footer" data-fixed="true">
    <button onclick="location.href='/';">Volver</button>
    <h5>&copy; Juan Pablo Rodriguez 2026@</h5>
</footer> 
</html>