<?php
/*
 * xraydemo.widget.php
 * 
 * This widget displays the status of the Xray service, log file location, and configuration.
 * 
 * Copyright (C) 2024 YourName
 * All rights reserved.
 */

// Collect Xray service status
$xray_status_cmd = "service xray status";
$xray_status_output = shell_exec($xray_status_cmd);

// Xray log file location and configuration file
$log_file = "/var/log/xray_service.log";
$config_file = "/usr/local/etc/xray/config.json";

// Check if commands return null or empty
if (!$xray_status_output) {
    $xray_status_output = "Error: Unable to fetch Xray service status.";
}

// Determine if Xray is running
$xray_status = strpos($xray_status_output, "is running") !== false ? "Running" : "Stopped";

// Debugging output (log to the system log)
error_log("XRay VPN Widget - Service Status: " . $xray_status_output);
error_log("XRay VPN Widget - Log File Location: " . $log_file);
error_log("XRay VPN Widget - Config File Location: " . $config_file);
?>

<div class="widget-content">
    <h3 class="widget-title"><?php echo gettext('XRay VPN Status'); ?></h3>
    <ul class="list-group">
        <li class="list-group-item">
            <strong><?php echo gettext('XRay Service Status'); ?>:</strong> 
            <span class="<?php echo $xray_status === 'Running' ? 'text-success' : 'text-danger'; ?>">
                <?php echo htmlspecialchars($xray_status); ?>
            </span>
        </li>
        <li class="list-group-item">
            <strong><?php echo gettext('Log File Location'); ?>:</strong> 
            <?php echo htmlspecialchars($log_file); ?>
        </li>
        <li class="list-group-item">
            <strong><?php echo gettext('Configuration File Location'); ?>:</strong> 
            <?php echo htmlspecialchars($config_file); ?>
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
    .text-success {
        color: green;
        font-weight: bold;
    }
    .text-danger {
        color: red;
        font-weight: bold;
    }
</style>
