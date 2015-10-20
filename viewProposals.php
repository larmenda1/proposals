<?php 
	include("includes/functions/proposalTools.php");  // get function list for Proposals data
	include("includes/header.php");

	$data = getProposals();
?>


<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">
  <div class="row">
    <div class="column menu-box">

      <h4>My Proposals</h4>

	      <table class="proposals-list">
	      	<thead>
	      		<tr>
	      			<th>Customer Name</th>
	      			<th>Service</th>
	      			<th>Total</th>
	      			<th>Date Created</th>
	      		</tr>
	      	</thead>
	      	
	      	<tbody>
	          <?php
	            // create data rows
	            $dataRows = "";
	            if(!empty($data))
	            {
		            while($row = mysql_fetch_array($data, MYSQL_ASSOC))
		            {
		            	$rowDate=substr($row['DateCreated'], 0, strrpos($row['DateCreated'], ' '));
		              	$dataRows .= "<tr>".
		                    "<td>{$row['FirstName']} {$row['LastName']}</td>".
		                    "<td>{$row['Name']}</td>";

		                $proposalLineItems = getProposalLineItems($row['ProposalID']);
		                $total = 0;
		                while($pLineItem = mysql_fetch_array($proposalLineItems, MYSQL_ASSOC))
		                {
		                	$lineItem = getLineItem($pLineItem['LineItemID']);
		                	
		                	while($lineItemRow =  mysql_fetch_array($lineItem, MYSQL_ASSOC))
		                	{
		                		$total += ((($lineItemRow['Price'] * $pLineItem['Count']) * (100 - $pLineItem['Discount'])) / 100);
		                	}
		                }

		                $total = round($total, 2);

		                $dataRows .=  "<td>$".$total."</td>".
		                    "<td>$rowDate</td>".
		                    "</tr>";
		            }
		        }

	            // check data rows
	            if(strlen($dataRows) > 0)
	            {
	              // display all proposals
	              echo $dataRows;
	            }
	            else
	            {
	              // no proposals. display default message.
	              echo "<tr><td colspan='4'>The Proposal list is empty.</td></tr>";
	            }
	          ?>
	      	</tbody>

	      </table>

    </div>
  </div>
</div>


<?php include("includes/footer.php") ?>