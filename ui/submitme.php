<html>
<head></head>
<body>
<?php 

echo "HERE IS SOME CODE IN A TABLE.";
?>
<form method="get" action="submitme.php"
<TABLE border=1px solid;>
<field type="hidden" value="<?php echo $_GET["timesheet_id"]?>">
<TR><TD>January 10</TD></TR>
<TR><TD>20</TD></TR>
</TABLE>
<button type="submit">Click to Submit</button>

1. add the project_assigned_by variable to get put into project_person when the project is set up.
2. When the user submits a timesheet, get out the variable for who assigned it.
3. Update the timesheet "timesheet_submitted" variable to 1.
4. When the pm that assgned it looks in their page they should see it. Likewise, people should only see unsubmitted timesheets.

</form>
</body>
</html>