=== CollabPress ===
Contributors: scottbasgaard, williamsba1, cochran, boonebgorges, ericlewis
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3084056
Tags: project management, task, management, basecamp, to do, lists, team, planning, tracking, reporting, shortcode, widget, multisite, buddypress
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: 1.3.1.2

Project management and task tracking software for WordPress

== Description ==

CollabPress adds project and task management functionality to WordPress. Easily create projects and assign tasks to users.

Features include:

* Unlimited projects, task lists, tasks, and comments
* Easily edit/delete projects, task lists, tasks, and comments
* Front-end shortcode support
* BuddyPress group integration
* File upload on projects, task lists, task, and comments
* Email notifications for new tasks and comments
* Task due dates with a calendar view
* Uses built-in WordPress user accounts
* Activity log tracking all activity

Works with WordPress, Multisite, and BuddyPress!

= Get Involved =

CollabPress is an Open Source project. If you are a developer, make sure to check out and contribute to CollabPress on the [CollabPress GitHub Repository](https://github.com/WebDevStudios/CollabPress/ "CollabPress GitHub Repository").

== Screenshots ==

1. CollabPress Dashboard showing recent activity, users, overview, and more
2. Task list view showing active and completed tasks and who is assigned to each
3. Single task view with comments
4. Calendar view with tasks and due dates
5. Shortcode support example
6. BuddyPress Group integration

== Changelog ==

= 1.3.1.2 =
* Fix checking off tasks not working under task lists in project tasks screen

= 1.3.1.1 =
* Bug fixes

= 1.3.1 =
* Bug fixes from the 1.3 UI overhaul

= 1.3 =
* Complete user interface overhaul
* Themes can now override any CollabPress template
* Calendar overview of all projects, displaying tasks of projects the logged-in user has access to
* File uploads use new WordPress media manager
* Added Arabic translation [props Muneerah-Salah]

= 1.2.4 =
* Hardened security for all projects, task lists, and tasks
* Added new task and comment email filters for subject and body
* Added Slovak translation [props Branco](http://webhostinggeeks.com/user-reviews/)

= 1.2.3 =
* Removed remaining Google Hosted jQuery
* Show comments and comment form while viewing a task in BuddyPress on the front-end

= 1.2.2 =
* Calls to $wpdb have been replaced with calls to the WP APIs
* Fixed issue with screen options and missing columns
* Hardened security
* Use bundled jQuery UI and Datepicker instead of Google's
* WordPress 3.5 support

= 1.2.1 =
* Fixed bug when adding tasks without BuddyPress installed

= 1.2 =
* BuddyPress Group integration ( requires BP v1.5+ )
* Assign specific users to each project
* Task item priority setting
* Updated missing __() and _e() for translation
* Fixed missing styles for "Recent Activity" on WP Dashboard

= 1.1.3 =
* Fixed JS conflict with WordPress navigation menus
* Fixed WP nonce bug when adding a project/task list or task
* Added https with is_ssl() for Google jQuery API

= 1.1.2 =
* Fixed bug showing help menu to all users
* Exclude custom post type content from search
* Fixed bug to only show recent five projects
* Created a dedicated project page to view all projects
* Added translation file for internationalization support

= 1.1.1 =
* Fixed front-end shortcode viewing bug

= 1.1 =
* Front-end shortcode support using [collabpress]
* Set user level to view shortcode front-end
* File uploads on projects, task lists, tasks, and comments
* New CollabPress overview widget
* New setting to clear the activity log
* Limit number of users displayed
* New view all users page
* Various bug fixes

= 1.0 =
* Complete rebuild using custom post types

= 0.5.3 =
* Added CollabPress styles to necessary admin pages
* Updated readme.txt to display CollabPress screencast (0.4b)

= 0.5.2 =
* Fixed JavaScript bug conflicting w/ plugins and themes

= 0.5.1 =
* Fixed date formatting bug on comments and tasks

= 0.5 =
* Add and view comments for tasks
* Edit tasks
* Added single task detail page
* Added uninstall script
* fixed various bugs

= 0.4 =
* Added ability to edit projects
* Added minimum user role setting
* If a task is past the due date it will display in red

= 0.3 =
* Added ability to delete projects
* Added task level email notification toggle for new tasks
* Added different views for users, view more, etc
* Added links to user page for gravatars and usernames
* Fixed multiple PHP warnings

= 0.2 =
* Fixed PHP warning when loading My Tasks
* When task is completed stay on project page

= 0.1 =
* First beta release

== Upgrade Notice ==

= 1.3.1.1 =
* Critical bug fix

= 1.3 =
* New user interface
* Calendar overview of all projects, displaying tasks of projects the logged-in user has access to
* File uploads use new WordPress media manager

= 1.2.4 =
* Hardened security for all projects, task lists, and tasks
* Added new task and comment email filters for subject and body
* Added Slovak translation [props Branco](http://webhostinggeeks.com/user-reviews/)

= 1.2.3 =
* Removed remaining Google Hosted jQuery
* Show comments and comment form while viewing a task in BuddyPress on the front-end

= 1.2.2 =
* Calls to $wpdb have been replaced with calls to the WP APIs
* Fixed issue with screen options and missing columns
* Hardened security
* Use bundled jQuery UI and Datepicker instead of Google's
* WordPress 3.5 support

= 1.2.1 =
* Fixed bug when adding tasks without BuddyPress installed

= 1.2 =
* BuddyPress Group integration ( requires BP v1.5+ )
* Assign specific users to each project
* Task item priority setting
* Updated missing __() and _e() for translation
* Fixed missing styles for "Recent Activity" on WP Dashboard

= 1.1.3 =
* Fixed JS conflict with WordPress navigation menus
* Fixed WP nonce bug when adding a project/task list or task
* Added https with is_ssl() for Google jQuery API

= 1.1.2 =
* Fixed bug showing help menu to all users
* Exclude custom post type content from search
* Fixed bug to only show recent five projects
* Created a dedicated project page to view all projects
* Added translation file for internationalization support

= 1.1.1 =
* Fixed front-end shortcode viewing bug

= 1.1 =
* Front-end shortcode support using [collabpress]
* Set user level to view shortcode front-end
* File uploads on projects, task lists, tasks, and comments
* New CollabPress overview widget
* New setting to clear the activity log
* Limit number of users displayed
* New view all users page
* Various bug fixes

= 1.0 =
* CollabPress v1.0 does NOT support legacy CollabPress data at this time. If you are running a previous version, your data will not be lost, but it will not be displayed or used in the new version.

= 0.5.3 =
* Added CollabPress styles to necessary admin pages

= 0.5.2 =
* Fixed JavaScript bug conflicting w/ plugins and themes

= 0.5 =
* Adds a bunch of new features including task editing, task comments, uninstallation script, and more

== Installation ==

1. Upload the CollabPress folder to the plugins directory in your WordPress installation
2. Activate the plugin
3. Navigate to the CollabPress menu

That's it! Now you can easily start adding new projects and tasks

== Frequently Asked Questions ==

= Why are some features not implemented? =

CollabPress is currently in early development. If you have any bugs to report, ideas or anything else please visit the support forum. [CollabPress Support](http://wordpress.org/support/plugin/collabpress "Task Management Plugin Support")

= Does this plugin work with WordPress Multisite? =

Absolutely!  This plugin has been tested and verified to work on the most current version of WordPress Multisite

== Plugin Support ==
[CollabPress Support](http://wordpress.org/support/plugin/collabpress "CollabPress Plugin Support")
