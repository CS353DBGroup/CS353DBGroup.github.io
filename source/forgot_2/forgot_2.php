<?php
ob_start();
  session_start();

  $servername = "localhost";
  $username = "serdar.erkal";
  $password = "7ydo8hj2";
  $dbname = "serdar_erkal";
  // Create connection
  $conn = new mysqli($servername, $username, $password,$dbname);
  // Check connection
  if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
  }
  else{
    "Connected successfully";
  }
  $password = $_POST['password'];
  $var = $_GET['var'];

  if (isset($_POST['change_button'])){
    $sql = " update GeneralUser set password = '".$password."' where id = '".$var."';";
  	$result = $conn->query($sql);
  	if($result->num_rows > 0){
		while($row = $result->fetch_assoc() ){
			$control = $row['id'];
		}
	}
    header("Location:/~serdar.erkal/index.php");
    exit;
    
  }
?>
<!DOCTYPE html>
<html lang="en">

    <head>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
		<link href="css/forgot_2.css" type="text/css" rel="stylesheet">
    </head>

    <body>
	<div class ="container-fluid bg">
		<div class ="row">
			<div class = "col-md-4 col-sm-4 col-xs-12"><div class="login-image"></div>​</div>
			<div class = "col-md-4 col-sm-4 col-xs-12">
			
			<form class= "form-container" role = "form" action = "<?php echo htmlspecialchars($_SERVER['SELF']);?>" method = "post">
			<h1 align="center"> Forgot Password</h1>
			  <div class="form-group">
				<label for="exampleInputEmail1">New password</label>
				<input type="password" name = "password"class="form-control" id="exampleInputEmail1" placeholder="password">
			  </div>
			  <div class="form-group">
				<label for="exampleInputPassword1">Confirm</label>
				<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Confirm">
			  </div>

			  <button class="btn btn-success btn-block" type = "submit" name = "change_button">Change Password</button>
			</form>
			
			</div>
			
			<div class = "col-md-4 col-sm-4 col-xs-12"></div>	
		</div>
		
	</div>
	
    </body>

</html>