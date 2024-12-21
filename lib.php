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
 * 
 * @package    local_customcleanurl
 * @copyright  2024 https://santoshmagar.com.np/
 * @author     santoshtmp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */

defined('MOODLE_INTERNAL') || die();


/**
 * From moodle 4.4 callback are managed through callback hook
 * https://moodledev.io/docs/4.5/apis/core/hooks
 * https://docs.moodle.org/dev/Output_callbacks#before_http_headers
 * https://docs.moodle.org/dev/Callbacks
 */
function local_customcleanurl_before_http_headers()
{
    \local_customcleanurl\local\helper::urlrewriteclass_initialize();
}


/**
 * 
 * https://docs.moodle.org/dev/Login_callbacks#after_config
 */
function local_customcleanurl_after_config()
{
    \local_customcleanurl\local\helper::urlrewriteclass_initialize();
}

// 
/**
 * 
 */
// function local_customcleanurl_before_standard_html_head()
// {
// }

/**
 * @return string
 */
// function local_customcleanurl_render_navbar_output()
// {
// }

/**
 * Callback allowing to add contetnt inside the region-main, in the very end
 *
 * @return string
 */
// function local_customcleanurl_before_footer() {}


/**
 * 
 */
function local_customcleanurl_set_htaccess()
{

    \local_customcleanurl\local\htaccess::set_htaccess();
    set_config('set_htaccess', '0', 'local_customcleanurl');

    purge_all_caches();
}


/**
 * @return array 
 */
function get_cleanurl_define_route()
{

    $routes = [
        '/category' => '/theme/skilllab/pages/category/index.php',
        '/category/edit' => '/theme/skilllab/pages/category/edit.php',
        '/category/delete' => '/theme/skilllab/pages/category/delete.php',
        '/lms-admin-login' => '/theme/skilllab/pages/login/admin-login.php',
        '/logout' => '/theme/skilllab/pages/login/csc-lms-logout.php',
        '/course/delete' => '/theme/skilllab/pages/course/delete.php',
        '/view-certificate' => '/theme/skilllab/pages/view-certificate.php',
        '/time-track-report' => '/theme/skilllab/pages/report/time_track.php',
        '/student-course-report' => '/theme/skilllab/pages/report/index.php',
        '/scholarship' => '/theme/skilllab/pages/scholarship/index.php',
        '/career_road_map' => '/theme/skilllab/pages/career_road_map/index.php'
    ];

    return $routes;
}
