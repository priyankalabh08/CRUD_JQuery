<?php
require_once "pdo.php";
session_start();
?>


<!DOCTYPE html>
<html>
<head>
<title>Please Log In</title>
<!-- bootstrap.php - this is HTML -->

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" 
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" 
    crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" 
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" 
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" 
    crossorigin="anonymous">
	
	<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
<h1>Profile information</h1>
<table border="1">
<thead><tr>
<th>First_Name</th>
<th>Last_Name</th>
<th>Email</th>
<th>Headline</th>
<th>Summary</th>
</tr></thead>

</div>

<?php
$stmt = $pdo->query("SELECT first_name, last_name, email, headline, summary FROM Profile");
while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ) {
    echo "<tr><td>";
    echo(htmlentities($row['first_name']));
    echo("</td><td>");
    echo(htmlentities($row['last_name']));
    echo("</td><td>");
    echo(htmlentities($row['email']));
	echo("</td><td>");
	echo(htmlentities($row['headline']));
    echo("</td><td>");
	echo(htmlentities($row['summary']));
    echo("</td><td>");
	echo("</td></tr>\n");
}
?>
</table>

<a href="index.php">Done</a>
</body>
</html>