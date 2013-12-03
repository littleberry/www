<!DOCTYPE html>
<html lang="en">
<head>
	<title>Web Test test Site</title>
<base href="<?php echo "$base";?>">
<link rel="stylesheet" type="text/css" href="<?php //echo "$base/$css"; ?>mystyles.css">
<style>
h1 {
		margin: 5px;
		font-size: 36px;
		padding: 0px 10px 0px 10px;
		background: #ffffff;
		color:blue;
		width:100%;
	}

	.test {
		margin: 5px;
		background: #ffffff;
		border: 1px solid #D0D0D0;
		color: red;
		display: block;
		width:100%;
		font-size: 36px;
	}
</style>
</head>
<body>

	<h1><?php echo $mytitle;?></h1>

	<p class="test"><?php echo $myText;?></p>
	<?php print_r($menu);?>
	<select>
	<option value="<?php echo $menu;?>"></option>
	</select>
	<?php echo($stuff)?>
	<?php echo($username)?>
</body>
</html>