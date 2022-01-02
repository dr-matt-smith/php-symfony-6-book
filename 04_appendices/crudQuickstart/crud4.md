# Customising the Twig templates

## Let's add a new Campus column to our list of students

We need to edit file:     `/templates/student/index.html.twig`

See Figure \ref{twig_location} for the location of this file in the project folder structure.

![Location of Twig template files. \label{twig_location}](./03_figures/app_crud/crud23_templateLocation.png){ width=50% }

Figure \ref{twig_before} shows the 2 places where we need to edit.

![Where we will insert lines into template. \label{twig_before}](./03_figures/app_crud/crud20_studentBeforeInserts.png){ width=75% }

Do the following:

1. Edit file: `/templates/student/index.html.twig`

1. Insert a new HTML table column header for `Campus`

1. Insert a new HTML table data item for `student.campus`

Figure \ref{twig_before} shows how the file should look now - after the lines have been inserted.

![Twig template after lines inserted. \label{twig_after}](./03_figures/app_crud/crud21_studentAfterInserts (1).png){ width=75% }

And when we visit `/student` we should see a campus column added to the student details - see Figure \ref{campus_column}.

![CRUD list page with extra column. \label{campus_column}](./03_figures/app_crud/crud22_newColumnScreenshot.png){ width=75% }

## Turn the Campus name into a LINK to the related Campus object
We can wrap an HTML hyperlink <a> element around the campus name, to connect our Student object to its related Campus object

Here's the edit we need to add to file:     `/templates/student/index.html.twig`

![Where to edit to turn campus name into hyperlink.](./03_figures/app_crud/crud24_campusIntoLinkTwig.png)

When that's done, we can now click the campus and jump to the Campus objects 'show' page:

![Web page where campus is clickable link to Campus object.](./03_figures/app_crud/crud25_linkScreenshot.png){ width=75% }

