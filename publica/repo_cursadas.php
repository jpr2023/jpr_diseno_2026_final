<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cursadas</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            margin: 20px;
        }
        h2 {
            color: #2c3e50;
            text-align: center;
        }
        .search-container {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .search-container input {
            padding: 10px 15px;
            font-size: 0.95rem;
            border: 1px solid #ced4da;
            border-radius: 6px;
            width: 200px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .search-container input:focus {
            border-color: #34495e;
            box-shadow: 0 0 5px rgba(52, 73, 94, 0.2);
        }
        .table-container {
            overflow-x: auto;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background: #fff;
            padding: 10px;
        }
        table.styled-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        table.styled-table th, 
        table.styled-table td {
            padding: 12px 15px;
        }
        table.styled-table th {
            background-color: #34495e;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
        }
        table.styled-table tr {
            border-bottom: 1px solid #dddddd;
        }
        table.styled-table tr:nth-of-type(even) {
            background-color: #f3f4f6;
        }
        table.styled-table tr:hover {
            background-color: #e2e8f0;
        }
        .no-data {
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            text-align: center;
        }
    </style>
</head>
<header>
    <table>
  <thead>
    <tr>
        <th><img src="./logo.png" width="15%" height="15%" alt="Logo JPR software" class="login-logo"></th>
    </tr>
  </thead> 
  </table>
</header>
<body>

    <h2>Reporte General de Cursadas</h2>

    <!-- Contenedor de Buscadores -->
    <div class="search-container">
        <input type="text" id="buscarAnio" placeholder="Filtrar por año..." onkeyup="filtrarTabla()">
        <input type="text" id="buscarAlumno" placeholder="Filtrar por alumno..." onkeyup="filtrarTabla()">
        <input type="text" id="buscarMateria" placeholder="Filtrar por materia..." onkeyup="filtrarTabla()">
        <input type="text" id="buscarProfesor" placeholder="Filtrar por profesor..." onkeyup="filtrarTabla()">
        <button onclick="location.href='/';">Volver</button>
    </div>

    <?php
    try {
        // Conexión a la base de datos SQLite usando PDO
        $pdo = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
        
        // Configurar el modo de error para que lance excepciones
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Ejecutar la consulta
        $query = "  SELECT CO.ANIO AS AÑO
                    , A.NOMBRE AS ALUMNO
                    , M.NOMBRE AS MATERIA
                    , P.NOMBRE AS PROFESOR
                    , C.NOTA_FINAL as [NOTA FINAL]
                    , CO.ID AS [ID CURSADA]
                    FROM T_CURSADAS_A AS C
                    LEFT JOIN T_CURSADAS2 AS CO ON CO.ID = C_CURSADA
                    LEFT JOIN T_ALUMNOS AS A ON A.ID = C.C_ALUMNO
                    LEFT JOIN T_MATERIAS AS M ON M.ID = CO.C_MATERIA
                    LEFT JOIN T_PROFESORES AS P ON P.ID = CO.C_PROFESOR
                 ";
        $stmt = $pdo->query($query);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Verificar si hay registros
        if (count($usuarios) > 0) {
            echo "<div class='table-container'>";
            echo "<table class='styled-table' id='tablaCursadas'>";
            
            // Imprimir los encabezados de la tabla dinámicamente
            echo "<thead><tr>";
            foreach (array_keys($usuarios[0]) as $columna) {
                echo "<th>" . htmlspecialchars($columna ?? '') . "</th>";
            }
            echo "</tr></thead>";

            // Imprimir los datos de cada registro
            echo "<tbody>";
            foreach ($usuarios as $row) {
                echo "<tr>";
                foreach ($row as $valor) {
                    echo "<td>" . htmlspecialchars($valor ?? '') . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody>";
            
            echo "</table>";
            echo "</div>";
        } else {
            echo "<div class='no-data'><p>No se encontraron registros en la consulta.</p></div>";
        }

    } catch (PDOException $e) {
        echo "<div style='color: red; background: #ffe6e6; padding: 10px; border-radius: 5px;'>Error de conexión o consulta: " . htmlspecialchars($e->getMessage() ?? '') . "</div>";
    }
    ?>

    <!-- Script de filtrado en tiempo real -->
    <script>
        function filtrarTabla() {
            const inputAnio = document.getElementById('buscarAnio').value.toLowerCase();
            const inputAlumno = document.getElementById('buscarAlumno').value.toLowerCase();
            const inputMateria = document.getElementById('buscarMateria').value.toLowerCase();
            const inputProfesor = document.getElementById('buscarProfesor').value.toLowerCase();
            
            const tabla = document.getElementById('tablaCursadas');
            if (!tabla) return;
            
            const filas = tabla.getElementsByTagName('tr');

            // Comenzamos desde i = 1 para saltar la cabecera (thead)
            for (let i = 1; i < filas.length; i++) {
                const celdas = filas[i].getElementsByTagName('td');
                if (celdas.length > 0) {
                    // Orden de columnas según tu SELECT:
                    // [0] = AÑO, [1] = ALUMNO, [2] = MATERIA, [3] = PROFESOR, [4] = NOTA FINAL
                    const textoAnio = celdas[0].textContent.toLowerCase();
                    const textoAlumno = celdas[1].textContent.toLowerCase();
                    const textoMateria = celdas[2].textContent.toLowerCase();
                    const textoProfesor = celdas[3].textContent.toLowerCase();

                    const coincideAnio = textoAnio.includes(inputAnio);
                    const coincideAlumno = textoAlumno.includes(inputAlumno);
                    const coincideMateria = textoMateria.includes(inputMateria);
                    const coincideProfesor = textoProfesor.includes(inputProfesor);

                    // La fila se muestra solo si coincide con los criterios ingresados en todos los campos activos
                    if (coincideAnio && coincideAlumno && coincideMateria && coincideProfesor) {
                        filas[i].style.display = "";
                    } else {
                        filas[i].style.display = "none";
                    }
                }
            }
        }
    </script>

</body>
</html>