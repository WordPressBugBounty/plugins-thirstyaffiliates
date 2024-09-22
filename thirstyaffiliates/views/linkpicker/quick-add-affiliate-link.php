<html>

<head>
<?php
    do_action('admin_print_styles');
	do_action('admin_print_scripts');
	do_action('admin_head');
?>
</head>

<body>

    <div id="quick_add_affiliate_link" data-htmleditor="<?php echo esc_attr( $html_editor ); ?>">

        <form method="post" onsubmit="dummySubmitFunc(event)">

            <div class="field-row">
                <label for="ta_link_name">
                    <?php esc_html_e( 'Link Name:' , 'thirstyaffiliates' ); ?>
                </label>
                <input type="text" class="form-control" id="ta_link_name" name="ta_link_name" value="<?php echo esc_attr( $selection ); ?>" required>
            </div>

            <div class="field-row">
                <label for="ta_destination_url">
                    <?php esc_html_e( 'Destination URL:' , 'thirstyaffiliates' ); ?>
                </label>
                <span class="guide">
                    <?php esc_html_e( 'http:// or https:// is required' , 'thirstyaffiliates' ); ?>
                </span>
                <input type="url" class="form-control" id="ta_destination_url" name="ta_destination_url" value="" required>
            </div>

            <div class="field-row link-option">
                <label for="ta_redirect_type">
                    <?php esc_html_e( 'Redirect Type:' , 'thirstyaffiliates' ); ?>
                </label>
                <select id="ta_redirect_type" name="ta_redirect_type">
                    <option value="global">
                        <?php echo esc_html( sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $default_redirect_type ) ); ?>
                    </option>
                    <?php foreach ( $redirect_types as $redirect_type => $redirect_label ) : ?>
                        <option value="<?php echo esc_attr( $redirect_type ); ?>">
                            <?php echo esc_html( $redirect_label ); ?>
                        </option>
                    <?php endforeach; ?>
                <select>
            </div>

            <div class="field-row link-option">
                <label for="ta_no_follow">
                    <?php esc_html_e( 'No follow this link?' , 'thirstyaffiliates' ); ?>
                </label>
                <select id="ta_no_follow" name="ta_no_follow">
                    <option value="global"><?php echo esc_html( sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $global_no_follow ) ); ?></option>
                    <option value="yes"><?php esc_html_e( 'Yes' , 'thirstyaffiliates' ); ?></option>
                    <option value="no"><?php esc_html_e( 'No' , 'thirstyaffiliates' ); ?></option>
                <select>
            </div>

            <div class="field-row link-option">
                <label for="ta_new_window">
                    <?php esc_html_e( 'Open this link in new window?' , 'thirstyaffiliates' ); ?>
                </label>
                <select id="ta_new_window" name="ta_new_window">
                    <option value="global"><?php echo esc_html( sprintf( __( 'Global (%s)' , 'thirstyaffiliates' ) , $global_new_window ) ); ?></option>
                    <option value="yes"><?php esc_html_e( 'Yes' , 'thirstyaffiliates' ); ?></option>
                    <option value="no"><?php esc_html_e( 'No' , 'thirstyaffiliates' ); ?></option>
                <select>
            </div>

            <div class="field-row link-categories">
                <label for="ta_link_categories">
                    <?php esc_html_e( 'Link Categories' , 'thirstyaffiliates' ); ?>
                </label>
                <select multiple id="link_categories" name="ta_link_categories[]" data-placeholder="<?php esc_attr_e( 'Select categories...' , 'thirstyaffiliates' ); ?>">
                    <?php foreach ( $categories as $category ) : ?>
                        <option value="<?php echo esc_attr(  $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php do_action( 'ta_quick_add_affiliate_link_form' ); ?>

            <div class="field-row submit-row">
                <span class="ta_spinner" style="background-image: url('<?php echo esc_url( $this->_constants->IMAGES_ROOT_URL() . 'spinner.gif' ); ?>');"></span>
                <button class="button" type="submit" name="add_link">
                    <?php esc_html_e( 'Add Link' , 'thirstyaffiliates' ); ?>
                </button>
                <button class="button button-primary" type="submit" name="add_link_and_insert">
                    <?php esc_html_e( 'Add Link & Insert Into Post' , 'thirstyaffiliates' ); ?>
                </button>
            </div>

            <input type="hidden" name="action" value="ta_process_quick_add_affiliate_link">
            <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>">

            <?php wp_nonce_field( 'ta_process_quick_add_affiliate_link' ); ?>
        </form>
    </div>

<script>
jQuery( document ).ready( function( $ ) {
    $( 'select#link_categories' ).selectize({
        plugins   : [ 'remove_button' , 'drag_drop' ]
    });
});

function dummySubmitFunc( event ) { event.preventDefault(); }
</script>
</body>

</html>
