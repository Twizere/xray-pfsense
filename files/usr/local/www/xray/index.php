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

// Listening Port
$section->addInput(new Form_Input(
    'port',
    '*Listening Port',
    'text',
    ''
))->setHelp('Enter the port number for the Xray server to listen on.');

// Decryption Method
$section->addInput(new Form_Select(
    'decryption',
    '*Decryption Method',
    'none',
    ['none' => 'None']
))->setHelp('Select the decryption method.');

// Clients
$section->addInput(new Form_Textarea(
    'clients',
    'Clients (JSON)',
    ''
))->setHelp('Provide a JSON array of clients including ID, level, and email.');

// Stream Network
$section->addInput(new Form_Select(
    'network',
    '*Stream Network',
    'tcp',
    ['tcp' => 'TCP', 'kcp' => 'KCP', 'ws' => 'WebSocket', 'http' => 'HTTP']
))->setHelp('Select the network protocol.');

// TLS Settings
$section->addInput(new Form_Select(
    'security',
    '*TLS Security',
    'tls',
    ['tls' => 'TLS', 'none' => 'None']
))->setHelp('Select the security method for the stream.');

// Server Certificate Selection
$section->addInput(new Form_Select(
    'server_cert',
    '*Server Certificate',
    '',
    cert_build_list('cert', 'Xray')
))->setHelp('Select a certificate that will be used by the Xray server.');

// CA Certificate Selection
$section->addInput(new Form_Select(
    'ca_cert',
    '*Peer Certificate Authority',
    '',
    cert_build_list('ca', 'Xray')
))->setHelp('Select a CA certificate to verify client certificates.');

$form->add($section);
print($form);
include("foot.inc");
?>
