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

use core\exception\moodle_exception;
use core\output\html_writer;
use local_customcleanurl\handler\customcleanurl_handler;

// Get require config file.
require_once(dirname(__FILE__) . '/../../config.php');
defined('MOODLE_INTERNAL') || die();

// Get parameter
$id = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_TEXT);
$context = \context_system::instance();

// Access checks and validate.
require_login(null, false);
if (!has_capability('moodle/site:config', $context)) {
    throw new moodle_exception('invalidaccess', 'local_customcleanurl');
}
$enable_customcleanurl = (int)get_config('local_customcleanurl', 'enable_customcleanurl');
$cleanurl_options = get_config('local_customcleanurl', 'cleanurl_type');
$cleanurl_options = explode(",", $cleanurl_options);
if (!(in_array('define_url', $cleanurl_options) && $enable_customcleanurl)) {
    throw new moodle_exception('featureisnotenable', 'local_customcleanurl');
}

// Prepare the page information. 
$page_url = new moodle_url('/local/customcleanurl/define_custom_url.php');
$page_title = get_string('define_custom_url', 'local_customcleanurl');

// setup page information.
$PAGE->set_context($context);
$PAGE->set_url($page_url);
$PAGE->set_pagelayout('admin');
$PAGE->set_pagetype('define_custom_url');
$PAGE->set_title($page_title);
$PAGE->set_heading($page_title);
$PAGE->navbar->add($page_title);
$PAGE->set_blocks_editing_capability('moodle/site:manageblocks');
$PAGE->requires->jquery();

// FORM actions
$define_custom_url_form = new \local_customcleanurl\form\customcleanurl_form();
if ($define_custom_url_form->is_cancelled()) {
    redirect($page_url);
} else if ($form_data = $define_custom_url_form->get_data()) {
    customcleanurl_handler::save_data($form_data, $page_url, 'define_url');
} else {
    if ($action && $id) {
        // verify sesskey
        $sesskey = required_param('sesskey', PARAM_ALPHANUM);
        if ($sesskey != sesskey()) {
            redirect($page_url, get_string('invalidsesskey', 'local_customcleanurl'));
        }
        // For Delete
        if ($action == 'delete') {
            customcleanurl_handler::delete_data($id, $page_url);
        }
        // For Edit
        if ($action == 'edit') {
            customcleanurl_handler::edit_form($define_custom_url_form, $id, $page_url);
        }
    }
}

// Get the data and display
$contents = '';
$contents .= html_writer::start_tag('div', ['class' => 'add-custom-url-wrapper mt-4 mb-4']);
$contents .= html_writer::tag('h3', get_string('add_new_url', 'local_customcleanurl'));
$contents .= $define_custom_url_form->render();
$contents .= html_writer::end_tag('div');
$contents .= html_writer::start_tag('div', ['class' => 'custom-url-list-wrapper mt-4 mb-4']);
$contents .= html_writer::tag('h3', get_string('list_custom_url', 'local_customcleanurl'));
$contents .= customcleanurl_handler::get_custom_url_data_table(50);
$contents .= html_writer::end_tag('div');

// Output Content.
echo $OUTPUT->header();
echo $contents;
echo $OUTPUT->footer();
