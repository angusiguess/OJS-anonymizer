<?php
/**
 * @file index.php
 *
 * Copyright (c) 2017 Simon Fraser University Library
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @brief Wrapper for anonymizer plugin
 * @package plugins.generic.anonymizer
 *
 */
require_once('plugins/generic/anonymizer/AnonymizerPlugin.inc.php');

return new AnonymizerPlugin();

?>