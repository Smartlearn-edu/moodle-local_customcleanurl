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
 * Language file.
 *
 * @package    local_customcleanurl
 * @copyright  2025 https://santoshmagar.com.np/
 * @author     santoshtmp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'Custom Clean URL';
$string['pluginname_desc'] = 'Local plugin to customize moodle page url';
$string['configtitle'] = 'Custom Clean URL Settings';
$string['privacy:metadata'] = 'local customcleanurl plugin does not store any data itself.';
$string['invalidaccess'] = 'Invalid access.';
$string['featureisnotenable'] = "Custom Clean URL is not enable.";
$string['invalidsesskey'] = "Invalid sesskey";
$string['add_new_url'] = "Add new url";
$string['list_custom_url'] = "List of custom url";
$string['forbiddenpage'] = "Forbidden Page";
$string['pagenotfound'] = "Page Not Found";
$string['errorpage404'] = "404 error page";
$string['cachedef_clean_url'] = 'Clean URL Cache';
$string['cachedef_unclean_url'] = 'Default moodle URL cache';
$string['invalidcustomparam'] = 'Parameter "{$a->param}" is restricted as this parameter is alrady present in original url "{$a->responsepath}"';
$string['delete_data_heading'] = 'Delete custom url';
$string['delete_conform_text'] = 'Are you sure you want to delete "{$a->customcleanurl}"?';

$string['enable_customcleanurl'] = 'Enable Customcleanurl';
$string['change_htaccess_as_readme'] = 'Change the .htaccess accoding to readme file.';
$string['recheck_htaccess_as_readme'] = 'Re-change the .htaccess accoding to readme file, as there might be some changes. <br><strong>IGNORE</strong> if you have made the changes.';
$string['set_htaccess'] = 'Set htaccess route';
$string['set_htaccess_desc'] = 'change the .htaccess accoding to readme file. <br> After change conform by chacking .htaccess file, where there is rewrite rule for this plugin, and if there is any other rewrite rule remove other rules except from local_customcleanurl to match readme file rules.';
$string['course_url'] = 'Course URL';
$string['user_url'] = 'User URL';
$string['define_custom_url'] = 'Define Custom URL';
$string['cleanurl_options_desc'] = 'Selected options url will only be modified.
This will change the default moodle url to clean url.
<br> Example:
<br>
<ol>
    <li>Course URL
        <ol>
            <li>your_domain/course/view.php?id={ID} => your_domain/course/{course_shot_name}</li>
            <li>your_domain/course/edit.php?id={ID} => your_domain/course/edit/{course_shot_name}</li>
            <li>your_domain/course/index.php => your_domain/course</li>
            <li>your_domain/course/index.php?categoryid={ID} => your_domain/course/category/{ID}/{category_name}</li>
        </ol>
    </li>
    <li>User URL
        <ol>
            <li>your_domain/user/profile.php?id={ID} => your_domain/user/profile/{username}</li>
        </ol>
    </li>
    <li>Define Custom URL :: you can re-write particular custom moodle url.</li>
</ol>
';
$string['clean_url_type'] = 'Custom URL Type';
$string['define_custom_urldesc'] = 'Now you can define custom url for the existing moodle url at <a href="/local/customcleanurl/define_custom_url.php">HERE - Define Custom URL</a>.';

$string['sn'] = 'S.N.';
$string['default_url'] = 'Default Moodle URL';
$string['default_url_help'] = 'Default original moodle url. <br> It should be .php url and must start with your_domain or / .<br> Example: your_domain/course/view.php?id=7';
$string['custom_url'] = "Custom URL";
$string['custom_url_help'] = 'Clean custom url. <br> It should not match moodle default url, which is without .php file and moodle dir url.<br> Example: your_domain/course/math';
$string['error_default_url'] = 'Default Moodle URL "{$a->default_url}" is alrady taken.';
$string['error_default_url_alrady_clean'] = 'Default Moodle URL "{$a->default_url}" is alrady clean.';
$string['error_default_url_path'] = 'Provided path "{$a->default_url}" must start with forward slash "/".';
$string['error_default_url_not_originalurl'] = 'Provided URL "{$a->default_url}" is not the default original URL.';
$string['error_custom_url_is_default'] = 'Provided URL "{$a->custom_url}" is the default original moodle URL.';
$string['error_custom_url_path'] = 'Provided path "{$a->custom_url}" must start with forward slash "/".';
$string['error_custom_exist'] = 'Provided Custom Clean URL "{$a->custom_url}" is alrady taken.';
$string['error_custom_url_not_originalurl'] = 'Provided URL "{$a->custom_url}" is not the default original URL.';

$string['edit_custom_url_title'] = 'Edit custom url';
$string['data_saved'] = 'Custom url data is sucesfully saved.';
$string['data_updated'] = 'Custom url data is sucesfully updated.';
$string['data_saved_error'] = 'Custom url data error on save.';
$string['something_went_wrong'] = 'Something went wrong.';
$string['data_delete'] = 'successfully deleted.';
$string['data_delete_missing'] = 'Delete data is missing.';
$string['data_edit_missing'] = 'Edit data is missing.';
