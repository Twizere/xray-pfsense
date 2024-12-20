<?php
require_once("guiconfig.inc");
require_once("pfsense-utils.inc");
require_once("pkg-utils.inc");
require_once("filter.inc");
require_once("auth.inc");
require_once("certs.inc");

require_once("/usr/local/pkg/xray.inc");

$pgtitle = array("VPN", "Xray");
include("head.inc");

// JSON file path
$jsonFilePath = "/usr/local/etc/xray/config.json";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serverCertID = $_POST['server_cert'] ?? '';
    $caCertID = $_POST['ca_cert'] ?? '';
    $listen = $_POST['listen'] ?? '0.0.0.0';
    $port = $_POST['port'] ?? 49000;
    $decryption = $_POST['decryption'] ?? 'none';
    $network = $_POST['network'] ?? 'tcp';
    $security = $_POST['security'] ?? 'tls';
    $allowInsecure = isset($_POST['allow_insecure']) ? (bool)$_POST['allow_insecure'] : false;

    // Validate certificates
    $serverCert = lookup_cert($serverCertID);
    $caCert = lookup_ca($caCertID);

    if ($serverCert && $caCert) {
        // Read the current JSON configuration
        $config = json_decode(file_get_contents($jsonFilePath), true);

        if (!$config) {
            $savemsg = "Error: Unable to read or parse the JSON configuration file.";
        } else {
            // Update the inbound configuration
            $config['inbounds'][0]['listen'] = $listen;
            $config['inbounds'][0]['port'] = (int)$port;
            $config['inbounds'][0]['settings']['decryption'] = $decryption;
            $config['inbounds'][0]['streamSettings']['network'] = $network;
            $config['inbounds'][0]['streamSettings']['security'] = $security;
            $config['inbounds'][0]['streamSettings']['tlsSettings']['allowInsecure'] = $allowInsecure;

            // Update the certificates section
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

            // Save the updated configuration back to the file
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

include("fbegin.inc");

if ($savemsg) print_info_box($savemsg);

$form = new Form();
$section = new Form_Section('Inbound Settings');

// Listening Address
$section->addInput(new Form_Input(
    'listen',
    'Listen Address',
    'text',
    $listen
))->setHelp('The address the server will listen on (default: 0.0.0.0).');

// Listening Port
$section->addInput(new Form_Input(
    'port',
    'Port',
    'number',
    $port
))->setHelp('The port the server will listen on (default: 49000).');

// Decryption Method
$section->addInput(new Form_Select(
    'decryption',
    'Decryption',
    $decryption,
    [
        'none' => 'None',
        'optional' => 'Optional'
    ]
))->setHelp('Select the decryption method for the inbound connection.');

// Network Protocol
$section->addInput(new Form_Select(
    'network',
    'Network Protocol',
    $network,
    [
        'tcp' => 'TCP',
        'kcp' => 'KCP',
        'ws' => 'WebSocket',
        'http' => 'HTTP/2'
    ]
))->setHelp('Select the network protocol.');

// Security Settings
$section->addInput(new Form_Select(
    'security',
    'Security',
    $security,
    [
        'none' => 'None',
        'tls' => 'TLS'
    ]
))->setHelp('Select the security protocol.');

// Allow Insecure
$section->addInput(new Form_Checkbox(
    'allow_insecure',
    'Allow Insecure Connections',
    'Allow insecure TLS connections.',
    $allowInsecure
));

// Server Certificate Selection
$section->addInput(new Form_Select(
    'server_cert',
    '*Server Certificate',
    '',
    cert_build_list('cert', 'Xray')
))->setHelp('Select a certificate which will be used by the Xray server.');

// CA Certificate Selection
$section->addInput(new Form_Select(
    'ca_cert',
    '*Peer Certificate Authority',
    '',
    cert_build_list('ca', 'Xray')
))->setHelp('Select a CA certificate which the VPN will use to verify client certificates.');

$form->add($section);

print($form);

include("foot.inc");
?>
