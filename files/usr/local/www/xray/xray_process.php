<?php
require_once("guiconfig.inc");
require_once("pfsense-utils.inc");
require_once("pkg-utils.inc");

$jsonFilePath = "/usr/local/etc/xray/config.json";
$savemsg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $port = $_POST['port'] ?? '';
    $decryption = $_POST['decryption'] ?? 'none';
    $clients = $_POST['clients'] ?? '[]';
    $network = $_POST['network'] ?? 'tcp';
    $security = $_POST['security'] ?? 'tls';
    $serverCertID = $_POST['server_cert'] ?? '';
    $caCertID = $_POST['ca_cert'] ?? '';

    // Validate input
    $serverCert = lookup_cert($serverCertID);
    $caCert = lookup_ca($caCertID);

    if ($serverCert && $caCert) {
        $config = json_decode(file_get_contents($jsonFilePath), true);
        if (!$config) {
            $savemsg = "Error: Unable to read or parse the JSON configuration file.";
        } else {
            // Update the JSON configuration
            $config['inbounds'][0]['port'] = (int)$port;
            $config['inbounds'][0]['settings']['decryption'] = $decryption;
            $config['inbounds'][0]['settings']['clients'] = json_decode($clients, true) ?? [];
            $config['inbounds'][0]['streamSettings']['network'] = $network;
            $config['inbounds'][0]['streamSettings']['security'] = $security;
            $config['inbounds'][0]['streamSettings']['tlsSettings']['certificates'] = [
                [
                    'certificate' => explode("\n", base64_decode($serverCert['crt'])),
                    'key' => explode("\n", base64_decode($serverCert['prv']))
                ],
                [
                    'usage' => 'verify',
                    'certificate' => explode("\n", base64_decode($caCert['crt']))
                ]
            ];

            // Save the updated configuration
            if (file_put_contents($jsonFilePath, json_encode($config, JSON_PRETTY_PRINT)) !== false) {
                $savemsg = "Configuration updated successfully.";
            } else {
                $savemsg = "Error: Failed to save the configuration.";
            }
        }
    } else {
        $savemsg = "Error: Invalid certificate selection.";
    }
}

// Display result and redirect back to the UI
header("Location: xray_ui.php?savemsg=" . urlencode($savemsg));
exit();
?>
