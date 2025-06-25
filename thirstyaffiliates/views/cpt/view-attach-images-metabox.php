<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wp_nonce_field( 'thirsty_affiliates_cpt_nonce', '_thirstyaffiliates_nonce' ); ?>

<div class="metabox-actions">

    <?php if ( function_exists( 'wp_enqueue_media' ) && $legacy_uploader != 'yes' ) : ?>

        <div class="button-secondary" id="ta_upload_media_manager"
            data-editor="content"
            data-uploader-title="<?php esc_attr_e( 'Add Image To Affiliate Link' , 'thirstyaffiliates' ); ?>"
            data-uploader-button-text="<?php esc_attr_e('Add To Affiliate Link'); ?>" >
            <?php esc_html_e( 'Upload/Insert' , 'thirstyaffiliates' ); ?>
            <span class="wp-media-buttons-icon"></span>
        </div>

    <?php else : ?>

        <div class="button-secondary" id="ta_upload_insert_img">
            <?php esc_html_e( 'Upload/Insert' , 'thirstyaffiliates' ); ?>
            <a class="thickbox" href="<?php echo esc_attr( admin_url( 'media-upload.php?post_id=' . $post->ID . '?type=image&TB_iframe=1' ) ); ?>">
                <span class="wp-media-buttons-icon"></span>
            </a>
        </div>

    <?php endif; ?>

    <span class="or"><?php esc_html_e( 'or' , 'thirstyaffliates' ); ?></span>

    <div class="add-external-image">
        <button type="button" class="button" id="add-external-image"><?php esc_html_e( 'Add external image' , 'thirstyaffiliates' ); ?></button>
        <div class="external-image-form">
            <input type="url" value="" placeholder="<?php esc_html_e( 'Image source here' , 'thirstyaffiliates' ); ?>">
            <button type="button" class="button-primary add-external"><?php esc_html_e( 'Add image' , 'thirstyaffiliates' ); ?></button>
            <button type="button" class="button cancel"><?php esc_html_e( 'Cancel' , 'thirstyaffiliates' ); ?></button>
        </div>
    </div>

</div>

<script type="text/javascript">
    var wpActiveEditor = 'content';
</script>

<?php
    if ( ! empty( $attachments ) ) {
        ob_start();

        foreach ( $attachments as $key => $attachment_id ) {
            if ( filter_var( $attachment_id , FILTER_VALIDATE_URL ) ) {
                $img_url = esc_url_raw( $attachment_id );
                include( $this->_constants->VIEWS_ROOT_PATH() . 'cpt/view-attach-images-metabox-external-image.php' );
            } else {
                $img = wp_get_attachment_image_src( $attachment_id , 'full' );

                if (is_array($img) && !empty($img)) {
                    include( $this->_constants->VIEWS_ROOT_PATH() . 'cpt/view-attach-images-metabox-single-image.php' );
                } else {
                    unset( $attachments[$key] );
                }
            }
        }

        $images = ob_get_clean();

        if ( !empty( $attachments ) ) {
            printf(
                '<div id="thirsty_image_holder" data-attachments="%1$s">%2$s</div>',
                esc_attr( wp_json_encode( array_values( $attachments ) ) ),
                $images
            );
        }
    }
?>
