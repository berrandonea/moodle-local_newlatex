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
 * Updates Moodle TeX notation in the current course.
 *
 * @package   local_newlatex
 * @copyright 2018 Brice Errandonea <brice.errandonea@u-cergy.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * File : lib.php
 * Library functions
 */

defined('MOODLE_INTERNAL') || die();

function local_newlatex_extend_settings_navigation(settings_navigation $nav, context $context) {
	global $COURSE;
    if (has_capability('local/newlatex:convert', $context)) {
        $branch = $nav->get('courseadmin');        
        if ($branch) {
            $params = array('id' => $COURSE->id);
            $url = new moodle_url('/local/newlatex/newlatex.php', $params);
            $label = get_string('convert', 'local_newlatex');
            $branch->add($label, $url, $nav::TYPE_CONTAINER, null, null, null);
        }
    }
}
