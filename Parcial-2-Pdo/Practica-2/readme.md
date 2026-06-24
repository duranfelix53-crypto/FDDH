# Práctica 2 – Transacciones PHP con PDO
## Objetivo
Implementar un sistema en PHP que permita registrar alumnos en una base de datos utilizando PDO, aplicando el manejo de errores con `try/catch` y el uso de transacciones (commit y rollback) para asegurar la integridad de los datos.

## Tecnologías utilizadas
* PHP 8+
* MySQL
* PDO

## Instrucciones de ejecución
1. Crear la base de datos llamada `escuela`.
2. Crear las tablas `alumnos` y `logs_alumnos`.
3. Colocar el archivo PHP en la carpeta del servidor local.
4. Iniciar Apache y MySQL desde el servidor local.
5. Abrir el navegador y acceder al archivo.

## Evidencia de funcionamiento
* Al registrar un alumno sin activar la opción de error, los datos se guardan correctamente en la tabla `alumnos` y se genera un registro en `logs_alumnos` (COMMIT).
* Al activar la opción “Simular error”, la transacción se cancela y no se guarda ningún dato (ROLLBACK).
* Se muestran en pantalla los registros almacenados en ambas tablas.