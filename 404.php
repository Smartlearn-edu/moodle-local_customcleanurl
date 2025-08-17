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

// Get require config file.
require_once(dirname(__FILE__) . '/../../config.php');

// Prepare the page information. 
global $OUTPUT, $PAGE;
$page_path = '/local/customcleanurl/404.php';
$redirect_status = ($_SERVER['REDIRECT_STATUS'] === '403') ? "403" : http_response_code();
if ($redirect_status === '403') {
    $page_title = get_string('forbiddenpage', 'local_customcleanurl');
} else {
    $page_title = get_string('pagenotfound', 'local_customcleanurl');
}
$context = \context_system::instance();
$page_url = new moodle_url($page_path);
$strcssclass = $redirect_status . '-page';

// setup page information.
$PAGE->set_context($context);
$PAGE->set_url($page_url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title($page_title);
$PAGE->set_heading($page_title);
$PAGE->set_pagetype('error-404');
$PAGE->navbar->add($page_title);
$PAGE->requires->jquery();
$PAGE->add_body_class($strcssclass);

// output content
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_customcleanurl/404',[]);
echo $OUTPUT->footer();
