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

require_once(__DIR__ . '/../../config.php');
defined('MOODLE_INTERNAL') || die();
$requesturi = $_SERVER['REQUEST_URI'];
require_login(null, false);

$responsedata = \local_customcleanurl\local\helper::check_requesturl($requesturi);

if (isset($responsedata['status']) && $responsedata['status']) {
    $moodleurl = $responsedata['moodleurl'] ?? '';
    $urltype = $responsedata['urltype'] ?? '';
    $filepath = $responsedata['filepath'] ?? '';
    $param = $responsedata['param'] ?? [];

    if ($moodleurl && $urltype == 'mod_page_view') {
        redirect($moodleurl);
        die();
    }

    if ($urltype == '404') {
        header("HTTP/1.0 404 Not Found");
        http_response_code('404');
        $_SERVER['REDIRECT_STATUS'] = '404';
        chdir(dirname($filepath));
        require($filepath);
        die();
    }

    foreach ($param as $key => $value) {
        $_GET[$key] = $value;
    }
    if (is_file($filepath)) {
        chdir(dirname($filepath));
        require($filepath);
        die();
    }
}
// Output message content.
echo $OUTPUT->header();
echo $responsedata['message'] ?? '';
echo $OUTPUT->footer();
