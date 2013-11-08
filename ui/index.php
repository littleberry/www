<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Person.class.php");

if (isset($_GET["data"])) {
	$_SESSION['logged_in'] = $_GET["data"];
	$controlUI = 1;
} else {
	$controlUI = 0;
}
?>
<!DOCTYPE html>
	<?php	
if ($controlUI) {
include('header.php');
} else {
include('header_login.php'); //add header.php to page
}
?>
<section id="page-content" class="page-content">
<?php if ($controlUI) { ?>
HELLO! THIS PAGE WILL EVENTUALLY BE THE DASHBOARD.
<?php } else { ?>
START YOUR FREE TRIAL TODAY! WE OFFER AMAZING DEALS ON THE BEST TIME TRACKING SOFTWARE ANYWHERE!
CLICK HERE TO <a href="login.php">LOG IN</a>
<?php } ?>
</section>
<footer id="site-footer" class="site-footer">
</footer>
</body>
</html>