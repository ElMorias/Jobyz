<?php
class Validators
{
    private $errores = [];
    private $userRepo;
    private $alumnoRepo;
    private $empresaRepo; // si usas uno diferente para alumnos
    // Puedes añadir más repos como properties si tienes varias entidades

    public function __construct()
    {
        $this->userRepo = new RepositorioUser;
        $this->alumnoRepo = new RepositorioAlumno;
        $this->empresaRepo = new RepositorioEmpresa;
    }

    // ----------- Validaciones generales -----------

    public function validarCorreo($correo)
    {
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 80) {
            $this->errores[] = 'El correo electrónico es obligatorio y debe ser válido.';
        }
    }

    public function validarContrasenaEdicion($contrasena){
        if (strlen($contrasena) < 8 || strlen($contrasena) > 60 ||
            !preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/', $contrasena)) {
            $this->errores[] = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.';
        }
    }

    public function validarContrasena($contrasena, $repetir_contrasena)
    {
        if (empty($contrasena) || strlen($contrasena) < 8 || strlen($contrasena) > 60 ||
            !preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/', $contrasena)) {
            $this->errores[] = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.';
        }
        if ($contrasena !== $repetir_contrasena) {
            $this->errores[] = 'Las contraseñas no coinciden.';
        }
    }

    public function validarNombre($nombre)
    {
        if (empty($nombre) || strlen($nombre) > 60) {
            $this->errores[] = 'El nombre es obligatorio y máximo 60 caracteres.';
        }
    }

    public function validarDireccion($direccion)
    {
        if (empty($direccion) || strlen($direccion) > 80) {
            $this->errores[] = 'La dirección es obligatoria y máximo 80 caracteres.';
        }
    }

    public function validarCif($cif)
    {
        $cif = strtoupper(trim($cif));
        if (empty($cif) || strlen($cif) > 12 || !preg_match('/^[A-Z][0-9]{7}[A-Z0-9]$/', $cif)) {
            $this->errores[] = 'El CIF es obligatorio y debe tener un formato válido.';
        }
    }

    public function validarDni($dni)
    {
        $dni = strtoupper(trim($dni));
        if (empty($dni) || !preg_match('/^\d{8}[A-Za-z]$/', $dni)) {
            $this->errores[] = 'El DNI debe tener un formato válido (12345678A).';
        }
    }

    public function validarTelefono($telefono)
    {
        if (empty($telefono) || !preg_match('/^[0-9]{9,15}$/', $telefono)) {
            $this->errores[] = 'El teléfono debe tener entre 9 y 15 dígitos.';
        }
    }

    // ----------- Validaciones de unicos -----------

    public function validarUnicidadCorreo($correo)
    {
        if ($this->userRepo && $this->userRepo->existeCorreo($correo)) {
            $this->errores[] = 'El correo ya existe en el sistema.';
        }
    }

    public function validarUnicidadCorreoContacto($correo)
    {
        if ($this->empresaRepo && $this->empresaRepo->existeCorreo($correo)) {
            $this->errores[] = 'El correo ya existe en el sistema.';
        }
    }


    public function validarUnicidadDni($dni)
    {
        if ($this->alumnoRepo && $this->alumnoRepo->existeDni($dni)) {
            $this->errores[] = 'El DNI ya está registrado.';
        }
    }

    public function validarUnicidadTelefono($telefono)
    {
        if ($this->empresaRepo && $this->empresaRepo->existeTelefono($telefono)) {
            $this->errores[] = 'El teléfono ya está registrado.';
        }else{
            if ($this->alumnoRepo && $this->alumnoRepo->existeTelefono($telefono)) {
                $this->errores[] = 'El teléfono ya está registrado.';
            }
        }
    }

    public function validarUnicidadCif($cif)
    {
        if ($this->empresaRepo && $this->empresaRepo->existeCif($cif)) {
            $this->errores[] = 'Ese CIF ya existe en la base de datos.';
        }
    }

    // ----------- Validación lógica fechas -----------

    public function validarEdad($fechaNacimiento)
    {
        if (!empty($fechaNacimiento)) {
            $edad = (new DateTime($fechaNacimiento))->diff(new DateTime('now'))->y;
            if ($edad < 18) {
                $this->errores[] = 'Debes ser mayor de edad (18+).';
            }
        }
    }

    public function validarFechasEstudios($fechas)
    {
        // (int) es un cast a las fechas para que se comparen como numeros
        $anyoActual = (int)date('Y');
        foreach ($fechas as $fecha) {
            $anyo = (int)substr($fecha, 0, 4);
            if ($anyo > $anyoActual) {
                $this->errores[] = 'No puedes registrar estudios en un año posterior al actual.';
                break;
            }
        }
    }

    // ----------- Validación principal (puedes personalizar estos métodos según contexto) -----------

    public function validarAlumnoRegistro($datos)
    {
        $this->errores = []; // limpia errores

        $this->validarCorreo($datos['correo'] ?? '');
        $this->validarContrasena($datos['contrasena'] ?? '', $datos['repetir_contrasena'] ?? '');
        $this->validarNombre($datos['nombre'] ?? '');
        $this->validarDni($datos['dni'] ?? '');
        $this->validarTelefono($datos['telefono'] ?? '');
        $this->validarDireccion($datos['direccion'] ?? '');

        // UNICIDAD
        $this->validarUnicidadCorreo($datos['correo'] ?? '');
        $this->validarUnicidadDni($datos['dni'] ?? '');
        $this->validarUnicidadTelefono($datos['telefono'] ?? '');

        // FECHAS
        $this->validarEdad($datos['fnacimiento'] ?? '');

        // Estudios (opcional, si llegan por POST como array)
        if (isset($datos['fechainicio']) && is_array($datos['fechainicio'])) {
            $this->validarFechasEstudios($datos['fechainicio']);
        }

        return $this->errores;
    }

    public function validarAlumnoEdicion($datos){
        $this->errores = [];

        // No validamos correo (no se modifica)
        $this->validarNombre($datos['nombre'] ?? '');
        $this->validarDni($datos['dni'] ?? '');
        $this->validarTelefono($datos['telefono'] ?? '');
        $this->validarDireccion($datos['direccion'] ?? '');

        // Cambia 'id' por el campo real del ID en tus datos
        $id = $datos['id'] ?? null;

        // Validaciones de unicidad SOLO si permiten editar esos campos:
        if (!empty($datos['dni']) && $this->alumnoRepo->existeDniExceptoId($datos['dni'], $id)) {
            $this->errores[] = 'El DNI ya está registrado por otro alumno.';
        }
        if (!empty($datos['telefono']) && $this->alumnoRepo->existeTelefonoExceptoId($datos['telefono'], $id)) {
            $this->errores[] = 'El teléfono ya está registrado por otro alumno.';
        }

        // Solo valida nueva contraseña si el usuario la cambia
        if (!empty($datos['contrasena'])) {
            // Ya NO hay repetir contraseña
            $this->validarContrasenaEdicion($datos['contrasena']);
        }

        // Fechas (edad mínima, estudios)
        $this->validarEdad($datos['fnacimiento'] ?? '');
        if (isset($datos['fechainicio']) && is_array($datos['fechainicio'])) {
            $this->validarFechasEstudios($datos['fechainicio']);
        }

        return $this->errores;
    }


    public function validarEmpresa($datos)
    {
        $this->errores = [];
        $this->validarCorreo($datos['correo'] ?? '');
        $this->validarCorreo($datos['pcontactoemail'] ?? '');
        $this->validarContrasena($datos['contrasena'] ?? '', $datos['repetir_contrasena'] ?? '');
        $this->validarNombre($datos['nombre'] ?? '');
        $this->validarDireccion($datos['direccion'] ?? '');
        $this->validarCif($datos['cif'] ?? '');

        // UNICIDAD
        $this->validarUnicidadCorreo($datos['correo'] ?? '');
        $this->validarUnicidadCif($datos['cif'] ?? '');
        $this->validarUnicidadTelefono($datos['tfcontacto'] ?? '');
        $this->validarUnicidadCorreoContacto($datos['pcontactoemail'] ?? '');

        // ...lo demás según tus reglas...
        return $this->errores;
    }

    public function validarEmpresaEdicion($datos){
        $this->errores = [];

        // Validar formato de campos modificables
        $this->validarCorreo($datos['pcontactoemail'] ?? '');
        $this->validarNombre($datos['nombre'] ?? '');
        $this->validarDireccion($datos['direccion'] ?? '');
        $this->validarCif($datos['cif'] ?? '');

        // ID de la empresa que se edita
        $id = $datos['id'] ?? null;

        // Unicidad excluyendo el propio registro
        if (!empty($datos['cif']) && $this->empresaRepo->existeCifExceptoId($datos['cif'], $id)) {
            $this->errores[] = 'El CIF ya está registrado por otra empresa.';
        }
        if (!empty($datos['tfcontacto']) && $this->empresaRepo->existeTelefonoExceptoId($datos['tfcontacto'], $id)) {
            $this->errores[] = 'El teléfono de contacto ya está registrado.';
        }
        if (!empty($datos['pcontactoemail']) && $this->empresaRepo->existeCorreoExceptoId($datos['pcontactoemail'], $id)) {
            $this->errores[] = 'El email de contacto ya está registrado en otra empresa.';
        }

        // Solo valida la contraseña si se ha introducido (y NO hay repetir_contrasena)
        if (!empty($datos['contrasena'])) {
            $this->validarContrasenaEdicion($datos['contrasena']);
        }

        return $this->errores;
    }

    public function getErrores() {
        return $this->errores;
    }
}

?>