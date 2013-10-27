<?php
	//require_once($_SERVER["DOCUMENT_ROOT"] . "/usercake/models/config.php");
	require_once("../common/common.inc.php");
	require_once("../classes/Person.class.php");

//usercake stuff, take out for now.
//if(!isUserLoggedIn()){
//	$_SESSION["redirect"] = $_SERVER["PHP_SELF"];
//	header( 'Location: usercake/login.php' ) ;

//}	

//if(isset($_POST["action"]) and $_POST["action"] == "login") {
//	processForm();
//}else{
//	displayForm(array(), array(), new Person(array()));
if (isset($_GET["data"])) {
	$_SESSION['person'] = $_GET["data"];
}
?>
<!DOCTYPE html>
	<?php	include('header.php'); //add header.php to page ?>
<section id="page-content" class="page-content">
HELLO, I AM THE INDEX.PHP PAGE.
THIS PAGE WILL EVENTUALLY BE THE DASHBOARD.

</section>
<footer id="site-footer" class="site-footer">

</footer>
</body>
</html>
