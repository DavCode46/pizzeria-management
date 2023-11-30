<?php

    function connectDB(){
            $conexion = "mysql:dbname=data_base1;host=127.0.0.1";
            $user = "root";
            $password = "";

            try{
                $bd = new PDO($conexion, $user, $password);
                return $bd;
            }catch(PDOException $e){
                echo "Error al conectar con la base de datos " . $e;
                return false;
            }
        }