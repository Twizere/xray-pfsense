<?php
/*
 * xraydemo.widget.php
 * 
 * This is a simple demo widget for pfSense.
 * The widget will display the XRay service status, certificate locations, and client list in table format.
 * It also allows changing certificates via a form.
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

// Handle form submission for updating certificates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['certificate']) && isset($_FILES['key'])) {
        $certificate_file = $_FILES['certificate']['tmp_name'];
        $key_file = $_FILES['key']['tmp_name'];
        
        // Validate the uploaded files
        if ($certificate_file && $key_file) {
            // Update certificates in config
            $config_data['inbounds'][0]['streamSettings']['tlsSettings']['certificates'][0]['certificateFile'] = '/root/xray/cert/' . basename($_FILES['certificate']['name']);
            $config_data['inbounds'][0]['streamSettings']['tlsSettings']['certificates'][0]['keyFile'] = '/root/xray/cert/' . basename($_FILES['key']['name']);
            
            // Save the new certificate and key files to the desired location
            move_uploaded_file($certificate_file, '/root/xray/cert/' . basename($_FILES['certificate']['name']));
            move_uploaded_file($key_file, '/root/xray/cert/' . basename($_FILES['key']['name']));
            
            // Save the updated config back to the config file
            file_put_contents($config_file, json_encode($config_data, JSON_PRETTY_PRINT));
            
            // Output success message
            echo "<div class='alert alert-success'>Certificates updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: Certificate or Key file is missing.</div>";
        }
    }
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
        <strong><?php echo gettext('XRay Service Status'); ?>:</strong> 
        <span class="<?php echo $is_running == "Running" ? "status-running" : "status-stopped"; ?>">
            <?php echo $is_running; ?>
        </span>
    </div>

    <!-- Log File Location -->
    <div class="log-file">
        <strong><?php echo gettext('Log File Location'); ?>:</strong> <?php echo htmlspecialchars($log_file); ?>
    </div>

    <!-- Form to update certificates -->
    <h4><?php echo gettext('Update Certificates'); ?></h4>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="certificate"><?php echo gettext('Certificate File'); ?>:</label>
            <input type="file" name="certificate" id="certificate" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="key"><?php echo gettext('Private Key File'); ?>:</label>
            <input type="file" name="key" id="key" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary"><?php echo gettext('Update Certificates'); ?></button>
    </form>

    <!-- Certificates Table -->
    <h4><?php echo gettext('Current Certificates:'); ?></h4>
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

    /* Green for Running status */
    .status-running {
        color: green;
        font-weight: bold;
    }

    /* Red for Stopped status */
    .status-stopped {
        color: red;
        font-weight: bold;
    }

    .alert {
        margin-top: 10px;
        padding: 10px;
        border-radius: 5px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
