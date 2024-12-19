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

?>
<body>
<?php include("fbegin.inc"); ?>

<?php if ($savemsg) print_info_box($savemsg); ?>

<div> 

<?php
echo "Certificates \n"
print_r(cert_build_list('cert', 'Xray'));


echo "CA Certificates \n"
print_r(cert_build_list('ca', 'Xray'));

?>
</div>
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
