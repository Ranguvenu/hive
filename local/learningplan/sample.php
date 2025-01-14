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
 * TODO describe file sample
 *
 * @package    local_learningplan
 * @copyright  2023 Moodle India Information Solutions Pvt Ltd
 * @author     Narendra Patel <narendra.patel@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
global $DB, $USER;
$context = \context_system::instance();
$fields = [
    'employeeid' => 'employeeid',
];

require_once($CFG->libdir . '/csvlib.class.php');
$filename = clean_filename('traineesupload');
$csvexport = new csv_export_writer();
$csvexport->set_filename($filename);
$csvexport->add_data($fields);

$user = [1001];
$csvexport->add_data($user);

$csvexport->download_file();
die;