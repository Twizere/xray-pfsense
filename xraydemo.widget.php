<?php
/*
 * This is a simple demo widget for pfSense.
 * The widget will display some basic system information.
 * 
 * Copyright (C) 2024 
 * All rights reserved.
 */

// Make sure the user is authenticated
// if (!is_array($config['system']['widgets']) || !in_array('xraydemo', $config['system']['widgets'])) {
//     return;
// }



// You can collect whatever data you want here. For this example, let's get the system uptime and some basic stats.
$uptime = shell_exec("uptime -p");
$cpu_usage = sysctl("kern.cp_time");
$load = sysctl("vm.loadavg");

?>

<div class="widget-content">
    <h3 class="widget-title"><?php echo gettext('XRay Demo Widget'); ?></h3>
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
