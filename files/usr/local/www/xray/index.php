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

<?php
// echo "Certificates \n";
// print_r(cert_build_list('cert', 'Xray'));


// echo "CA Certificates \n";
// print_r(cert_build_list('ca', 'Xray'));


$form = new Form();

$section = new Form_Section('Authentication Certificates');
$section->addInput(new Form_Select(
	'certref',
	'*Server Certificate',
	$pconfig['certref'],
	cert_build_list('cert', 'Xray')
))->setHelp('Select a certificate which will be used by Xray server');

$section->addInput(new Form_Select(
	'certref',
	'*CA Certificate',
	$pconfig['certref'],
	cert_build_list('cert', 'Xray')
))->setHelp('Select a CA certificate which the vpn will use to verify both client and  server certificates');


print($form);
print($section);

?>

 

<?php include("foot.inc"); ?>
</body>
