<?php
require_once("guiconfig.inc");
require_once("pfsense-utils.inc");
require_once("pkg-utils.inc");
require_once("filter.inc");
require_once("auth.inc");

$pgtitle = array("VPN", "Xray");
include("head.inc");


$form = new Form('Submit', 'xray_process.php'); // Submit to the backend file
$section = new Form_Section('Inbound Settings');

// Server Listening
$section->addInput(new Form_Input(
    'listen',
    '*Server Listening Address',
    'text',
    '0.0.0.0'
))->setHelp('IP address the server will listen on (default is 0.0.0.0).');

$section->addInput(new Form_Input(
    'port',
    '*Listening Port',
    'text',
    '49000'
))->setHelp('Port number for the Xray server to listen on.');

// Protocol
$section->addInput(new Form_Select(
    'protocol',
    '*Protocol',
    'vless',
    ['vless' => 'VLESS', 'vmess' => 'VMess', 'trojan' => 'Trojan']
))->setHelp('Select the protocol for inbound connections.');

// Clients - Dynamically Generated
$clientsSection = new Form_Section('Clients');
$clientsSection->addInput(new Form_Textarea(
    'clients',
    'Clients (JSON)',
    '[{"id": "uuid-1", "email": "user@example.com"}]'
))->setHelp('Provide a JSON array of clients including ID, level, and email. You can add multiple clients here.');

// Decryption Method
$section->addInput(new Form_Select(
    'decryption',
    '*Decryption Method',
    'none',
    ['none' => 'None']
))->setHelp('Select the decryption method.');

// Stream Settings
$streamSection = new Form_Section('Security Settings');

$streamSection->addInput(new Form_Select(
    'network',
    '*Stream Network',
    'tcp',
    ['tcp' => 'TCP', 'kcp' => 'KCP', 'ws' => 'WebSocket', 'http' => 'HTTP']
))->setHelp('Select the network protocol.');

$streamSection->addInput(new Form_Select(
    'security',
    '*Stream Security',
    'tls',
    ['tls' => 'TLS', 'none' => 'None']
))->setHelp('Select the security method for the stream.');

// TLS Settings
$tlsSection = new Form_Section('TLS Settings');

$tlsSection->addInput(new Form_Input(
    'tls_server_name',
    '*Server Name',
    'text',
    ''
))->setHelp('Specify the server name for TLS.');

$tlsSection->addInput(new Form_Textarea(
    'tls_alpn',
    'ALPN',
    'h2,http/1.1'
))->setHelp('Enter Application-Layer Protocol Negotiation (ALPN) values, separated by commas.');

// Server Certificate Selection
$tlsSection->addInput(new Form_Select(
    'server_cert',
    '*Server Certificate',
    '',
    cert_build_list('cert', 'Xray')
))->setHelp('Select a certificate that will be used by the Xray server.');

// CA Certificate Selection
$tlsSection->addInput(new Form_Select(
    'ca_cert',
    '*Peer Certificate Authority',
    '',
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
