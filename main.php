<?php
/**
 * Plugin Name: WordPress Business Report
 * Description: ระบบออกรายงานประจำเดือน สรุปยอดขาย Performace ของ Developer
 * Version: 1.0
 * Author: Jirakit Pawnsakunrungrot
 * Author URI: https://www.linkedin.com/in/sunny-jirakit
 * Plugin URI: https://github.com/sunny420x/wordpress-business-reports
 */

//Deny access from URL.
if (!defined('ABSPATH'))
    exit;

// Function to add the menu page
function business_report_menu()
{
    add_menu_page(
        'ระบบออกรายงานประสิทธิภาพ', // Page title
        'ระบบออกรายงานประสิทธิภาพ', // Menu title
        'manage_options', // Capability required
        'business_reports', // Menu slug
        'business_reports_settings', // Callback function to display page content
        'dashicons-star-filled', // Icon URL or Dashicon class
        80 // Position in the menu (optional)
    );
}

add_action('admin_menu', function () {
    add_submenu_page(
        null,
        'ออกรายงานประสิทธิภาพ',
        'ออกรายงานประสิทธิภาพ',
        'manage_options',
        'business_reports_print',
        'business_reports_page'
    );
});

//Add Menu to Wordpress Admin
add_action('admin_menu', 'business_report_menu');

function business_reports_settings()
{
    ?>
    <div class="wrapper" style="background: #fff; padding: 20px; border-radius: 10px; margin-top: 20px;">
        <h1>ระบบออกรายงานประสิทธิภาพประจำเดือน</h1>
        <p>ระบบช่วยออกรายงาน สรุปยอดขาย ประสิทธิภาพของเว็บไซต์</p>
        <form action="options.php" method="POST">
            <?php
            settings_fields('business_reports_settings_group');
            ?>
            <label for="developer_name">URL โลโก้:</label>
            <input type="text" name="company_logo" value="<?= esc_html(get_option('company_logo', '')) ?>"
                style="width: 500px;">
            <br>
            <br>
            <label for="developer_name">ชื่อนักพัฒนา / ผู้ดูแลเว็บไซต์ ปัจจุบัน:</label>
            <input type="text" name="developer_name" value="<?= esc_html(get_option('developer_name', '')) ?>"
                style="width: 500px;">
            <br>
            <br>
            <label for="website_domain_name">โดเมนเนม (Domain Name):</label>
            <input type="text" name="website_domain_name" value="<?= esc_html(get_option('website_domain_name', '')) ?>"
                style="width: 500px;">
            <br>
            <br>
            <h2>Desktop Insights</h2>

            <label for="">Performace:</label>
            <input type="number" name="desktop_performace" value="<?= esc_html(get_option('desktop_performace', '')) ?>">

            <label for="">Accessiblity:</label>
            <input type="number" name="desktop_accessibility"
                value="<?= esc_html(get_option('desktop_accessibility', '')) ?>">

            <label for="">Best Practices:</label>
            <input type="number" name="desktop_best_practices"
                value="<?= esc_html(get_option('desktop_best_practices', '')) ?>">

            <label for="">SEO:</label>
            <input type="number" name="desktop_seo" value="<?= esc_html(get_option('desktop_seo', '')) ?>">
            <br>
            <br>
            <h2>Mobile Insights</h2>

            <label for="">Performace:</label>
            <input type="number" name="mobile_performace" value="<?= esc_html(get_option('mobile_performace', '')) ?>">

            <label for="">Accessiblity:</label>
            <input type="number" name="mobile_accessibility" value="<?= esc_html(get_option('mobile_accessibility', '')) ?>">

            <label for="">Best Practices:</label>
            <input type="number" name="mobile_best_practices"
                value="<?= esc_html(get_option('mobile_best_practices', '')) ?>">

            <label for="">SEO:</label>
            <input type="number" name="mobile_seo" value="<?= esc_html(get_option('mobile_seo', '')) ?>">
            <br>
            <br>
            <input type="submit" class="button button-primary" value="บันทึกการเปลี่ยนแปลง">
        </form>
        <br>
        <h2>ยอดขายเดือนนี้ (Net Sales)</h2>

        <h3><?= number_format(get_current_month_sales(), 2) ?> บาท</h3>

        <a href="admin.php?page=business_reports_print" class="button button-primary">ออกรายงาน</a>
        <?php
        global $wpdb;
        $history = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}business_reports_snapshots ORDER BY report_month DESC LIMIT 12");
        ?>
        <br>
        <br>
        <hr>
        <h2>ประวัติรายงานย้อนหลัง (Snapshot)</h2>
        <table class="widefat fixed striped" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>เดือนที่บันทึก</th>
                    <th>ยอดขายสุทธิ</th>
                    <th>คะแนนเฉลี่ย (D/M)</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $row):
                    $d = json_decode($row->desktop_scores);
                    $m = json_decode($row->mobile_scores);
                    ?>
                    <tr>
                        <td><strong>
                                <?= esc_html($row->report_month); ?>
                            </strong></td>
                        <td>
                            <?= number_format($row->sales_amount, 2); ?> บาท
                        </td>
                        <td>D:
                            <?= $d->perf; ?> / M:
                            <?= $m->perf; ?>
                        </td>
                        <td>
                            <a href="admin.php?page=business_reports_print&snapshot_id=<?= $row->id; ?>"
                                class="button">ดูรายงานฉบับเต็ม</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

