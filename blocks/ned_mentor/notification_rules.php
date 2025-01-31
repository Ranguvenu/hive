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
 * @package    block_ned_mentor
 * @copyright  Michael Gardener <mgardener@cissq.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/ned_mentor/lib.php');
require_once($CFG->dirroot . '/blocks/ned_mentor/notificaton_form.php');

// Parameters.
$menteeid = optional_param('menteeid', null, PARAM_INT);
$courseid = optional_param('courseid', null, PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);
$action   = optional_param('action', 'add', PARAM_TEXT);

require_login(null, false);

// PERMISSION.
require_capability('block/ned_mentor:createnotificationrule', context_system::instance());

if (($action == 'edit') && ($id)) {
    $notificationrule = $DB->get_record('block_ned_mentor_notific', array('id' => $id), '*', MUST_EXIST);
}

$title = get_string('page_title_assign_mentor', 'block_ned_mentor');
$heading = $SITE->fullname;

$PAGE->set_url('/blocks/ned_mentor/notification.php');
$PAGE->set_pagelayout('course');
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($heading);
$PAGE->set_cacheable(true);

$PAGE->requires->css('/blocks/ned_mentor/css/styles.css');

$PAGE->navbar->add(get_string('pluginname', 'block_ned_mentor'), new moodle_url('/blocks/ned_mentor/course_overview.php'));
$PAGE->navbar->add(get_string('notification_rules', 'block_ned_mentor'),
    new moodle_url('/blocks/ned_mentor/notification_rules.php')
);

echo $OUTPUT->header();

echo '<div id="notification-wrapper">';
echo '<h1>' . get_string('notification_rules', 'block_ned_mentor') . '</h1>';

if ($notificationrules = $DB->get_records('block_ned_mentor_notific')) {
    $rulenumber = 0;
    foreach ($notificationrules as $notificationrule) {
        $rulenumber++;
        echo block_ned_mentor_render_notification_rule_table($notificationrule, $rulenumber);
    }
}

echo block_ned_mentor_single_button_form ('create_new_rule',
    new moodle_url('/blocks/ned_mentor/notification.php'), null, get_string('create_new_rule', 'block_ned_mentor')
);
echo '</div>';



echo $OUTPUT->footer();