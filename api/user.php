<?php 
include("./connectDB.php");

session_start();
$conn = connectDB();

$user = $_SESSION['user'];

if($conn){
    $query = "SELECT * FROM pizzas ORDER BY name";
    $statement = $conn->prepare($query);
    $statement->execute();
    $pizzas = $statement->fetchAll(PDO::FETCH_ASSOC);

    ?>

    <div class="header">
        <img src="../assets/img/logo.png" alt="Pizza logo" class="logo">
        <h1 class="animation">
            <span class="green">La </span>
            <span class="white">Bella </span>
            <span class="red">Pizza</span>
        </h1>
        <a href="./login.php" class="log-out"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>

    <h2 class="heading">Nuestro Menu</h2>

    <div class="container-menu">
        <?php foreach($pizzas as $pizza): ?>
            <figure class="figure">
                <img src="../assets/img/pizza<?= $pizza['id'] ?>.jpg" alt="pizza image" class="img">
                <figcaption class="custom-caption"><?= $pizza['name'] ?></figcaption>
            </figure>
        <?php endforeach; ?>
    </div>

    <?php if(count($pizzas) > 0): ?>
        <form method="post" action="orders.php">
            <table border="1" class="userTable">
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Ingredientes</th>
                    <th>Cantidad</th>
                </tr>
                <?php foreach($pizzas as $pizza): ?>
                    <tr>
                        <td><?= $pizza['name'] ?></td>
                        <td><?= $pizza['price'] ?></td>
                        <td><?= $pizza['ingredients'] ?></td>
                        <td><input type="number" name="quantity[<?= $pizza['id'] ?>]" value="0"></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" class="orderColumn"><input class="btn order-btn" type="submit" value="Realizar Pedido"></td>
                </tr>
            </table>
        </form>
    <?php else: ?>
        <p>No results found</p>
    <?php endif;

    $conn = null;
} else {
    echo "Error al conectar a la Base de datos";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $user ?></title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <script type="module" src="../scripts/main.js"></script>
    <script src="https://kit.fontawesome.com/c3db1c8a5f.js" crossorigin="anonymous"></script>
</body>
</html>
