<?php 
    // Mostrar todos los errores
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Incluir el archivo para la conexión a la base de datos
    include("./connectDB.php");

    // Iniciar la sesión
    session_start();

    // Variable para mensajes
    $message = null;

    // Verificar si se ha enviado un formulario por el método POST
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Obtener el nombre de usuario y la contraseña del formulario
        $username = $_POST["username"];
        $password = $_POST["password"];
        $rol = null;

        // Verificar el tipo de usuario (admin o no admin)
        if($username == "admin"){
            $rol = 1; // Si el nombre de usuario es "admin", asignar rol 1
        }else{
            $rol = 2; // Si no, asignar rol 2
        }

        // Conexión a la base de datos
        $conn = connectDB();

        // Verificar si la conexión fue exitosa
        if($conn){
            // Preparar la consulta para insertar un nuevo usuario en la base de datos
            $query = "INSERT INTO usuarios (user, password, rol) VALUES (:username, :password, :rol)";
            $statement = $conn->prepare($query);

            // Encriptar la contraseña antes de almacenarla
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $statement->bindParam(":username", $username);
            $statement->bindParam(":password", $hashed_password);
            $statement->bindParam(":rol", $rol);

            // Ejecutar la consulta para insertar el nuevo usuario
            if($statement->execute() && !empty($username) && !empty($password)){
                $message = "Usuario registrado correctamente";
                echo "<p class='message color-message-success'>" . $message . "</p>";
                header("Refresh: 3; url=./login.php");
                exit();
            }else{
                $message = "Error al registrar al usuario";
                echo "<p class='message color-message-error'>" . $message . "</p>";
                header("Refresh: 3; url=" . $_SERVER['PHP_SELF']); 
            }
        }else{
            echo "Error al conectar a la Base de Datos";
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <!-- Formulario para el registro de usuarios -->
    <form method="post" class="enter">
        <label for="username">Nombre de usuario</label>
        <input type="text" name="username" id="username">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password">
        <div>
            <button class="btn">Registrarse</button>
        </div>
    </form>
    
    <!-- Enlace para iniciar sesión -->
    <a href="./login.php">Iniciar sesión</a>
</body>
</html>
