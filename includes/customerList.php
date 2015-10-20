<?php
  if(!isset($data))
  {
    $data = "";
  }
?>

<div class='customer-search'>
  <label class='button search-label' for="customer-search-box">Search: </label>
  <input id='customer-search-box' class='search-box' type="text"></input>
</div>

<table class="customer-list">
	<thead>
		<tr>
			<th>Customer Name</th>
			<th>Address</th>
			<th>Phone</th>
			<th>Action</th>
		</tr>
	</thead>
	
	<tbody>
    <?php
      // create customer rows
      $customerRows = "";
      while($row = mysql_fetch_array($data, MYSQL_ASSOC))
      {
        $customerRows .= "<tr>".
              "<td>{$row['FirstName']} {$row['LastName']}</td>".
              "<td>{$row['AddressLine']}, {$row['AddressCity']}, {$row['AddressState']} {$row['AddressZIP']}</td>".
              "<td>{$row['Phone']}</td>".
              "<td><button type='submit' name='submitCustomer' value='{$row['CustomerID']}'>Select</button></td>".
              "</tr>";
      }

      // check customer rows
      if(strlen($customerRows) > 0)
      {
        // display all customers
        echo $customerRows;
      }
      else
      {
        // no customers. display default message.
        echo "<tr><td colspan='4'>The Customer list is empty.</td></tr>";
      }
    ?>
	</tbody>
</table>