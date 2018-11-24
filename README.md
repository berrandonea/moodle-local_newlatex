# moodle-local_newlatex
Replaces $$ LaTeX markers with \( and \)

In previous versions of Moodle, LaTeX expressions were surrounded by $$ symbols.

Since version 3.3, a LaTeX expression will be displayed alone on its line, which is usually not what's wanted.
To include a LaTeX expression inside a line of normal text, it now has to be surrounded with symbols \( and \)

When you import, on a new version of Moodle, a course that was created on an old one, or when you update a Moodle that already has old courses, you may want to replace all your $$ with \( and \)

Symbols are replaced in labels, assigns, pages, workshops and sections descriptions within the current course.
They are also replaced in all the databank questions (texts, answers, hints and feedbacks).

