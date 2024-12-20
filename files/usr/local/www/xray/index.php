<?php
require_once("guiconfig.inc");
require_once("pfsense-utils.inc");
require_once("pkg-utils.inc");
require_once("filter.inc");
require_once("auth.inc");

$pgtitle = array("VPN", "Xray");
include("head.inc");

// Define the path to the Xray configuration file
$configFilePath = '/path/to/xray/config.json';

// Initialize default values
$currentConfig = [
    'listen' => '0.0.0.0',
    'port' => '49000',
    'protocol' => 'vless',
    'clients' => '[{"id": "uuid-1", "email": "user@example.com"}]',
    'decryption' => 'none',
    'network' => 'tcp',
    'security' => 'tls',
    'tls_server_name' => '',
    'tls_alpn' => 'h2,http/1.1',
    'server_cert' => '',
    'ca_cert' => ''
];

// Load the current configuration from the JSON file if it exists
if (file_exists($configFilePath)) {
    $config = json_decode(file_get_contents($configFilePath), true);

    if (is_array($config)) {
        $inbound = $config['inbounds'][0] ?? [];
        $currentConfig['listen'] = $inbound['listen'] ?? $currentConfig['listen'];
        $currentConfig['port'] = $inbound['port'] ?? $currentConfig['port'];
        $currentConfig['protocol'] = $inbound['protocol'] ?? $currentConfig['protocol'];
        $currentConfig['clients'] = json_encode($inbound['settings']['clients'] ?? json_decode($currentConfig['clients'], true), JSON_PRETTY_PRINT);
        $currentConfig['decryption'] = $inbound['settings']['decryption'] ?? $currentConfig['decryption'];
        $streamSettings = $inbound['streamSettings'] ?? [];
        $currentConfig['network'] = $streamSettings['network'] ?? $currentConfig['network'];
        $currentConfig['security'] = $streamSettings['security'] ?? $currentConfig['security'];
        $tlsSettings = $streamSettings['tlsSettings'] ?? [];
        $currentConfig['tls_server_name'] = $tlsSettings['serverName'] ?? $currentConfig['tls_server_name'];
        $currentConfig['tls_alpn'] = implode(',', $tlsSettings['alpn'] ?? explode(',', $currentConfig['tls_alpn']));
    }
}

// Create the form
$form = new Form('Submit', 'xray_process.php'); // Submit to the backend file
$section = new Form_Section('Inbound Settings');

// Server Listening
$section->addInput(new Form_Input(
    'listen',
    '*Server Listening Address',
    'text',
    $currentConfig['listen']
))->setHelp('IP address the server will listen on (default is 0.0.0.0).');

$section->addInput(new Form_Input(
    'port',
    '*Listening Port',
    'text',
    $currentConfig['port']
))->setHelp('Port number for the Xray server to listen on.');

// Protocol
$section->addInput(new Form_Select(
    'protocol',
    '*Protocol',
    $currentConfig['protocol'],
    ['vless' => 'VLESS', 'vmess' => 'VMess', 'trojan' => 'Trojan']
))->setHelp('Select the protocol for inbound connections.');

// Clients - Dynamically Generated
$clientsSection = new Form_Section('Clients');
$clientsSection->addInput(new Form_Textarea(
    'clients',
    'Clients (JSON)',
    $currentConfig['clients']
))->setHelp('Provide a JSON array of clients including ID, level, and email. You can add multiple clients here.');

// Decryption Method
$section->addInput(new Form_Select(
    'decryption',
    '*Decryption Method',
    $currentConfig['decryption'],
    ['none' => 'None']
))->setHelp('Select the decryption method.');

// Stream Settings
$streamSection = new Form_Section('Security Settings');

$streamSection->addInput(new Form_Select(
    'network',
    '*Stream Network',
    $currentConfig['network'],
    ['tcp' => 'TCP', 'kcp' => 'KCP', 'ws' => 'WebSocket', 'http' => 'HTTP']
))->setHelp('Select the network protocol.');

$streamSection->addInput(new Form_Select(
    'security',
    '*Stream Security',
    $currentConfig['security'],
    ['tls' => 'TLS', 'none' => 'None']
))->setHelp('Select the security method for the stream.');

// TLS Settings
$tlsSection = new Form_Section('TLS Settings');

$tlsSection->addInput(new Form_Input(
    'tls_server_name',
    '*Server Name',
    'text',
    $currentConfig['tls_server_name']
))->setHelp('Specify the server name for TLS.');

$tlsSection->addInput(new Form_Textarea(
    'tls_alpn',
    'ALPN',
    $currentConfig['tls_alpn']
))->setHelp('Enter Application-Layer Protocol Negotiation (ALPN) values, separated by commas.');

// Server Certificate Selection
$tlsSection->addInput(new Form_Select(
    'server_cert',
    '*Server Certificate',
    $currentConfig['server_cert'],
    cert_build_list('cert', 'Xray')
))->setHelp('Select a certificate that will be used by the Xray server.');

// CA Certificate Selection
$tlsSection->addInput(new Form_Select(
    'ca_cert',
    '*Peer Certificate Authority',
    $currentConfig['ca_cert'],
    cert_build_list('ca', 'Xray')
))->setHelp('Select a CA certificate to verify client certificates.');

// Add sections to the form
$form->add($section);
$form->add($clientsSection);
$form->add($streamSection);
$form->add($tlsSection);
print($form);
include("foot.inc");
?>
