<?php
session_start();

// Validar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario_autenticado'])) {
    header("Location: login.php");
    exit();
}

// Obtenemos los datos de la sesión (perfil, nombre y usuario)
$perfil = isset($_SESSION['perfil']) ? $_SESSION['perfil'] : 1;
$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : (isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario');

// Mapeo descriptivo de los perfiles para mostrar en pantalla
$descPerfiles = [
    1 => 'Administrador (Acceso Total)',
    2 => 'Implementador (Sin acceso a usuarios)',
    3 => 'Usuario (Solo Reportes)'
];
$nombrePerfil = isset($descPerfiles[$perfil]) ? $descPerfiles[$perfil] : "Perfil {$perfil}";
?>
<html>
    <head>
        <link href="https://cdn.simplecss.org/simple.css" rel="stylesheet" />
        <link rel="stylesheet" href="./estilos.css">
        <style>
            .grid-botones {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
            }
            .grid-botones button {
                width: 100%;
                margin: 0;
            }
            .info-usuario {
                background: var(--accent-bg, #f4f4f4);
                padding: 0.8rem 1.2rem;
                border-radius: 4px;
                margin-bottom: 1.5rem;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .logo-container {
                display: flex;
                justify-content: center; /* Centra horizontalmente */
                align-items: center;     /* Centra verticalmente (opcional) */
                width: 100%;             Ocupa todo el ancho disponible
                margin-bottom: 15px;     /* Espacio debajo del logo */
            }
        </style>
    </head>
    
    <body>
        <div>
            <h1 class="banner">Sistema Gestión de Alumnos</h1>
        </div>

        <div class="logo-container">
            <img src="./logo.png" width="20%" height="20%" alt="Logo JPR software" class="login-logo">
        </div>
        <!-- Tarjeta de bienvenida y perfil -->
        <div class="info-usuario">
            <div>
                <strong>Usuario:</strong> <span><?php echo htmlspecialchars($nombreUsuario); ?></span>
            </div>
            <div>
                <strong>Perfil:</strong> <span><?php echo htmlspecialchars($nombrePerfil); ?></span>
            </div>
        </div>

        <?php if ($perfil != 3): // El perfil 3 no visualiza la sección de módulos/carpetas ?>
        <div class="grid-botones">
            <?php
                $archivos = scandir( "." );
                foreach( $archivos as $cadaArchivo ):
                    if( $cadaArchivo == "." ) continue ;
                    if( $cadaArchivo == ".." ) continue ;
                    if( !is_dir($cadaArchivo) ) continue ;
                    
                    // Si el perfil es 2, ocultamos la carpeta de usuarios
                    if ($perfil == 2 && strtolower($cadaArchivo) == 'usuarios') {
                        continue;
                    }

                    print "<button onclick='location.href=`$cadaArchivo`'>$cadaArchivo</button>\n" ;
                endforeach;  
            ?>
        </div>
        <hr>
        <?php endif; ?>

        <div class="grid-botones">
            <button class="mi-boton_r" onclick="window.location.href='repo_cursadas.php';">Reporte Cursadas</button>
            <button class="mi-boton_r" onclick="window.location.href='repo_notas.php';">Reporte Notas</button>
            <button class="mi-boton_r" onclick="window.location.href='repo_asistencias.php';">Reporte Asistencias</button>
            <button class="mi-boton_c" onclick="window.location.href='carga_notas.php';">Cargas de Notas/Asist.</button>
        </div>
        <hr>
        <button class="mi-boton" id="btnSalir" onclick="window.location.href='logout.php';">Salir del Sistema</button>
    </body>
    <footer data-role="footer" data-fixed="true" >
        <h5>&copy; Juan Pablo Rodriguez 2026@</h5>
    </footer> 
    <script>
        const btnSalir = document.getElementById('btnSalir');
        if (btnSalir) {
            btnSalir.addEventListener('click', function() {
                window.location.href = 'logout.php';
            });
        }
    </script>
</html>