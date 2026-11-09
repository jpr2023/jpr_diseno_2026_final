<?php
    session_start() ;
    $url = (object)$_GET ;
    $reglaSQL = "SELECT * FROM T_PERFILES;" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
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
            <h1>Viendo los perfiles </h1>
            
            <nav>
                <button onclick="location.href='/';">Volver</button>
                <button onclick="location.href='/perfiles/nuevo/'">Agregar nuevo perfiles</button>
            </nav>
        </header>
<?php
    if( !$accion ):
        printf( "<p class='error' >%s</p><details >%s</details>", "No hay ningún registro en la tabla", $base->errorInfo()[2] ) ;
    else:
        $todos = $accion->fetchAll( PDO::FETCH_OBJ ) ;
        printf( "<p>En total hay %d perfiles</p>", count($todos) );
        foreach( $todos as $cadaRegistro ):
            include "./render.php" ;
        endforeach;
    endif;
?>
    </body>
        <footer data-role="footer" data-fixed="true" >
        <button onclick="location.href='/';">Volver</button>
        <h5>&copy; Juan Pablo Rodriguez 2026@</h5>
    </footer> 
</html>
