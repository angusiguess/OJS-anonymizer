<?php

/**
 * @file AnonymizerPlugin.inc.php
 *
 * Copyright (c) 2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.anonymizer
 * @class AnonymizerPlugin
 * @ingroup plugins
 * @brief Plugin that will remove identifying information from submissions on upload
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class AnonymizerPlugin extends GenericPlugin {
	/**
	 * @copydoc GenericPlugin::register()
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		if($success) {
			HookRegistry::register('FileManager::uploadFileFinished', array($this, 'fileHandlerCallback'));
		}
		return $success;
	}

	/**
	 * @copydoc GenericPlugin::getName()
	 */
	function getName() {
		return 'anonymizerPlugin';
	}

	/**
	 * @copydoc GenericPlugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.generic.anonymizer.displayName');
	}

	/**
	 * @copydoc GenericPlugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.generic.anonymizer.description');
	}

	/**
	 * File upload hook that scrubs metadata from submissions as they're uploaded.
	 * @param $hookName string
	 * @param $args array
	 * @return boolean
	 */
	function fileHandlerCallback($hookName, $args) {
		$fileName = $args[0];
		$destFileName = $args[1];
		$fileType = $args[2];
		$returnValue = $args[3];
		$output = "";
		$returnCode = 0;

		$command = Config::getVar('anon', "anon[${fileType}]");
		$command = str_replace('$fileName', $destFileName, $command);

		if(!empty($command)) {
			exec($command, $output, $returnCode);
		}

		if($returnCode != 0) {
			error_log("Anonymization command failed, command: $command return code: $returnCode");
			error_log("Command output: " . var_dump($output));
		}

		$returnValue = ($returnCode == 0);
		return $returnCode == 0;
	}
}

?>