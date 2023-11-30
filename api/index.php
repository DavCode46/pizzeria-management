<?php 
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    include("./connectDB.php");

    session_start();

    $message = null;

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = $_POST["username"];
        $password = $_POST["password"];
        $rol = null;

        if($username == "admin"){
            $rol = 1;
        }else{
            $rol = 2;
        }

        $conn = connectDB();

        if($conn){
            $query = "INSERT INTO usuarios (user, password, rol) VALUES (:username, :password, :rol)";
            $statement = $conn->prepare($query);

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $statement->bindParam(":username", $username);
            $statement->bindParam(":password", $hashed_password);
            $statement->bindParam(":rol", $rol);

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
    <title>Document</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
<form method="post" class="enter">
        <label for="username">Nombre de usuario</label>
        <input type="text" name="username" id="username">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password">
        <div>
            <button class="btn">Registrarse</button>
        </div>
        
    </form>
    <a href="./login.php">Iniciar sesión</a>
</body>
</html>