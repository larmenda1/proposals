<?php 
  include("includes/functions/proposalTools.php");  // get function list for Proposals data
  include("includes/header.php");                   // get site header

  // Start the session
  session_start();

  $step = 1;      // default step
  $errors = "";   // error messages
  $success = "";  // success message

  // check if Form POST
  if(($_SERVER['REQUEST_METHOD'] == 'POST') && (!empty($_POST)))
  {

    // check step
    if(isset($_POST['submitCustomer']))
    {
      $tempCID = cleanData($_POST['submitCustomer']);   // get and clean customer ID

      // validate
      if(is_numeric($tempCID))                          // is ID numeric?
      {
        $_SESSION['customerID'] = $tempCID;
        $step = 2;                                      // display step 2
      }
      else
      {
        $errors .= "Invalid Customer ID<br/>";
      }
    }
    else if(isset($_POST['selectService']))
    {
      $tempSID = cleanData($_POST['selectService']);    // get and clean service ID

      // validate
      if(is_numeric($tempSID))                          // is ID numeric?
      {
        $_SESSION['serviceID'] = $tempSID;
        $step = 3;                                      // display step 3
      }
      else
      {
        $errors .= "Invalid Service ID<br/>";
      }
    }
    else if(isset($_POST['submitProposal']))
    {
      // validate
      $validData = true;

      // if not pass, redisplay with errors

      // if pass, submit for processing, destroy session variables, and reset to Step 1, and display success message
      if($validData)
      {
        $proposalCID = $_SESSION['customerID'];
        $proposalSID = $_SESSION['serviceID'];
        $results = saveProposal($proposalCID, $proposalSID, $_POST);

        if($results)
        {
          $success .= "Your proposal has been successfully saved!<br/>";
        }
        else
        {
          $errors .= "Error: There was an issue while attempting to save proposal. Please try submitting your proposal again.<br/>";
        }
      }
    }

  }

  // get page data and list
  $data = "";
  $list = "";
  if($step == 3)
  {
    $customerName = getCustomerName($_SESSION['customerID']);
    $data = getLineItems($_SESSION['serviceID']);
    $list = "includes/lineItemsList.php";
  }
  else if($step == 2)
  {
    $customerName = getCustomerName($_SESSION['customerID']);
    $data = getServices();
    $list = "includes/serviceList.php";
  }
  else
  {
    // default to step 1
    $data = getCustomers();
    $list = "includes/customerList.php";
  }
?>


<!-- Primary Page Layout
–––––––––––––––––––––––––––––––––––––––––––––––––– -->
<div class="container">
  <div class="row">
    <div class="column menu-box">

      <h4>New Proposal - Step <?php echo $step; ?></h4>

      <?php 
        if(!empty($errors)) {
          echo "<div class='notice-area'>$errors</div>";
        } 
        else if(!empty($success)) {
          echo "<div class='success-area'>$success</div>";
        }
      ?>

      <form id='createProposalForm' name='createProposalForm' method="POST" action="#">
        <?php include("$list"); ?>
      </form>
    </div>
  </div>
</div>


<?php include("includes/footer.php") ?>