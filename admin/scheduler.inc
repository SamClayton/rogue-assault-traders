<?php
// Run Scheduler

echo "<FORM ACTION=scheduler.php TARGET=_blank METHOD=POST>";
echo "<INPUT TYPE=HIDDEN NAME=swordfish VALUE=$_POST[swordfish]>";
echo "<INPUT TYPE=HIDDEN NAME=adminexecuted VALUE=1>";
echo "<table><TR nowrap><TD>Click the RUN button if you want to exit the Admin program and run the Scheduler.&nbsp;&nbsp;</TD>";
echo "<TD ALIGN=RIGHT><INPUT TYPE=SUBMIT NAME=command VALUE=\"RUN\"></TD></TR>";
echo "</table></form>";
?>
