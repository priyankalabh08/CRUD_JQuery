<?php
session_start();
require_once "pdo.php";
require_once "util.php";

if ( isset($_POST['cancel'] ) ) {
    header("Location: index.php");
    return;
}

if  (! isset($_SESSION['user_id'] ) ) {
	die("ACCESS DENIED");
	return;
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Priyanka Labh Add</title>
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
<div class="container">
<h1>Adding Profile</h1>
<body>
<?php

$failure = false; 

 //handle the incoming data
if ( isset($_POST['first_name']) && isset($_POST['last_name'])  && 
     isset($_POST['email']) && isset($_POST['headline']) && 
     isset($_POST['summary'])){ 
 

     $msg = validateProfile();
	 if ( is_string($msg) ) {
		 $_SESSION['error'] = $msg;
		 header("Location: add.php");
		 return;
	 }
	 
//validate position ebtries if present

	 $msg = validatePos();
     if ( is_string($msg) ) {
		 $_SESSION['error'] = $msg;
		 header("Location: add.php");
		 return;
	 } 
		
		// data is valid - time to insert
       $stmt = $pdo->prepare('INSERT INTO Profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fn, :ln, :em, :he, :su)');
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
		);
		$profile_id = $pdo->lastInsertId();
		
		// insert the position entries
		$r_rank = 1;
		for($i=1; $i<=9; $i++) {
			if ( ! isset($_POST['y_year'.$i]) ) continue;
			if ( ! isset($_POST['desc'.$i]) ) continue;
			$y_year = $_POST['y_year'.$i];
			$desc = $_POST['desc'.$i];
			
			$stmt = $pdo->prepare('INSERT INTO Position_new (profile_id, r_rank, y_year, description)
			        VALUES (:pid, :r_rank, :y_year, :desc)');					
			$stmt->execute(array(
				':pid' => $profile_id,
				':r_rank' => $r_rank,
				':y_year' => $y_year,
				':desc' => $desc) 
				);
				$r_rank++;
		}
		$_SESSION['success'] = "profile added ";
		header("Location: index.php");
		return;
	 }
		       
?>

<?php flashMessages(); ?>
<form method="post">
<p>First Name:
<input type="text" name="first_name" size="60"/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"/></p>
<p>Email:
<input type="text" name="email" size="30"/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80"></textarea>
<p>
Position: <input type="submit" id="addPos" value="+" >
<div id="position_field">
</div>
</p>
<p>
<input type="submit" value="Add">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script>
countPos = 0;

$(document).ready(function(){
	window.consle && console.log( "Document ready called");
	$('#addPos').click(function(event){
		event.preventDefault();
		if ( countPos >= 9 ) {
			alert("Maximum of nine position entries exceeded");
			return;
		}
		countPos++;
		window.consle && console.log( "Adding position " +countPos);
		$('#position_field').append(
		'<div id="position'+countPos+'"> \
		<p> Year: <input type="text" name="y_year'+countPos+'" value="" /> \
		<input type="button" value="-" \
		     onclick="$(\'#position'+countPos+'\').remove(); return false;"></p> \
			 <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
			 </div>');
	});
});
</script>
</div>
</body>
</html>