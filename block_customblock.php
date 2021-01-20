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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Custom block displays user name, photo, department, and current role groups.
 *
 * @package block_customblock
 * @copyright 2020 Daniel Kearnan
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_customblock extends block_base
{

    public function init()
    {
        $this->title = get_string('pluginname', 'block_customblock');
    }

    public function specialization()
    {
        if (isset($this->config)) {

            if (empty($this->config->title)) {
                $this->title = get_string('pluginname', 'block_customblock');
            } else {
                $this->title = $this->config->title;
            }
        }
    }

    public function get_content()
    {
        global $USER, $PAGE;

        $PAGE->requires->js_call_amd('block_customblock/helloworld', 'init');

    //    if ($USER->id !== null) {
    //        $userpicture = new user_picture($USER);
    //        $url = $userpicture->get_url($PAGE);
     //   }

        $role_table = $this->build_table();

        // print_object($USER);

        try {
            require_login();
        } catch (coding_exception $e) {
        } catch (require_login_exception $e) {
        } catch (moodle_exception $e) {
        }

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        $this->content->text = html_writer::start_tag('div', $this->html_attributes());
        $this->content->text .= html_writer::tag('h6', get_string('welcome',block_customblock ), $this->html_attributes());
        $this->content->text .= html_writer::end_tag('div',  $this->html_attributes());

        $this->content->text .= html_writer::start_tag('div', $this->html_attributes());
        $this->content->text .= html_writer::tag('p', '' . $USER->{'firstname'} . ' ' . $USER->{'lastname'} . '', $this->html_attributes());
       //  $this->content->text .= html_writer::empty_tag('img', array('src' => $url, 'class' => 'block_customblock'));
        $this->content->text .= html_writer::end_tag('div',  $this->html_attributes());

        $this->content->text .= html_writer::start_tag('div',  $this->html_attributes());
        $this->content->text .= html_writer::tag('p', get_string('department', block_customblock), $this->html_attributes());
        $this->content->text .= html_writer::tag('p', '' . $USER->{'profile'}['dept'] . '', $this->html_attributes());
        $this->content->text .= html_writer::end_tag('div',  $this->html_attributes());

        $this->content->text .= html_writer::start_tag('div',  $this->html_attributes());
        $this->content->text .= html_writer::tag('p', get_string('roles', block_customblock), $this->html_attributes());
        $this->content->text .= $role_table;
        $this->content->text .= html_writer::end_tag('div',  $this->html_attributes());
        return $this->content;
    }

    // no need for multiples
    public function instance_allow_multiple()
    {
        return false;
    }

    // no setup
    function has_config()
    {
        return false;
    }

    public function hide_header()
    {
        return true;
    }

    // only allow block on front page
    public function applicable_formats()
    {
        return array(
            'site-index' => true
        );
    }

    // set html attributes
    public function html_attributes()
    {
        $attributes = parent::html_attributes(); // Get default values
        $attributes['class'] .= $this->name(); // Append this class to class attribute
        return $attributes;
    }

    // build html table
    function build_table()
    {
        $table = '<table>';
        $rows = $this->load_query();

        for ($i = 0; $i <= count($rows); $i++) {
            $table .= '<tr><td>' . ($rows[$i]) . '</td></tr>';
        }

        $table .= '</table>';
        return $table;
    }

    // get roles by user
    function load_query()
    {
        global $DB, $USER;
        $query = 'SELECT mdl_role.name
                  FROM mdl_role
                  inner join mdl_role_assignments on mdl_role_assignments.roleid=mdl_role.id
                  where mdl_role_assignments.userid='.$USER->id;

        return $DB->get_fieldset_sql($query, array('name'));
    }
}


