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

    // Validate certificates
    $serverCert = lookup_cert($serverCertID);
    $caCert = lookup_ca($caCertID);

    if ($serverCert && $caCert) {
        // Read the current JSON configuration
        $config = json_decode(file_get_contents($jsonFilePath), true);

        if (!$config) {
            $savemsg = "Error: Unable to read or parse the JSON configuration file.";
        } else {
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
$section = new Form_Section('Authentication Certificates');

// Server Certificate Selection
$section->addInput(new Form_Select(
    'server_cert',
    '*Server Certificate',
    '',
    cert_build_list('cert', 'Xray')
))->setHelp('Select a certificatewhich will be used by the Xray server.');

// CA Certificate Selection
$section->addInput(new Form_Select(
    'ca_cert',
    '*Client Certificate Authority',
    '',
    cert_build_list('ca', 'Xray')
))->setHelp('Select a CA certificate which the VPN will use to verify client certificates.');

$form->add($section);

print($form);

include("foot.inc");
?>
