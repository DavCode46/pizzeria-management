<?php
// Incluir el archivo para la conexión a la base de datos
include("./connectDB.php");

// Verificar si se ha enviado un formulario por el método POST y si se ha establecido la cantidad de pizzas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['quantity'])) {
    // Iniciar la sesión
    session_start();
    // Establecer la conexión a la base de datos
    $conn = connectDB();
    // Obtener el ID del cliente de la sesión
    $customer_id = $_SESSION['id'];

    // Verificar la conexión a la base de datos
    if ($conn) {
        // Obtener la fecha actual para el pedido
        $order_date = date("Y-m-d H:i:s"); // Fecha de hoy
        $totalToPay = 0;
        $orderDetails = []; // Detalles del pedido

        // Recorrer todas las cantidades de pizzas seleccionadas en el formulario
        foreach ($_POST['quantity'] as $pizzaId => $quantity) {
            // Obtener el precio y el nombre de la pizza desde la base de datos
            $query_price = "SELECT id, price, name FROM pizzas WHERE id = :pizza_id";
            $statement_price = $conn->prepare($query_price);
            $statement_price->bindParam(':pizza_id', $pizzaId);
            $statement_price->execute();
            $pizza = $statement_price->fetch(PDO::FETCH_ASSOC);

            $unitPrice = $pizza['price'];
            $subtotal = $unitPrice * intval($quantity);

            // Verificar si la cantidad es mayor que cero para incluirlo en el pedido
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

        // Insertar el pedido en la base de datos solo si hay detalles de pedido
        if (!empty($orderDetails)) {

            $orderDetailString = ''; // Inicializar el string de detalles del pedido

            foreach ($orderDetails as $detail) {
                $pizzaId = $detail['id'];
                $quantity = $detail['quantity'];

                // Repetir la ID de la pizza según la cantidad pedida
                $pizzaIdsRepeated = array_fill(0, $quantity, $pizzaId);

                // Agregar estas IDs repetidas al string de detalles del pedido
                $orderDetailString .= implode(",", $pizzaIdsRepeated) . ",";
            }

            $orderDetailString = rtrim($orderDetailString, ","); // Eliminar la última coma del string si es necesario

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
            // Insertar el pedido en la tabla de pedidos
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