<?php
/*
 * xraydemo.widget.php
 * 
 * This is a simple demo widget for pfSense.
 * The widget will display the XRay service status, certificate locations, and client list in table format.
 * 
 * Copyright (C) 2024 YourName
 * All rights reserved.
 */

// Path to the Xray configuration file
$config_file = '/usr/local/etc/xray/config.json';

// Path to the log file
$log_file = '/var/log/xray_service.log';

// Get the XRay service status
$status = shell_exec("service xray status");
$is_running = strpos($status, "running") !== false ? "Running" : "Stopped";

// Check if the configuration file exists
if (file_exists($config_file)) {
    $config_data = json_decode(file_get_contents($config_file), true);

    // Extract the certificates and clients data
    $certificates = [];
    $clients = [];

    if (isset($config_data['inbounds'][0]['streamSettings']['tlsSettings']['certificates'])) {
        $certificates = $config_data['inbounds'][0]['streamSettings']['tlsSettings']['certificates'];
    }

    if (isset($config_data['inbounds'][0]['settings']['clients'])) {
        $clients = $config_data['inbounds'][0]['settings']['clients'];
    }
} else {
    $config_data = null;
}

// Display error if config file is missing
if (!$config_data) {
    echo "<div class='widget-content'><p>Error: Xray config file not found.</p></div>";
    return;
}

// Debugging output (log to the system log)
error_log("XRay VPN Widget - Xray config file found: " . $config_file);
?>

<div class="widget-content">
    <h3 class="widget-title"><?php echo gettext('XRay VPN Status'); ?></h3>

    <!-- XRay Service Status -->
    <div class="status">
        <strong><?php echo gettext('XRay Service Status'); ?>:</strong> <?php echo $is_running; ?>
    </div>

    <!-- Log File Location -->
    <div class="log-file">
        <strong><?php echo gettext('Log File Location'); ?>:</strong> <?php echo htmlspecialchars($log_file); ?>
    </div>

    <?php if (isset($config_data)): ?>
        <!-- Certificates Table -->
        <h4><?php echo gettext('Certificates:'); ?></h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><?php echo gettext('Certificate File'); ?></th>
                    <th><?php echo gettext('Key File'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($certificates as $cert): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cert['certificateFile']); ?></td>
                        <td><?php echo isset($cert['keyFile']) ? htmlspecialchars($cert['keyFile']) : 'N/A'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Clients Table -->
        <h4><?php echo gettext('Clients:'); ?></h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><?php echo gettext('ID'); ?></th>
                    <th><?php echo gettext('Email'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($client['id']); ?></td>
                        <td><?php echo htmlspecialchars($client['email']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?php echo gettext('Error: Unable to load Xray configuration.'); ?></p>
    <?php endif; ?>
</div>

<style>
    .widget-content {
        padding: 10px;
    }
    .widget-title {
        font-size: 1.2em;
        margin-bottom: 10px;
    }
    .status, .log-file {
        margin-bottom: 10px;
        font-size: 1em;
    }
    .table {
        width: 100%;
        margin-bottom: 15px;
        border-collapse: collapse;
    }
    .table-bordered {
        border: 1px solid #ddd;
    }
    .table th, .table td {
        padding: 8px 15px;
        text-align: left;
        border: 1px solid #ddd;
    }
    .table th {
        background-color: #f2f2f2;
    }
</style>
