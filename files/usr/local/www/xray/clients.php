<?php
require_once("guiconfig.inc");
require_once("util.inc"); // For uuidgen()

$tab_array = array();
$tab_array[] = array(gettext("Server"), false, "index.php");
$tab_array[] = array(gettext("Clients"), true, "clients.php");
$tab_array[] = array(gettext("Client Export"), false, "index.php");

add_package_tabs("Xray", $tab_array);
display_top_tabs($tab_array);


// Path to the JSON configuration file
$configFilePath = '/usr/local/etc/xray/config.json';

// Function to load the JSON configuration
function loadConfig($filePath)
{
    if (!file_exists($filePath)) {
        return false;
    }
    $content = file_get_contents($filePath);
    return json_decode($content, true);
}

// Function to save the JSON configuration
function saveConfig($filePath, $data)
{
    $jsonData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    return file_put_contents($filePath, $jsonData);
}

// Load existing configuration
$config = loadConfig($configFilePath);

if (!$config) {
    $config = [
        "log" => ["loglevel" => "warning"],
        "inbounds" => [],
        "outbounds" => []
    ];
}

// Get the clients list
$clients = $config['inbounds'][0]['settings']['clients'] ?? [];

// Handle form submission
if ($_POST) {
    if (isset($_POST['save'])) {
        // Collect form data
        $newClients = [];
        foreach ($_POST['id'] as $index => $id) {
            if (!empty($id)) {
                $newClients[] = [
                    "id" => $id,
                    "level" => intval($_POST['level'][$index]),
                    "email" => $_POST['email'][$index],
                ];
            }
        }

        // Save the new clients configuration
        $config['inbounds'][0]['settings']['clients'] = $newClients;
        saveConfig($configFilePath, $config);
        $savemsg = "Clients updated successfully.";
    }

    if (isset($_POST['add_new'])) {
        // Add a new client
        $newClient = [
            "id" => uuidgen(),
            "level" => intval($_POST['new_level']),
            "email" => $_POST['new_email']
        ];
        $config['inbounds'][0]['settings']['clients'][] = $newClient;
        saveConfig($configFilePath, $config);
        $savemsg = "New client added successfully.";
    }

    if (isset($_POST['delete_client'])) {
        // Delete a client
        $deleteIndex = intval($_POST['delete_client']);
        if (isset($config['inbounds'][0]['settings']['clients'][$deleteIndex])) {
            unset($config['inbounds'][0]['settings']['clients'][$deleteIndex]);
            $config['inbounds'][0]['settings']['clients'] = array_values($config['inbounds'][0]['settings']['clients']);
            saveConfig($configFilePath, $config);
            $savemsg = "Client deleted successfully.";
        }
    }
}

// Display the form
$form = new Form();

// Display existing clients in a table
$clientsSection = new Form_Section('Existing Clients');

$clientsTable = '<table class="table table-striped table-hover">';
$clientsTable .= '<thead><tr><th>UUID</th><th>Level</th><th>Email</th><th>Actions</th></tr></thead><tbody>';

foreach ($clients as $index => $client) {
    $clientsTable .= '<tr>';
    $clientsTable .= '<td><input type="text" name="id[' . $index . ']" value="' . htmlspecialchars($client['id']) . '" class="form-control" readonly></td>';
    $clientsTable .= '<td><input type="text" name="level[' . $index . ']" value="' . htmlspecialchars($client['level']) . '" class="form-control"></td>';
    $clientsTable .= '<td><input type="text" name="email[' . $index . ']" value="' . htmlspecialchars($client['email']) . '" class="form-control"></td>';
    $clientsTable .= '<td><button type="submit" name="delete_client" value="' . $index . '" class="btn btn-danger btn-sm">Delete</button></td>';
    $clientsTable .= '</tr>';
}

$clientsTable .= '</tbody></table>';
$clientsSection->addInput(new Form_StaticText('', $clientsTable));

// Add the table to the form
$form->add($clientsSection);

// Add a section for adding new clients
$newClientSection = new Form_Section('Add New Client');
$newClientSection->addInput(new Form_Input(
    'new_level',
    'Level',
    'text',
    ''
))->setHelp('Specify the access level for the new client.');

$newClientSection->addInput(new Form_Input(
    'new_email',
    'Email',
    'text',
    ''
))->setHelp('Email address for the new client.');

$newClientSection->addInput(new Form_Button(
    'add_new',
    'Add New Client',
    null,
    'fa-plus'
))->addClass('btn-success');

// Add sections to the form
$form->add($newClientSection);
$form->addGlobal(new Form_Button(
    'save',
    'Save Changes',
    null,
    'fa-save'
))->addClass('btn-primary');

// Display any messages
if (isset($savemsg)) {
    print_info_box($savemsg);
}

if (!empty($inputErrors)) {
    print_input_errors($inputErrors);
}

print($form);

include("foot.inc");
