<?php

# vynucene nacteni helperu
$smarty = Atk14Utils::GetSmarty();
$plugins = [__DIR__."/app/helpers"] + $smarty->getPluginsDir();
$smarty->setPluginsDir($plugins);
Atk14Require::Helper("function.gtm_datalayer", $smarty);
