<?php 
include("./connectDB.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quantity'])) {
    session_start();
    $conn = connectDB();
    $customer_id = $_SESSION['id'];

    if ($conn) {
        $fecha_pedido = date("Y-m-d H:i:s"); // Today's date
        $totalToPay = 0;
        $orderDetails = []; 

        foreach ($_POST['quantity'] as $pizzaId => $quantity) {
            // Get the price and name of the pizza from the database
            $query_price = "SELECT id, price, name FROM pizzas WHERE id = :pizza_id";
            $statement_price = $conn->prepare($query_price);
            $statement_price->bindParam(':pizza_id', $pizzaId);
            $statement_price->execute();
            $pizza = $statement_price->fetch(PDO::FETCH_ASSOC);

            $unitPrice = $pizza['price'];
            $subtotal = $unitPrice * intval($quantity);

            // Check if the quantity is greater than zero to include it in the order
            if ($quantity > 0) {
                $orderDetail = [
                    "id" => $pizza["id"],
                    "name" => $pizza['name'],
                    "quantity" => $quantity,
                    "unit_price" => $unitPrice,
                    "subtotal" => $subtotal 
                ]; 
                $orderDetails[] = $orderDetail; 
                $totalToPay += $subtotal;
            }
        }

        echo "<div class='header'>";
        echo "<img src='../assets/img/logo.png' alt='Pizza logo' class='logo'>";   
        echo "<h1 class='animation'> <span class='green'>La </span><span class='white'>Bella </span><span class='red'>Pizza</span></h1>";
        echo "<a href='./login.php' class='log-out'><i class='fa-solid fa-right-from-bracket'></i></a>";
        echo "</div>";
    
        echo "<a href='./user.php' class='home-icon'><i class='fa-solid fa-house'></i></a>";

        // Insert the order into the database only if there are order details
        if (!empty($orderDetails)) {
            $pizzaIds = array_column($orderDetails, 'id');
            $orderDetailString = implode(",", $pizzaIds);

            echo "<p class='message success'>Pedido realizado correctamente. Gracias por elegir la Bella Pizza.</p>";
            echo "<h2>Detalles del pedido</h2>";
            echo "<table border='1' class='userTable order-table'>
                    <tr>
                        <th>Nombre</th>
                        <th>Cantidad</th>
                        <th>Precio Ud</th>
                        <th>Subtotal</th>
                    </tr>";

            foreach ($orderDetails as $detail) {
                echo "<tr>";
                echo "<td>" . $detail['name'] . "</td>";
                echo "<td>" . $detail['quantity'] . " pcs" . "</td>";
                echo "<td>" . $detail['unit_price'] . " €" . "</td>";
                echo "<td>" . $detail['subtotal'] . " €" . "</td>";
                echo "</tr>";
            }

            echo "<tr>
                    <td colspan='3'><strong>Total pagado:</strong></td>
                    <td><strong>$totalToPay €</strong></td>
                </tr>";
            echo "</table>";

            $query_insert_order = "INSERT INTO pedidos (customer_id, order_date, order_details, total) VALUES (:customer_id, :order_date, :order_details, :total)";
            $statement_insert_order = $conn->prepare($query_insert_order);
            $statement_insert_order->bindParam(':customer_id', $customer_id);
            $statement_insert_order->bindParam(':order_date', $order_date);
            $statement_insert_order->bindParam(':order_details', $orderDetailString);
            $statement_insert_order->bindParam(':total', $totalToPay);
            $statement_insert_order->execute();
        } else {
            echo "<p class='message error'>No se ha seleccionado ninguna pizza.</p>";
        }

        $conn = null;
    } else {
        echo "Error al conectar a la base de datos";
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
    <script type="module" src="../scripts/main.js"></script>
    <script src="https://kit.fontawesome.com/c3db1c8a5f.js" crossorigin="anonymous"></script>
</body>
</html>
