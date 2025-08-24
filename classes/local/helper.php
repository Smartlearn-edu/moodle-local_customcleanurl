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
 * Helper class for local_customcleanurl plugin.
 *
 * @package    local_customcleanurl
 * @copyright  2025 https://santoshmagar.com.np/
 * @author     santoshtmp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace local_customcleanurl\local;

use moodle_url;
use stdClass;

/**
 * helper class for customcleanurl local
 *
 * @package    local_customcleanurl
 * @copyright  2025 santoshtmp <https://santoshmagar.com.np/>
 * @author     santoshtmp
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Checks whether custom clean URL feature is enabled in plugin settings.
     *
     * @return bool True if custom clean URLs are enabled, false otherwise.
     */
    public static function is_enable_customcleanurl() {
        global $CFG;

        try {
            if (isset($CFG->enablecustomcleanurl)) {
                return $CFG->enablecustomcleanurl;
            }
            $enablecustomcleanurl = get_config('local_customcleanurl', 'enable_customcleanurl');
            if ($enablecustomcleanurl && $enablecustomcleanurl == '1') {
                $CFG->enablecustomcleanurl = true;
            } else {
                $CFG->enablecustomcleanurl = false;
            }
        } catch (\Throwable $th) {
            $CFG->enablecustomcleanurl = false;
        }
        return $CFG->enablecustomcleanurl;
    }

    /**
     * Attempts to resolve the current request URL into a default Moodle URL
     * if it matches a defined clean URL pattern.
     *
     * Supported clean URL types:
     * - `define_url` (admin-defined mappings)
     * - `course_url` (/course/{shortname}, /course/edit/{shortname}, /course/category/{id}/{name})
     * - `user_url` (/user/profile/{username})
     *
     * @return moodle_url|false The resolved Moodle URL object if a mapping exists,
     *                          false if no mapping is found or feature disabled.
     */
    public static function get_default_moodle_url() {
        global $DB, $OUTPUT;
        if (!self::is_enable_customcleanurl()) {
            return;
        }

        $requestmoodleurl = new moodle_url($_SERVER['REQUEST_URI']);
        $requestpath = $requestmoodleurl->get_path(false);

        $parts = explode("/", trim($requestpath, '/'));
        $uniquename = urldecode(end($parts));
        $responsepath = '';

        $cleanurltype = get_config('local_customcleanurl', 'cleanurl_type');
        $cleanurltype = explode(",", $cleanurltype);

        // Case 1: Admin-defined custom mapping.
        if (in_array('define_url', $cleanurltype)) {
            $checkcustomurlpath = $DB->get_record('local_customcleanurl', ['custom_url' => $requestpath]);
            if ($checkcustomurlpath) {
                $responsepath = $checkcustomurlpath->default_url;
            }
        }

        // Case 2: Course-related URLs.
        if (in_array('course_url', $cleanurltype) && !$responsepath && $parts[0] === 'course') {
            $course = $DB->get_record('course', ['shortname' => $uniquename]);
            if ($course && count($parts) == '2') {
                $responsepath = "/course/view.php?id=" . $course->id;
            } else if ($course && count($parts) == '3' && $parts[1] === 'edit') {
                $responsepath = "/course/edit.php?id=" . $course->id;
            } else if (count($parts) == '4') {
                $coursecategories = $DB->get_record('course_categories', ['id' => $parts[2]]);
                $responsepath = "/course/index.php?categoryid=" . $coursecategories->id;
            }
        }

        // Case 3: User profile URLs.
        if (in_array('user_url', $cleanurltype) && !$responsepath && $parts[0] === 'user') {
            $user = $DB->get_record('user', ['username' => $uniquename]);
            if ($user && count($parts) == '3') {
                $responsepath = "/user/profile.php?id=" . $user->id;
            }
        }

        // Return the resolved Moodle URL if found.
        if ($responsepath) {
            $requestparam = $requestmoodleurl->params();
            $url = new moodle_url($responsepath);
            foreach ($url->params() as $k => $v) {
                if (array_key_exists($k, $requestparam)) {
                    if (isset($_GET[$k])) {
                        $a = new stdClass();
                        $a->param = $k;
                        $a->responsepath = $responsepath;
                        echo $OUTPUT->header();
                        echo get_string('invalidcustomparam', 'local_customcleanurl', $a);
                        echo $OUTPUT->footer();
                        die;
                    }
                }
                $v = str_replace('+', ' ', $v);
                $_GET[$k] = $v;
            }
            return $url;
        }
        return false;
    }



    /**
     * Initializes the custom clean URL rewrite class by assigning it
     * to $CFG->urlrewriteclass if enabled.
     *
     * This method is safe during installation and upgrade phases (skips setup).
     *
     * @return void
     */
    public static function urlrewriteclass_initialize() {
        global $CFG;
        if (during_initial_install() || isset($CFG->upgraderunning)) {
            // Do nothing during installation or upgrade.
            return;
        }
        if (self::is_enable_customcleanurl() && class_exists('\local_customcleanurl\customcleanurl')) {
            $CFG->urlrewriteclass = '\\local_customcleanurl\\customcleanurl';
        }
    }
}
