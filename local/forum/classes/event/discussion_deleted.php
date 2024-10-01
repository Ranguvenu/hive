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
 * The local_forum discussion deleted event.
 *
 * @package    local_forum
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_forum\event;
use stdclass;
defined('MOODLE_INTERNAL') || die();

/**
 * The local_forum discussion deleted event class.
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int forumid: The id of the forum the discussion is in.
 * }
 *
 * @package    local_forum
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class discussion_deleted extends \core\event\base {

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
        $this->data['objecttable'] = 'local_forum_discussions';
    }

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {

        $stringHelper=new stdClass();
        $stringHelper->userid=$this->userid;
        $stringHelper->objectid=$this->objectid;
        $stringHelper->contextinstanceid=$this->contextinstanceid;
        return get_string("strdiscussiondeleted",'local_forum',$stringHelper);
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventdiscussiondeleted', 'local_forum');
    }

    /**
     * Get URL related to the action
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url('/local/forum/view.php', array('id' => $this->contextinstanceid));
    }

    /**
     * Return the legacy event log data.
     *
     * @return array|null
     */
    protected function get_legacy_logdata() {
        return array($this->contextinstanceid, 'forum', 'delete discussion', 'view.php?id=' . $this->contextinstanceid,
            $this->other['forumid'], $this->contextinstanceid);
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();
        if (!isset($this->other['forumid'])) {
            throw new \coding_exception('The \'forumid\' value must be set in other.');
        }
    }

    public static function get_objectid_mapping() {
        return array('db' => 'local_forum_discussions', 'restore' => 'local_forum_discussion');
    }

    public static function get_other_mapping() {
        $othermapped = array();
        $othermapped['forumid'] = array('db' => 'local_forum', 'restore' => 'local_forum');

        return $othermapped;
    }
}

