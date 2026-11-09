<?php
    $utiles_version = 1.19 ;
    session_start() ;
    date_default_timezone_set('America/Argentina/Buenos_Aires') ;

    $get    = (object)$_GET ;
    $post   = (object)$_POST ;
    $sesion = (object)$_SESSION ;
    $url    = (object)$_GET ;
    $form   = (object)$_REQUEST ;

    function carpetaPublica() {
        return $_SERVER["DOCUMENT_ROOT"] ;
    }
    $carpetaPublica = carpetaPublica() ;
    function carpetaBase() {
        return "$_SERVER[DOCUMENT_ROOT]/.." ;
    }
    $carpetaBase = carpetaBase() ;

    function redirigir( $nuevaRuta ) {
        header( "Location:$nuevaRuta" ) ;
        exit() ;
    }
    function redirigirConError( $nuevaRuta, $error="Error" ) {
        $_SESSION["error"] = $error ;
        redirigir( $nuevaRuta ) ;
    }

    class Programa {

        public $ultimaCondicion = false ;
        public $ultimoValor = null ;
        
        function decir( $mensaje, ...$otros ) {
            $mensaje = str_replace( "TANTOS", "%s", $mensaje ) ;
            $mensaje = str_replace( "TANTAS", "%s", $mensaje ) ;
            $mensaje = str_replace( "TANTO", "%s", $mensaje ) ;
            $mensaje = str_replace( "TANTA", "%s", $mensaje ) ;
            printf( $mensaje, ...$otros ) ;
            return $this ;
        }

        function preguntar( $mensaje, &$variable=false, $sep=":" ) {
            $this->ultimoValor = readline( "$mensaje$sep " ) ;
            if( func_num_args() > 1 ):
                $variable = $this->ultimoValor ;
            endif;
            return $this ;
        }

        function asignar( $valor, &$variable=false ) {
            $this->ultimoValor = $valor ;
            if( $variable !== false ):
                $variable = $valor ;
            endif;
            return $this ;
        }

        function calcular( $valor ) {
            $this->ultimoValor = $valor ;
            return $this ;
        }

        function en( &$variable ) {
            if( func_num_args() == 0 ):
                printf( "ERROR, variable no encontrada...\n" ) ;
                $this->ultimoValor = null ;
            else:
                $variable = $this->ultimoValor ;
            endif;
            return $this ;
        }

        function enter() {
            print "\n" ;
            return $this ;
        }

        function pausa( $mensaje = "Presioná ENTER para continuar..." ) {
            if( is_numeric($mensaje) ):
                sleep( $mensaje ) ;
            else:
                readline( $mensaje ) ;
            endif;
            return $this ;
        }

        function decirSi( $condicion, $mensaje, ...$otros ) {
            $this->ultimaCondicion = $condicion ;
            if( $condicion ):
                return $this->decir( $mensaje, ...$otros ) ;
            endif;
            return $this ;
        }

        function enCasoContrarioDecir( $mensaje, ...$otros ) {
            if( !$this->ultimaCondicion ):
                return $this->decir( $mensaje, ...$otros );
            endif;

            return $this ;
        }

        function incluir( $archivo ) {
            include $archivo ;
            return $this ;
        }

        function __toString() {return "";}

    }

    class Frase {
        public $f = "" ;
        public function __construct($data) {
            $this->f = $data ;
            return $this ;
        }
        public function incluye( $subfrase ) {
            return stripos( $this->f, $subfrase ) !== false ;
        }
        public function donde( $subfrase ) {
            return stripos( $this->f, $subfrase ) ;
        }
        public function izq( $subfraseOnumero ) {
            if( is_numeric($subfraseOnumero) )
                return substr( $this->f, 0,$subfraseOnumero ) ;
            return strtolower(substr($this->f,0,strlen($subfraseOnumero))) == strtolower($subfraseOnumero) ;
        }
        public function der( $subfraseOnumero ) {
            if( is_numeric($subfraseOnumero) )
                return substr( $this->f, strlen($this->f)-$subfraseOnumero ) ;
            return strtolower(
                substr( $this->f, strlen($this->f)-strlen($subfraseOnumero) )
            ) == strtolower($subfraseOnumero);
        }

        public function extraer( $patron ) {
            preg_match( $patron, $this->f, $resultados ) ;
            return $resultados ;
        }
        public function separar( $separador="" ) {
            return explode( $separador, $this->f ) ;
        }
        public function __toString() {
            return $this->f ;
        }
    }
    function frase($x="") { return new Frase($x) ; }
    function fr($x="") { return new Frase($x) ; }

  class BaseDatos extends PDO {
    public $archivo = "" ;
    public $error = "" ;
    public function __construct( $dadoArchivo ) {
      $f = trim( $dadoArchivo ) ;
      if( !file_exists($f) ):
        if( file_exists(carpetaPublica()."/$f") ):
            $f = carpetaPublica() . "/$f" ;
        else:
            if( file_exists(carpetaBase()."/$f") ):
                $f = carpetaBase() . "/$f" ;
            else:
                $f = $f ;
            endif;
        endif;
      endif;
      $this->archivo = $f ;
      parent::__construct( "sqlite:$this->archivo" ) ;
      return $this ;
    }
    public function recuperar( $dadaReglaSQL, ...$dadosTantos ) {
      // Ayuda: Reglas como SELECT y PRAGMA 
      $sql = preg_replace( '/TANTOS|TANTAS|TANTO|TANTA/', '?', $dadaReglaSQL );
      try {
        $intento = $this->prepare( $sql ) ;
        if( !$intento ) 
          return $this->informe( $sql, $this->errorInfo()[2], [] );
        $procede = $intento->execute( ...$dadosTantos );
        $items = $intento->fetchAll( PDO::FETCH_OBJ ) ;
        return $this->informe( $sql, "", $items ); 
      }
      catch( Exception $error ) {
        return $this->informe( $sql, $error->getMessage(), [] );
      }
    }
    public function informe( $sql, $error, $registros, $cant=false ) {
      return (object)[
          "sql"      => $sql
        , "error"    => $error
        , "archivo"  => $this->archivo
        , "items"    => $registros
        , "cantidad" => !$cant ? count( $registros ) : $cant
        , "json"     => json_encode( $registros ) 
      ];
    }
    public function ejecutar( $dadaReglaSQL, ...$dadosTantos ) {
      // Ayuda: Reglas como INSERT INTO, UPDATE, DELETE
      $sql = preg_replace( '/TANTOS|TANTAS|TANTO|TANTA/', '?', $dadaReglaSQL );
      try {
        $intento = $this->prepare( $sql ) ;
        if( !$intento )
          return $this->informe( $sql, $this->errorInfo()[2], [] );
        $procede = $intento->execute( ...$dadosTantos );
        return $this->informe( $sql, "", [], $intento->rowCount() ); 
      }
      catch( Exception $error ) {
        return $this->informe( $sql, $error->getMessage(), [] );
      }
    }
    public function tablas() {
      return $this->recuperar( "SELECT * FROM sqlite_master WHERE type='TABLE'" ); 
    }
    public function crear( $dadoNombreTabla, $dadosCampos, $siNoExiste=false ) {
      $sql = "CREATE TABLE " ;
      if( $siNoExiste ) $sql = "$sql IF NOT EXISTS " ;
      $sql = "$sql $dadoNombreTabla " ;
      $lista = [] ;
      foreach( $dadosCampos as $cadaCampo => $cadaValor ):
        $lista[] = "$cadaCampo $cadaValor" ;
      endforeach ;
      $campos = implode( ",", $lista );
      $sql = "$sql( $campos );" ;
      return $this->recuperar( $sql );
    }
    public function insertar( $dadoNombreTabla, $dadosCampos ) {
      $sql = "INSERT INTO $dadoNombreTabla " ;
      $campos = [] ;
      $valores = [] ; $tantos = [] ;
      foreach( $dadosCampos as $clave => $valor ):
        $campos[] = $clave ;
        $valores[] = $valor ;
        $tantos[] = "?" ;
      endforeach ;
      $sql = $sql . "(" . implode(",", $campos) . " ) VALUES " ;
      $sql = $sql . "(" . implode(",", $tantos) . " );" ;
      return $this->ejecutar( $sql, ...$valores );
    }
    public function actualizar( $dadoNombreTabla, $dadaCondicion, $dadosCampos ){
      $sql = "UPDATE $dadoNombreTabla SET " ;
      $campos = [] ;
      $valores = [] ;
      foreach( $dadosCampos as $clave => $valor ):
        $campos[] = "$clave=?" ;
        $valores[] = $valor ;
      endforeach ;
      $sql = $sql . implode(",", $campos) ;
      $sql = "WHERE $dadaCondicion ;" ;
      return $this->ejecutar( $sql, ...$valores );
    }
  }

    function bdRecuperar( $sql, ...$d ) {
        global $archivo ;
        return (new BaseDatos($archivo))->recuperar($sql, ...$d) ;
    }

    function bdEjecutar( $sql, ...$d ) {
        global $archivo ;
        return (new BaseDatos($archivo))->ejecutar($sql, ...$d) ;
    }

    class Coleccion {
        public $colecta = [] ;
        public function __construct($colecta) {
            $this->colecta = $colecta ;
            return $this ;
        }
        public function get( $clave, $siNoExiste=null ) {
            if( !isSet($this->colecta[$clave]) ) return $siNoExiste ;
            return $this->colecta[$clave] ;
        }
        public function set( $clave, $nuevoValor ) {
            $this->colecta[$clave] = $nuevoValor ;
            return $this ;
        }
        public function pop( $clave ) {
            if( isSet($this->colecta[$clave]) ):
                $r = $this->colecta[$clave] ;
                unset( $this->colecta[$clave] ) ;
                return $r ;
            else:

            endif;
        }

    }

    function programa() {
        return new Programa() ;
    }
    function continuar() {
        return new Programa() ;
    }
    function baseDatos($archivo) {
        return new BaseDatos($archivo) ;
    }
    function decir( $mensaje, ...$datos ) {
        return Programa()->decir( $mensaje, ...$datos ) ;
    }


    function iterar( $lista, $archivo ) {
        $items = $lista ;
        $existeArchivo = is_string($archivo) and file_exists($archivo) ;
        foreach( $items as $orden => $x ):
            if( is_string($archivo) ):
                if( stripos($archivo,"%") !== false ):
                    printf( $archivo, $x ) ;
                else:
                    if( $existeArchivo ):
                        include $archivo;
                    else:
                        printf( $archivo ) ;
                    endif;
                endif;
            else:
                $archivo( $x, $orden, $items ) ;
            endif;
        endforeach ;
    }

    function incluir( $archivo, $x=null ) {
        ob_start() ;
        if($x!==null) extract($x) ;
        include $archivo ;
        $r = ob_get_contents() ;
        ob_get_clean() ;
        return $r ;
    }

    function transformar( $lista, $archivo, ...$y ) {
        $retornar = [] ;
        $items = $lista ;
        $existeArchivo = is_string($archivo) and file_exists($archivo) ;
        foreach( $items as $orden => $x ):
            if( is_string($archivo) ):
                if( stripos($archivo,"%") !== false ):
                    $retornar[] = sprintf( $archivo, ...$y ) ;
                else:
                    if( $existeArchivo ):
                        $retornar[] = incluir( $archivo, compact("orden","x") );
                    else:
                        $retornar[] = sprintf( $archivo, ...$x->$y) ;
                    endif;
                endif;
            else:
                $retornar[] = $archivo( $x, $orden, $items ) ;
            endif;
        endforeach ;
        return $retornar ;
    }

    class Sesiones {
        static public function agregar( $clave, $valor ) {
            $_SESSION[$clave] = $valor ;
        }
        static public function cambiar( $clave, $valor ) {
            $_SESSION[$clave] = $valor ;
        }
        static public function ver( $clave, $siNoHay=null ) {
            if( $siNoHay === null )
                return $_SESSION[$clave] ;
            return isSet($_SESSION[$clave]) ? $_SESSION[$clave] : $siNoHay ;
        }
        static public function existe( $clave ) {
            return isset( $_SESSION[$clave] ) ;
        }
        static public function borrar( $clave, $confirmacion=false ) {
            if( !$confirmacion ) return ;
            unset( $_SESSION[$clave] ) ;
        }
        static public function pop( $clave ) {
            $r = $_SESSION[$clave] ;
            unset( $_SESSION[$clave] ) ;
            return $r ;
        }
    }
    function sesiones() {
        return new Sesiones();
    }

    class HTML {
        public $etiqueta = "div" ;
        public $props = [] ;
        public $cont = "" ;

        public function __construct( $etiqueta ) {
            $this->etiqueta = $etiqueta ;
            return $this ;
        }
        public function atrib( $nombre, $valor ) {
            $this->props[$nombre] = $valor ;
            return $this ;
        }
        public function data( $nombre, $valor ) {
            $this->props["data-" . $nombre] = $valor ;
            return $this ;
        }
        
        public function atribs( $datos ) {
            foreach( $datos as $clave => $valor ):
                $this->props[$clave] = $valor ;
            endforeach ;
            return $this ;
        }
        public function id($nuevoId=null) {
            if( $nuevoId===null ) return $this->props["id"] ;
            return $this->atrib( "id", $nuevoId ) ;
        }
        public function cierre() {
            return sprintf( "</%s>\n", $this->etiqueta ) ;
        }
        public function abre() {
            $atribs = '' ;
            foreach( $this->props as $clave => $valor ):
                $atribs = sprintf( '%s %s="%s"', $atribs, $clave, $valor ) ;
            endforeach ;
            return sprintf( "<%s %s>\n", $this->etiqueta, $atribs ) ;
        }

        public function contenido( ...$valor ) {
            if( $valor === null ) return $this->cont ;
            $this->cont = "" ;
            foreach( $valor as $orden => $cadaValor ):
                $this->cont = $cadaValor ;
            endforeach ;
            return $this ;
        }

        public function __toString() {
            $atribs = '' ;
            foreach( $this->props as $clave => $valor ):
                $atribs = sprintf( '%s %s="%s"', $atribs, $clave, $valor ) ;
            endforeach ;
            return sprintf( "<%s %s>\n%s</%s>\n", $this->etiqueta, $atribs, $this->cont, $this->etiqueta ) ;
        }
    }
    function HTML( $v ) {
        return new HTML( $v ) ;
    }

    class Ancla extends HTML {
        public $destino = "" ;
        public function __construct( $mensaje ) {
            parent::__construct( "a" ) ;
            if( is_array($mensaje) ):
                $c = count($mensaje) ;
                if( $c==2 ):
                    $a1 =( new HTML("h2"))->contenido($mensaje[0]) ;
                    $a2 = (new HTML("p"))->contenido($mensaje[1]) ;
                    $mensaje = $a1 . $a2 ;
                elseif( $c == 3 ):
                    $a1 = Imagen($mensaje[0]);
                    $a2 = (new HTML("h2"))->contenido($mensaje[1]) ;
                    $a3 = (new HTML("p"))->contenido($mensaje[2]) ;
                    $mensaje = $a1.$a2.$a3;
                else:
                    $mensaje = $mensaje[0] ;
                endif;
            endif;
            $this->cont = $mensaje ;
            $this->atrib( "data-ajax", "false" ) ;
            return $this ;
        }
        public function hacia( $destino ) {
            $this->destino = $destino ;
            return $this->atrib("href", $destino) ;
        }
        public function objetivo( $destino="_blank" ) {
            return $this->atrib( "target", $destino ) ;
        }
        public function javascript( $codigo ) {
            return $this->hacia( "javascript:$codigo" ) ;
        }
        public function descarga( $archivo="" ) {
            return $this->atrib( "download", $archivo ) ;
        }
        public function boton() {
            $this->atrib( "onclick", sprintf("location.href=`%s`", $this->destino ) );
            $this->etiqueta = "button" ;
            return $this ;
        }
        public function icono($nombre="home") {
            return $this->data("icon", $nombre) ;
        }
    }
    function Ancla($mensaje) {
        return new Ancla($mensaje) ;
    }

    class Formulario extends HTML {
        public function __construct() {
            parent::__construct( "form" ) ;
            return $this->atrib( "data-ajax", "false" ) ;
        }
        public function hacia( $destino ) {
            return $this->atrib("action", $destino) ;
        }
        public function metodo( $getOpostOarchivo="get" ) {
            return $this->atrib( "method", $getOpostOarchivo ) ;
        }
        public function paraArchivos() {
            $this->metodo( "post" ) ;
            return $this->atrib( "enctype", "multipart/form-data" ) ;
        }
        static function vieneDeUno($nameExistente="") {
            if( $_SERVER["REQUEST_METHOD"]=="POST" or ($_SERVER["REQUEST_METHOD"]=="GET" and !empty($_GET)) )
                return true ;
            return false ;
        }
    }
    function Formulario() {
        return new Formulario() ;
    }

    class JSON {
        static function codificar( $objeto ) {
            return json_encode( $objeto, null, 4 ) ;
        }
        static function generar( $objeto ) {
            return json_encode( $objeto, null, 4 ) ;
        }
        static function decodificar( $frase ) {
            return json_decode( $data ) ;
        }
        static function interpretar( $frase ) {
            return json_decode( $data ) ;
        }
    }

    class HTMLSinCierre extends HTML {
        public function __toString() {
            $atribs = '' ;
            foreach( $this->props as $clave => $valor ):
                $atribs = sprintf( '%s %s="%s"', $atribs, $clave, $valor ) ;
            endforeach ;
            return sprintf( "<%s %s />\n", $this->etiqueta, $atribs ) ;
        }
    }
    function HTMLSinCierre() {return new HTMLSinCierre();}

    class Vinculo extends HTMLSinCierre {
        public function __construct( $objetivo="" ) {
            parent::__construct( "link" ) ;
            if( !empty($objetivo) ) $this->hacia($objetivo) ;
            return $this ;
        }
        public function relacion( $r="stylesheet" ) {
            return $this->atrib( "rel", $r ) ;
        }
        public function hojaDeEstilos() {
            return $this->relacion() ;
        }
        public function icono() {
            return $this->relacion( "shortcut icon" ) ;
        }
        public function hacia( $d ) {
            return $this->atrib( "href", $d ) ;
        }
    }
    function Vinculo($objetivo="") {
        return new Vinculo($objetivo) ;
    }

    class Imagen extends HTMLSinCierre {
        public function __construct($ruta="") {
            parent::__construct("img") ;
            if( !empty($ruta) ) $this->origen($ruta) ;
            return $this ;
        }
        public function origen($dadaRuta) {
            return $this->atrib( "src", $dadaRuta ) ;
        }
        public function ancho( $n ) { return $this->atrib("width", $n) ;}
        public function alto( $n ) { return $this->atrib("height", $n) ;}
    }
    function Imagen( $archivo ) {
        return new Imagen($archivo);
    }

    class HEAD extends HTML {
        public $titulo = "" ;
        public $responsive = false ;
        public $csss = [] ;
        public $jss = [] ;
        public $escalable = "no" ;
        public $color = "" ;
        public $metadatos = [] ;
        public $ancho = "device-width" ;

        public function __construct( $titulo="" ) {
            $this->titulo = $titulo ;
            $this->etiqueta = "body" ;
            return $this->instalar() ;
        }
        
        public function __toString() {
            $mtd = "" ;
            foreach( $this->metadatos as $name => $content ):
                $mtd .= sprintf( "<meta name='%s' content='%s' />\n", $name, $content ) ;
            endforeach ;
            $s = sprintf("<html lang='es'>
    <head >
        <title>%s</title>
        %s %s %s
        <meta name='viewport' content='width=%s,user-scalable=%s' />
        <meta charset='utf-8' />
    </head>
    %s\n"
                , $this->titulo
                , implode( "\n", array_map( function($x){return "<link rel='stylesheet' href='$x' />";}, $this->csss ) )
                , implode( "\n", array_map( function($x){return"<script src='$x' ></script>";}, $this->jss ) )
                , $mtd
                , $this->ancho
                , $this->escalable
                , $this->abre()
            );
            return $s ;
        }

        public function autor( $nombre ) {
            $this->metadatos["author"] = $nombre ;
            return $this ;
        }
        public function descripcion( $desc ) {
            $this->metadatos["description"] = $desc ;
            return $this ;
        }
        public function color( $color ) {
            $this->metadatos["theme-color"] = $color ;
            return $this ;
        }
        public function ancho( $ancho="600px" ) {
            $this->ancho = $ancho ;
            return $this ;
        }
        public function simple($color="blue") {
            $this->csss[] = "https://gorosito.red/dame/simple.php?color=$color" ;
            return $this ;
        }
        public function imprimir() {
            printf( $this->__toString() ) ;
        }
        public function css($ruta) {
            $this->csss[] = $ruta ;
            return $this ;
        }
        public function js($ruta) {
            $this->jss[] = $ruta ;
            return $this ;
        }

        public function instalar() {
            return $this ;
        }
        public function habilitar() {
            return $this ;
        }
    }
    function inicio($titulo="Mi App") {
        return new HEAD($titulo) ;
    }
    function inicioSimple($titulo="Mi app",$color="purple") {
        return (new HEAD($titulo))->simple($color) ;
    }

    function fin($tag=null) {
        if( $tag !== null )
            return "</$tag>\n" ;
        return "    </body>\n</html>" ;
    }

    function linkCSS( $ruta ) {
        return sprintf( "<link rel='stylesheet' href='%s' />", $ruta ) ;
    }
    function scriptJS( $ruta ) {
        return sprintf( "<script src='%s' ></script>", $ruta ) ;
    }

    class JQM extends HEAD {
        public function __construct($tema=null) {
            if( !!$tema ):
                $this->data("theme", $tema) ;
            endif;
            parent::__construct();
            ;
        }

        public function instalar() {
            return $this
                ->css("https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.css") 
                ->css("https://gorosito.red/bloques/apps/vendor/themes/themario2.min.css")
                ->js("https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js")
                ->js("https://ajax.googleapis.com/ajax/libs/jquerymobile/1.4.5/jquery.mobile.min.js")
            ;
        }

        static public function header( $titulo, $boton1="", $boton2="" ) {
            return sprintf('<header data-role="header" data-position="fixed" >
    <h1>%s</h1>
    %s
    %s
</header>', $titulo, $boton1, $boton2) ;
        }

        static public function footer( $titulo ) {
            if( is_string($titulo) ):
                return sprintf( 
                    '<footer data-role="footer" data-position="fixed" ><h3>%s</h3></footer>'
                    , $titulo 
                );
            else:
                $lis = "" ;
                foreach( $titulo as $cadaVinculo ):
                    $lis .= "<li>$cadaVinculo</li>\n" ;
                endforeach;
                return sprintf( '
<footer data-role="footer" data-position="fixed" >
    <div data-role="navbar" >
        <ul>
%s
        </ul>
    </div>
</footer>
'               , $lis ) ;
            endif;
        }

        static public function listview( $texto, $lista=null ) {
            if( $lista === null ):
                $lista = $texto ;
                $filtro = "Qué buscar" ;
            else:
                $filtro = $texto ;
            endif;
            $r = "" ;
            foreach( $lista as $cadaItem ):
                $comoFrase = $cadaItem ;
                if( frase($comoFrase)->incluye("data-icon=\"") ):
                    $icono = 'data-icon="' . frase($comoFrase)->extraer('/data-icon="([^"]+)/')[1] . '"' ;
                elseif( frase($comoFrase)->incluye("data-icon='") ):
                    $icono = 'data-icon="' . frase($comoFrase)->extraer("/data-icon='([^']+)/")[1] . '"' ;
                else:
                    $icono = "" ;
                endif;
                $r .= sprintf("<li $icono>$comoFrase</li>\n");
            endforeach ;
            return sprintf(
'<ul data-role="listview" data-inset="true" data-filter="true" data-filter-placeholder="%s" >
    %s
</ul>'
                , $filtro
                , $r
            );
        }

        static public function colapsable( $titulo, $contenido, $colapsado=false ) {
            return sprintf('<div data-role="collapsible" %s >
    <h3>%s</h3>
    <div>%s</div>
</div>', $colapsado ? "data-collapsed='true'" :"", $titulo, $contenido);
        }

        static public function colapsables(...$colapsables) {
            return sprintf("<div data-role='collapsible-set' >\n\t$colapsables\n</div>\n") ;
        }
    }

    class BootstrapCSS extends HEAD {
        public function instalar() {
            return $this
                ->css("https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css")
                ->js("https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js")
            ;
        }
    }

    class TailwindCSS extends HEAD {
        public function instalar() {
            return $this->js("https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4") ;
        }
    }

    class BulmaCSS extends HEAD {
        public function instalar() {
            return $this->css("https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css") ;
        }
    }

    class W3CSS extends HEAD {
        public function instalar() {
            return $this->css("https://www.w3schools.com/w3css/5/w3.css") ;
        }
    }

    class HTMLInput extends HTMLSinCierre {
        public $id = "" ;
        public $name = "" ;
        public $tipo = "text" ;
        public $multirenglon1 = false ;
        public function __construct( $id, $tipo="text", $mismoName=false ) {
            parent::__construct("input") ;
            $this->id = $id ;
            if( $mismoName ):
                $this->name = $id ;
                $this->atrib( "name", $id ) ;
            endif;
            
            $this->tipo = $tipo ;
            $this->atrib( "type", $tipo ) ;
            $this->atrib( "id", $this->id ) ;
            
            return $this ;
        }
        public function multirenglon( $cant=null ) {
            $this->tipo = "textarea" ;
            $this->etiqueta = "textarea" ;
            $this->multirenglon1 = true ;
            if( $cant !== null ):
                $this->atrib( "rows", $cant ) ;
            endif;
            return $this->atrib( "type", "textarea" ) ;
        }
        public function guia( $placeholder ) {
            return $this->atrib( "placeholder", $placeholder ) ;
        }
        public function obligatorio() {
            return $this->atrib( "required", 1 ) ;
        }
        public function deshabilitado() {
            return $this->atrib( "disabled", 1 ) ;
        }
        public function enfocado() {
            return $this->atrib( "autofocus", 1 ) ;
        }
        public function valor( $valor="" ) {
            return $this->atrib( "value", $valor ) ;
        }
        public function nombre( $nuevoName ) {
            $this->name = $nuevoName ;
            return $this->atrib( "name", $nuevoName ) ;
        }
        public function contexto( $etiqueta, $conBreak=false, $contenedor="article" ) {
            $s = '<%s >
    <label for="%s" >%s</label>%s
    %s
</%s>';
            return sprintf( $s
                , $contenedor, $this->id, $etiqueta
                , $conBreak ? "<br />" : ""
                , $this->multirenglon1 
                    ? $this->abre() . $this->cierre()
                    :$this->__toString()
                , $contenedor
            );
        }
    }
    function entrada( $nombre, $tipo="text" ) {
        return new HTMLInput( $nombre, $tipo ) ;
    }

    class Tabla extends BaseDatos {
        public $tabla = "" ;
        public $id = "id" ;

        public function __construct( $dadaTabla, $dadaId = false ) {
            $this->tabla = $dadaTabla ;
            if( $dadaId ) $this->id = $dadaId ;
            return $this ;
        }
        public function archivo( $dadoNombreArchivo ) {
            parent::__construct( "$dadoNombreArchivo" ) ;
            return $this ;
        }
        public function todos() {
            return $this->recuperar( "SELECT * FROM $this->tabla" ) ;
        }
        public function verId( $dadaID ) {
            return $this->recuperar( "SELECT * FROM $this->tabla WHERE $this->id = '$dadaID'" ) ;
        }
        public function buscar( $dadoCampo, $dadoValor, $dadaCondicion="=" ) {
            $cond = $dadaCondicion ;
            return $this->recuperar( "SELECT * FROM $this->tabla WHERE $dadoCampo $cond '$dadoValor'" ) ;
        }
        public function cambiar( $condicion, $nuevosValores=[] ) {
            $l = [] ;
            foreach( $nuevosValores as $clave=>$valor ):
                $l[] = sprintf( "%s='%s'", $clave, $valor ) ;
            endforeach ;
            $lista = implode( " ", $l ) ;
            $s = "UPDATE $this->tabla SET $lista WHERE $condicion" ;
            return $this->ejecutar( $s ) ;
        }
        public function borrar( $condicion, $confirmacion=false ) {
            if( !$confirmacion ) return (object)["error" => "No está confirmado"] ;
            $s = "DELETE FROM $this->tabla WHERE $condicion" ;
            return $this->ejecutar( $s ) ;
        }
    }