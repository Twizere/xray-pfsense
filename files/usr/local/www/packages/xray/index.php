<?php
require_once("guiconfig.inc");
require_once("/usr/local/pkg/xray.inc");

$pgtitle = array("VPN", "Xray");
include("head.inc");

if ($_POST['save']) {
    $config['xray']['loglevel'] = $_POST['loglevel'];
    write_config("Xray configuration updated.");
    xray_restart(); // Ensure this function exists in xray.inc
    $savemsg = "Configuration saved.";
}

$pconfig = $config['xray'];

?>
<body>
<?php include("fbegin.inc"); ?>

<?php if ($savemsg) print_info_box($savemsg); ?>

<form action="index.php" method="post">
    <table class="table">
        <thead>
            <tr>
                <th colspan="2">Xray Configuration</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Log Level</td>
                <td>
                    <input name="loglevel" type="text" value="<?=htmlspecialchars($pconfig['loglevel'])?>" />
                </td>
            </tr>
        </tbody>
    </table>
    <button type="submit" name="save" class="btn btn-primary">Save</button>
</form>

<?php include("foot.inc"); ?>
</body>
