<?php
/*
 * xraydemo.widget.php
 * 
 * This is a simple demo widget for pfSense.
 * The widget will display the XRay service status, certificate locations, and client list.
 * 
 * Copyright (C) 2024 YourName
 * All rights reserved.
 */

// Path to the Xray configuration file
$config_file = '/usr/local/etc/xray/config.json';

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

    <?php if (isset($config_data)): ?>
        <ul class="list-group">
            <!-- Display the certificates -->
            <li class="list-group-item">
                <strong><?php echo gettext('Certificates:'); ?></strong>
                <ul>
                    <?php foreach ($certificates as $cert): ?>
                        <li>
                            <?php echo gettext('Certificate File:'); ?> <?php echo htmlspecialchars($cert['certificateFile']); ?><br>
                            <?php if (isset($cert['keyFile'])): ?>
                                <?php echo gettext('Key File:'); ?> <?php echo htmlspecialchars($cert['keyFile']); ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <!-- Display the list of clients -->
            <li class="list-group-item">
                <strong><?php echo gettext('Clients:'); ?></strong>
                <ul>
                    <?php foreach ($clients as $client): ?>
                        <li>
                            <?php echo gettext('ID:'); ?> <?php echo htmlspecialchars($client['id']); ?><br>
                            <?php echo gettext('Email:'); ?> <?php echo htmlspecialchars($client['email']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        </ul>
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
    .list-group-item {
        font-size: 1em;
        padding: 8px 15px;
    }
</style>
