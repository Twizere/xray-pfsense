<?php
// Load the pfSense configuration file (config.xml)
$config = parse_xml_pfconfig("/conf/config.xml");

// Function to parse the config and get certificates
function parse_xml_pfconfig($config_file) {
    $xml = simplexml_load_file($config_file);
    $certificates = [];

    // Extract certificates from the config
    foreach ($xml->cert as $cert) {
        $certificates[] = (string) $cert->name;
    }

    return $certificates;
}

// Generate the dropdown options
$certificates = parse_xml_pfconfig("/conf/config.xml");
?>
<!-- HTML for the Dropdown -->
<select name="certificates" id="certificates-dropdown">
    <option value="">Select a certificate</option>
    <?php
    foreach ($certificates as $cert) {
        echo "<option value=\"$cert\">$cert</option>";
    }
    ?>
</select>
