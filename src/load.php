<?php

# vynucene nacteni helperu
# pri behu aplikace funkcni
# pri testu z adresare projektu, ktery tento balicek pouziva - funkcni
# pri testu samotneho composer balicku s pouzitim phpunit nefunkcni - hleda Atk14Utils - chtelo by vynechat nacitani tohoto souboru
if (class_exists("Atk14Utils")) {
	$smarty = Atk14Utils::GetSmarty();
	$plugins = [__DIR__."/app/helpers"] + $smarty->getPluginsDir();
	$smarty->setPluginsDir($plugins);
	Atk14Require::Helper("function.gtm_datalayer", $smarty);
}
