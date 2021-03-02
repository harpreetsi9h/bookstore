<?php
session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>BookStore - Checkout</title>
    </head>
    <body>
        <nav>
            <br>
            <h2>BookStore</h2>
            <br>
            <span><a href="index.html">Home</a></span>&nbsp;&nbsp;&nbsp;<span><a href="store.php">Store</a></span>
        </nav>
        <main>
            <br><br>
            <h3>Checkout</h3>
            <br>
            <form action="checkout.php" method="POST">
                <table width="50%">
                    <tr><td><label for="firstName">First Name:</label></td><td><input type="text" name="firstName"></td><td></td></tr>
                    <tr><td><label for="lastName">Last Name:</label></td><td><input type="text" name="lastName"></td><td></td></tr>
                    <tr><td>Payment Options:</td><td> </td><td></td></tr>
                    <tr><td><label>Credit Card <input type="radio" name="payment" value="credit"></label></td>
                        <td><label>Debit Card <input type="radio"name="payment" value="debit"></label></td>
                        <td><label>Cash <input type="radio" name="payment" value="cash"></label></td></tr>
                   
                </table>
                <br>
                <input type="submit" value="Submit">
            </form>
        </main>
        <footer>
            <br><br>
            <small><i>BookStore &copy; 2021</i></small>
        </footer>
    </body>
</html>
<?php

if(!empty($_GET['bookid'])){
    $_SESSION['bookid'] = $_GET['bookid'];
}

require('mysqli_connect.php');

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    if(empty($_POST['firstName']) && empty($_POST['lastName']) && empty($_POST['payment'])){
        echo 'Please provide all fields!';
    }
    else{
        $first_name = mysqli_real_escape_string($dbc, trim($_POST['firstName']));
        $last_name = mysqli_real_escape_string($dbc, trim($_POST['lastName']));
        $payment_options = mysqli_real_escape_string($dbc, $_POST['payment']);

        $query = "INSERT INTO bookinventoryorder (FirstName, LastName, PaymentOption) VALUES ('".$first_name."','".$last_name."','".$payment_options."')";
        $r = mysqli_query($dbc, $query);
        if($r){
            $get_query = "SELECT Quantity FROM bookinventory WHERE BookID=".$_SESSION['bookid'];
            $get_result = mysqli_query($dbc, $get_query);
            if($get_result){
                $qty = 0;
                while($row = mysqli_fetch_array($get_result, MYSQLI_ASSOC)){
                    $qty = $row['Quantity'];
                }
                if($qty>0){
                    $qty = $qty - 1;
                    $update_query = "UPDATE bookinventory SET Quantity='".$qty."' WHERE BookID=".$_SESSION['bookid'];
                    $update_result = mysqli_query($dbc, $update_query);
                    if($update_result){
                        header("location:index.html");
                    }
                    else{
                        echo('<p class="error">Contact insertion failed</p>');
                        echo '<p>' . mysqli_error($dbc) . '<br><br>Query: ' . $update_result . '</p>';
                    }
                }
            }
            else{   
                echo('<p class="error">Contact insertion failed</p>');
                echo '<p>' . mysqli_error($dbc) . '<br><br>Query: ' . $get_query . '</p>';
            }
            echo '<script>alert("Your Order has been placed Successfully. Thank You!")</script>';
            session_unset();
            session_destroy();
        }
        else{
            echo('<p class="error">Contact insertion failed</p>');
            echo '<p>' . mysqli_error($dbc) . '<br><br>Query: ' . $query . '</p>';
        }
    }
}

?>