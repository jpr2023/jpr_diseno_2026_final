<?php
    session_start() ;
    $form = (object)$_GET ;
    $reglaSQL = "   UPDATE T_TUTORES SET  
                    NOMBRE = '$form->nmNOMBRE'
                    ,TELEFONO = '$form->nmTEL'
                    ,DIRECCION = '$form->nmDIRECCION'
                    ,EMAIL = '$form->nmEMAIL'
                    WHERE ID = '$form->nmID'
" ;
    $base = new PDO( "sqlite:$_SERVER[DOCUMENT_ROOT]/../CURSADAS.db" ) ;
    $accion = $base->query( $reglaSQL ) ;
    if( $accion ):
        $_SESSION["mensaje"] = "Tutor actualizado" ;
        header( "location:../index.php" ) ;
    else:
        $_SESSION["error"] = $base->errorInfo()[2] ;
        header( "location:../index.php" ) ;
    endif;