add_action('admin_init', 'business_reports_settings_init');

function business_reports_settings_init()
{
    register_setting('business_reports_settings_group', 'company_logo');

    register_setting('business_reports_settings_group', 'developer_name');
    register_setting('business_reports_settings_group', 'website_domain_name');
    register_setting('business_reports_settings_group', 'affiliate_logo');

    register_setting('business_reports_settings_group', 'desktop_performace');
    register_setting('business_reports_settings_group', 'desktop_accessibility');
    register_setting('business_reports_settings_group', 'desktop_best_practices');
    register_setting('business_reports_settings_group', 'desktop_seo');

    register_setting('business_reports_settings_group', 'mobile_performace');
    register_setting('business_reports_settings_group', 'mobile_accessibility');
    register_setting('business_reports_settings_group', 'mobile_best_practices');
    register_setting('business_reports_settings_group', 'mobile_seo');
}
function business_reports_page()
{
    ?>
    <div class="wrapper" style="background: #fff; padding: 10px 30px 30px 30px; margin: 20px;">
        <div style="display: flex;">
            <img src="<?= esc_html(get_option('company_logo')) ?>" width="150" alt="company_logo" style="padding: 20px;">
            <div style="padding-left: 20px;">
                <h1>รายงานประสิทธิภาพของเว็บไซต์</h1>
                <h2><?= esc_html(get_option('website_domain_name')) ?></h2>
                <h2 style="font-size: 20px;">ยอดขายเดือนนี้: <span
                        style="color: blue;"><?= number_format(get_current_month_sales()) ?></span> บาท</h2>
                <p style="font-size: 16px;">วันที่ออกรายงาน: <?= date('d/m/Y') ?> โดย
                    <?= esc_html(get_option('developer_name')) ?></p>
            </div>
        </div>
        <div>
            <h2>คะแนน Performance บน Desktop</h2>

            <h3
                style="color: <?php if (get_option('desktop_performace') >= 80) {
                    echo "green";
                } else {
                    echo "orange";
                } ?>;">
                <?= esc_html(get_option('desktop_performace', 0)) ?>/100 | Performace</h3>
            <p>วัดตาม ความเร็วของเว็บไซต์ เวลาในการตอบสนอง First Contentful Paint, Largest Contentful Paint, Total Blocking
                Time, Cumulative Layout Shift และ Speed Index</p>

            <h3
                style="color: <?php if (get_option('desktop_accessibility') >= 80) {
                    echo "green";
                } else {
                    echo "orange";
                } ?>;">
                <?= esc_html(get_option('desktop_accessibility', 0)) ?>/100 | Accessiblity</h3>
            <p>ความยากง่ายในการใช้งาน การจัดวาง โอกาสในการเข้าถึงแอปพลิเคชันบนเว็บ</p>

            <h3
                style="color: <?php if (get_option('desktop_best_practices') >= 80) {
                    echo "green";
                } else {
                    echo "orange";
                } ?>;">
                <?= esc_html(get_option('desktop_best_practices', 0)) ?>/100 | Best Practices</h3>
            <p>ความปลอดภัยของเว็บไซต์ การป้องกัน XSS XFO CSP มีการใช้มาตรการ HSTS</p>

            <h3 style="color: <?php if (get_option('desktop_seo') >= 80) {
                echo "green";
            } else {
                echo "orange";
            } ?>;">
                <?= esc_html(get_option('desktop_seo', 0)) ?>/100 | SEO</h3>
            <p>หน้าเว็บปฏิบัติตามคำแนะนำพื้นฐานเกี่ยวกับการเพิ่มประสิทธิภาพการค้นหา (SEO)</p>

            <h2>คะแนน Performance บน Mobile / อุปกรณ์เคลื่อนที่</h2>

            <h3
                style="color: <?php if (get_option('mobile_performace') >= 80) {
                    echo "green";
                } else {
                    echo "orange";
                } ?>;">
                <?= esc_html(get_option('mobile_performace', 0)) ?>/100 | Performace</h3>
            <p>วัดตาม ความเร็วของเว็บไซต์ เวลาในการตอบสนอง First Contentful Paint, Largest Contentful Paint, Total Blocking
                Time, Cumulative Layout Shift และ Speed Index</p>

            <h3
                style="color: <?php if (get_option('mobile_accessibility') >= 80) {
                    echo "green";
                } else {
                    echo "orange";
                } ?>;">
                <?= esc_html(get_option('mobile_accessibility', 0)) ?>/100 | Accessiblity</h3>
            <p>ความยากง่ายในการใช้งาน การจัดวาง โอกาสในการเข้าถึงแอปพลิเคชันบนเว็บ</p>

            <h3
                style="color: <?php if (get_option('mobile_best_practices') >= 80) {
                    echo "green";
                } else {
                    echo "orange";
                } ?>;">
                <?= esc_html(get_option('mobile_best_practices', 0)) ?>/100 | Best Practices</h3>
            <p>ความปลอดภัยของเว็บไซต์ การป้องกัน XSS XFO CSP มีการใช้มาตรการ HSTS</p>

            <h3 style="color: <?php if (get_option('mobile_seo') >= 80) {
                echo "green";
            } else {
                echo "orange";
            } ?>;">
                <?= esc_html(get_option('mobile_seo', 0)) ?>/100 | SEO</h3>
            <p>หน้าเว็บปฏิบัติตามคำแนะนำพื้นฐานเกี่ยวกับการเพิ่มประสิทธิภาพการค้นหา (SEO)</p>
        </div>
        <style>
            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>
        <button class="button no-print" onclick="window.print()">พิมพ์รายงาน</button>
    </div>
    <?php
}

