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
 * This file contains the Moodle Test Block.
 *
 * @package    block_moodle_test
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_moodle_test extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_moodle_test');
    }    
    public function get_content() {
        global $CFG, $COURSE, $USER;

        if (isset($this->content)) {
            return $this->content;
        }

        $modinfo = get_array_of_activities($COURSE->id);

        $html = '';
        $html .= html_writer::start_tag('ul', array());

        foreach ($modinfo as $cm) {
            $status = '';
            if ($this->is_module_complete($cm->cm, $USER->id) == 1) {

                $status = ' - ' . get_string('completed', 'block_moodle_test');
            }

            $modfulldetail = html_writer::tag('li', $cm->cm . ' - ' . $cm->name . ' - ' . date('d-M-Y', $cm->added) . $status);

            $url = new moodle_url($CFG->wwwroot . '/mod/' . $cm->mod . '/view.php', array('id' => $COURSE->id));
            $link = html_writer::link($url, $modfulldetail);

            $html .= $link;
        }
        $html .= html_writer::end_tag('ul');

        $this->content = new stdClass;
        $this->content->text   = $html;
        $this->content->footer = '';

        return $this->content;
    }

    public function applicable_formats() {
        return array(
            'course-view' => true, 'mod' => false, 'my' => false, 'admin' => false,
            'tag' => false
        );
    }

    public function is_module_complete($cmid, $userid) {
        global $DB;
        $table = 'course_modules_completion';
        $result = $DB->get_field($table, 'completionstate', array('coursemoduleid' => $cmid, 'userid' => $userid));

        if (empty($result)) {
            return 0;
        } else {
            return $result;
        }
    }
}
