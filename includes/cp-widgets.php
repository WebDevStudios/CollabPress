<?php
//widget hook
add_action( 'widgets_init', 'cp_register_widgets' );

 //register CollabPress widgets
function cp_register_widgets() {
    register_widget( 'cp_widget_overview' );
}

//boj_widget_my_info class
class cp_widget_overview extends WP_Widget {

    //process the new widget
    function cp_widget_overview() {
        $widget_ops = array(
			'classname' => 'cp_widget_overview_class',
			'description' => __('Display CollabPress overview stats', 'collabpress')
			);
        $this->WP_Widget( 'cp_widget_overview', __('CollabPress: Overview Widget', 'collabpress'), $widget_ops );
    }

     //build the widget settings form
    function form( $instance ) {
        $defaults = array( 'title' => __('CollabPress Overview', 'collabpress'), 'cp_projects' => 'on', 'cp_task_lists' => 'on', 'cp_tasks' => 'on', 'cp_users' => 'on' );
        $instance = wp_parse_args( (array) $instance, $defaults );
        
        $title = $instance['title'];
        $cp_projects = $instance['cp_projects'];
        $cp_task_lists = $instance['cp_task_lists'];
        $cp_tasks = $instance['cp_tasks'];
        $cp_users = $instance['cp_users'];
        ?>
            <p><?php _e( 'Title', 'collabpress' ); ?>: <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>"  type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
            <p><?php _e( 'Show Project Count', 'collabpress' ); ?>? <input name="<?php echo $this->get_field_name( 'cp_projects' ); ?>"  type="checkbox" <?php checked( $cp_projects, 'on' ); ?> /></p>
            <p><?php _e( 'Show Task List Count', 'collabpress' ); ?>? <input name="<?php echo $this->get_field_name( 'cp_task_lists' ); ?>"  type="checkbox" <?php checked( $cp_task_lists, 'on' ); ?> /></p>
            <p><?php _e( 'Show Task Count', 'collabpress' ); ?>? <input name="<?php echo $this->get_field_name( 'cp_tasks' ); ?>"  type="checkbox" <?php checked( $cp_tasks, 'on' ); ?> /></p>
            <p><?php _e( 'Show User Count', 'collabpress' ); ?>? <input name="<?php echo $this->get_field_name( 'cp_users' ); ?>"  type="checkbox" <?php checked( $cp_users, 'on' ); ?> /></p>
        <?php
    }

    //save the widget settings
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['cp_projects'] = strip_tags( $new_instance['cp_projects'] );
        $instance['cp_task_lists'] = strip_tags( $new_instance['cp_task_lists'] );
        $instance['cp_tasks'] = strip_tags( $new_instance['cp_tasks'] );
        $instance['cp_users'] = strip_tags( $new_instance['cp_users'] );
        return $instance;
    }

    //display the widget
    function widget( $args, $instance ) {
        extract( $args );

        echo $before_widget;

        //load widget settings
        $title = apply_filters( 'widget_title', esc_html( $instance['title'] ) ) ;
        $cp_projects = empty( $instance['cp_projects'] ) ? '&nbsp;' : $instance['cp_projects'];
        $cp_task_lists = empty( $instance['cp_task_lists'] ) ? '&nbsp;' : $instance['cp_task_lists'];
        $cp_tasks = empty( $instance['cp_tasks'] ) ? '&nbsp;' : $instance['cp_tasks'];
        $cp_users = empty( $instance['cp_users'] ) ? '&nbsp;' : $instance['cp_users'];

        if ( !empty( $title ) ) { echo $before_title . $title . $after_title; };

        echo '<div class="cp-overview">';

        // Project Count
        if ( $cp_projects == 'on' ) :
            $projectCount = wp_count_posts('cp-projects');
            $projectCount = $projectCount->publish;
            echo '<p>';
            echo sprintf( _n('<span class="overview-count">%d</span> Project', '<span class="overview-count">%d</span> Projects', 'collabpress', $projectCount ), $projectCount );
            echo '</p>';
        endif;
        
        // Task Lists Count
        if ( $cp_task_lists == 'on' ) :
            $taskListCount = wp_count_posts('cp-task-lists');
            $taskListCount = $taskListCount->publish;
            echo '<p>';
            echo sprintf( _n('<span class="overview-count">%d</span> Task List', '<span class="overview-count">%d</span> Task Lists', 'collabpress', $taskListCount ), $taskListCount );
            echo '</p>';
        endif;

        // Tasks Count
        if ( $cp_tasks == 'on' ) :
            $taskCount = wp_count_posts('cp-tasks');
            $taskCount = $taskCount->publish;
            echo '<p>';
            echo sprintf( _n('<span class="overview-count">%d</span> Task', '<span class="overview-count">%d</span> Tasks', 'collabpress', $taskCount ), $taskCount );
            echo '</p>';
        endif;

        // User Count
        if ( $cp_users == 'on' ) :
            $result = count_users();
            echo '<p>';
            echo sprintf( _n('<span class="overview-count">%d</span> Task', '<span class="overview-count">%d</span> Tasks', 'collabpress', $result['total_users'] ), $result['total_users'] );
            echo '</p>';
        endif;

        echo '</div>';

        echo $after_widget;
    }
}
?>
