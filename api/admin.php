<?php 
include("./connectDB.php");

session_start();

$user = $_SESSION['user'];
$message = null;

function deletePizza($conn, $id){
    $query = $conn->prepare("DELETE FROM pizzas WHERE id = :id");
    $query->bindParam(":id", $id);
    return $query->execute();
}

function editPizza($conn, $id){
    $query = $conn->prepare("SELECT * FROM pizzas WHERE id = :id");
    $query->bindParam(":id", $id);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
}

function insertPizza($conn, $name, $cost, $price, $ingredients){
    if(isset($name, $cost, $price, $ingredients)){
        $query = $conn->prepare("INSERT INTO pizzas (name, cost, price, ingredients) VALUES (:name, :cost, :price, :ingredients)");
        $query->bindParam(":name", $name);
        $query->bindParam(":cost", $cost);
        $query->bindParam(":price", $price);
        $query->bindParam(":ingredients", $ingredients);
        return $query->execute();
    }   
}
?>
    <div class='header'>
        <img src='../assets/img/logo.png' alt='Pizzeria Logo' class='logo'>   
        <h1 class='animation'> <span class='green'>La </span><span class='white'>Bella </span><span class='red'>Pizza</span></h1>
        <a href='./login.php' class='log-out'><i class='fa-solid fa-right-from-bracket'></i></a>
    </div>
<?php
function listPizzas($conn){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['delete'])){
            $id = $_POST['delete_id'];
            deletePizza($conn, $id);
            echo "<p id='message' class='message color-message-success'>Pizza eliminada correctamente.</p>";
        } else if(isset($_POST['edit'])){
            $id = $_POST['edit_id'];
            editPizza($conn, $id);
        }else if(isset($_POST['insert'])){
            $name = $_POST['name'];
            $cost = $_POST['cost'];
            $price = $_POST['price'];
            $ingredients = $_POST['ingredients'];

            if(!empty($name) && !empty($cost) && !empty($price) && !empty($ingredients)){
                insertPizza($conn, $name, $cost, $price, $ingredients);
                $message = "Pizza insertada correctamente.";
                echo "<p id='message' class='message color-message-success'>" . $message . "</p>";
            }else{
                $message = "Error al insertar la pizza.";
                echo "<p id='message' class='message color-message-error'>" . $message . "</p>";
            }
           
        }
    }

    if(isset($_POST['update'])){
        $id = $_POST['pizza_id'];
        $name = $_POST['name'];
        $cost = $_POST['cost'];
        $price = $_POST['price'];
        $ingredients = $_POST['ingredients'];
        
       
        if(!empty($name) && !empty($cost) && !empty($price) && !empty($ingredients)){
            $query = $conn->prepare("UPDATE pizzas SET name = :name, cost = :cost, price = :price, ingredients = :ingredients WHERE id = :id");
            $query->bindParam(":id", $id);
            $query->bindParam(":name", $name);
            $query->bindParam(":cost", $cost);
            $query->bindParam(":price", $price);
            $query->bindParam(":ingredients", $ingredients);
              
            if($query->execute()){
                $message = "Pizza actualizada correctamente.";
                 echo "<p id='message' class='message color-message-success'>" . $message . "</p>";
             }
        }else {
            $message = "Error al actualizar la pizza.";
            echo "<p id='message' class='message color-message-error'>" . $message . "</p>";
        }
    }          

    $query = $conn->prepare("SELECT * FROM pizzas");
    $query->execute();

    $pizzaToEdit = null;

    if(isset($_POST['edit_id'])){
        $id = $_POST['edit_id'];
        $pizzaToEdit = editPizza($conn, $id);
    }
    ?>
    
    <form method='post' class='shadow'>
        <legend><?= ($pizzaToEdit ? "Edit Pizza" : "Insert Pizza") ?></legend>   
        <label for='name'>Name</label>
        <input type='text' name='name' id='name' value='<?= ($pizzaToEdit ? $pizzaToEdit["name"] : "") ?>'>
        <label for='cost'>Cost</label>
        <input type='text' name='cost' id='cost' value='<?= ($pizzaToEdit ? $pizzaToEdit["cost"] : "") ?>'>
        <label for='price'>Price</label>
        <input type='text' name='price' id='price' value='<?= ($pizzaToEdit ? $pizzaToEdit["price"] : "") ?>'>
        <label for='ingredients'>Ingredients</label>
        <input type='text' name='ingredients' id='ingredients' value='<?= ($pizzaToEdit ? $pizzaToEdit["ingredients"] : "") ?>'>
        <input type='hidden' name='pizza_id' value='<?= ($pizzaToEdit ? $pizzaToEdit['id'] : "") ?>'>
        
        <div class='btn-container'>
            <button class='btn insertar' type='submit' name='<?= ($pizzaToEdit ? "update" : "insert") ?>' id='insert-btn'><?= ($pizzaToEdit ? "Editar" : "Insertar") ?> Pizza</button>
        </div>
    </form>
    <h2 class='heading'>Our Pizzas</h2>
    <table border='1' class='userTable'>
        <tr>
            <th>Nombre</th>
            <th>Coste</th>
            <th>Precio</th>
            <th>Ingredientes</th>
            <th>Acciones</th>
        </tr>
    <?php
    foreach($query as $row){          
        echo "<tr>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["cost"] . "</td>";
        echo "<td>" . $row["price"] . "</td>";
        echo "<td>" . $row["ingredients"] . "</td>";
        echo "<td>
                <form method='post'>
                    <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                    <input class='btn' type='submit' name='delete' value='Eliminar'>
                </form>
                <form method='post'>
                    <input type='hidden' name='edit_id' value='" . $row["id"] . "'>
                    <input class='btn' type='submit' name='edit' value='Editar' id='edit-btn'>
                </form>
              </td>";
        echo "</tr>";           
    }
    echo "</table>";    
}

function getBestSellingPizzas($conn){
    $query = $conn->prepare("SELECT order_details FROM pedidos");
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);

    $soldPizzas = [];

    foreach($results as $result){
        $order_details = explode(",", $result['order_details']);

        foreach($order_details as $pizza_id){
            if(!isset($soldPizzas[$pizza_id])){
                $soldPizzas[$pizza_id] = 0;
            }
            $soldPizzas[$pizza_id]++;          
        }
    }

    arsort($soldPizzas);

    // Get the names of the pizzas corresponding to the best-selling IDs
    $pizzaNames = [];
    foreach ($soldPizzas as $pizza_id => $quantity) {
        $query = $conn->prepare("SELECT name FROM pizzas WHERE id = :id");
        $query->bindParam(":id", $pizza_id);
        $query->execute();
        $pizzaName = $query->fetchColumn();

        if ($pizzaName) {
            $pizzaNames[$pizzaName] = $quantity;
        }
    }

    return $pizzaNames;
}



$conn = connectDB();
$bestSellingPizzas = getBestSellingPizzas($conn);

listPizzas($conn);

echo "<h2>Pizzas Más Vendidas</h2>";
echo "<table border='1' class='userTable'>
        <tr>
            <th>Pizza</th>
            <th>Total Vendido</th>
        </tr>";

foreach ($bestSellingPizzas as $pizzaName => $quantity) {
    echo "<tr>";
    echo "<td>" . $pizzaName . "</td>";
    echo "<td>" . $quantity . "</td>";
    echo "</tr>";
}
echo "</table>";
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