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

if ( ! isset($_REQUEST['profile_id']) ){
	$_SESSION['error'] = "Missing profile id";
	header('Location: index.php');
	return;
}

$stmt = $pdo->prepare('Select * from Profile 
      where profile_id = :pid and user_id = :uid');
$stmt->execute(array( ':pid' => $_REQUEST['profile_id'],
	  ':uid' => $_SESSION['user_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if ( $profile === false) {
    $_SESSION['error'] = "Could not load profile";
	header('Location: index.php');
	return;
}
//handle incoming data
if ( isset($_POST['first_name']) && isset($_POST['last_name']) && 
     isset($_POST['email']) && isset($_POST['headline']) && 
	 isset($_POST['summary']) ) { 
 
    $msg = validateProfile();
	 if ( is_string($msg) ) {
		 $_SESSION['error'] = $msg;
		 header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
		 return;
	 }
	 
//validate position ebtries if present

	 $msg = validatePos();
     if ( is_string($msg) ) {
		 $_SESSION['error'] = $msg;
		 header("Location: edit.php?profile_id=" . $_REQUEST["profile_id"]);
		 return;
	 }  
		
		$stmt = $pdo->prepare('UPDATE Profile SET 
		  first_name = :fn, last_name = :ln , 
		  email = :em, headline = :he, summary = :su
		  where profile_id = :pid AND user_id=:uid');
    $stmt->execute(array(
        ':pid' => $_REQUEST['profile_id'],
		':uid' => $_SESSION['user_id'],
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'])
		);
		
$stmt = $pdo->prepare('DELETE FROM Position_new WHERE profile_id=:pid');
$stmt->execute(array( ':pid' => $_REQUEST['profile_id']));

//$profile_id = $pdo->lastInsertId();

// insert the position entries
		$r_rank = 1;
		for($i=1; $i<=9; $i++) {
			if ( ! isset($_POST['y_year'.$i]) ) continue;
			if ( ! isset($_POST['description'.$i]) ) continue;
			$y_year = $_POST['y_year'.$i];
			$description = $_POST['description'.$i];
			
			$stmt = $pdo->prepare("INSERT INTO Position_new 
			       (profile_id,r_rank, y_year, description)
			        VALUES (:pid, :r_rank, :y_year, :description)");
			$stmt->execute(array(
				':pid' => $profile_id,
				':r_rank' => $r_rank,
				':y_year' => $y_year,
				':description' => $description)
				);
				$r_rank++;
		}
		
		$_SESSION['sucess'] = "profile updated ";
		header("Location: index.php");
		return;
	 }

//load position table rows
$positions = loadPos($pdo, $_REQUEST['profile_id']);
?>


<!DOCTYPE html>
<html>
<head>
<title>Priyanka Labh edit</title>
</head>
<body>
<div class="container">
<h1>Editing Profile for <?= htmlentities($_SESSION['name']); ?></h1>
<?php flashMessages();?>
<form method="post" action="edit.php">
<input type="hidden" name="profile_id" 
value="<?=htmlentities($_GET['profile_id']); ?>" 
/>
<p>First Name:
<input type="text" name="first_name" size="60"
value="<?= htmlentities($profile['first_name']); ?>" 
/></p>
<p>Last Name:
<input type="text" name="last_name" size="60"
value="<?= htmlentities($profile['last_name']); ?>" 
/></p>
<p>Email:
<input type="text" name="email" size="30"
value="<?= htmlentities($profile['email']); ?>" 
/></p>
<p>Headline:<br/>
<input type="text" name="headline" size="80"
value="<?= htmlentities($profile['headline']); ?>" 
/></p>
<p>Summary:<br/>
<textarea name="summary" rows="8" cols="80">
<?= htmlentities($profile['summary']); ?>
</textarea>


<?php
$pos = 0;
echo('<p>Position: <input type="submit" id="addPos" value="+">'."\n");
echo('<div id="position_fields">'."\n");
foreach($positions as $position) {
	$pos++;
	echo('<div id="position'.$pos.'">'."\n");
	echo('<p>Year: <input type="text" name="y_year'.$pos.'"');
	echo('value="'.$position['y_year'].'"/>'."\n");
	echo('<input type="button" value="-"');
	echo('onclick="$(\'#position'.$pos.'\').remove(); return false;">'."\n");
	echo("</p>\n");
	echo('<textarea name="description'.$pos.'" rows="8" cols="80">'."\n");
	echo(htmlentities($position['description'])."\n");
	echo("\n</textarea>\n</div>\n");
}
echo("</div></p>\n");
?>

<p>
<input type="submit" value="Save">
<input type="submit" name="cancel" value="Cancel">
</p>
</form>
<script>
countPos = <?= $pos ?>;;

$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="y_year'+countPos+'" value="" /> \
            <input type="button" value="-" \
            onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
</script>
</div>
</body>
</html>