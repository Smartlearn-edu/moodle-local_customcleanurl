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
 * Setting file.
 *
 * @package    local_customcleanurl
 * @copyright  2025 https://santoshmagar.com.np/
 * @author     santoshtmp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

use html_writer;
use local_customcleanurl\local\helper;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/adminlib.php');

if ($hassiteconfig) {

    $checkrewritehtaccess = '';
    $isenablecustomcleanurl = helper::is_enable_customcleanurl();
    $customcleanurlroutecheck = false;

    // ... Heading.
    $settings = new admin_settingpage('local_customcleanurl', get_string('pluginname', 'local_customcleanurl'));
    $ADMIN->add('localplugins', $settings);

    // ... Enable custom clean url
    $name = 'local_customcleanurl/enable_customcleanurl';
    $title = get_string('enable_customcleanurl', 'local_customcleanurl');
    $description = '';
    if ($isenablecustomcleanurl) {
        $section =  optional_param('section', '', PARAM_TEXT);
        if ($section == 'local_customcleanurl') {
            $customcleanurlroutecheck = helper::customcleanurl_routecheck();
            if ($customcleanurlroutecheck) {
                $description .= html_writer::tag(
                    'div',
                    get_string('pass_customcleanurlroutecheck', 'local_customcleanurl'),
                    ["class" => "alert alert-info alert-block fade in  alert-dismissible"]
                );
            } else {
                $description .= html_writer::tag(
                    'div',
                    get_string('fail_customcleanurlroutecheck', 'local_customcleanurl'),
                    ["class" => "alert alert-danger alert-block fade in  alert-dismissible"]
                );
                $description .= html_writer::tag(
                    'div',
                    html_writer::tag('pre', \local_customcleanurl\local\htaccess::get_default_htaccess_content()),
                    ["class" => "alert alert-info alert-block fade in  alert-dismissible"]
                );
            }
        }
    }
    $setting = new admin_setting_configcheckbox($name, $title, $description, 0);
    $settings->add($setting);

    // ... after enable enable_customcleanurl, check route.
    if ($isenablecustomcleanurl && $customcleanurlroutecheck) {
        // ... define custom url type.
        $checkboxoptions  = [
            'courseurl' => get_string('course_url', 'local_customcleanurl'),
            'userurl' => get_string('user_url', 'local_customcleanurl'),
            'defineurl' => get_string('define_custom_url', 'local_customcleanurl'),
        ];
        $defaultvalues = [
            'courseurl' => 0,
            'userurl' => 0,
            'defineurl' => 1,
        ];
        $name = 'local_customcleanurl/cleanurl_type';
        $title = get_string('clean_url_type', 'local_customcleanurl');
        $description = get_string('cleanurl_options_desc', 'local_customcleanurl');
        $setting = new admin_setting_configmulticheckbox($name, $title, $description, $defaultvalues, $checkboxoptions);
        $settings->add($setting);
    }

    // ... define custom url link
    $cleanurloptions = get_config('local_customcleanurl', 'cleanurl_type');
    $cleanurloptions = explode(",", $cleanurloptions);
    if (in_array('defineurl', $cleanurloptions) && $isenablecustomcleanurl) {
        $a = new stdClass();
        $a->url = (new moodle_url('/local/customcleanurl/define_custom_url.php'))->out(false);
        $settings->add(new admin_setting_heading(
            'local_customcleanurl',
            get_string('define_custom_url', 'local_customcleanurl'),
            get_string('define_custom_urldesc', 'local_customcleanurl', $a)
        ));
    }
}
