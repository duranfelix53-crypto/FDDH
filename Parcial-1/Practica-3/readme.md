Practica 3 Sistema de Usuarios con Validaciones y Excepciones

Descripcion del sistema

Este proyecto implementa un sistema básico de usuarios utilizando Programación Orientada a Objetos en PHP. El sistema incluye validación de correos electrónicos y manejo de excepciones para evitar datos inválidos.

Estructura del proyecto

practica-3 │ ├── clases │ ├── Usuario.php │ ├── Admin.php │ └── Alumno.php │ ├── index.php └── README.md

Flujo de clases

Usuario

Es la clase base del sistema. Contiene los atributos:

nombre correo

El constructor valida que el correo tenga un formato valido. Si el correo no es valido, se lanza una excepción.

Admin

Clase que hereda de Usuario. Incluye el método:

getRol() → devuelve Administrador.

Alumno

Clase que también hereda de Usuario. Incluye un atributo adicional:

matrícula

Además tiene el método:

getRol() → devuelve Alumno.

Manejo de excepciones

En el archivo index.php se utiliza un bloque try/catch para capturar errores generados por correos invalidos.

Si el correo no cumple con el formato correcto, el sistema muestra un mensaje de error controlado sin detener la ejecución del programa.

Ejemplo de error controlado

Si se intenta crear un usuario con un correo inválido:

Error: Correo inválido