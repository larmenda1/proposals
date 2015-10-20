<?php

	// setup OpenShift environment variables in PHP
	getEV('OPENSHIFT_MYSQL_DB_HOST');
	getEV('OPENSHIFT_MYSQL_DB_PORT');
	getEV('OPENSHIFT_MYSQL_DB_USERNAME');
	getEV('OPENSHIFT_MYSQL_DB_PASSWORD');

	// setup db info 
	$dbhost = "$OPENSHIFT_MYSQL_DB_HOST".":"."$OPENSHIFT_MYSQL_DB_PORT";
	$dbuser = "$OPENSHIFT_MYSQL_DB_USERNAME";
	$dbpass = "$OPENSHIFT_MYSQL_DB_PASSWORD";
	$dbname = "proposals";





	// get OpenShift environment variable
	function getEV($evName)
	{
		$GLOBALS["$evName"] = getenv("$evName");
		return true;
	}

	// show OpenShift environment variable (i.e. global variable now)
	function showEV($evName)
	{
		echo "$evName : " . $GLOBALS["$evName"] . "<br/>";
	}

	// opens connection to database (true=success; false=otherwise)
	function connectDB()
	{
		global $dbhost;
		global $dbuser;
		global $dbpass;
		$connection = false;

		// attempt to connect to database
	  	$conn = mysql_connect($dbhost, $dbuser, $dbpass);
		if(! $conn )
		{
			$connection = false;
		}
		else
		{
			$connection = $conn;
		}

		return $connection;
	}

	// closes connection to database
	function disconnectDB($conn)
	{
		mysql_close($conn);
		return true;
	}

	// query the database
	function queryDB($sql, $command="") {
		global $dbname;
		$results = "";
		$conn = connectDB();	// connect to db

		// if valid connection
		if($conn)
		{
			// query database
			mysql_select_db($dbname);
			$data = mysql_query( $sql, $conn );

			// check return data
			if($data)
			{
				if($command == "getInsertID")
				{
					$results = mysql_insert_id();
				}
				else
				{
					$results = $data;
				}
			}
			else
			{
				//echo 'Could not get data: ' . mysql_error();
			}

			disconnectDB($conn);
		}
		else
		{
			// invalid db connection
		}

		return $results;
	}

	function cleanData($data)
	{
		$conn = connectDB();
		$cleanData = mysql_real_escape_string($data);
		disconnectDB($conn);

		return $cleanData;
	}


	// get list of customers 
	function getCustomers() {
		$customers = "";
		$sql = 'SELECT * FROM Customer';

		$customers = queryDB($sql);

		return $customers;
	}

	// get a customer's full name, given a customer ID
	function getCustomerName($customerID) {
		$data = "";
		$name = "";
		$sql = "SELECT FirstName, LastName FROM Customer WHERE CustomerID = '$customerID'";

		$data = queryDB($sql);
		if((!empty($data)) && ($data != ""))
		{
			$row = mysql_fetch_array($data, MYSQL_ASSOC);
			$name = "{$row['FirstName']} {$row['LastName']}";
		}

		return $name;
	}

	// get list of services
	function getServices() {
		$results = "";
		$sql = 'SELECT * FROM Service';

		$results = queryDB($sql);

		return $results;
	}

	// get list of line items, given a service ID
	function getLineItems($serviceID) {
		$results = "";
		$sql = "SELECT * FROM LineItem WHERE ServiceID = '$serviceID'";

		$results = queryDB($sql);

		return $results;
	}

	// get list of proposals
	function getProposals() {
		$results = "";
		$sql = "SELECT Proposal.ProposalID, Proposal.CustomerID, Proposal.ServiceID, Proposal.DateCreated, Customer.FirstName, Customer.LastName, Service.Name ".
		"FROM Proposal ".
		"JOIN Customer ON Proposal.CustomerID = Customer.CustomerID ".
		"JOIN Service ON Proposal.ServiceID = Service.ServiceID";

		$results = queryDB($sql);

		return $results;
	}

	function getProposalLineItems($proposalID) {
		$results = "";
		$sql = "SELECT ProposalLineItem.ProposalID, ProposalLineItem.LineItemID, ProposalLineItem.Count, ProposalLineItem.Discount ".
		"FROM ProposalLineItem ".
		"WHERE ProposalLineItem.ProposalID = '".$proposalID."'";

		$results = queryDB($sql);

		return $results;
	}

	function getLineItem($lineItemID)
	{
		$results = "";
		$sql = "SELECT LineItem.LineItemID, LineItem.Price ".
		"FROM LineItem ".
		"WHERE LineItem.LineItemID = '".$lineItemID."'";

		$results = queryDB($sql);

		return $results;
	}

	function saveProposal($customerID, $serviceID, $uncleanData)
	{
		$success = false; 

		// create proposal
		$results = createProposal($customerID, $serviceID);

		if(!empty($results))
		{
			// create proposal line items
			$proposalID = $results;
			$results = createProposalLineItems($proposalID, $uncleanData);
			
			if(empty($results))
			{
				$success = true;
			}
		}

		return $success;
	}

	function createProposal($customerID, $serviceID) {
		$results = "";

		$cleanCID = cleanData($customerID);
		$cleanSID = cleanData($serviceID);

		$sql = 'INSERT INTO Proposal (CustomerID, ServiceID, DateCreated) VALUES ("'. $cleanCID. '", "'. $cleanSID . '", NOW())';
		$results = queryDB($sql, "getInsertID");

		return $results;
	}

	function createProposalLineItems($proposalID, $uncleanData)
	{
		$results = "";

		// loop through data
		foreach($uncleanData as $key => $value)
		{
			$lineItemID = -1;

			// if count found
			if(strpos($key, 'count') !== false)
			{
				$pieces = explode("t", $key);
				$lineItemID = $pieces[1];
				$lineItemID = cleanData($lineItemID);

				// check line item ID
				if(is_numeric($lineItemID))
				{
					// get count value
					$countNumber = 1;
					$countValue = cleanData($value);

					// check count value
					if(is_numeric($countValue))
					{
						$countNumber = abs(intval($countValue));
					}

					// get discount value
					$discountNumber = 0;
					$dKey = "discount".$lineItemID;
					if(array_key_exists($dKey, $uncleanData))
					{
						$discountValue = $uncleanData["$dKey"];
						$discountValue = cleanData($discountValue);

						// check discount value
						if(is_numeric($discountValue))
						{
							$discountValue = intval($discountValue);

							if($discountValue > 100)
							{
								$discountValue = 100;
							}
							else if($discountValue < 0)
							{
								$discountValue = 0;
							}

							$discountNumber = $discountValue;
						}
					}

					// insert new proposal line item
					$sql = 'INSERT INTO ProposalLineItem (ProposalID, LineItemID, Count, Discount, DateCreated, DateModified)'.
							' VALUES ("'.$proposalID.'", "'.$lineItemID.'","'.$countNumber.'","'.$discountNumber.'", NOW(), NOW() )';
					$results = queryDB($sql);
				}
			}
		}

		return $results;
	}
?>