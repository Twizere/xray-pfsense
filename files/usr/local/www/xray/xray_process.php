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
            // Process inbound settings
            $config['inbounds'][0]['listen'] = $_POST['listen'] ?? '0.0.0.0';
            $config['inbounds'][0]['port'] = intval($_POST['port']) ?? 49000;
            $config['inbounds'][0]['protocol'] = $_POST['protocol'] ?? 'vless';

            // Process clients
            $clients = json_decode($_POST['clients'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($clients)) {
                $config['inbounds'][0]['settings']['clients'] = $clients;
            } else {
                // Handle invalid client input
                header('Location: xray_ui.php?error=Invalid clients JSON');
                exit;
            }

            // Process decryption
            $config['inbounds'][0]['settings']['decryption'] = $_POST['decryption'] ?? 'none';

            // Process stream settings
            $config['inbounds'][0]['streamSettings']['network'] = $_POST['network'] ?? 'tcp';
            $config['inbounds'][0]['streamSettings']['security'] = $_POST['security'] ?? 'tls';

            // Process TLS settings
            $config['inbounds'][0]['streamSettings']['tlsSettings']['serverName'] = $_POST['tls_server_name'] ?? '';
            $config['inbounds'][0]['streamSettings']['tlsSettings']['alpn'] = explode(',', $_POST['tls_alpn'] ?? 'h2,http/1.1');

            // Keep default values for allowInsecure and disableSystemRoot
            $config['inbounds'][0]['streamSettings']['tlsSettings']['allowInsecure'] = false;
            $config['inbounds'][0]['streamSettings']['tlsSettings']['disableSystemRoot'] = true;

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
header("Location: index.php?savemsg=" . urlencode($savemsg));
exit();
?>
