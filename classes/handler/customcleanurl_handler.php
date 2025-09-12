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
 * Custom Clean URL handler class.
 *
 * @package    local_customcleanurl
 * @copyright  2025 https://santoshmagar.com.np/
 * @author     santoshtmp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace local_customcleanurl\handler;

use core\output\html_writer;
use core_table\flexible_table;
use moodle_url;
use stdClass;

/**
 * Class to handle custom Clean URL data.
 *
 * @package    local_customcleanurl
 * @copyright  2025 santoshtmp <https://santoshmagar.com.np/>
 * @author     santoshtmp
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class customcleanurl_handler {

    /** @var string Database table name */
    protected static $dbtable = 'local_customcleanurl';

    /**
     * Save or update a custom clean URL entry.
     *
     * @param \stdClass $mformdata       Form data containing default_url, custom_url, and optionally id.
     * @param moodle_url|string $returnurl URL to redirect to after saving.
     * @param string $cleanurltype Type of clean URL (e.g., 'defineurl'). Defaults to 'defineurl'.
     * @return void Redirects to the given return URL with a status message.
     */
    public static function save_data($mformdata, $returnurl, $cleanurltype = 'defineurl') {
        global $DB, $CFG;
        $status = false;
        $message = '';
        $data = new stdClass();
        $data->id = $mformdata->id;
        $data->action = $mformdata->action;
        if ($cleanurltype == $mformdata->type) {
            try {
                $data->cleanurl_type = $cleanurltype;
                $data->default_url = str_replace($CFG->wwwroot, '', trim($mformdata->default_url));
                $data->default_url = rtrim($data->default_url, '/');
                $data->custom_url = str_replace($CFG->wwwroot, '', trim($mformdata->custom_url));

                $data->timemodified = time();

                if ($data->id && ($data->action == 'edit')) {
                    $dataexists = $DB->record_exists(self::$dbtable, ['id' => $data->id]);
                    if ($dataexists) {
                        $status = $DB->update_record(self::$dbtable, $data);
                        if ($status) {
                            $message = get_string('data_updated', 'local_customcleanurl');
                        }
                    }
                } else {
                    $data->timecreated = time();
                    $status = $DB->insert_record(self::$dbtable, $data);
                    if ($status) {
                        $message = get_string('data_saved', 'local_customcleanurl');
                    }
                }
            } catch (\Throwable $th) {
                $message = get_string('data_saved_error', 'local_customcleanurl');
            }
        }
        if (!$message) {
            $message = get_string('something_went_wrong', 'local_customcleanurl');
        }

        redirect($returnurl, $message);
    }

    /**
     * Delete a custom clean URL entry by ID.
     *
     * @param int $id         Record ID to delete.
     * @param moodle_url|string $returnurl URL to redirect to after deletion.
     * @return void Redirects to the given return URL with a status message.
     */
    public static function delete_data($id, $returnurl) {
        global $DB, $USER;
        $message = '';

        $data = $DB->get_record(self::$dbtable, ['id' => $id]);
        if ($data) {
            $delete = $DB->delete_records(self::$dbtable, ['id' => $data->id]);
            if ($delete) {
                $message = get_string('data_delete', 'local_customcleanurl');
            }
        } else {
            $message = get_string('data_delete_missing', 'local_customcleanurl');
        }
        if (!$message) {
            $message = get_string('something_went_wrong', 'local_customcleanurl');
        }

        redirect($returnurl, $message);
    }


    /**
     * Populate the edit form with data from an existing record.
     *
     * @param \moodleform $mform     The Moodle form instance.
     * @param int $id                Record ID to edit.
     * @param moodle_url|string $returnurl URL to redirect to if record not found.
     * @return void Outputs form UI or redirects if record is missing.
     */
    public static function edit_form($mform, $id, $returnurl) {

        global $DB, $PAGE, $OUTPUT;
        $data = $DB->get_record(self::$dbtable, ['id' => $id]);
        if ($data) {
            $PAGE->navbar->add(get_string('edit_custom_url_title', 'local_customcleanurl'));
            $entry = new stdClass();
            $entry->id = $id;
            $entry->default_url = $data->default_url;
            $entry->custom_url = $data->custom_url;
            $entry->action = 'edit';
            // ... output content
            echo $OUTPUT->header();
            echo html_writer::tag(
                'h3',
                get_string('edit_custom_url_title', 'local_customcleanurl')
            );
            $mform->set_data($entry);
            $mform->display();
            echo $OUTPUT->footer();
            die;
        } else {
            $message = get_string('data_edit_missing', 'local_customcleanurl');
        }
        redirect($returnurl, $message);
    }

    /**
     * Generate and return a paginated table of custom clean URLs.
     *
     * @param string $pagepath
     * @param int $perpage Number of records per page. Default = 12.
     * @param string $cleanurltype Type of clean URL (e.g., 'defineurl'). Defaults to 'defineurl'.
     * @return string HTML output of the table.
     */
    public static function get_custom_url_data_table($pagepath, int $perpage = 12, $cleanurltype = 'defineurl') {
        global $CFG, $DB, $PAGE;
        $outputdata = '';
        $pageurl = new moodle_url($pagepath);
        // ... table generate.
        require_once($CFG->libdir . '/tablelib.php');
        $table = new flexible_table('moodle-clean-custom-url-data');
        $tablecolumns = [
            'id',
            'default_url',
            'custom_url',
            'action',
        ];
        $tableheaders = [
            get_string('sn', 'local_customcleanurl'),
            get_string('default_url', 'local_customcleanurl'),
            ($cleanurltype == 'defineurl') ?
                get_string('custom_url', 'local_customcleanurl') : get_string('redirect_url', 'local_customcleanurl'),
            get_string('action'),
        ];
        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($pageurl);
        $table->sortable(true);
        $table->set_attribute('id', 'moodle-clean-custom-url-data');
        $table->set_attribute('class', 'moodle-clean-custom-url');
        $table->set_control_variables(
            [
                TABLE_VAR_SORT    => 'ssort',
                TABLE_VAR_IFIRST  => 'sifirst',
                TABLE_VAR_ILAST   => 'silast',
                TABLE_VAR_PAGE    => 'spage',
            ]
        );
        $table->no_sorting('action');
        $table->no_sorting('id');
        $table->setup();
        $table->pagesize($perpage, $DB->count_records(self::$dbtable, ['cleanurl_type' => $cleanurltype]));
        $limitfrom = $table->get_page_start();
        $limitnum = $table->get_page_size();
        if (isset($_GET['ssort']) && $table->get_sql_sort()) {
            $sort = $table->get_sql_sort();
        } else {
            $sort = 'id DESC';
        }
        // ... get data from db.
        $datarecords = $DB->get_records(
            self::$dbtable,
            ['cleanurl_type' => $cleanurltype],
            $sort,
            $fields = '*',
            $limitfrom,
            $limitnum,
        );
        ob_start();
        if ($datarecords) {
            $i = $limitfrom + 1;
            foreach ($datarecords as $record) {
                $editlink = (new moodle_url(
                    $pagepath,
                    [
                        'action' => 'edit',
                        'id' => $record->id,
                        'sesskey' => sesskey(),
                    ]
                ))->out(false);
                $deletelink = (new moodle_url(
                    $pagepath,
                    [
                        'action' => 'delete',
                        'id' => $record->id,
                        'sesskey' => sesskey(),
                    ]
                ))->out(false);
                $default_url = $CFG->wwwroot . $record->default_url;
                $custom_url = $CFG->wwwroot . $record->custom_url;
                $row = [];
                $row[] = $i;
                $row[] = html_writer::link($default_url, $default_url);
                $row[] = html_writer::link($custom_url, $custom_url);
                $row[] = html_writer::link($editlink, get_string('edit'), ["class" => "btn btn-primary"]) .
                    html_writer::link(
                        $deletelink,
                        get_string('delete'),
                        ["class" => "btn btn-secondary delete-action", "data-title" => $record->custom_url]
                    );
                $table->add_data($row);
                $i = $i + 1;
            }
        }
        $table->finish_output();
        $outputdata = ob_get_contents();
        ob_end_clean();

        $PAGE->requires->js_call_amd('local_customcleanurl/customcleanurl', 'conformdelete');

        return $outputdata;
    }

    // ... END
}
