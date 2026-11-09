<?php
    session_start() ;
    $url = (object)$_GET ;
    $id = $url->id ?? 1 ;
    $reglaSQL = "   SELECT A.*, T.NOMBRE AS NOMBRET, C.NOMBRE AS NOMBREC 
                    FROM T_ALUMNOS AS A
                    LEFT JOIN T_TUTORES AS T ON T.ID = A.TUTOR
                    LEFT JOIN T_CARRERAS AS C ON C.ID = A.TUTOR 
                    WHERE A.ID='$id';" ;
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
    <title >Tu App</title>
    <meta name="viewport" content="width=device-width,user-scalable=no" />
    <meta charset="utf-8" />
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.css" />
    <link rel="stylesheet" href="../estilosuser.css">
    <style >[full]{ width: 100% }</style>
</head>

    <body >
        <header >
            <h1>Detalle de Alumno</h1>
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
        printf( "<details ><pre>%s</pre></details>", json_encode( $encontrado ) ) ;
        printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "ID", $encontrado->ID );
        printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "NOMBRE", $encontrado->NOMBRE );
        printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "ACTIVO", $encontrado->ACTIVO );
        printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "F_NACIMIENTO", $encontrado->F_NACIMIENTO );
        printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "TUTOR", $encontrado->NOMBRET );
        printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "CARRERA", $encontrado->NOMBREC );
        printf( "<p><strong>%s</strong>: <mark>%s</mark></p>", "DIRECCION", $encontrado->DIRECCION );
    endif;
?>
    </body>
</html>
