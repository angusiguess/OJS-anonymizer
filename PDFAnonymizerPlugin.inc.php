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
		 * Scrub EXIF Data from a file by filename
		 * @param $fileName string
		 * @return boolean
		 */
		function scrubExif($fileName) {
				// Remove exif metadata
				$output = "";
				$exifSuccess = 0;
				exec("exiftool -all:all= " . escapeshellarg($fileName),
						 $output,
						 $exifSuccess);
				$linearizeSuccess = 0;
				if($exifSuccess == 0) {
						unlink($fileName . "_original");
						$tempFilePath = $fileName . "tmp";
						// Linearizing the pdf removes any pre-exif metadata.
						exec("qpdf --linearize " . escapeshellarg($fileName) . " " . escapeshellarg($tempFilePath),
								 $output,
								 $linearizeSuccess);
						if($linearizeSuccess == 0) {
								unlink($fileName);
								rename($tempFilePath, $fileName);
						}
				}

			  return $exifSuccess && ($linearizeSuccess == 0);
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

				if ($fileType === 'pdf') {
						$returnValue = $this->scrubExif($destFileName);
				}
				return $returnValue;
		}


}

?>