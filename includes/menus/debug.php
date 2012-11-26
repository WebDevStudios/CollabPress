<?php
//verify user has permission to view settings
if ( cp_check_permissions( 'settings_user_role' ) ) {
    
    function cp_debug_display_loop($query) {

        if ( $query->have_posts() ) :
        while( $query->have_posts() ) : $query->the_post();

            echo '<p style="color: green; border-bottom: 2px solid #eee; width: 25%"><strong>'.__('Title: ', 'collabpress').'</strong>'.get_the_title().'</p>';

            echo '<p><strong>'.__('ID: ', 'collabpress').'</strong>' . get_the_ID() . '</p>';

            echo '<p><strong>'.__('Author: ', 'collabpress').'</strong>' . get_the_author() . '</p>';

            echo '<p><strong>'.__('Created: ', 'collabpress').'</strong>' . get_the_date() . ' '.__('at', 'collabpress').' ' . get_the_time() . '</p>';

            echo '<p><strong>'.__('Modified: ', 'collabpress').'</strong>' . get_the_modified_date() . ' '.__('at', 'collabpress').' ' . get_the_modified_time() . '</p>';

            $custom_fields = get_post_custom();

            if ($custom_fields) :

            echo '<strong>'.__('Custom Fields: '. 'collabpress').'</strong>';

            echo '<div style="padding-left: 10px">';

            foreach ( $custom_fields as $field_key => $field_values ) {
                foreach ( $field_values as $key => $value ) {
                    echo '<p style="font-weight:bold; color: blue;">'.__('name: ', 'collabpress'). $field_key . '</strong></p>';
                    echo '<p><strong>'.__('value', 'collabpress').'</strong>: '. $value . '</p>';
                }
            }

            echo '</div>';

            endif;

        endwhile;
        wp_reset_query();
        else :
            echo '<p>'.__('No results...', 'collabpress').'</p>';
        endif;

        wp_reset_query();
    }
?>
    <div id="collabpress-wrap" class="wrap">
	<?php echo cp_screen_icon( 'collabpress' ); ?>
        <h2><?php _e( 'CollabPress Debug', 'collabpress' ); ?></h2>

        <h2><?php _e('Delete', 'collabpress') ?></h2>

        <?php

        if ( isset($_POST['cp-delete-data']) ) :

            if ( isset($_POST['cp-debug-delete-all']) || isset($_POST['cp-debug-delete-projects']) ) :

                $debug_query_args = array( 'showposts' => '-1', 'post_type' => 'cp-projects');
                $debug_query = new WP_Query( $debug_query_args );
                while( $debug_query->have_posts() ) : $debug_query->the_post();
                    global $post;
                    wp_delete_post( $post->ID, true);
                endwhile; wp_reset_query();

            endif;

            if ( isset($_POST['cp-debug-delete-all']) || isset($_POST['cp-debug-delete-task-lists']) ) :

                $debug_query_args = array( 'showposts' => '-1', 'post_type' => 'cp-task-lists');
                $debug_query = new WP_Query( $debug_query_args );
                while( $debug_query->have_posts() ) : $debug_query->the_post();
                    global $post;
                    wp_delete_post( $post->ID, true);
                endwhile; wp_reset_query();

            endif;

            if ( isset($_POST['cp-debug-delete-all']) || isset($_POST['cp-debug-delete-tasks']) ) :

                $debug_query_args = array( 'showposts' => '-1', 'post_type' => 'cp-tasks');
                $debug_query = new WP_Query( $debug_query_args );
                while( $debug_query->have_posts() ) : $debug_query->the_post();
                    global $post;
                    wp_delete_post( $post->ID, true);
                endwhile; wp_reset_query();

            endif;

            if ( isset($_POST['cp-debug-delete-all']) || isset($_POST['cp-debug-delete-meta-data']) ) :

                $debug_query_args = array( 'showposts' => '-1', 'post_type' => 'cp-meta-data');
                $debug_query = new WP_Query( $debug_query_args );
                while( $debug_query->have_posts() ) : $debug_query->the_post();
                    global $post;
                    wp_delete_post( $post->ID, true);
                endwhile; wp_reset_query();

            endif;

        endif;

        ?>

        <form method="post">
            <input type="hidden" name="cp-delete-data" />
            <label for="cp-debug-delete-all"><?php _e('Delete All CollabPress Data: ', 'collabpress'); ?></label>
            <input type="submit" name="cp-debug-delete-all" value="<?php _e('Submit', 'collabpress') ?>" />
            <br /><br />
            <label for="cp-debug-delete-all"><?php _e('Delete CollabPress Projects: ', 'collabpress'); ?></label>
            <input type="submit" name="cp-debug-delete-projects" value="<?php _e('Submit', 'collabpress') ?>" />
            <br /><br />
            <label for="cp-debug-delete-all"><?php _e('Delete CollabPress Task Lists: ', 'collabpress'); ?></label>
            <input type="submit" name="cp-debug-delete-task-lists" value="<?php _e('Submit', 'collabpress') ?>" />
            <br /><br />
            <label for="cp-debug-delete-all"><?php _e('Delete CollabPress Tasks: ', 'collabpress'); ?></label>
            <input type="submit" name="cp-debug-delete-tasks" value="<?php _e('Submit', 'collabpress') ?>" />
            <br /><br />
            <label for="cp-debug-delete-all"><?php _e('Delete CollabPress Meta Data: ', 'collabpress'); ?></label>
            <input type="submit" name="cp-debug-delete-meta-data" value="<?php _e('Submit', 'collabpress') ?>" />
        </form>

        <br />
        <br />

        <h2><?php _e('All Projects', 'collabpress') ?></h2>

        <?php

            $projects_args = array(
                                'post_type' => 'cp-projects',
                                );
            $projects_query = new WP_Query( $projects_args );

            cp_debug_display_loop($projects_query);

        ?>

        <br />
        <br />

        <h2><?php _e('All Task Lists', 'collabpress') ?></h2>

        <?php

            $task_lists_args = array(
                                'post_type' => 'cp-task-lists',
                                );
            $task_lists_query = new WP_Query( $task_lists_args );

            cp_debug_display_loop($task_lists_query);

        ?>

        <br />
        <br />

        <h2>All Tasks</h2>

        <?php

            $tasks_args = array(
                                'post_type' => 'cp-tasks',
                                );
            $tasks_query = new WP_Query( $tasks_args );

            cp_debug_display_loop($tasks_query);

        ?>

        <br />
        <br />

        <h2><?php _e('All Meta', 'collabpress') ?></h2>

        <?php

            $meta_data_args = array(
                                'post_type' => 'cp-meta-data',
                                );
            $meta_data_query = new WP_Query( $meta_data_args );

            cp_debug_display_loop($meta_data_query);

        ?>

    </div>

    <?php

    
}
?>