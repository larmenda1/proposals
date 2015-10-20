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


<div class='service-list'>
	<?php
      // create service options
      $serviceOptions = "";
      while($row = mysql_fetch_array($data, MYSQL_ASSOC))
      {
        $serviceOptions .= "<option value='{$row['ServiceID']}'>{$row['Name']}</option>";
      }

      // check service options
      if(strlen($serviceOptions) > 0)
      {
        // display all service options
        echo "<select name='selectService' class='service-select'>". $serviceOptions . "</select>";
        echo "<input type='submit' name='submit' value='Next'>";
      }
      else
      {
        // no services. display default message.
        echo "<p>The Service list is empty.</p>";
      }
    ?>
</div>