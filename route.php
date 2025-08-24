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
 * @copyright  2025 https://santoshmagar.com.np/
 * @author     santoshtmp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_login(null, false);

global $CFG, $PAGE;
$url = $_SERVER['REQUEST_URI'];
$urlpath = parse_url($url, PHP_URL_PATH);
$urlquery = ($urlquery = parse_url($url, PHP_URL_QUERY)) ? '?' . $urlquery : '';

// Check if clean url is present.
$moodledefaulturl = \local_customcleanurl\local\helper::get_default_moodle_url();
if ($moodledefaulturl) {
    $file = $moodledefaulturl->out_omit_querystring();
    if (strpos($file, $CFG->wwwroot) === 0) {
        $file = substr($file, strlen($CFG->wwwroot));
        $file = $CFG->dirroot . $file;
    } else {
        $file = null;
    }
    if (is_file($file)) {
        chdir(dirname($file));
        $PAGE->set_url($moodledefaulturl);
        $CFG->moodledefaulturl = $moodledefaulturl;
        require($file);
        die();
    }
}

// Directory as path.
$dirpath = $CFG->dirroot . $urlpath;
if (is_dir($dirpath)) {
    $files = scandir($dirpath);
    foreach ($files as $filename) {
        if ($filename === 'index.html' || $filename === 'index.php') {
            $pathinfofolder = pathinfo($filename);
            $filepath = $dirpath . 'index.' . $pathinfofolder['extension'];
            chdir(dirname($filepath));
            require($filepath);
            die();
        }
    }
}

// Check if php file is present in path.
if (str_contains($urlpath, '.php')) {
    $filepath = $CFG->dirroot . explode('.php', $urlpath)[0] . '.php';
    if (file_exists($filepath)) {
        chdir(dirname($filepath));
        require($filepath);
        die();
    }
}

// At last redirect to 404 page if the path is not found.
header("HTTP/1.0 404 Not Found");
http_response_code('404');
$_SERVER['REDIRECT_STATUS'] = '404';
$pagepath404 = '/local/customcleanurl/404.php';
$filepath = $CFG->dirroot . $pagepath404;
chdir(dirname($filepath));
require($filepath);
die();
