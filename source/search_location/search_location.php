<?php
	require '../config.php';
	require '../utils.php';
	$conn = acc_header();
	$acc = get_acc($conn);
	
	if(!isset($_GET["search"])) {
		header("Location: index.php");
		exit();
	}
	
	$search = mysqli_real_escape_string($conn, $_GET["search"]);
	//$query = "SELECT * FROM location WHERE name LIKE '%$search%'";
	$query = "SELECT * FROM (SELECT * FROM location WHERE LOWER(name) LIKE LOWER('%$search%')) AS location LEFT JOIN (select loc_id, AVG(rate) AS avg_rate FROM checkin GROUP BY loc_id) AS avg_rates ON location.id = avg_rates.loc_id";
	$result = $conn->query($query);
	
	if(!$result) {
		if(CFG_DEBUG)
			die('An error occurred while getting search results : ' . mysqli_error($conn));
		else
			die('An error occurred. We will look at it as soon as possible!');
	}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="css/search_location.css" type="text/css" rel="stylesheet">
</head>
<body>

<div class="topnav">
  <a href="../main_page/main_page.php">Home</a>
  <a href="../friends/friends.php">Friends</a>
  <a href="../messages/messages.php">Messages</a>
  <a href="../myProfile/myProfile.php">myProfile</a>
  <a href="../index.php">Logout</a>
  <div class="search-container">
    <form action="../search_location/search_location.php">
      <input type="text" placeholder="Search.." name="search">
      <button class="button">?</button>
    </form>
  </div>
</div>

<div st mhyle="padding-left:16px">
  <h2>&emsp;&emsp;You Searched: <?php echo htmlspecialchars($_GET['search']); ?></h2>
</div>
<?php
	while($row = $result->fetch_assoc()) {
?>
<div class="rectangle">
    <div class="column left" style="background-color:#aaa;">
<?php
	$sql = "SELECT * FROM Photo WHERE loc_id = ".$row['id']." ORDER BY id LIMIT 1";
	$result2 = $conn->query($sql);
	$photo_name = "";
	while($row2 = $result2->fetch_assoc() ){
		$photo_name = "locImage".$row['id']."-".$row2['id'].".png";
		break;
	}
	if($photo_name != "") {
		echo "<img class=\"img-thumbnail\" src=\"../images/$photo_name\">";
	}
	else {
?>
    <img class="img-thumbnail" src="../images/first.jpg">
<?php
	}
?>
    </div>
    <div class="column right" style="background-color:#black;">
		<a href="../location/location.php?search=<?php echo $row['name']; ?>"><font size="5"><?php echo $row['name']; ?></font></a>
		&emsp;<p><?php echo $row['info']; ?></p><br>
		<p>Rating: <?php echo $row['avg_rate']; ?></p>
		</div>
</div>
<hr class="style1"  width=60%>
<?php
	}
?>
</body>
</html>
