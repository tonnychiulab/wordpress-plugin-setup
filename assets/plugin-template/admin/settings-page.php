<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <div class="my-plugin-card">
        <h2 class="text-xl font-bold mb-4"><?php _e( 'Settings', '{{text-domain}}' ); ?></h2>

        <form method="post" action="options.php">
            <?php
            // Output security fields for the registered settings.
            settings_fields( '{{option-group}}' );

            // Output setting sections and their fields.
            do_settings_sections( '{{menu-slug}}' );

            // Output save button.
            submit_button( __( 'Save Settings', '{{text-domain}}' ) );
            ?>
        </form>
    </div>
</div>
