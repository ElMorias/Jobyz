<?php
class Validators
{
    // esto lo que hace es crear una clase validacion con un array de errores
    //en php se llama  a la clase cuando queramos validar alog, y cuando se haya hecho
    //pasa el arrat con todos los erroes
    private $errores;

    public function __construct()
    {
        $this->errores = array();
    }

    public function enteroRango($campo, $MIN=PHP_INT_MIN, $MAX=PHP_INT_MAX)
    {
        if (!filter_var($_POST[$campo], FILTER_VALIDATE_INT, array("options" => array("min_range" => $MIN, "max_range" => $MAX)))) {
            $this->errores[] = "El campo no es un entero en el rango permitido.";
        }
    }


}

?>