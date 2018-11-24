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
 * File : newlatex.php
 * Process conversion
 */

require_once('../../config.php');

$courseid = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$course = get_course($courseid);

require_login($course);
$coursecontext = context_course::instance($courseid);
require_capability('local/newlatex:convert', $coursecontext);

$title = get_string('convert', 'local_newlatex');
$pageurl = $CFG->wwwroot."/local/newlatex/newlatex.php?id=$courseid";
$courseurl = "$CFG->wwwroot/course/view.php?id=$courseid";
$PAGE->set_url($pageurl);
$PAGE->set_title($title);
$PAGE->set_heading($title);

$nbupdated = 0;
if ($confirm) {
    $labelsql = "SELECT * FROM {label} WHERE course = $courseid AND intro LIKE '%$$%$$%'";
    $latexlabels = $DB->get_recordset_sql($labelsql);
    foreach ($latexlabels as $latexlabel) {
        $latexlabel->intro = newlatex($latexlabel->intro);
        $DB->update_record('label', $latexlabel);
        $nbupdated++;
    }
    $latexlabels->close();

    $assignsql = "SELECT * FROM {assign} WHERE course = $courseid AND intro LIKE '%$$%$$%'";
    $latexassigns = $DB->get_recordset_sql($assignsql);
    foreach ($latexassigns as $latexassign) {
        $latexassign->intro = newlatex($latexassign->intro);
        $DB->updaterecord('assign', $latexassign);
        $nbupdated++;
    }

    $pagesql = "SELECT * FROM {page} WHERE course = $courseid AND (intro LIKE '%$$%$$%' OR content LIKE '%$$%$$%')";
    $latexpages = $DB->get_recordset_sql($pagesql);
    foreach ($latexpages as $latexpage) {
        $latexpage->intro = newlatex($latexpage->intro);
        $latexpage->content = newlatex($latexpage->content);
        $DB->update_record('page', $latexpage);
        $nbupdated++;
    }
    $latexpages->close();

    $sectionsql = "SELECT * FROM {course_sections} WHERE course = $courseid AND summary LIKE '%$$%$$%'";
    $latexsections = $DB->get_recordset_sql($sectionsql);
    foreach ($latexsections as $latexsection) {
        $latexsection->summary = newlatex($latexsection->summary);
        $DB->update_record('course_sections', $latexsection);
        $nbupdated++;
    }
    $latexsections->close();

    $questionsql = "SELECT * FROM {question} WHERE questiontext LIKE '%$$%$$%' OR generalfeedback LIKE '%$$%$$%'";
    $latexquestions = $DB->get_recordset_sql($questionsql);
    foreach ($latexquestions as $latexquestion) {
        $latexquestion->questiontext = newlatex($latexquestion->questiontext);
        $latexquestion->generalfeedback = newlatex($latexquestion->generalfeedback);
        $DB->update_record('question', $latexquestion);
        $nbupdated++;
    }
    $latexquestions->close();

    $answersql = "SELECT * FROM {question_answers} WHERE answer LIKE '%$$%$$%' OR feedback LIKE '%$$%$$%'";
    $latexanswers = $DB->get_recordset_sql($answersql);
    foreach ($latexanswers as $latexanswer) {
        $latexanswer->answer = newlatex($latexanswer->answer);
        $latexanswer->feedback = newlatex($latexanswer->feedback);
        $DB->update_record('question_answers', $latexanswer);
        $nbupdated++;
    }
    $latexanswers->close();

    $hintsql = "SELECT * FROM {question_hints} WHERE hint LIKE '%$$%$$%'";
    $latexhints = $DB->get_recordset_sql($hintsql);
    foreach ($latexhints as $latexhint) {
        $latexhint->hint = newlatex($latexhint->hint);
        $DB->update_record('question_hints', $latexhint);
        $nbupdated++;
    }
    $latexhints->close();

    $workshopsql = "SELECT * FROM {workshop} WHERE course = $courseid ".
        "AND (intro LIKE '%$$%$$%') OR (instructauthors LIKE '%$$%$$%') ".
        "OR (instructreviewers LIKE '%$$%$$%') OR (conclusion LIKE '%$$%$$%')";
    $latexworkshops = $DB->get_recordset_sql($workshopsql);
    foreach ($latexworkshops as $latexworkshop) {
        $latexworkshop->intro = newlatex($latexworkshop->intro);
        $latexworkshop->instructauthors = newlatex($latexworkshop->instructauthors);
        $latexworkshop->instructreviewers = newlatex($latexworkshop->instructreviewers);
        $latexworkshop->conclusion = newlatex($latexworkshop->conclusion);
        $DB->update_record('workshop', $latexworkshop);
        $nbupdated++;
    }
    $latexworkshops->close();
}

echo $OUTPUT->header();
if ($confirm) {
    echo $nbupdated.' '.get_string('updatedtexts', 'local_newlatex').".<br>";
    echo "<p><a href='$courseurl'>".get_string('back')."</a></p>";
} else {
    echo "<p style='text-align:justify'>".get_string('thiswillconvert', 'local_newlatex')."</p>";
    echo "<p style='text-align:center'>";
    echo "<a href='$pageurl&confirm=1'><button class='btn btn-primary'>".get_string('confirm')."</button></a>";
    echo " &nbsp; &nbsp; ";
    echo "<a href='$courseurl'>".get_string('cancel')."</a>"."</p>";
}
echo $OUTPUT->footer();

function newlatex($oldstring) {
    $oldtable = explode ('$$', $oldstring);
    $nbchunks = count($oldtable);
    if ($nbchunks > 1) {
        $newstring = '';
        for ($i = 0; $i < ($nbchunks - 1); $i++) {
            $newstring .= $oldtable[$i];
            if ($i % 2) {
                $newstring .= '\)';
            } else {
                $newstring .= '\(';
            }
        }
        $newstring .= $oldtable[$nbchunks - 1];
        return $newstring;
    } else {
        return $oldstring;
    }
}
