<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "UPDATE T_USUARIOS SET  
      NOMBRE = '$form->nmNOMBRE'
    , CLAVE = '$form->nmCLAVE'
	, PERFIL = '$form->nmPERFIL'
    WHERE USUARIO = '$form->nmUsuario'
" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "USUARIO actualizad@" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;
