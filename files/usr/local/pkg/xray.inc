<?php
function xray_start() {
    mwexec("/usr/local/bin/xray -config /usr/local/etc/xray/config.json");
}

function xray_stop() {
    mwexec("pkill xray");
}
?>
