<?php
    session_start() ;
    $url = (object)$_GET ;
    $id = $url->id ?? 1 ;
    $reglaSQL = "SELECT U.*, P.NOMBRE AS P_PERFIL FROM T_USUARIOS AS U LEFT JOIN T_PERFILES AS P ON P.ID = U.PERFIL WHERE USUARIO='$id';" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( !$accion ):
        printf( "Hubo un error - %s", $base->errorInfo()[2] ) ;
        die() ;
    endif;
    $todos = $accion->fetchAll( PDO::FETCH_OBJ ) ;
?>
<html >
    
<head>
    <title >X</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css" />
    <style >[full]{ width: 100% }</style>
</head>

    <body >
        <header >
            <h1>Eliminación de Usuarios</h1>
            <nav>
                <button onclick="history.back()">Volver</button>
            </nav>
        </header>
        <section >

<?php
    if( count($todos) == 0 ):
        printf( "<p> No se encontró el registro %d </p>", $id );
    else:
        $encontrado = $todos[0] ;
        //printf( "<details ><pre>%s</pre></details>", json_encode( $encontrado ) ) ;
        //printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "CODIGO", $encontrado->CODIGO );
        //printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "NOMBRE", $encontrado->NOMBRE );
        //echo '<input type="text" name="mi_campo" placeholder="Nuevo nombre aquí">';
        //printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "NACIONALIDAD", $encontrado->NACIONALIDAD );
        //echo '<input type="text" name="mi_campo" placeholder="Nueva nacionalidad aquí">';
        //printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "AÑO", $encontrado->AÑO );        
        //echo '<input type="text" name="mi_campo" placeholder="Nuevo año aquí">';

        $Usuario=$encontrado->USUARIO;
        $Nombre = $encontrado->NOMBRE;
        $Clave = $encontrado->CLAVE;
        $Perfil = $encontrado->P_PERFIL;
        $IDPerfil = $encontrado->PERFIL;
    endif;
?>

<form action='/USUARIOS/borrar/procesar.php' method="get" >
     
    <article >
        <label for='idUsuario'>USUARIO: </label><br>
        <input type="TEXT" id="idUsuario" name="nmUsuario"  readonly value="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article >
        <label for='idNOMBRE'>NOMBRE: </label><br>
        <input type="TEXT" id="idNOMBRE" name="nmNOMBRE"  readonly value="<?php echo htmlspecialchars($Nombre, ENT_QUOTES, 'UTF-8'); ?>"/>
    </article>

    <article >
        <label for='idCLAVE'>CLAVE: </label><br>
	    <input type="TEXT" id="idCLAVE" name="nmCLAVE" readonly value="<?php echo htmlspecialchars($Clave, ENT_QUOTES, 'UTF-8'); ?>"   />
    </article>

    <article >
        <label for='idPERFIL'>PERFIL: </label><br>
	    <input type="TEXT" id="idPERFIL" readonly name="nmPERFIL_ORI" readonly value="<?= htmlspecialchars($Perfil, ENT_QUOTES, 'UTF-8'); ?>" />
    </article>

    <input type="submit" value="Eliminar ítem" />
</form>


    </body>
</html>
