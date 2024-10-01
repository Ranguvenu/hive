<?php

/**
 * Version details
 *
 * @package    local_courses
 * @copyright  @eabyas
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/learningplan/lib.php');

require_login();
global $DB, $PAGE, $CFG;

$PAGE->set_url(new moodle_url('/local/learningplan/usersunenrolled.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_learningplan'));
$PAGE->set_heading(get_string('pluginname', 'local_learningplan'));
$PAGE->requires->js_call_amd('local_learningplan/lpunenrol', 'Datatable', array());

$result = get_unenrolled_lpaths_list(); 

echo $OUTPUT->header();
$templatecontext = (object)[
    'data' => array_values($result),  
    'downloadurl' => new moodle_url('/local/learningplan/export.php'),
    'configpath' => $CFG->wwwroot,
];

echo $OUTPUT->render_from_template('local_learningplan/unenrolledusers', $templatecontext);
echo $OUTPUT->footer();