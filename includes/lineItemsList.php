<?php
  if(!isset($data))
  {
    $data = "";
  }
?>

<?php 
  // check to display customer name
  if(isset($customerName) and (!empty($customerName))) { 
?>
  <div class='customer-name'>Customer Name: <?php echo $customerName ?></div>
<?php } ?>


<div class="line-item-area">
  <table class="line-item-list">
  	<thead>
  		<tr>
  			<th>Line Item</th>
  			<th>Price</th>
  			<th>Count</th>
  			<th>Discount</th>
        <th>Total</th>
  		</tr>
  	</thead>
  	
  	<tbody>
      <?php
        // create line item rows
        $lineItemRows = "";
        $totalStart = 0;
        while($row = mysql_fetch_array($data, MYSQL_ASSOC))
        {
          $lineItemRows .= "<tr>".
                "<td class='line-item-name'>{$row['Name']}</td>".
                "<td class='price'>{$row['Price']}</td>".
                "<td><input type='text' name='count{$row['LineItemID']}' class='count' value='1'></td>".
                "<td><input type='text' name='discount{$row['LineItemID']}' class='discount' value='0'> %</td>".
                "<td class='total'>{$row['Price']}</td>".
                "</tr>";

          $totalStart = $totalStart + $row['Price'];
        }

        // check line item rows
        $dataFound = (strlen($lineItemRows) > 0);
        if($dataFound)
        {
          // display all line items
          echo $lineItemRows;
        }
        else
        {
          // no customers. display default message.
          echo "<tr><td colspan='5'>There are no Line Items listed for this service.</td></tr>";
        }
      ?>
  	</tbody>
  </table>

  <?php 
    if($dataFound) { 
      echo "<div class='list-total-area'>Total: <div class='list-total'>$totalStart</div></div>";
      echo "<input type='submit' class='list-item-submit' name='submitProposal' value='Save'>";
    } 
   ?>
</div>