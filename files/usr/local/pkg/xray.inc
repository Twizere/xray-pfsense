<?php
function xray_restart() {
    mwexec("service xray restart ");
}

function xray_stop() {
    mwexec("service xray stop ");
}

function xray_start() {
    mwexec("service xray stop ");
}
?>
