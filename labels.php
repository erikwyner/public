<?php
require '../functions/connect.php';
require '../functions/meta.html';
menu('public','labels');
$db = dbconnect();
?>
<!doctype html>
<html>
<head>
<title>Photo Labeler</title>
</head>
<body>
<?php
$index = $_POST['index'];
$list = scandir('../images/photos');
switch($_POST['view']) {
	case 'Next': $index++; break;
	case 'Previous': $index--; break;
	case 'Unlabeled':
		while(!($unlabeled)) {
			$current++;
			if(!($row = mysqli_fetch_array(mysqli_query($db,"SELECT 1 FROM photos WHERE photo = '$list[$index]'")))) {
				$unlabeled = 1;
			}
		}
	break;
}
if($_POST['save'] or $_POST['simpleSave']) {
	mysqli_query($db,"DELETE FROM photos WHERE photo = '$current'");
	tag('Public');
	tag('Roster');
	tag('Staff');
	tag('Room Heads');
	tag('Tour Guides');
	tag('Thriller');
	tag('Makeup Artisits');
	tag('Festival');
	$result = mysqli_query($db,"SELECT room FROM rooms");
	while($row = mysqli_fetch_array($result)) {
		tag($row['room']);
	}
	if($_POST['save']) {
		$result = mysqli_query($db,"SELECT firstName, lastName FROM roster WHERE code != 'X'");
		while($row = mysqli_fetch-array($result)) {
			tag($row['firstName']." ".$row['lastName']);
		}
	}
}
echo "<form method='post'>";
echo "<select name='index'>";
$a = 3;
$photo = $list[3];
while($list[$a]) {
	if($a == $index) {
		$selected = ' selected';
		$photo = $list[$a];
	}
	echo "<option".$selected.">".$list[$a]."</option>";
	$a++;
}
echo "</select>";
?>
<p class='main'>View:
	<input type='submit' name='view' value='Selected'>
	<input type='submit' name='view' value='Previous'>
	<input type='submit' name='view' value='Next'>
	<input type='submit' name='view' value='Unlabeled'>
</p>
<img style='border: solid red 3px' src='../images/photos/<?= $photo ?>'>
<div class='flexbox-menu'>
<?php
echo "<div class='menu-item'>";
checkbox('Public');
checkbox('Roster');
echo "</div><div class='menu-item'>";
checkbox('Staff');
checkbox('Room Heads');
checkbox('Tour Guides');
checkbox('Thriller');
checkbox('Makeup Artists');
echo "<input type='submit' name='simpleSave' value='Save'>";
echo "</div><div class='menu-item'>";
$result = mysqli_query($db,"SELECT firstName, lastName FROM roster WHERE code != 'X' ORDER BY code, room, lastName, firstName");
while($row = mysqli_fetch_array($result)) {
	checkbox($row['firstName']." ".$row['lastName']);
}
?>
<p><input type='submit' name='save' value='Save'></p>
</form>
</body>
</html>
<?php
function checkbox($group) {
	global $tagged;
	echo "<br><input type='checkbox' name='".str_replace(' ','',$group)."' ".($tagged[$group] ? ' checked' : '')."> ".$group;
}
function tag($group) {
	$label = str_replace(' ','',$group);
	global $db, $current;
	if($_POST[$label]) {
		mysqli_query($db,"INSERT INTO photos SET photo = '$current', tag = '$group'");
	}
}
