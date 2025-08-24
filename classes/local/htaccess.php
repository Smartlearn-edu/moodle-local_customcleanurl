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
 * Htaccess management helper for local_customcleanurl plugin.
 *
 * @package    local_customcleanurl
 * @copyright  2025 https://santoshmagar.com.np/
 * @author     santoshtmp
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

namespace local_customcleanurl\local;

/**
 * A class to check and modify htaccess file to rewrite the server route
 *
 * @package    local_customcleanurl
 * @copyright  2025 santoshtmp <https://santoshmagar.com.np/>
 * @author     santoshtmp
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class htaccess {

    /**
     * Get absolute path to the `.htaccess` file inside Moodle root.
     *
     * @return string Path to .htaccess file.
     */
    protected static function get_htaccessfilepath() {
        global $CFG;
        return $CFG->dirroot . '/.htaccess';
    }

    /**
     * Check if the required rewrite rules exist in `.htaccess`.
     *
     * @return bool True if customcleanurl rewrite rules are found, false otherwise.
     */
    public static function check_rewrite_htaccess() {
        try {
            if (file_exists(self::get_htaccessfilepath())) {
                $contents = file_get_contents(self::get_htaccessfilepath());
                return str_contains($contents, self::get_default_htaccess_content());
            } else {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if there are exactly two RewriteRule entries in `.htaccess`.
     *
     * Useful to detect if extra conflicting rules are present.
     *
     * @return bool True if exactly two RewriteRules exist, false otherwise.
     */
    public static function check_other_rewrite_rule_htaccess() {

        try {
            if (file_exists(self::get_htaccessfilepath())) {
                $contents = file_get_contents(self::get_htaccessfilepath());
                $wordcount = substr_count(strtolower($contents), strtolower('RewriteRule'));
                return ($wordcount == '1' && self::check_rewrite_htaccess()) ? true : false;
            } else {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Set (or update) the required rewrite rules in `.htaccess`.
     *
     * Used during install, upgrade, or plugin setting changes.
     *
     * @return bool True if rewrite rules were successfully written, false otherwise.
     */
    public static function set_htaccess() {

        try {
            if (file_exists(self::get_htaccessfilepath())) {
                $contents = file_get_contents(self::get_htaccessfilepath());
                $contents = self::string_except_between_two_string(
                    $contents,
                    '# BEGIN_MOODLE_LOCAL_CUSTOMCLEANURL',
                    '# END_MOODLE_LOCAL_CUSTOMCLEANURL'
                );
                $updatecontent = $contents . "\n" . self::get_default_htaccess_content();
                $updatecontent = trim($updatecontent);
                file_put_contents(self::get_htaccessfilepath(), $updatecontent);
            } else {
                $defaultcontents = self::get_default_htaccess_content();
                file_put_contents(self::get_htaccessfilepath(), $defaultcontents);
            }
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Remove the custom rewrite rules from `.htaccess`.
     *
     * Used during uninstall or plugin disable.
     *
     * @return bool True if rules were successfully removed, false otherwise.
     */
    public static function unset_htaccess() {

        try {
            if (file_exists(self::get_htaccessfilepath())) {
                $contents = file_get_contents(self::get_htaccessfilepath());
                $contents = self::string_except_between_two_string(
                    $contents,
                    '# BEGIN_MOODLE_LOCAL_CUSTOMCLEANURL',
                    '# END_MOODLE_LOCAL_CUSTOMCLEANURL'
                );
                $updatecontent = trim($contents);
                file_put_contents(self::get_htaccessfilepath(), $updatecontent);
            }
            return true;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }


    /**
     * Remove content between two markers inside a string.
     *
     * @param string $contentstring The original content.
     * @param string $startingword The starting marker string.
     * @param string $endingword The ending marker string.
     * @return string Cleaned string without the section between markers.
     */
    private static function string_except_between_two_string($contentstring, $startingword, $endingword) {
        $startpos = ($startpos = strpos($contentstring, $startingword)) ? $startpos : 0;
        $endpos = strrpos($contentstring, $endingword);
        if ($endpos) {
            $endpos += strlen($endingword);
            $contentstring = substr($contentstring, 0, $startpos) . substr($contentstring, $endpos);
        }
        return $contentstring;
    }


    /**
     * Get the default `.htaccess` rewrite rules required by this plugin.
     *
     * @return string The default rewrite rules block.
     */
    private static function get_default_htaccess_content() {
        global $CFG;
        $basepath = (new \moodle_url($CFG->wwwroot))->get_path(false);
        return trim("
# BEGIN_MOODLE_LOCAL_CUSTOMCLEANURL
# DO NOT EDIT route
<IfModule mod_rewrite.c>
# Enable RewriteEngine
RewriteEngine On
# All relative URLs are based from root
RewriteBase /
# Do not change URLs that point to an existing file and directory.
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ " . $basepath . "/local/customcleanurl/route.php [L]
ErrorDocument 403 " . $basepath . "/local/customcleanurl/404.php
ErrorDocument 404 " . $basepath . "/local/customcleanurl/404.php
</IfModule>
# DO NOT EDIT route

# Deny access to hidden files - files that start with a dot (.)
<FilesMatch \"^\.\">
Order allow,deny
Deny from all
</FilesMatch>

# Deny directory view
Options +FollowSymLinks
Options -MultiViews
Options -Indexes

# END_MOODLE_LOCAL_CUSTOMCLEANURL

        ");
    }
}
