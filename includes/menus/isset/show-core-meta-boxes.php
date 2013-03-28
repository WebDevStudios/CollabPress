<?php

global $cp_dashboard_page;
global $cp_project_page;
global $cp_task_list_page;
global $cp_task_page;
global $cp_user_page;
global $cp_calendar_page;
global $cp_view_projects;
global $cp_all_users_page;

if ( $cp_project_page || $cp_task_list_page || $cp_task_page ) :

	// Task Page
	if ( $cp_task_page ) :

		if ( !isset($_GET['view']) ) :
			cp_task_comments();
		else :
			do_meta_boxes($this->pagehook, 'collabpress-task-edit', NULL);
		endif;

	// Task List Page
	elseif ( $cp_task_list_page ) :

		if ( !isset($_GET['view']) ) :
			do_meta_boxes($this->pagehook, 'collabpress-main', NULL);
			do_meta_boxes($this->pagehook, 'collabpress-task', NULL);
			do_meta_boxes($this->pagehook, 'collabpress-task-query', NULL);
		else :
			do_meta_boxes($this->pagehook, 'collabpress-task-list-edit', NULL);
		endif;

	// Project Page
	else:

		if ( !isset($_GET['view']) ) :
			do_meta_boxes($this->pagehook, 'collabpress-main', NULL);
			do_meta_boxes($this->pagehook, 'collabpress-task-list', NULL);
			do_meta_boxes($this->pagehook, 'collabpress-task-list-query', NULL);
		else :
			do_meta_boxes($this->pagehook, 'collabpress-project-edit', NULL);
		endif;

	endif;

// User Page
elseif ( $cp_user_page ) :
	cp_user_page();

// Calendar Page
elseif ( $cp_calendar_page ) :
	cp_draw_calendar();

// View All Projects Page
elseif ( $cp_view_projects ) :
	cp_view_all_projects();

// Dashboard Page
else :
	do_meta_boxes($this->pagehook, 'collabpress-main', NULL);
	do_meta_boxes($this->pagehook, 'collabpress-project', NULL);
endif;

do_meta_boxes($this->pagehook, 'collabpress-footer', NULL);