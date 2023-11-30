# Gestión de pizzería

**Autor:** ***David Menéndez Blanco*** <br>
**GitHub:** https://github.com/DavidMenendezBlanco/pizzeria-management.git

## Estructura de proyecto:
1. api
    * admin.php -> Gestiona la página del administrador. Funcionalidad:
        + Insertar Productos.
        + Editar Productos.
        + Eliminar Productos.
        + Controlar pizzas más vendidas.
    * connectDB.php -> Conecta con la DB.
    * index.php -> Página de registro de usuarios, consta de:
        + Ingreso de nombre de usuario.
        + Ingreso de contraseña de usuario.
        + Enlace a la página de Login.
        + Funcionalidad:
            - Una vez registrado el usuario te redirige a la página de login
    * login.php -> Página de inicio de sesión.
        + Si se introducen las credenciales correctas:
            - Te redirige a la página principal.
    * orders.php -> Gestiona los pedidos. Funcionalidad:
        + Muestra mensaje de agradecimiento por la compra.
        + Muestra tabla con los datos del pedido realizado.
        + Inserta el pedido en la DB
    * user.php ->Gestiona la página del usuario. Funcionalidad:
        + Muestra imágenes de los productos.
        + Muestra una tabla con los siguientes detalles:
            - Nombre del producto.
            - Precio del producto.
            - Ingredientes del producto.
            - Cantidad que se quiere comprar.
            - Botón para realizar el pedido.
        + Una vez realizado el pedido te redirige a la página orders.php.
2. assets
    * img
        + Imágenes de los productos.
3. scripts
    * heading-animation.js
        + Animación del nombre de la pizzería.
    * main.js
        + Animaciones de rotación de las imágenes con el scroll.
        + Temporizador que oculta mensajes tras 3 segundos.
4. styles
    * styles.css -> Hoja de estilos del proyecto.

