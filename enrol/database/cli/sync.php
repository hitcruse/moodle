<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * CLI sync for full external database synchronisation.
 *
 * Sample cron entry:
 * # 5 minutes past 4am
 * 5 4 * * * $sudo -u www-data /usr/bin/php /var/www/moodle/enrol/database/cli/sync.php
 *
 * Notes:
 *   - it is required to use the web server account when executing PHP CLI scripts
 *   - you need to change the "www-data" to match the apache user account
 *   - use "su" if "sudo" not available
 *
 * @package    enrol_database
 * @copyright  2010 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($template_base.'/enrol/database/lib.php');
require_once($CFG->libdir.'/clilib.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('verbose'=>false, 'help'=>false), array('v'=>'verbose', 'h'=>'help'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
"Execute enrol sync with external database.
The enrol_database plugin must be enabled and properly configured.

Options:
-v, --verbose         Print verbose progess information
-h, --help            Print out this help

Example:
\$sudo -u www-data /usr/bin/php enrol/database/cli/sync.php

Sample cron entry:
# 5 minutes past 4am
5 4 * * * \$sudo -u www-data /usr/bin/php /var/www/moodle/enrol/database/cli/sync.php
";

    echo $help;
    die;
}

/*
if (!enrol_is_enabled('database')) {
    echo('enrol_database plugin is disabled, sync is disabled'."\n");
    exit(1);
}
*/

// add by eALPS Developer
$siteArray = array (
    'd'  => 'デフォルト',
    'g' => '共通教育',
    'l' => '人文学部',
    'e' => '教育学部',
    'k' => '経済学部',
    's' => '理学部',
    'm' => '医学部',
    't' => '工学部',
    'a' => '農学部',
    'f' => '繊維学部',
    'mv' => '医学部閲覧用',
    'help' => 'eALPSヘルプ',
    'fdsd' => 'eALPS教職員用',
    'hospital' => '附属病院',
    'facility' => '大学施設',
    'teachingCredential' => '教員免許更新講習会',
    'eChes' => 'eChes',
    'photo' => 'フォト',
    'other' => 'その他',
);

$fiscalYear = 0;
if(date('n') < 3) {
	$fiscalYear = date('Y') - 1;
} else {
	$fiscalYear = date('Y');
}
// end by eALPS Developer

// add by eALPS Developer
foreach($siteArray as $siteEnName => $siteJaName) {
// end by eALPS Developer

	// add by eALPS Developer
	$CFG->wwwroot   = $base_wwwroot.'/'.$fiscalYear.'/'.$siteEnName;
	$CFG->dbname    = $fiscalYear.'_'.$siteEnName;
    $CFG->dirroot = $template_base;
    $CFG->dataroot  = $base_dataroot.'/'.$fiscalYear.'/'.$siteEnName;
	// end by eALPS Developer
	
	if (!enrol_is_enabled('database')) {
	    echo('enrol_database plugin is disabled, sync is disabled'."\n");
	    continue;
	}

	$verbose = !empty($options['verbose']);
	$enrol = enrol_get_plugin('database');
	$result = 0;
	
	$result = $result | $enrol->sync_courses($verbose);
	$result = $result | $enrol->sync_enrolments($verbose);
	
// add by eALPS Developer
}
// end by eALPS Developer
exit($result);