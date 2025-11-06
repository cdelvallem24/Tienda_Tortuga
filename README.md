# Tienda_Tortuga
Aplicación web desarrollada en PHP, MySQL y XAMPP, como parte del curso Aseguramiento de la Calidad de Software en la Universidad Mariano Gálvez de Guatemala.

El sistema permite gestionar una tienda en línea de celulares y accesorios, con funciones para registro de usuarios, administración de productos, carrito de compras, gestión de pedidos y procesamiento de pagos simulados.

Descripción General

Tienda Tortuga está diseñada para ofrecer una experiencia sencilla y eficiente de compra en línea.

El sistema permite que los usuarios se registren, inicien sesión, agreguen productos al carrito, realicen pedidos y consulten su historial de compras.

El administrador, por su parte, puede agregar nuevos productos, controlar el inventario y visualizar las ventas realizadas.

Requerimiento del Sistema

Sistema operativo: Windows 10 o superior

Servidor web: XAMPP (Apache + MySQL)

Versión de PHP: 8.0 o superior

Editor recomendado: Visual Studio Code

Navegador compatible: Google Chrome, Edge o Firefox

Instalación y configuración

Instalar XAMPP y asegurarte de iniciar los módulos Apache y MySQL.

Importar la base de datos:

Abre phpMyAdmin desde XAMPP.

Crea una base llamada tienda.

Importa el archivo base_datos/tienda.sql.

Copiar el código fuente:

Copia la carpeta codigo_fuente dentro de C:\xampp\htdocs\ y renómbrala como carrito.

Abrir en el navegador: http://localhost/carrito

Acceso inicial:

Si aún no tienes usuarios, regístrate desde el formulario de inicio de sesión.

El administrador puede ingresar directamente desde la vista “Ventas” o “Agregar producto”.

Ejecución de pruebas

El proyecto incluye scripts de prueba dentro de la carpeta pruebas/.
Para ejecutarlas:

Copia la carpeta pruebas dentro de: C:\xampp\htdocs\carrito\pruebas\

Configura las credenciales de base de datos en el archivo:

pruebas/config.php

Ejecuta las pruebas desde el navegador:

http://localhost/carrito/pruebas/pruebas_unidad.php

Las pruebas realizan validaciones sobre:

Conexión a base de datos

Inserción y consulta de productos

Flujo de compra e integración con pedidos

Rendimiento básico
