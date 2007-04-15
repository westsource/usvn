<?php
/**
 * Root for installation
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package install
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

define('CONFIG_FILE', "../config.ini");
define('HTACCESS_FILE', "../.htaccess");

set_include_path(get_include_path() .PATH_SEPARATOR ."..");
require_once 'USVN/autoload.php';
require_once 'Install.php';

USVN_Translation::initTranslation('en_US', '../locale');
if (file_exists(CONFIG_FILE)) {
	$config = new USVN_Config(CONFIG_FILE, 'general');
	if (isset($config->translation->locale)) {
		USVN_Translation::initTranslation($config->translation->locale, '../locale');
	}
	if (isset($config->database->adapterName)) {
		Zend_Db_Table::setDefaultAdapter(Zend_Db::factory($config->database->adapterName, $config->database->options->asArray()));
		Zend_Db_Table::getDefaultAdapter()->getProfiler()->setEnabled(true);
		USVN_Db_Table::$prefix = $config->database->prefix;
	}
}

include "views/head.html";

if (Install::installPossible(CONFIG_FILE)) {
	installationStep();
}
else {
	echo "<h1>" . T_("Error") . "</h1>";
	echo T_("USVN is already install.");
}

include "views/footer.html";

//------------------------------------------------------------------------------------------------
function installationStep()
{
	if (!isset($_GET['step'])) {
		$_GET['step'] = 1;
	}
	try {
		switch ($_GET['step']) {
			case 1:
				include "views/step1.html";
			break;

			case 2:
				include "views/step2.html";
			break;

			case 3:
				Install::installLanguage(CONFIG_FILE, $_POST['language']);
				$language = $_POST['language'];
				include "views/step3.html";
			break;

			case 4:
				include "views/step4.html";
			break;

			case 5:
				Install::installConfiguration(CONFIG_FILE, $_POST['title']);
				include "views/step5.html";
			break;

			case 6:
				Install::installDb(CONFIG_FILE, "../SQL/", $_POST['host'], $_POST['user'], $_POST['password'], $_POST['database'], $_POST['prefix']);
				include "views/step6.html";
			break;

			case 7:
				Install::installAdmin(CONFIG_FILE, $_POST['login'], $_POST['password']);
				Install::installUrl(CONFIG_FILE, HTACCESS_FILE);
				Install::installEnd(CONFIG_FILE);
				include "views/step7.html";
			break;
		}
	}
	catch (USVN_Exception $e) {
		echo "<h1>" . T_("Error") . "</h1>";
		echo $e->getMessage();
		echo "<br /><br />" . T_("Please go back.");
	}
}
?>

