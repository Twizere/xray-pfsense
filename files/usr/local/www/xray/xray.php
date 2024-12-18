<?php
require_once("guiconfig.inc");

if ($_POST['save']) {
    write_config("Xray configuration saved.");
    mwexec("service xray restart");
}

include("head.inc");
?>
<form method="post">
  <input type="text" name="loglevel" value="info" />
  <input type="submit" name="save" value="Save" />
</form>
