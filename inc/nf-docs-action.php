<?php if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'NF_Notification_Base_Type' ) )
    return FALSE;



/**
 * Class for our custom action type.
 *
 * @package     Ninja Forms
 * @subpackage  Classes/Actions
 * @copyright   Copyright (c) 2014, WPNINJAS
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.8
*/
// I know this says NF_Notification_Base_Type, but the name will eventually be changed to reflect the action nomenclature.
class NF_Action_Doc_Meta extends NF_Notification_Base_Type
{
    /**
     * @var name
     */
    public $name;
    /**
     * Get things rolling
     */
    function __construct() {
        $this->name = __( 'Save Post Meta' );
        add_filter( 'nf_notification_types', array( $this, 'register_action_type' ) );
    }
    public function register_action_type( $types )
    {
        $types[ $this->name ] = $this;
        return (array) $types;
    }
    /**
     * Output our edit screen
     *
     * @access public
     * @since 2.8
     * @return void
     */
    public function edit_screen( $id = '' )
    {
        /*
        This is how we get setting values to output them into our settings page.
        notification is an old naming convention. Eventually it will be changed to action.
        */
        $post_id = Ninja_Forms()->notification( $id )->get_setting( 'post_id' );
        $meta_key = Ninja_Forms()->notification( $id )->get_setting( 'meta_key' );
        $meta_value = Ninja_Forms()->notification( $id )->get_setting( 'meta_value' );
        /*
        By default, settings are output into a table. We need to wrap our settings in <tr> and <td> tags.
        This lets all of our settings within the action page to be similar.

        The most important thing to keep in mind is the naming convention for your settings: settings[setting_name]
        This will allow Ninja Forms to save the setting for you.
        */
        ?>
        <tr>
            <th scope="row"><label for="settings-post_id"><?php _e( 'Post ID' ); ?></label></th>
            <td><input type="text" name="settings[post_id]" id="settings-post_id" value="<?php echo esc_attr( $post_id ); ?>" class="regular-text"/></td>
        </tr>
        <tr>
            <th scope="row"><label for="settings-meta_key"><?php _e( 'Meta Key' ); ?></label></th>
            <td><input type="text" name="settings[meta_key]" id="settings-meta_key" value="<?php echo esc_attr( $meta_key ); ?>" class="regular-text"/></td>
        </tr>
        <tr>
            <th scope="row"><label for="settings-meta_value"><?php _e( 'Meta Value' ); ?></label></th>
            <td><input type="text" name="settings[meta_value]" id="settings-meta_value" value="<?php echo esc_attr( $meta_value ); ?>" class="regular-text"/></td>
        </tr>
        <?php
    }
    /**
     * Process our Redirect notification
     *
     * @access public
     * @since 2.8
     * @return void
     */
    public function process( $id ) {
        /*
        We declare our $ninja_forms_processing global so that we can access submitted values.
        */
        global $ninja_forms_processing;

        /*
        Get our setting
        */
        $post_id = do_shortcode( Ninja_Forms()->notification( $id )->get_setting( 'post_id' ) );
        $meta_key = Ninja_Forms()->notification( $id )->get_setting( 'meta_key' );
        $meta_value = do_shortcode( Ninja_Forms()->notification( $id )->get_setting( 'meta_value' ) );

        add_post_meta( $post_id, $meta_key, $meta_value, false );

    }
}
return new NF_Action_Doc_Meta();
