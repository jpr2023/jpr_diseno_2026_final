<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "   UPDATE T_PROFESORES SET  
                    NOMBRE = '$form->nmNOMBRE'
                    ,ACTIVO = '$form->nmACTIVO'
                    ,F_NACIMIENTO = '$form->nmFECHANAC'
                    ,DIRECCION = '$form->nmDIRECCION'
                    ,TELEFONO = '$form->nmTEL'
                    ,TITULO = '$form->nmTITULO'
                    WHERE ID = '$form->nmID'
" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "PROFESOR actualizad@" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;
