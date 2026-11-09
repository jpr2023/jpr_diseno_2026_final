<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "   UPDATE T_PERFILES SET  
                    NOMBRE = '$form->nmNOMBRE'
                    ,NIVEL = '$form->nmNIVEL'
                    ,ACTIVO = '$form->nmACTIVO'
                    WHERE ID = '$form->nmID'
" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "PERFIL actualizad@" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;
