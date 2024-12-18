<?php
require_once("guiconfig.inc"); // Includes pfSense GUI functions
require_once("/usr/local/pkg/xray.inc"); // Include your package-specific functions

global $config;

// Load the Xray configuration
if (!is_array($config['installedpackages']['xray']['config'])) {
    $config['installedpackages']['xray']['config'] = array();
}

$a_config = &$config['installedpackages']['xray']['config'];
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Handle edit mode
if ($id !== null && isset($a_config[$id])) {
    $pconfig = $a_config[$id];
} else {
    $pconfig = array(
        'server' => '',
        'port' => '',
        'protocol' => '',
        'certificate' => ''
    );
}

// Handle form submission
if ($_POST) {
    $input_errors = array();
    $pconfig = $_POST;

    // Validate input
    if (empty($_POST['server'])) {
        $input_errors[] = gettext("Server address is required.");
    }
    if (empty($_POST['port']) || !is_numeric($_POST['port'])) {
        $input_errors[] = gettext("A valid port number is required.");
    }

    // If no errors, save the configuration
    if (empty($input_errors)) {
        $new_entry = array(
            'server' => $_POST['server'],
            'port' => $_POST['port'],
            'protocol' => $_POST['protocol'],
            'certificate' => $_POST['certificate']
        );

        if ($id !== null && isset($a_config[$id])) {
            $a_config[$id] = $new_entry; // Update existing entry
        } else {
            $a_config[] = $new_entry; // Add a new entry
        }

        write_config("Updated Xray package configuration"); // Save the configuration to the pfSense config
        generate_xray_config($config); // Call a custom function to generate the config.json
        header("Location: /vpn_xray.php"); // Redirect to the Xray dashboard
        exit;
    }
}

include("head.inc");
?>

<body>
<?php include("fbegin.inc"); ?>

<section class="page-content-main">
    <div class="container-fluid">
        <div class="row">
            <section class="col-xs-12">
                <?php if (!empty($input_errors)) print_input_errors($input_errors); ?>
                <form action="xray_edit.php<?=($id !== null) ? "?id=" . htmlspecialchars($id) : "" ?>" method="post">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h2 class="panel-title"><?=gettext("Edit Xray Configuration")?></h2>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="server"><?=gettext("Server Address")?></label>
                                <input name="server" type="text" id="server" class="form-control" value="<?=htmlspecialchars($pconfig['server']);?>">
                            </div>
                            <div class="form-group">
                                <label for="port"><?=gettext("Port")?></label>
                                <input name="port" type="text" id="port" class="form-control" value="<?=htmlspecialchars($pconfig['port']);?>">
                            </div>
                            <div class="form-group">
                                <label for="protocol"><?=gettext("Protocol")?></label>
                                <select name="protocol" id="protocol" class="form-control">
                                    <option value="vmess" <?= $pconfig['protocol'] == "vmess" ? "selected" : ""; ?>>VMess</option>
                                    <option value="vless" <?= $pconfig['protocol'] == "vless" ? "selected" : ""; ?>>VLESS</option>
                                    <option value="trojan" <?= $pconfig['protocol'] == "trojan" ? "selected" : ""; ?>>Trojan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="certificate"><?=gettext("Certificate Path")?></label>
                                <input name="certificate" type="text" id="certificate" class="form-control" value="<?=htmlspecialchars($pconfig['certificate']);?>">
                                <small class="help-block"><?=gettext("Enter the path to the certificate managed by pfSense.");?></small>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary"><?=gettext("Save")?></button>
                            <button type="button" class="btn btn-default" onclick="window.location='/vpn_xray.php';"><?=gettext("Cancel")?></button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</section>

<?php include("foot.inc"); ?>
</body>
</html>