function get_current_month_sales()
{
    global $wpdb;

    // 1. ดึงเวลาปัจจุบันตาม Timezone ของเว็บ (เช่น 2026-04-28 13:45:00)
    $now = current_time('mysql');

    // 2. หาจุดเริ่มต้นของเดือนนี้ (เช่น 2026-04-01 00:00:00)
    $start_of_month = date('Y-m-01 00:00:00', strtotime($now));

    $table_name = $wpdb->prefix . 'wc_order_stats';

    // 3. Query โดยระบุสถานะให้ชัดเจน
    $sales = $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(total_sales) 
         FROM $table_name 
         WHERE date_created >= %s 
         AND date_created <= %s
         AND status IN ('wc-processing', 'wc-completed')",
        $start_of_month,
        $now
    ));

    return $sales ? $sales : 0;
}

add_filter('admin_title', function ($admin_title, $title) {
    if (isset($_GET['page']) && $_GET['page'] === 'business_reports_print') {

        $current_date = wp_date('d/m/Y');

        return "รายงานประสิทธิภาพเว็บไซต์ " . esc_html(get_option('website_domain_name')) . " วันที่ {$current_date}";
    }

    return $admin_title;
}, 999, 2);

register_activation_hook(__FILE__, function () {
    global $wpdb;
    $table_name = $wpdb->prefix . 'business_reports_snapshots';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        report_month char(7) NOT NULL,
        sales_amount decimal(15,2) DEFAULT 0,
        desktop_scores text,
        mobile_scores text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        UNIQUE KEY report_month (report_month)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});

function maybe_save_monthly_snapshot()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'business_reports_snapshots';
    $current_month = wp_date('Y-m'); // ดึงเดือนปัจจุบันตาม Timezone เว็บ

    // เช็คว่าเดือนนี้มี Snapshot หรือยัง
    $exists = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table_name WHERE report_month = %s", $current_month));

    if (!$exists) {
        $sales = get_current_month_sales();

        $desktop = json_encode([
            'perf' => get_option('desktop_performace', 0),
            'acc' => get_option('desktop_accessibility', 0),
            'bp' => get_option('desktop_best_practices', 0),
            'seo' => get_option('desktop_seo', 0),
        ]);

        $mobile = json_encode([
            'perf' => get_option('mobile_performace', 0),
            'acc' => get_option('mobile_accessibility', 0),
            'bp' => get_option('mobile_best_practices', 0),
            'seo' => get_option('mobile_seo', 0),
        ]);

        $wpdb->insert($table_name, [
            'report_month' => $current_month,
            'sales_amount' => $sales,
            'desktop_scores' => $desktop,
            'mobile_scores' => $mobile
        ]);
    }
}

add_action('admin_init', function() {
    if (isset($_GET['page']) && $_GET['page'] === 'business_reports_print') {
        maybe_save_monthly_snapshot();
    }
});