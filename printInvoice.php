<!DOCTYPE html>
<html lang="en">

<!-----------------------------Main Navigation Header------------------------------>
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="./style.css">
  <title>Dream Team Auto View Inventory </title>
</head>
    <header class="main-header">
      <div class="logo-container">
          <img class="logoImage" src="images/logo.PNG" alt="Logo"> <!-- IF IMAGE DOES NOT LOAD, EITHER ADD OR REMOVE SLASH FROM images-->
      </div>
      <nav class="nav-menu">
          <ul>
              <li><a href="index.html">Home</a></li>
              <li><a href="product.php">Products</a></li>
              <li><a href="login.php">Sign in</a></li>
          </ul>
      </nav>
    </header>
<!-----------------------------Workstation Navigation Header------------------------------>
<div class="workstationNav">
<ul>
  <li class="workstationDrop">
    <a href="warehouse.php" class="workstationButton">Warehouse</a>
      <div class="workDropContent">
	<a href="printPacking.php">Print Packing List</a>
        <a href="printInvoice.php">Print Invoice</a>
        <a href="printShipping.php">Print Shipping Labels</a>
	<a href="updateOrderStatus.php">Update Order Status</a>
      </div>
     </li>
  <li class="workstationDrop">
    <a href="admin-login.html" class="workstationButton">Administrative</a>
      <div class="workDropContent">
        <a href="admin-login.html">Sign In</a>
      </div>
  </li>
  <li class="workstationDrop">
    <a href="receiving.php" class="workstationButton">Receiving</a>
    <div class="workDropContent">
      <a href="viewInventory.php">View Inventory</a>
      <a href="addInventory.php">Add Inventory</a>
    </div>
  </li>
</ul>
</div>

<!----------------- IMPLEMENTING THE PRINTING INVOICE CODE HERE --------------------------->
<body>

<?php
    $OrderNum = "";
    include 'secrets.php';

    if (isset($_GET['newOrderNumber']) || isset($_GET['OrderNumber'])) 
    {
	$TotalPrice = 0;
	$Shipping = 0;	
	$OrderNum = $_GET["OrderNumber"];
        $sql = "SELECT users.Name, users.Address, orders.ShippingCharge, orders.TotalPrice FROM users JOIN orders ON users.userID=orders.CustomerID WHERE orders.OrderID = ?";
        $result = $pdo->prepare($sql);
        $result->bindParam(1, $_GET['OrderNumber']);
        $result->execute();
        $orderInfo = $result->fetchAll(PDO::FETCH_ASSOC);

	if ($result->rowCount() > 0)
	{

	echo "<div id='printableArea' style=\"background-color:white; border-style:solid; border: 5px black; width: 800px;\">";

		echo "<h1>Invoice</h1>";
		echo "Dream Team Auto Parts<br><br>";
	
		foreach($orderInfo as $rows)
		{

			$TotalPrice = $rows['TotalPrice'];
			$Shipping = $rows['ShippingCharge'];

			echo "Bill to:<br>";
			echo $rows['Name'];
        		echo "<br>";
			echo $rows['Address']. "<br>";
			echo "<br>";
    			echo "Order Number: " .$OrderNum . "<br>";	
			echo "Payment: $" . $rows['TotalPrice'] . "<br><br>";	
		}

        	$sql = "SELECT products.Name, orderdetails.PartNumber, orderdetails.Quantity, products.Price FROM orderdetails LEFT JOIN products ON orderdetails.PartNumber=products.PartNumber WHERE OrderID=?";
       		$result = $pdo->prepare($sql);
        	$result->bindParam(1, $_GET['OrderNumber']);
        	$result->execute();
        	$orderInfo = $result->fetchAll(PDO::FETCH_ASSOC);

		echo '<table border=3px solid black, style="background-color:white; border-color:black;">';
		echo "<tr>";
		echo "<th>Product Name</th>";
		echo "<th>Product Number</th>";
		echo "<th>Quantity</th>";
		echo "<th style='color: black;'>Price</th>";	
		echo "</tr>";

		foreach($orderInfo as $rows)
		{

			echo "<tr>";
			echo "<td>". $rows['Name'] . "</td>";
			echo "<td>". $rows['PartNumber'] . "</td>";
			echo "<td>". $rows['Quantity'] . "</td>";
			echo "<td style='color: black;'>". $rows['Price'] . "</td>";			 
	  		echo "</tr>";
    	  	}
       		echo "</table><br>";
	
		echo "Subtotal: $" .$TotalPrice - $Shipping . "<br>";
		echo "Shipping and Handling: $" . $Shipping . "<br>";
		echo "Total: $" .$TotalPrice . "<br><br>";

		echo"</div>";
		echo "<br><button onClick='printContent(\"printableArea\")'>Print</button>";		
	}
	else
	{
		echo"Unable to locate the order number provided. Please try again or return to the main Warehouse page to view the current orders pending shipment.<br>";
	}
}
?>


<br>

<!--------Used to print specified id element --------------->
<script type="text/javascript">
function printContent(id){
str=document.getElementById(id).innerHTML
newwin=window.open('','printwin','left=100,top=100,width=800,height=800')
newwin.document.write('<HTML>\n<HEAD>\n')
newwin.document.write('<TITLE>Invoice</TITLE>\n')
newwin.document.write('<script>\n')
newwin.document.write('function checkState(){\n')
newwin.document.write('if(document.readyState=="complete"){\n')
newwin.document.write('window.close()\n')
newwin.document.write('}\n')
newwin.document.write('else{\n')
newwin.document.write('setTimeout("checkState()",2000)\n')
newwin.document.write('}\n')
newwin.document.write('}\n')
newwin.document.write('function print_win(){\n')
newwin.document.write('window.print();\n')
newwin.document.write('checkState();\n')
newwin.document.write('}\n')
newwin.document.write('<\/script>\n')
newwin.document.write('</HEAD>\n')
newwin.document.write('<BODY onload="print_win()">\n')
newwin.document.write(str)
newwin.document.write('</BODY>\n')
newwin.document.write('</HTML>\n')
newwin.document.close()
}
</script>
<br>

<!---------Form that will send the order number to the corresponding selection---->

<div>
<form action="" method="GET">
 <label for="orderID" style="color: white;">Order Number:</label>
 <input type="text" id="OrderNumber" value="<?php echo $OrderNum;?>" name="OrderNumber" required>
<input type="submit" name="newOrderNumber" formaction="printInvoice.php" value="View New Order Number">
<br> <br>
<input type="submit" name="printPacking" formaction="printPacking.php" value="Print Packing List">
<input type="submit" name="printInvoice" formaction="printInvoice.php" value="Print Invoice">
<input type="submit" name="printShippingLabel" formaction="printShipping.php" value="Print Shipping Label">
<input type="submit" name="updateOrderStatus" formaction="updateOrderStatus.php" value="Update Order Status">
</form>
</div>

</body>
</html>

