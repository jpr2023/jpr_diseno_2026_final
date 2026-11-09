<?php
    session_start() ;
    $url = (object)$_GET ;
    $id = $url->id ?? 1 ;
    
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    
    // Consulta para obtener los datos del usuario actual
    $reglaSQL = "SELECT U.*, P.NOMBRE AS P_PERFIL FROM T_USUARIOS AS U LEFT JOIN T_PERFILES AS P ON P.ID = U.PERFIL WHERE USUARIO='$id';" ;
    $accion = $base->query( $reglaSQL ) ;
    if( !$accion ):
        printf( "Hubo un error - %s", $base->errorInfo()[2] ) ;
        die() ;
    endif;
    $todos = $accion->fetchAll( PDO::FETCH_OBJ ) ;

    // Consulta para obtener todos los perfiles y poblar el desplegable
    $perfilesSQL = "SELECT ID, NOMBRE FROM T_PERFILES;";
    $accionPerfiles = $base->query($perfilesSQL);
    $listaPerfiles = $accionPerfiles ? $accionPerfiles->fetchAll(PDO::FETCH_OBJ) : [];
?>
<html>
    
<head>
    <title>X</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css" />
    <style>[full]{ width: 100% }</style>
</head>

    <body>
        <header>
            <h1>Edición de Usuarios</h1>
            <nav>
                <button onclick="history.back()">Volver</button>
            </nav>
        </header>
        <section>

<?php
    if( count($todos) == 0 ):
        printf( "<p> No se encontró el registro %d </p>", $id );
    else:
        $encontrado = $todos[0] ;
        $Usuario = $encontrado->USUARIO;
        $Nombre = $encontrado->NOMBRE;
        $Clave = $encontrado->CLAVE;
        $IDPerfil = $encontrado->PERFIL; // Código del perfil actual
    endif;
?>

<form action='/USUARIOS/editar/procesar.php' method="get">
     
    <article>
        <label for='idUsuario'>USUARIO: </label><br>
        <input type="TEXT" id="idUsuario" name="nmUsuario" readonly value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article>
        <label for='idNOMBRE'>NOMBRE: </label><br>
        <input type="TEXT" id="idNOMBRE" name="nmNOMBRE" value="<?php echo htmlspecialchars($Nombre, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article>
        <label for='idCLAVE'>CLAVE: </label><br>
        <input type="TEXT" id="idCLAVE" name="nmCLAVE" value="<?php echo htmlspecialchars($Clave, ENT_QUOTES, 'UTF-8'); ?>" />
    </article>

    <article>
        <label for='idPERFIL'>PERFIL: </label><br>
        <select id="idPERFIL_ORI" name="nmPERFIL_ORI">
            <?php foreach ($listaPerfiles as $p): ?>
                <option value="<?php echo htmlspecialchars($p->ID, ENT_QUOTES, 'UTF-8'); ?>" 
                    <?php echo ($p->ID == $IDPerfil) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($p->NOMBRE, ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" id="idPERFIL" name="nmPERFIL" value="<?php echo htmlspecialchars($IDPerfil, ENT_QUOTES, 'UTF-8'); ?>" readonly />
    </article>

    <input type="submit" value="Modificar ítem" />
</form>

<script>
    const selectPerfil = document.getElementById('idPERFIL_ORI');
    const inputPerfil = document.getElementById('idPERFIL');

    selectPerfil.addEventListener('change', function() {
        inputPerfil.value = this.value;
    });
</script>

    </body>
</html>