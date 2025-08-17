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

namespace local_customcleanurl\form;

use moodle_url;
use stdClass;

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once($CFG->libdir . '/formslib.php');
/**
 * define custom url form
 *
 * @package    local_customcleanurl
 * @copyright  2025 santoshtmp <https://santoshmagar.com.np/>
 * @author     santoshtmp
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customcleanurl_form extends \moodleform {
    // define form
    public function definition() {
        $mform = $this->_form;

        // Moodle default url
        $mform->addElement('text', 'default_url', get_string('default_url', 'local_customcleanurl'), ['size' => 70]);
        $mform->addRule('default_url', null, 'required', null, 'client');
        $mform->addHelpButton('default_url', 'default_url', 'local_customcleanurl');
        $mform->setType('default_url', PARAM_TEXT);

        // Custom url
        $mform->addElement('text', 'custom_url', get_string('custom_url', 'local_customcleanurl'), ['size' => 70]);
        $mform->addRule('custom_url', null, 'required', null, 'client');
        $mform->addHelpButton('custom_url', 'custom_url', 'local_customcleanurl');
        $mform->setType('custom_url', PARAM_TEXT);

        // action btn
        $this->add_action_buttons();

        // hidden fields
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setDefault('id', 0);

        $mform->addElement('hidden', 'action');
        $mform->setType('action', PARAM_TEXT);
        $mform->setDefault('action', '');
    }

    // form validation
    function validation($data, $files) {
        global $CFG, $DB;
        $db_table = 'local_customcleanurl';

        $errors = parent::validation($data, $files);
        $a = new stdClass();

        // Add field validation check for duplicate default_url.
        if ($data['default_url']) {
            $mooodle_url = new moodle_url(trim($data['default_url']));
            $moodle_file = $CFG->dirroot . $mooodle_url->get_path(false);
            $a->default_url =  trim($data['default_url']);
            if (is_file($moodle_file)) {
                if ($existing = $DB->get_record($db_table, array('default_url' => trim($data['default_url'])))) {
                    if (!$data['id'] || $existing->id != $data['id']) {
                        $errors['default_url'] = get_string('error_default_url', 'local_customcleanurl', $a);
                    }
                }
            } else if (is_dir($moodle_file)) {
                $errors['default_url'] = get_string('error_default_url_alrady_clean', 'local_customcleanurl', $a);
            } else if (strpos($mooodle_url->get_path(false), '/') != 0) {
                $errors['default_url'] = get_string('error_default_url_path', 'local_customcleanurl', $a);
            } else if ($mooodle_url->get_host() != (new moodle_url($CFG->wwwroot))->get_host()) {
                $errors['default_url'] = get_string('error_default_url_not_originalurl', 'local_customcleanurl', $a);
            } else {
                $errors['default_url'] = get_string('error_default_url_not_originalurl', 'local_customcleanurl', $a);
            }
        }

        // Add field validation check for duplicate custom_url.
        if ($data['custom_url']) {
            $clean_url = new moodle_url(trim($data['custom_url']));
            $clean_url_file = $CFG->dirroot . $clean_url->get_path(false);
            $a->custom_url =  trim($data['custom_url']);

            if (is_file($clean_url_file)) {
                $errors['custom_url'] = get_string('error_custom_url_is_default', 'local_customcleanurl', $a);
            } else if (is_dir($clean_url_file)) {
                $errors['custom_url'] = get_string('error_default_url_alrady_clean', 'local_customcleanurl', $a);
            } else if (strpos($clean_url->get_path(false), '/') != 0) {
                $errors['custom_url'] = get_string('error_custom_url_path', 'local_customcleanurl', $a);
            } else if ($clean_url->get_host() != (new moodle_url($CFG->wwwroot))->get_host()) {
                $errors['custom_url'] = get_string('error_custom_url_not_originalurl', 'local_customcleanurl', $a);
            } else {
                if ($existing = $DB->get_record($db_table, array('custom_url' => trim(rtrim($data['custom_url'], '/'))))) {
                    if (!$data['id'] || $existing->id != $data['id']) {
                        $errors['custom_url'] = get_string('error_custom_exist', 'local_customcleanurl', $a);
                    }
                }
            }
        }

        return $errors;
    }
}
