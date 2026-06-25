# Tienda MVC — Desarrollo Web Avanzado

Proyecto desarrollado para la materia **Desarrollo Web Avanzado** en FIMAZ-UAS,
bajo la supervisión del Dr. José Alfonso Aguilar Calderón.

---

## Tecnologías utilizadas

- PHP 8
- MySQL
- PDO
- Bootstrap 5
- Patrón de diseño MVC
- Programación Orientada a Objetos (POO)
- Namespaces y Autoload (PSR-4)
- Transacciones con rollback
- Try/Catch para manejo de errores

---

## Características

- Autenticación de administrador con sesiones
- CRUD completo de productos
- Validación de campos (precios, existencia, SKU duplicado)
- Validación precio_venta ≥ precio_compra
- Subida de imágenes por producto
- Paginación de productos
- Protección CSRF en formularios
- Catálogo público con buscador
- Bitácora de acciones del administrador
- Rutas amigables con .htaccess

---

## Instalación

1. Clonar o descargar el repositorio en `htdocs/`:

git clone https://github.com/Cristopher-Emmanuel-Lizarraga-Hernandez/desarrollo-web-avanzado-fimaz-uas.git

2. Importar la base de datos en phpMyAdmin:
   - Crear base de datos llamada `tienda_mvc`
   - Importar el archivo `db/database.sql`

3. Configurar la conexión en `config/Database.php`:
```php
   private string $host = "localhost";
   private string $db_name = "tienda_mvc";
   private string $username = "root";
   private string $password = "";
```

4. Crear la carpeta de imágenes:
uploads/productos/

5. Acceder desde el navegador:
http://localhost/db/

---

## Estructura del proyecto
db/
├── config/
│   ├── Autoload.php
│   └── Database.php
├── controllers/
│   ├── AuthController.php
│   ├── BitacoraController.php
│   ├── ProductoController.php
│   └── PublicController.php
├── models/
│   ├── BitacoraModel.php
│   ├── ProductoModel.php
│   └── UsuarioModel.php
├── views/
│   ├── Auth/
│   │   └── login.php
│   ├── bitacora/
│   │   └── index.php
│   ├── layouts/
│   │   ├── header.php
│   │   └── footer.php
│   ├── productos/
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── index.php
│   └── public/
│       └── catalogo.php
├── uploads/
│   └── productos/
├── .htaccess
├── database.sql
└── index.php

---

## Autor

**Cristopher Emmanuel Lizárraga Hernández**  
Facultad de Informática Mazatlán — Universidad Autónoma de Sinaloa