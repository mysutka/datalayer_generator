<?php

Atk14Require::Helper("block.javascript_tag");

function smarty_function_gtm_datalayer($params, $template) {
	$smarty = atk14_get_smarty_from_template($template);

	$out = [];

	$gtm = $smarty->getTemplateVars("gtm");
	if (!$gtm) {
		return "";
	}

	$out[] = "// out by helper gtm_datalayer";
	$out[] = "var dataLayer = window.dataLayer || [];";
	foreach($gtm->getDataLayerMessagesJson() as $msg) {
		$out[] = "dataLayer.push($msg);\n";
	}

	return smarty_block_javascript_tag($params, join("\n", $out), $template, $repeat);
}
