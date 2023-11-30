<?php 
    include("./connectDB.php");

     session_start();

     $message = null;

     if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = $_POST["username"];
        $password = $_POST["password"];

        $conn = connectDB();

        if($conn){
            $query = "SELECT id, user, password, rol FROM usuarios WHERE user = :username";
            $statement = $conn->prepare($query);
            $statement->bindParam(":username", $username);
            $statement->execute(); 

            $user = $statement->fetch(PDO::FETCH_ASSOC);
        
            if($user && password_verify($password, $user["password"])){
                $_SESSION["id"] = $user["id"];
                $_SESSION["user"] = $username;
               if($user["rol"] == 1){
                    $message = "Inicio de sesión exitoso";
                    echo "<p class='message color-message-success'>" . $message . "</p>";
                    header("Refresh: 3; url=./admin.php"); 
                }else{
                    $message = "Inicio de sesión exitoso";
                    echo "<p class='message color-message-success'>" . $message . "</p>";
                    header("Refresh: 3; url=./user.php");  
                }
            }if(!$user){
                $message = "Error al iniciar sesión";
                echo "<p class='message color-message-error'>" . $message . "</p>";
                header("Refresh: 3; URL=" . $_SERVER['PHP_SELF']);
            } 
            exit();
        }
     }

     ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <form method="post" class=enter>
        <label for="username">Nombre de usuario</label>
        <input type="text" name="username" id="username">
        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password">
        <div>
            <button type="submit" class="btn login">Iniciar Sesión</button>
        </div>
        
    </form>
    <a href="./index.php">Registrarse</a>
</body>
</html>