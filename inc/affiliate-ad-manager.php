<?php
/**
 * Affiliate Ad Manager System
 * アフィリエイト広告管理システム
 * 
 * Features:
 * - WordPress管理画面での広告管理
 * - 複数の広告位置対応（サイドバー、コンテンツ内など）
 * - クリック統計・表示統計
 * - A/Bテスト機能
 * - スケジュール配信
 * 
 * @package Joseikin_Insight
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class JI_Affiliate_Ad_Manager {
    
    private $table_name_ads;
    private $table_name_stats;
    
    public function __construct() {
        global $wpdb;
        $this->table_name_ads = $wpdb->prefix . 'ji_affiliate_ads';
        $this->table_name_stats = $wpdb->prefix . 'ji_affiliate_stats';
        
        // フック登録
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_ji_save_ad', array($this, 'ajax_save_ad'));
        add_action('wp_ajax_ji_delete_ad', array($this, 'ajax_delete_ad'));
        add_action('wp_ajax_ji_get_ad_stats', array($this, 'ajax_get_ad_stats'));
        add_action('wp_ajax_ji_track_ad_impression', array($this, 'ajax_track_impression'));
        add_action('wp_ajax_nopriv_ji_track_ad_impression', array($this, 'ajax_track_impression'));
        add_action('wp_ajax_ji_track_ad_click', array($this, 'ajax_track_click'));
        add_action('wp_ajax_nopriv_ji_track_ad_click', array($this, 'ajax_track_click'));
    }
    
    /**
     * 初期化
     */
    public function init() {
        // テーブル作成
        $this->create_tables();
    }
    
    /**
     * データベーステーブル作成
     */
    private function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        // 広告テーブル
        $sql_ads = "CREATE TABLE IF NOT EXISTS {$this->table_name_ads} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            ad_type varchar(50) NOT NULL DEFAULT 'html',
            content longtext NOT NULL,
            link_url varchar(500) DEFAULT '',
            positions text NOT NULL,
            target_pages text DEFAULT NULL,
            device_target varchar(20) NOT NULL DEFAULT 'all',
            status varchar(20) NOT NULL DEFAULT 'active',
            priority int(11) NOT NULL DEFAULT 0,
            start_date datetime DEFAULT NULL,
            end_date datetime DEFAULT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY priority (priority),
            KEY device_target (device_target)
        ) $charset_collate;";
        
        // 統計テーブル
        $sql_stats = "CREATE TABLE IF NOT EXISTS {$this->table_name_stats} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            ad_id bigint(20) NOT NULL,
            date date NOT NULL,
            impressions int(11) NOT NULL DEFAULT 0,
            clicks int(11) NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            UNIQUE KEY ad_date (ad_id, date),
            KEY ad_id (ad_id),
            KEY date (date)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_ads);
        dbDelta($sql_stats);
        
        // デバイスターゲット列を追加（既存テーブル用）
        $column_exists = $wpdb->get_results(
            "SHOW COLUMNS FROM {$this->table_name_ads} LIKE 'device_target'"
        );
        if (empty($column_exists)) {
            $wpdb->query(
                "ALTER TABLE {$this->table_name_ads} 
                ADD COLUMN device_target varchar(20) NOT NULL DEFAULT 'all' AFTER target_pages,
                ADD KEY device_target (device_target)"
            );
        }
        
        // positionカラムをpositionsに変更（複数位置対応）
        $position_column = $wpdb->get_results(
            "SHOW COLUMNS FROM {$this->table_name_ads} LIKE 'position'"
        );
        if (!empty($position_column)) {
            // 既存のpositionカラムをpositionsに変更
            $wpdb->query(
                "ALTER TABLE {$this->table_name_ads} 
                CHANGE COLUMN position positions text NOT NULL"
            );
        }
    }
    
    /**
     * 管理メニュー追加
     */
    public function add_admin_menu() {
        add_menu_page(
            'アフィリエイト広告管理',
            'アフィリエイト広告',
            'manage_options',
            'ji-affiliate-ads',
            array($this, 'admin_page'),
            'dashicons-megaphone',
            25
        );
        
        add_submenu_page(
            'ji-affiliate-ads',
            '広告一覧',
            '広告一覧',
            'manage_options',
            'ji-affiliate-ads',
            array($this, 'admin_page')
        );
        
        add_submenu_page(
            'ji-affiliate-ads',
            '統計情報',
            '統計情報',
            'manage_options',
            'ji-affiliate-stats',
            array($this, 'stats_page')
        );
        
        add_submenu_page(
            'ji-affiliate-ads',
            '設定',
            '設定',
            'manage_options',
            'ji-affiliate-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * 管理画面アセット読み込み
     */
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'ji-affiliate') === false) {
            return;
        }
        
        wp_enqueue_style('ji-admin-ads', get_template_directory_uri() . '/assets/css/admin-ads.css', array(), '1.0.0');
        wp_enqueue_script('ji-admin-ads', get_template_directory_uri() . '/assets/js/admin-ads.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('ji-admin-ads', 'jiAdminAds', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ji_ad_nonce'),
        ));
    }
    
    /**
     * 広告管理ページ
     */
    public function admin_page() {
        global $wpdb;
        
        $ads = $wpdb->get_results(
            "SELECT * FROM {$this->table_name_ads} ORDER BY priority DESC, id DESC"
        );
        
        include get_template_directory() . '/inc/admin-templates/affiliate-ads-list.php';
    }
    
    /**
     * 統計ページ
     */
    public function stats_page() {
        global $wpdb;
        
        // 過去30日間の統計を取得
        $stats = $wpdb->get_results($wpdb->prepare(
            "SELECT 
                a.id,
                a.title,
                a.position,
                SUM(s.impressions) as total_impressions,
                SUM(s.clicks) as total_clicks,
                CASE 
                    WHEN SUM(s.impressions) > 0 
                    THEN ROUND((SUM(s.clicks) / SUM(s.impressions)) * 100, 2)
                    ELSE 0
                END as ctr
            FROM {$this->table_name_ads} a
            LEFT JOIN {$this->table_name_stats} s ON a.id = s.ad_id
            WHERE s.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY a.id
            ORDER BY total_clicks DESC",
            ''
        ));
        
        include get_template_directory() . '/inc/admin-templates/affiliate-stats.php';
    }
    
    /**
     * 設定ページ
     */
    public function settings_page() {
        include get_template_directory() . '/inc/admin-templates/affiliate-settings.php';
    }
    
    /**
     * AJAX: 広告保存
     */
    public function ajax_save_ad() {
        check_ajax_referer('ji_ad_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('権限がありません');
        }
        
        global $wpdb;
        
        $ad_id = isset($_POST['ad_id']) ? intval($_POST['ad_id']) : 0;
        
        // 複数位置を配列として受け取り、カンマ区切りで保存
        $positions = isset($_POST['positions']) && is_array($_POST['positions']) 
            ? $_POST['positions'] 
            : (isset($_POST['position']) ? array($_POST['position']) : array());
        $positions_string = implode(',', array_map('sanitize_text_field', $positions));
        
        $data = array(
            'title' => sanitize_text_field($_POST['title']),
            'ad_type' => sanitize_text_field($_POST['ad_type']),
            'content' => wp_kses_post($_POST['content']),
            'link_url' => esc_url_raw($_POST['link_url']),
            'positions' => $positions_string,
            'target_pages' => sanitize_text_field($_POST['target_pages']),
            'device_target' => sanitize_text_field($_POST['device_target']),
            'status' => sanitize_text_field($_POST['status']),
            'priority' => intval($_POST['priority']),
            'start_date' => !empty($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : null,
            'end_date' => !empty($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : null,
        );
        
        if ($ad_id > 0) {
            // 更新
            $result = $wpdb->update($this->table_name_ads, $data, array('id' => $ad_id));
        } else {
            // 新規作成
            $result = $wpdb->insert($this->table_name_ads, $data);
            $ad_id = $wpdb->insert_id;
        }
        
        if ($result === false) {
            wp_send_json_error('保存に失敗しました');
        }
        
        wp_send_json_success(array(
            'message' => '保存しました',
            'ad_id' => $ad_id
        ));
    }
    
    /**
     * AJAX: 広告削除
     */
    public function ajax_delete_ad() {
        check_ajax_referer('ji_ad_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('権限がありません');
        }
        
        global $wpdb;
        
        $ad_id = intval($_POST['ad_id']);
        
        // 統計データも削除
        $wpdb->delete($this->table_name_stats, array('ad_id' => $ad_id));
        
        $result = $wpdb->delete($this->table_name_ads, array('id' => $ad_id));
        
        if ($result === false) {
            wp_send_json_error('削除に失敗しました');
        }
        
        wp_send_json_success('削除しました');
    }
    
    /**
     * AJAX: 広告統計取得
     */
    public function ajax_get_ad_stats() {
        check_ajax_referer('ji_ad_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('権限がありません');
        }
        
        global $wpdb;
        
        $ad_id = intval($_POST['ad_id']);
        $days = isset($_POST['days']) ? intval($_POST['days']) : 30;
        
        $stats = $wpdb->get_results($wpdb->prepare(
            "SELECT 
                date,
                impressions,
                clicks,
                CASE 
                    WHEN impressions > 0 
                    THEN ROUND((clicks / impressions) * 100, 2)
                    ELSE 0
                END as ctr
            FROM {$this->table_name_stats}
            WHERE ad_id = %d
            AND date >= DATE_SUB(CURDATE(), INTERVAL %d DAY)
            ORDER BY date ASC",
            $ad_id,
            $days
        ));
        
        wp_send_json_success($stats);
    }
    
    /**
     * AJAX: インプレッション記録
     */
    public function ajax_track_impression() {
        $ad_id = isset($_POST['ad_id']) ? intval($_POST['ad_id']) : 0;
        
        if ($ad_id <= 0) {
            wp_send_json_error('Invalid ad ID');
        }
        
        global $wpdb;
        
        $today = current_time('Y-m-d');
        
        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$this->table_name_stats} (ad_id, date, impressions, clicks)
            VALUES (%d, %s, 1, 0)
            ON DUPLICATE KEY UPDATE impressions = impressions + 1",
            $ad_id,
            $today
        ));
        
        wp_send_json_success();
    }
    
    /**
     * AJAX: クリック記録
     */
    public function ajax_track_click() {
        $ad_id = isset($_POST['ad_id']) ? intval($_POST['ad_id']) : 0;
        
        if ($ad_id <= 0) {
            wp_send_json_error('Invalid ad ID');
        }
        
        global $wpdb;
        
        $today = current_time('Y-m-d');
        
        $wpdb->query($wpdb->prepare(
            "INSERT INTO {$this->table_name_stats} (ad_id, date, impressions, clicks)
            VALUES (%d, %s, 0, 1)
            ON DUPLICATE KEY UPDATE clicks = clicks + 1",
            $ad_id,
            $today
        ));
        
        wp_send_json_success();
    }
    
    /**
     * デバイスタイプを検出
     * 
     * @return string 'mobile' または 'desktop'
     */
    private function detect_device() {
        if (wp_is_mobile()) {
            return 'mobile';
        }
        return 'desktop';
    }
    
    /**
     * 指定位置の広告を取得（複数位置対応）
     * 
     * @param string $position 広告位置
     * @param string $page_type ページタイプ（optional）
     * @return object|null 広告オブジェクト
     */
    public function get_ad_for_position($position, $page_type = '') {
        global $wpdb;
        
        $current_datetime = current_time('mysql');
        $device = $this->detect_device();
        
        // FIND_IN_SET を使用して、カンマ区切りのpositionsから検索
        $query = $wpdb->prepare(
            "SELECT * FROM {$this->table_name_ads}
            WHERE FIND_IN_SET(%s, REPLACE(positions, ' ', '')) > 0
            AND status = 'active'
            AND (device_target = 'all' OR device_target = %s)
            AND (start_date IS NULL OR start_date <= %s)
            AND (end_date IS NULL OR end_date >= %s)
            ORDER BY priority DESC, RAND()
            LIMIT 1",
            $position,
            $device,
            $current_datetime,
            $current_datetime
        );
        
        $ad = $wpdb->get_row($query);
        
        return $ad;
    }
    
    /**
     * 広告HTML出力
     * 
     * @param string $position 広告位置
     * @param string $page_type ページタイプ（optional）
     * @return string 広告HTML
     */
    public function render_ad($position, $page_type = '') {
        $ad = $this->get_ad_for_position($position, $page_type);
        
        if (!$ad) {
            return '';
        }
        
        ob_start();
        ?>
        <div class="ji-affiliate-ad" 
             data-ad-id="<?php echo esc_attr($ad->id); ?>"
             data-position="<?php echo esc_attr($position); ?>">
            
            <?php if ($ad->ad_type === 'html'): ?>
                <?php echo $ad->content; ?>
            <?php elseif ($ad->ad_type === 'image'): ?>
                <a href="<?php echo esc_url($ad->link_url); ?>" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="ji-ad-link"
                   data-ad-id="<?php echo esc_attr($ad->id); ?>">
                    <?php echo wp_kses_post($ad->content); ?>
                </a>
            <?php elseif ($ad->ad_type === 'script'): ?>
                <?php echo $ad->content; ?>
            <?php endif; ?>
            
        </div>
        
        <script>
        (function() {
            // インプレッション追跡
            if (typeof jQuery !== 'undefined') {
                jQuery(document).ready(function($) {
                    $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                        action: 'ji_track_ad_impression',
                        ad_id: <?php echo intval($ad->id); ?>
                    });
                });
            }
            
            // クリック追跡
            document.querySelectorAll('[data-ad-id="<?php echo intval($ad->id); ?>"] a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (typeof jQuery !== 'undefined') {
                        jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'ji_track_ad_click',
                            ad_id: <?php echo intval($ad->id); ?>
                        });
                    }
                });
            });
        })();
        </script>
        <?php
        return ob_get_clean();
    }
}

// インスタンス化
new JI_Affiliate_Ad_Manager();

/**
 * ヘルパー関数: 広告表示
 * 
 * @param string $position 広告位置
 * @param string $page_type ページタイプ（optional）
 */
function ji_display_ad($position, $page_type = '') {
    global $wpdb;
    $manager = new JI_Affiliate_Ad_Manager();
    echo $manager->render_ad($position, $page_type);
}
