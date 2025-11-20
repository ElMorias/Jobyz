<?php

class DB {
  private static $conexion = null;

  public static function getConnection() {
    if (self::$conexion === null) {
      try {
        self::$conexion = new PDO(
          'mysql:host=db;dbname=jobyz;charset=utf8mb4',
          'jobyz', // usuario
          'jobyzpass',     // contraseña
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
          ]
        );
      } catch (PDOException $e) {
        die('Error de conexión: ' . $e->getMessage());
      }
    }

    return self::$conexion;
  }
}