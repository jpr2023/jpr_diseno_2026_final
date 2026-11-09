<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "INSERT INTO T_ALUMNOS VALUES ( 
    '$form->nmID'
    , '$form->nmNOMBRE'
	, '$form->nmACTIVO'
	, '$form->nmF_NACIMIENTO'
	, '$form->nmTUTOR'
	, '$form->nmCARRERA'
	, '$form->nmDIRECCION'
)" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "T_ALUMNOS agregad@" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;
