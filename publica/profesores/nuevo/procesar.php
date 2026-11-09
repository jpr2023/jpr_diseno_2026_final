<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "INSERT INTO T_PROFESORES VALUES ( 
    '$form->nmID'
    , '$form->nmNOMBRE'
	, '$form->nmACTIVO'
	, '$form->nmTELEFONO'
	, '$form->nmDIRECCION'
	, '$form->nmTITULO'
	, '$form->nmF_NACIMIENTO'
)" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "Profesor agregad@" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;
