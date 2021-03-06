<?php
	require '../config.php';
	require '../utils.php';
	$conn = acc_header();
	
  $id = $_SESSION['id'];
  $_SESSION['id'] = $id;
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="css/location.css" type="text/css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<style> 
input[type=text] {
    width: 60%;
    padding: 12px 20px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid red;
    border-radius: 4px;
}
</style>

</head>
<body>

<div class="topnav">
  <a href="../main_page/main_page.php">Home</a>
  <a href="../friends/friends.php">Friends</a>
  <a href="../messages/messages.php">Messages</a>
  <a href="../myProfile/myProfile.php">myProfile</a>
  <a href="../logout.php">Logout</a>
  <div class="search-container">
    <form action="../search_location/search_location.php">
      <input type="text" placeholder="Search.." name="search">
      <button class="button">?</button>
    </form>
  </div>
</div>

<?php
  $loc_id;
  $number_of_checkin;
  $adre;
  $user_id;
  
  $search = mysqli_real_escape_string($conn, $_GET["search"]);
  $sql = "SELECT * FROM (SELECT * FROM location WHERE name = '$search') AS location LEFT JOIN (select loc_id, AVG(rate) AS avg_rate, COUNT(*) AS num_of_checkin FROM checkin GROUP BY loc_id) AS avg_rates ON location.id = avg_rates.loc_id";
  $loc;
  $photo_name = "";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc() ){
      $loc_id = $row['id'];
	  $loc = $row;
	  
		$sql = "SELECT * FROM Photo WHERE loc_id = ".$loc_id." ORDER BY id LIMIT 1";
		$result = $conn->query($sql);
		if(!$result) {
			if(CFG_DEBUG)
				die('An error occurred while trying to get location photo : ' . mysqli_error($conn));
			else
				die('An error occurred. We will look at it as soon as possible!');
		}
		
		while($row = $result->fetch_assoc() ){
			$photo_name = "locImage".$loc_id."-".$row['id'].".png";
			break;
		}
		break;
    }
  }
  else {
	header("Location: ../main_page/main_page.php");
	exit();
  }
  $sql = "select id from GeneralUser where username = '".$id."' ;";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc() ){
      $user_id = $row['id'];
    }
  }
  
  $sql1;
  if (isset($_POST['check_button'])){
    $date = date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s"))+150);
    $comment = $_POST['fname'];
    $rate = $_POST['frate'];
    $sql1 = " insert into Checkin (loc_id, time , text, rate ,user_id) values ('".$loc_id."', '".$date."', '".$comment."' , '".$rate."','".$user_id."') ;";
  $result = $conn->query($sql1);
  }

  $info;
  $address;
  $sql = "select address from Location where id = '".$loc_id."' ;";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc() ){
      $address = $row['address'];
    }
  }
  $sql = "select info from Location where id = '".$loc_id."' ;";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc() ){
      $info = $row['info'];
    }
  }
?>

<div style="padding-left:16px">
  <h2>Location <?php echo $_GET['search']; ?></h2>  <!-- there will be php database content-->
</div>


<div class="rectangle">
  <div class="column left" style="background-color:#aaa;">
<?php
	if($photo_name != "") {
		echo "<img class=\"img-thumbnail\" src=\"../images/$photo_name\">";
	}
	else {
?>
    <img class="img-thumbnail" src="../images/first.jpg">
<?php
	}
?>
    <font style="color:red;">Rate:  <?php echo (float)$loc['avg_rate']; ?></font><br>
    <font style="color:red;">Number of Checkins: <?php echo (int)$loc['num_of_checkin']; ?></font>
  </div>
  <div class="column right" style="background-color:#bbb;">
    <h2><?php echo $search; ?></h2>
    <p> info: <?php echo $loc['info']; ?></p>,
    <p> address: <?php echo $loc['address']; ?></p>
  </div>
  <form class= "form-container" role = "form" method = "post">
  <label style="color:red;" for="fname"><br>&emsp;Enter Comment: </label><br> <br>
      &emsp;<input type="text" name="fname" placeholder="Comment..">
  <br>
  <div class="slidecontainer">

  <p style="color:red;">&emsp;Rate: <span id="demo" style="color:black;"></span></p>
  &emsp;<input type="range" min="1" max="5" value="5" class="slider" id="myRange" name = "frate">
    &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<button    style=" background: transparent;" type = "submit" name = "check_button">
      <img src="images/check.png" width="90" height="50" align="center"/>
    </button>
  </form>
</div>

<script>
var slider = document.getElementById("myRange");
var output = document.getElementById("demo");
output.innerHTML = slider.value;

slider.oninput = function() {
  output.innerHTML = this.value;
}
</script>
</div>

<hr class=style1  width="60%">

<?php

  $sql = "select * from (select * from Checkin where loc_id = '".$loc_id."' order by time asc) as checkin left join (select checkin_id, count(*) as num_of_likes from checkin_likes group by checkin_id) as checkin_likes on checkin.id = checkin_likes.checkin_id";
  $result = $conn->query($sql);
  if($result->num_rows == 0)
    echo "<p> There is no check-in</p>";
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc() ){
      $sql1 = "select username from GeneralUser where id = '".$row['user_id']."';";
      $result1 = $conn->query($sql1);
      if($result1->num_rows > 0){
        while($row1 = $result1->fetch_assoc() ){
          $checker_name = $row1['username'];
        }
      }

      echo "<div class= "."rectangle_1"." >";
        echo "<div class= "."column_1 left1"." style= "."background-color:#aaa;".">";
		if (file_exists("../images/profile".$row['user_id'].".png")) {
			echo "<img class=\"img-thumbnail\" src=\"../images/profile".$row['user_id'].".png\" width=\"200\">";
		}
		else {
			echo "<img class=\"img-thumbnail\" src=\"../images/elon.png\">";
		}
        echo "</div>";
        echo "<div class="."column_1 right1"." style="."background-color:#black;".">";
        echo "<a href="."../check_in_comment/check_in_comment.php?var=".$row['id']."&var2=".$row['user_id'].""."><font size="."5".">".$checker_name." Has checked-in: ".$search."</font></a>
        &emsp;<p>".$checker_name."'s comment: ".$row['text']."</p><br>
        <p>".$row['time'].".&emsp;&emsp;&emsp;Number of Likes: ".(int)$row['num_of_likes']."</p>";
        echo "</div>";
      echo "</div>";
      echo "<hr class=style1  width=60%> ";
    }
  }
?>



</body>
</html>
