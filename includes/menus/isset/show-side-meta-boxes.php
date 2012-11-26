<?php

global $cp_dashboard_page;
global $cp_project_page;
global $cp_task_list_page;
global $cp_task_page;
global $cp_user_page;
global $cp_calendar_page;
global $cp_view_projects;

global $cp_project;
global $cp_task_list;
global $cp_task;
global $cp_user;

if ( $cp_project_page || $cp_task_list_page || $cp_task_page ) :
	
	// Task Page
	if ( $cp_task_page ) :
	
		if ( !isset($_GET['view']) ) :
		else :
		endif;
		
		do_meta_boxes($this->pagehook, 'collabpress-files', $cp_task->id);
		do_meta_boxes($this->pagehook, 'collabpress-side', NULL);
	
	// Task List Page
	elseif ( $cp_task_list_page ) :
		
		if ( !isset($_GET['view']) ) :
		else :
		endif;
		
		do_meta_boxes($this->pagehook, 'collabpress-files', $cp_task_list->id);
		do_meta_boxes($this->pagehook, 'collabpress-side', NULL);
		
	// Project Page
	else:
		
		if ( !isset($_GET['view']) ) :
		else :
		endif;
		
		do_meta_boxes($this->pagehook, 'collabpress-files', $cp_project->id);
		do_meta_boxes($this->pagehook, 'collabpress-side', NULL);
		
	endif;
	
// User Page
elseif ( $cp_user_page ) :
	do_meta_boxes($this->pagehook, 'collabpress-side', NULL);

// Calendar Page
elseif ( $cp_calendar_page ) :
	do_meta_boxes($this->pagehook, 'collabpress-side', NULL);
	
// View All Projects Page
elseif ( $cp_view_projects ) :
	do_meta_boxes($this->pagehook, 'collabpress-side', NULL);

// Dashboard Page	
else :
	do_meta_boxes($this->pagehook, 'collabpress-side', NULL);

endif;