<?php

# vynucene nacteni helperu
# pri behu aplikace funkcni
# pri testu aplikace funkcni
# pri testu samotneho balicku s pouzitim phpunit nefunkcni - hleda Atk14Utils - chtelo by vynechat nacitani tohoto souboru
if (class_exists("Atk14Utils")) {
	$smarty = Atk14Utils::GetSmarty();
	$plugins = [__DIR__."/app/helpers"] + $smarty->getPluginsDir();
	$smarty->setPluginsDir($plugins);
	Atk14Require::Helper("function.gtm_datalayer", $smarty);
}
