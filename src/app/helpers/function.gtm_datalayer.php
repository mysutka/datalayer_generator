<?php

Atk14Require::Helper("block.javascript_tag");

function smarty_function_gtm_datalayer($params, $template) {
	$params += [
		"format" => "js",
	];
	$smarty = atk14_get_smarty_from_template($template);

	$out = [];

	$gtm = $smarty->getTemplateVars("gtm");
	$request = $smarty->getTemplateVars("request");
	if (!$gtm) {
		return "";
	}

	if ($params["format"]==="js") {
		$out[] = "// out by helper gtm_datalayer";
		$out[] = "var dataLayer = window.dataLayer || [];";
	}
	if ($params["format"]==="json") {
		return join("\n", $gtm->getDataLayerMessagesJson());
	}
	foreach($gtm->getDataLayerMessagesJson() as $msg) {
		$out[] = "dataLayer.push($msg);\n";
	}

	if ($request->xhr()) {
		return join("\n", $out);
	} else {
		return smarty_block_javascript_tag($params, join("\n", $out), $template, $repeat);
	}
}
