<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or localify
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
 * Courses external API
 *
 * @package    local_courses
 * @category   external
 * @copyright  eAbyas <www.eabyas.in>
 */

namespace local_courses\task;

class course_completion_frequency extends \core\task\scheduled_task
{

    public function get_name()
    {
        return get_string('taskcoursecompletionfrequency', 'local_courses');
    }

    public function execute()
    {
        global $DB;
        $emailtype = 'course_completion_reminder';
        $corecomponent = new \core_component();
        $costcenterexist = $corecomponent::get_plugin_directory('local', 'costcenter');
        $fromuser = \core_user::get_support_user();
        $availiablenotifications = $this->course_completion_due_notifications();
        foreach ($availiablenotifications as $notification) {
            $this->send_frequency_notification( $notification, $emailtype, $costcenterexist);
           
        }   
    
    }

    private function course_completion_due_notifications()
    {
        global $DB;
        $globalnotification_sql = "SELECT lni.* FROM {local_notification_info} AS lni 
                WHERE lni.notificationid=(SELECT id FROM {local_notification_type} WHERE shortname=:shortname) order by id desc ";
        $notifications = $DB->get_records_sql($globalnotification_sql, array('shortname' => 'course_completion_reminder'));
        return $notifications;
    }

    public function send_frequency_notification($notification, $type, $costcenterexist){
      
        global $DB;
       
        $day = $notification->reminderdays;
        $today = date('Y-m-d');
        $starttime = strtotime(date('d-m-Y', strtotime("+" .  $day . " day")));
        $endtime = $starttime + 86400;

        $params = array();
        $sql = "SELECT  ue.*, e.id AS enrolid, c.id AS courseid, c.fullname ,el.notification_infoid,date(from_unixtime( el.timecreated)) as timecreated,
                    date(from_unixtime( ue.timecreated)) as enrolmentdate,
                    date(from_unixtime( el.timecreated))
                    FROM {user_enrolments} ue  
                    JOIN {enrol} e ON e.id = ue.enrolid AND e.enrol IN ('manual', 'self') 
                    JOIN {course} c ON e.courseid = c.id 
                    JOIN (SELECT  mle.to_userid,mle.notification_infoid,mle.moduleid,max(mle.timecreated) as timecreated 
                            FROM {local_emaillogs} as mle 
                            JOIN {local_notification_info} as mln on mln.id= mle.notification_infoid                
                            WHERE mln.notificationid = $notification->notificationid and mln.id=$notification->id  and mln.frequencyflag = 1 
                            GROUP BY  mle.to_userid,mle.notification_infoid,mle.moduleid ) as el ON el.moduleid = c.id AND el.to_userid = ue.userid                     
                    LEFT JOIN {course_completions} AS cc ON cc.course=e.courseid AND cc.userid = ue.userid AND c.id = cc.course
                    WHERE date(from_unixtime( el.timecreated+($day*86400))) =  :today 
                    AND c.id>1 AND (cc.timecompleted IS NULL OR cc.timecompleted = '' ) AND el.notification_infoid = $notification->id ";
        if ($notification->moduleid) {
            $sql .= " AND c.id IN ( $notification->moduleid)";
        }
        if ($costcenterexist) {
            $sql .= " AND c.open_costcenterid=:costcenterid ";
            $params['costcenterid'] = $notification->costcenterid;
        }    
 

        $params['today'] = $today;
        $enrolcourses = $DB->get_records_sql($sql, $params);     
       // print_r($enrolcourses);
        $this->notification_to_user($enrolcourses, $notification, $type);
    }
  

    public function notification_to_user($enrolcourses, $notification, $type)
    {
        $coursenotification = new \local_courses\notification();
        $courses = array();
        foreach ($enrolcourses as $enrolcourse) {
            $touser = \core_user::get_user($enrolcourse->userid);
            if (empty($courses[$enrolcourse->courseid])) {
                $courses[$enrolcourse->courseid] = get_course($enrolcourse->courseid);
            }
            $course = $courses[$enrolcourse->courseid];
            $coursenotification->send_course_email($course, $touser, $type, $notification);
        }
    }
}