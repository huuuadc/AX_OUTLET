<?php
namespace Joomunited\WPSOL;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class DatabaseCleanup
 */
class WpsolDatabaseCleanup
{
    /**
     * Use query to clean system
     *
     * @param string $type Type of clean
     *
     * @return string
     */
    public static function cleanSystem($type)
    {
        check_admin_referer('wpsol_speed_optimization', '_wpsol_nonce');

        self::cleanupDb($type);
        $message = 'Database cleanup successful';

        return $message;
    }

    /**
     * Exclude clean element
     *
     * @param string $type Type of database
     *
     * @return void
     */
    public static function cleanupDb($type)
    {
        global $wpdb;

        /**
         * Clean database by type
         *
         * @param string Type of database object cleaned (revisions, drafted, trash, comments, trackbacks, transient)
         */
        do_action('wpsol_clean_database', $type);

        switch ($type) {
            case 'revisions':
                $revisions = $wpdb->query(
                    $wpdb->prepare(
                        'DELETE FROM '.$wpdb->posts.' WHERE post_type = %s',
                        'revision'
                    )
                );
                break;
            case 'drafted':
                $autodraft = $wpdb->query($wpdb->prepare(
                    'DELETE FROM '.$wpdb->posts.' WHERE post_status = %s',
                    'auto-draft'
                ));
                break;
            case 'trash':
                $posttrash = $wpdb->query($wpdb->prepare(
                    'DELETE FROM '.$wpdb->posts.' WHERE post_status = %s',
                    'trash'
                ));
                break;
            case 'comments':
                $comments = $wpdb->query($wpdb->prepare(
                    'DELETE FROM '.$wpdb->comments.' WHERE comment_approved = %s OR comment_approved = %s',
                    'spam',
                    'trash'
                ));
                break;
            case 'trackbacks':
                $comments = $wpdb->query($wpdb->prepare(
                    'DELETE FROM '.$wpdb->comments.' WHERE comment_type = %s OR comment_type = %s',
                    'trackback',
                    'pingback'
                ));
                break;
            case 'transient':
                $comments = $wpdb->query($wpdb->prepare(
                    'DELETE FROM '.$wpdb->options.' WHERE option_name LIKE %s',
                    '%\_transient\_%'
                ));
                break;
        }

        /**
         * Action display optimize and clean duplicate table settings.
         *
         * @param string Type of database
         *
         * @internal
         */
        do_action('wpsol_addon_optimize_and_clean_duplicate_table', $type);
    }


    /**
     * Load database element by ajax
     *
     * @return void
     */
    public static function ajaxLoadDatabaseElement()
    {
        check_ajax_referer('wpsolSpeedOptimizationSystem', 'ajaxnonce');

        global $wpdb;

        $lists = array(
            'revisions' => 0,
            'drafted' => 0,
            'trash' => 0,
            'comments' => 0,
            'trackbacks' => 0,
            'transient' => 0,
            'dup_postmeta' => 0,
            'dup_commentmeta' => 0,
            'dup_usermeta' => 0,
            'dup_termmeta' => 0,
            'optimize_table' => 0
        );

        foreach ($lists as $type => $value) {
            switch ($type) {
                case 'revisions':
                    $lists[$type] = $wpdb->query($wpdb->prepare(
                        'SELECT * FROM '.$wpdb->posts.' WHERE post_type = %s',
                        'revision'
                    ));
                    break;
                case 'drafted':
                    $lists[$type] = $wpdb->query($wpdb->prepare(
                        'SELECT * FROM '.$wpdb->posts.' WHERE post_status = %s',
                        'auto-draft'
                    ));
                    break;
                case 'trash':
                    $lists[$type] = $wpdb->query($wpdb->prepare(
                        'SELECT * FROM '.$wpdb->posts.' WHERE post_status = %s',
                        'trash'
                    ));
                    break;
                case 'comments':
                    $lists[$type] = $wpdb->query($wpdb->prepare(
                        'SELECT * FROM '.$wpdb->comments.' WHERE comment_approved = %s OR comment_approved = %s',
                        'spam',
                        'trash'
                    ));
                    break;
                case 'trackbacks':
                    $lists[$type] = $wpdb->query($wpdb->prepare(
                        'SELECT * FROM '.$wpdb->comments.' WHERE comment_type = %s OR comment_type = %s',
                        'trackback',
                        'pingback'
                    ));
                    break;
                case 'transient':
                    $lists[$type] = $wpdb->query($wpdb->prepare(
                        'SELECT * FROM '.$wpdb->options.' WHERE option_name LIKE %s',
                        '%\_transient\_%'
                    ));
                    break;
            }
        }

        /**
         * Filter count number of database to display
         *
         * @param integer Number return
         * @param string Type of database
         *
         * @internal
         *
         * @return string|integer
         */
        $lists = apply_filters('wpsol_addon_count_number_db', $lists);

        echo json_encode(array('list_elements' => $lists));
        die();
    }
}
