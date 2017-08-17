<?php

/**
 * @file PDFAnonymizerPlugin.inc.php
 *
 * Copyright (c) 2017 Simon Fraser University Library
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.pdfAnonymizer
 * @class PDFAnonymizerPlugin
 * @ingroup plugins
 * @brief Plugin that will remove identifying information from PDFs on upload
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class PDFAnonymizerPlugin extends GenericPlugin {

		function register($category, $path) {
				$success = parent::register($category, $path);
				if($success) {
						HookRegistry::register('FileManager::uploadFileFinished', array($this, 'fileHandlerCallback'));
				}
				return $success;
		}

		function getName() {
				return 'pdfAnonymizerPlugin';
		}

		function getDisplayName() {
				return __('plugins.generic.pdfAnonymizer.displayName');
		}

		function getDescription() {
				return __('plugins.generic.pdfAnonymizer.description');
		}

		/**
		 * File upload hook that scrubs metadata from PDF submissions as they're uploaded.
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
				$command = str_replace("\$fileName", $destFileName, $command);

				if(!empty($command)) {
						exec($command, $output, $returnCode);
				}

				if($returnCode != 0) {
						error_log("Anonymization command failed, return code: $returnCode");
						error_log(var_dump($output));
						debug_backtrace();
				}

				return $returnCode == 0;
		}


}

?>