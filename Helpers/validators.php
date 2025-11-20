<?php
require_once dirname(__DIR__) . '/autoloader.php';

/**
 * Clase Validators
 * 
 * Encapsula y centraliza la validación de formularios y reglas de negocio para
 * las entidades Usuario, Alumno, Empresa, etc.
 * Se apoya en los repositorios para comprobaciones de unicidad y lógica cruzada.
 */
class Validators
{
    /** @var array Lista de mensajes de error producidos tras cada validación */
    private $errores = [];

    /** @var RepositorioUser */
    private $userRepo;
    /** @var RepositorioAlumno */
    private $alumnoRepo;
    /** @var RepositorioEmpresa */
    private $empresaRepo;

    /**
     * Instancia los repositorios requeridos para comprobaciones únicas.
     */
    public function __construct()
    {
        $this->userRepo = new RepositorioUser;
        $this->alumnoRepo = new RepositorioAlumno;
        $this->empresaRepo = new RepositorioEmpresa;
    }

    // ----------- Validaciones generales -----------

    /**
     * Valida que el correo sea correcto, no vacío y con formato.
     */
    public function validarCorreo($correo)
    {
        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 80) {
            $this->errores[] = 'El correo electrónico es obligatorio y debe ser válido.';
        }
    }

    /**
     * Valida la contraseña en edición (sin repetir)
     */
    public function validarContrasenaEdicion($contrasena)
    {
        if (strlen($contrasena) < 8 || strlen($contrasena) > 60 || !preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/', $contrasena)) {
            $this->errores[] = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.';
        }
    }

    /**
     * Valida que las contraseñas son seguras y coinciden (en registro)
     */
    public function validarContrasena($contrasena, $repetir_contrasena)
    {
        if (empty($contrasena) || strlen($contrasena) < 8 || strlen($contrasena) > 60 || !preg_match('/(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}/', $contrasena)) {
            $this->errores[] = 'La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número.';
        }
        if ($contrasena !== $repetir_contrasena) {
            $this->errores[] = 'Las contraseñas no coinciden.';
        }
    }

    /**
     * Valida que el nombre tenga el formato adecuado.
     */
    public function validarNombre($nombre)
    {
        if (empty($nombre) || strlen($nombre) > 60) {
            $this->errores[] = 'El nombre es obligatorio y máximo 60 caracteres.';
        }
    }

    /**
     * Valida la dirección postal.
     */
    public function validarDireccion($direccion)
    {
        if (empty($direccion) || strlen($direccion) > 80) {
            $this->errores[] = 'La dirección es obligatoria y máximo 80 caracteres.';
        }
    }

    /**
     * Comprueba formato y plausibilidad del CIF.
     */
    public function validarCif($cif)
    {
        $cif = strtoupper(trim($cif));
        if (empty($cif) || strlen($cif) > 12 || !preg_match('/^[A-Z][0-9]{7}[A-Z0-9]$/', $cif)) {
            $this->errores[] = 'El CIF es obligatorio y debe tener un formato válido.';
        }
    }

    /**
     * Valida incertidumbre y formato del DNI.
     */
    public function validarDni($dni)
    {
        $dni = strtoupper(trim($dni));
        if (empty($dni) || !preg_match('/^\d{8}[A-Za-z]$/', $dni)) {
            $this->errores[] = 'El DNI debe tener un formato válido (12345678A).';
        }
    }

    /**
     * Valida el campo teléfono: solo dígitos y longitud aceptada.
     */
    public function validarTelefono($telefono)
    {
        if (empty($telefono) || !preg_match('/^[0-9]{9,15}$/', $telefono)) {
            $this->errores[] = 'El teléfono debe tener entre 9 y 15 dígitos.';
        }
    }

    // ----------- Validaciones de unicidad (consultas a base de datos) -----------

    /**
     * Comprueba que el correo no esté ya registrado en users.
     */
    public function validarUnicidadCorreo($correo)
    {
        if ($this->userRepo && $this->userRepo->existeCorreo($correo)) {
            $this->errores[] = 'El correo ya existe en el sistema.';
        }
    }

    /**
     * Comprueba que el correo de contacto no esté ya en empresa.
     */
    public function validarUnicidadCorreoContacto($correo)
    {
        if ($this->empresaRepo && $this->empresaRepo->existeCorreo($correo)) {
            $this->errores[] = 'El correo ya existe en el sistema.';
        }
    }

    /**
     * Comprueba que el DNI no esté ya registrado.
     */
    public function validarUnicidadDni($dni)
    {
        if ($this->alumnoRepo && $this->alumnoRepo->existeDni($dni)) {
            $this->errores[] = 'El DNI ya está registrado.';
        }
    }

    /**
     * Comprueba que el teléfono no esté ya registrado ni en empresa ni en alumno.
     */
    public function validarUnicidadTelefono($telefono)
    {
        if ($this->empresaRepo && $this->empresaRepo->existeTelefono($telefono)) {
            $this->errores[] = 'El teléfono ya está registrado.';
        } else if ($this->alumnoRepo && $this->alumnoRepo->existeTelefono($telefono)) {
            $this->errores[] = 'El teléfono ya está registrado.';
        }
    }

    /**
     * Valida que el CIF sea único entre empresas.
     */
    public function validarUnicidadCif($cif)
    {
        if ($this->empresaRepo && $this->empresaRepo->existeCif($cif)) {
            $this->errores[] = 'Ese CIF ya existe en la base de datos.';
        }
    }

    // ----------- Validaciones adicionales: fechas/edad -----------

    /**
     * Valida que el usuario tenga al menos 18 años según fecha de nacimiento.
     */
    public function validarEdad($fechaNacimiento)
    {
        if (!empty($fechaNacimiento)) {
            $edad = (new DateTime($fechaNacimiento))->diff(new DateTime('now'))->y;
            if ($edad < 18) {
                $this->errores[] = 'Debes ser mayor de edad (18+).';
            }
        }
    }

    /**
     * Valida que las fechas de estudios no sean posteriores al año actual
     * y que la fecha de fin (si existe) no sea menor a la de inicio.
     * @param array $fechasInicio  Array de fechas tipo "YYYY-MM-DD" (inicio)
     * @param array|null $fechasFin Array de fechas tipo "YYYY-MM-DD" (fin)
    */
    public function validarFechasEstudios($fechasInicio, $fechasFin)
    {
        $anyoActual = (int)date('Y');

        // Verifica todas las fechas de inicio
        foreach ($fechasInicio as $i => $fi) {
            $anyo = (int)substr($fi, 0, 4);
            if ($anyo > $anyoActual) {
                $this->errores[] = 'No puedes registrar estudios en un año posterior al actual.';
                break;
            }

            // Si hay fechas de fin, verifica que no sea menor que la de inicio
            if ($fechasFin && !empty($fechasFin[$i])) {
                $fin = $fechasFin[$i];
                if ($fin < $fi) {
                    $this->errores[] = 'La fecha de fin de un estudio no puede ser anterior a la de inicio.';
                    // Puedes añadir info extra usando el índice $i si quieres saber el ciclo exacto
                }
            }
        }
    }


    // ----------- Validaciones compuestas/principales -----------

    /**
     * Validación completa para registro de alumno.
     * @param array $datos
     * @return array
     */
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

        // Fechas
        $this->validarEdad($datos['fnacimiento'] ?? '');

        // Estudios (si hay)
        if (isset($datos['fechainicio']) && is_array($datos['fechainicio'])) {
            $finArray = isset($datos['fechafin']) && is_array($datos['fechafin']) ? $datos['fechafin'] : [];
            $this->validarFechasEstudios($datos['fechainicio'], $finArray);
        }


        return $this->errores;
    }

    /**
     * Validación para edición de alumno.
     */
    public function validarAlumnoEdicion($datos){
        $this->errores = [];
        $this->validarNombre($datos['nombre'] ?? '');
        $this->validarDni($datos['dni'] ?? '');
        $this->validarTelefono($datos['telefono'] ?? '');
        $this->validarDireccion($datos['direccion'] ?? '');
        $id = $datos['id'] ?? null;
        if (!empty($datos['dni']) && $this->alumnoRepo->existeDniExceptoId($datos['dni'], $id)) {
            $this->errores[] = 'El DNI ya está registrado por otro alumno.';
        }
        if (!empty($datos['telefono']) && $this->alumnoRepo->existeTelefonoExceptoId($datos['telefono'], $id)) {
            $this->errores[] = 'El teléfono ya está registrado por otro alumno.';
        }
        if (!empty($datos['contrasena'])) {
            $this->validarContrasenaEdicion($datos['contrasena']);
        }
        $this->validarEdad($datos['fnacimiento'] ?? '');

        if (isset($datos['fechainicio']) && is_array($datos['fechainicio'])) {
            $finArray = isset($datos['fechafin']) && is_array($datos['fechafin']) ? $datos['fechafin'] : [];
            $this->validarFechasEstudios($datos['fechainicio'], $finArray);
        }

        return $this->errores;
    }

    /**
     * Validación para crear empresa.
     */
    public function validarEmpresa($datos)
    {
        $this->errores = [];
        $this->validarCorreo($datos['correo'] ?? '');
        $this->validarCorreo($datos['pcontactoemail'] ?? '');
        $this->validarContrasena($datos['contrasena'] ?? '', $datos['repetir_contrasena'] ?? '');
        $this->validarNombre($datos['nombre'] ?? '');
        $this->validarDireccion($datos['direccion'] ?? '');
        $this->validarCif($datos['cif'] ?? '');
        $this->validarUnicidadCorreo($datos['correo'] ?? '');
        $this->validarUnicidadCif($datos['cif'] ?? '');
        $this->validarUnicidadTelefono($datos['tfcontacto'] ?? '');
        $this->validarUnicidadCorreoContacto($datos['pcontactoemail'] ?? '');
        return $this->errores;
    }

    /**
     * Validación para la edición de empresa.
     */
    public function validarEmpresaEdicion($datos){
        $this->errores = [];
        $this->validarCorreo($datos['pcontactoemail'] ?? '');
        $this->validarNombre($datos['nombre'] ?? '');
        $this->validarDireccion($datos['direccion'] ?? '');
        $this->validarCif($datos['cif'] ?? '');
        $id = $datos['id'] ?? null;
        if (!empty($datos['cif']) && $this->empresaRepo->existeCifExceptoId($datos['cif'], $id)) {
            $this->errores[] = 'El CIF ya está registrado por otra empresa.';
        }
        if (!empty($datos['tfcontacto']) && $this->empresaRepo->existeTelefonoExceptoId($datos['tfcontacto'], $id)) {
            $this->errores[] = 'El teléfono de contacto ya está registrado.';
        }
        if (!empty($datos['pcontactoemail']) && $this->empresaRepo->existeCorreoExceptoId($datos['pcontactoemail'], $id)) {
            $this->errores[] = 'El email de contacto ya está registrado en otra empresa.';
        }
        if (!empty($datos['contrasena'])) {
            $this->validarContrasenaEdicion($datos['contrasena']);
        }
        return $this->errores;
    }

    /**
     * Devuelve todos los errores acumulados tras la última validación.
     * @return array
     */
    public function getErrores() {
        return $this->errores;
    }
}
