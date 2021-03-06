<?php
	require '../config.php';
	require '../utils.php';
	$conn = acc_header();
	
  $id = $_SESSION['id'];
  $_SESSION['id'] = $id;
  $check_in_id = $_GET['var'];
  $checker_id = $_GET['var2'];


  $user_id;
  $sql1 = "select id from GeneralUser where username = '".$id."';";
  $result1 = $conn->query($sql1);
  if($result1->num_rows > 0){
    while($row1 = $result1->fetch_assoc() ){
      $user_id = $row1['id'];
    }
  }
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="css/check_in_comment.css" type="text/css" rel="stylesheet">

<style>
input[type=text], select {
    width: 65%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type=submit] {
    width: 50%;
    background-color: #4CAF50;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type=submit]:hover {
    background-color: #45a049;
}

</style>
</head>
<body>

<div class="topnav">
  <a class="active" href="../main_page/main_page.php">Home</a>
  <a href="../friends/friends.php">Friends</a>
  <a href="../myProfile/myProfile.php">myProfile</a>
  <a href="../logout.php">Logout</a>
  <div class="search-container">
    <form action="../search_location/search_location.php">
      <input type="text" placeholder="Search.." name="search">
      <button class="button">?</button>
    </form>
  </div>
</div>

<div style="padding-left:16px">
  <h2>Check-in Comments</h2>  <!-- there will be php database content-->
</div>

<?php
  $sql = "select * from (select * from Checkin where id = ".$check_in_id.") as checkin left join (select checkin_id, count(*) as num_of_likes from checkin_likes group by checkin_id) as checkin_likes on checkin.id = checkin_likes.checkin_id";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc() ){

      $sql1 = "select username from GeneralUser where id = '".$checker_id."';";
      $result1 = $conn->query($sql1);
      if($result1->num_rows > 0){
        while($row1 = $result1->fetch_assoc() ){
          $checker_name = $row1['username'];
        }
      }

      $sql1 = "select name from Location where id = '".$row['loc_id']."';";
      $result1 = $conn->query($sql1);
      if($result1->num_rows > 0){
        while($row1 = $result1->fetch_assoc() ){
          $checkin_location = $row1['name'];
        }
      }
      echo "<div class= "."rectangle"." >";
        echo "<div class= "."column left"." style= "."background-color:#aaa;".">";
		if (file_exists("../images/profile".$checker_id.".png")) {
			echo "<img class=\"img-thumbnail\" src=\"../images/profile".$checker_id.".png\" width=\"200\">";
		}
		else {
			echo "<img class=\"img-thumbnail\" src=\"../images/elon.png\">";
		}
        echo "</div>";
        echo "<div class="."column right"." style="."background-color:#black;".">";
        echo "<a><font size="."5".">".$checker_name." Has checked-in: ".$checkin_location."</font></a>
        &emsp;<p>".$checker_name."'s comment: ".$row['text']."</p><br>
        <p>".$row['time'].".&emsp;&emsp;&emsp; Number of like: ".(int)$row['num_of_likes']."</p>";
        echo "</div>";
      echo "</div>";
      echo "<hr class=style1  width=60%> ";

    }
  }

?>

<h1><center>Comments</h1>

<?php
  $sql = "select comment_id from checkin_comments where checkin_id = '".$check_in_id."';";
  $result = $conn->query($sql);
  if($result->num_rows > 0){
    while($row = $result->fetch_assoc() ){
      $sql1 = "select * from (select * from Comment where id = ".$row['comment_id'].") as comment join (select comment_id, count(*) as num_of_likes from comment_likes group by comment_id) as comment_likes on comment.id = comment_likes.comment_id";
      $result1 = $conn->query($sql1);
      if($result1->num_rows > 0){
        while($row1 = $result1->fetch_assoc() ){
          $sql2 = "select username from GeneralUser where id = '".$row1['user_id']."';";
          $result2 = $conn->query($sql2);
          if($result2->num_rows > 0){
            while($row2 = $result2->fetch_assoc() ){
              $commenter_name = $row2['username'];

              echo "<div class= "."rectangle"." >";
                echo "<div class= "."column left"." style= "."background-color:#aaa;".">";
				if (file_exists("../images/profile".$user_id.".png")) {
					echo "<img class=\"img-thumbnail\" src=\"../images/profile".$row1['user_id'].".png\" width=\"200\">";
				}
				else {
					echo "<img class=\"img-thumbnail\" src=\"../images/elon.png\">";
				}
                echo "</div>";
                echo "<div class="."column right"." style="."background-color:#black;".">";
                echo "<a><font size="."5".">".$commenter_name." Has Commented: </font></a>
                &emsp;<p>".$row1['text']."</p><br>
                <p>".$row1['time'].".&emsp;&emsp;&emsp;";
				
				$rc_query = "SELECT * FROM comment_reports WHERE user_id =$user_id and comment_id =".$row1['id'];
				$rc_result = $conn->query($rc_query);
				
				if(!$rc_result) {
					if(CFG_DEBUG)
						die('An error occurred while checking reports : ' . mysqli_error($conn));
					else
						die('An error occurred. We will look at it as soon as possible!');
				}
				$rc_row = mysqli_fetch_array($rc_result);
				
				if($rc_result->num_rows < 1) {
					echo "<a href=\"../report_comment.php?comment_id=".$row1['id']."\">Report</a>&emsp;&emsp;";
				}
				echo " Number of like: ".(int)$row1['num_of_likes']."</p>";
                echo "</div>";
              echo "</div>";
              echo "<hr class=style1  width=60%> ";

            }
          }
        }
      }

    }
  }


  /*submit new comment
  *
  *
  */

  $date = date('Y-m-d H:i:s',strtotime(date("Y-m-d H:i:s"))+150);
  if (isset($_POST['submit_button'])){
    $comment = mysqli_real_escape_string($conn, $_POST['comment_text']);
    $sql = " insert into Comment (user_id , text, time) values (".$user_id.", '".$comment."', '".$date."') ;";
    $result = $conn->query($sql);
    $sql = " insert into checkin_comments (checkin_id , comment_id) values (".$check_in_id.", LAST_INSERT_ID()) ;";
    $result = $conn->query($sql);
  }
?>



<form class= "form-container" role = "form" method = "post"><center>
    <input type="text" id="fname" name="comment_text" placeholder="Comment..">
    <input type="submit" name= "submit_button"  value="Submit Your Comment">
  </form>



</body>
</html>
