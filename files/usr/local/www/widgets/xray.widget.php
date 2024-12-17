<?php
/*
 * xraydemo.widget.php
 * 
 * This is a simple demo widget for pfSense.
 * The widget will display some basic system information.
 * 
 * Copyright (C) 2024 YourName
 * All rights reserved.
 */

// Make sure the user is authenticated
// if (!is_array($config['system']['widgets']) || !in_array('xraydemo', $config['system']['widgets'])) {
//     return;
// }

// Collecting data
$uptime = shell_exec("uptime");
$cpu_usage = shell_exec("sysctl -n kern.cp_time");
$load = shell_exec("sysctl -n vm.loadavg");

// Check if any of the commands return null or empty
if (!$uptime) {
    $uptime = "Error: Unable to fetch uptime.";
}
if (!$cpu_usage) {
    $cpu_usage = "Error: Unable to fetch CPU usage.";
}
if (!$load) {
    $load = "Error: Unable to fetch load average.";
}

// Debugging output (log to the system log)
error_log("XRay VPN Widget - Uptime: " . $uptime);
error_log("XRay VPN Widget - CPU Usage: " . $cpu_usage);
error_log("XRay VPN Widget - Load Avg: " . $load);

?>

<div class="widget-content">
    <h3 class="widget-title"><?php echo gettext('XRay VPN Status'); ?></h3>
    <ul class="list-group">
        <li class="list-group-item">
            <strong><?php echo gettext('System Uptime'); ?>:</strong> <?php echo htmlspecialchars($uptime); ?>
        </li>
        <li class="list-group-item">
            <strong><?php echo gettext('CPU Usage'); ?>:</strong> <?php echo htmlspecialchars($cpu_usage); ?>
        </li>
        <li class="list-group-item">
            <strong><?php echo gettext('Load Average'); ?>:</strong> <?php echo htmlspecialchars($load); ?>
        </li>
    </ul>
</div>

<style>
    .widget-content {
        padding: 10px;
    }
    .widget-title {
        font-size: 1.2em;
        margin-bottom: 10px;
    }
    .list-group-item {
        font-size: 1em;
        padding: 8px 15px;
    }
</style>
