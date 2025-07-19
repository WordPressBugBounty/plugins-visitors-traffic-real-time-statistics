<?php
// Add columns to posts and pages tables - Free Version
function ahcfree_add_hits_column($columns)
{
    $columns['hits'] = 'Hits';
    unset($columns['views'], $columns['pageviews']); // Remove old ones
    return $columns;
}
add_filter('manage_posts_columns', 'ahcfree_add_hits_column');
add_filter('manage_pages_columns', 'ahcfree_add_hits_column');

// Make columns sortable - Free Version
function ahcfree_sortable_hits_column($columns)
{
    $columns['hits'] = 'hits';
    return $columns;
}
add_filter('manage_edit-post_sortable_columns', 'ahcfree_sortable_hits_column');
add_filter('manage_edit-page_sortable_columns', 'ahcfree_sortable_hits_column');

// Handle the sorting - Free Version
function ahcfree_sort_hits_column($query)
{
    if (!is_admin()) return;

    $orderby = $query->get('orderby');
    if ($orderby === 'hits') {
        $query->set('meta_key', '_ahcfree_total_views'); // sort by visitors
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'ahcfree_sort_hits_column');

// Populate the columns with data - Free Version
function ahcfree_populate_hits_column($column, $post_id)
{
    if ($column !== 'hits') return;

    global $wpdb;

    // Fetch hits from ahc_hits table
    $results = $wpdb->get_row($wpdb->prepare(
        "SELECT 
            COUNT(*) as total_hits,
            COUNT(DISTINCT hit_ip_address) as unique_visitors
         FROM ahc_hits 
         WHERE hit_page_id = %d",
        $post_id
    ));

    $total_hits = $results ? intval($results->total_hits) : 0;
    $unique_visitors = $results ? intval($results->unique_visitors) : 0;

    // Store in post meta for sorting
    update_post_meta($post_id, '_ahcfree_total_views', $total_hits);
    update_post_meta($post_id, '_ahcfree_unique_visitors', $unique_visitors);

    $page_title = htmlspecialchars(get_the_title($post_id), ENT_QUOTES, 'UTF-8');

    echo '<div class="ahc-stats-cell">';
    echo '<a href="#" class="ahc-stats-number" data-post-id="' . $post_id . '" data-page-title="' . $page_title . '">';
    echo '<span class="dashicons ahc-icon"></span> ';
    echo '<span class="stat-number">' . number_format($total_hits) . ' hits</span>';
    echo '<span class="stat-visitors">' . number_format($unique_visitors) . ' visitors</span>';
    echo '</a>';
    echo '</div>';

    // Add styles and modal (only once) - Free Version with Simplified Modal
    static $modal_added = false;
    if (!$modal_added) {
        echo '<style>
            .column-hits { width: 120px !important; text-align: center; }
            .ahc-icon:before {
                content: "\\f185";
                color: #1DAE22;
                font-family: dashicons;
                position: relative;
                top: 3px;
            }
            .ahc-stats-number {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-decoration: none;
                color: #2271b1;
                gap: 2px;
                padding: 4px;
                border-radius: 3px;
                transition: background-color 0.2s;
            }
            
            .stat-number { font-size: 13px; font-weight: 500; }
            .stat-visitors { font-size: 11px; color: #666; }
            .ahc-modal {
                display: none;
                position: fixed;
                z-index: 999999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.4);
            }
            .ahc-modal-content {
                background-color: #fff;
                margin: 5% auto;
                padding: 20px;
                border-radius: 8px;
                width: 80%;
                max-width: 700px;
                max-height: 80vh;
                overflow: hidden;
            }
            .ahc-modal-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 20px;
                border-bottom: 2px solid #f0f0f0;
                padding-bottom: 10px;
            }
            .ahc-modal-title-section {
                flex: 1;
            }
            .ahc-modal-title-section h2 {
                margin: 0 0 3px 0;
                font-size: 18px;
                font-weight: 600;
                color: #2c3e50;
            }
            .ahc-modal-subtitle {
                font-size: 12px;
                color: #7f8c8d;
                font-style: italic;
            }
            .ahc-modal-close {
                cursor: pointer;
                font-size: 24px;
                padding: 6px;
                color: #7f8c8d;
                border-radius: 50%;
                width: 35px;
                height: 35px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
                background: #f8f9fa;
                border: 1px solid #e9ecef;
            }
           
            .ahc-stats-summary {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 25px;
                padding: 15px;
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 8px;
                border: 1px solid #dee2e6;
            }
            .ahc-stat-item {
                text-align: center;
            }
            .ahc-stat-number {
                font-size: 24px;
                font-weight: bold;
                color: #1DAE22;
                display: block;
            }
            .ahc-stat-label {
                font-size: 11px;
                color: #666;
                text-transform: uppercase;
                font-weight: 500;
            }
            .ahc-chart-promo-container {
                margin-top: 15px;
                display: flex;
                justify-content: center;
            }
            .ahc-chart-promo {
                position: relative;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                transition: transform 0.3s ease;
                width: 60%;
                max-width: 400px;
            }
          
            .ahc-chart-promo img {
                width: 100%;
                height: auto;
                display: block;
            }
        </style>';

        echo '<div id="ahcHitsModal" class="ahc-modal">
            <div class="ahc-modal-content">
                <div class="ahc-modal-header">
                    <div class="ahc-modal-title-section">
                        <h2 id="ahcModalTitle">Page Statistics</h2>
                        <div class="ahc-modal-subtitle">Detailed Analytics for Your Content</div>
                    </div>
                    <span class="ahc-modal-close">&times;</span>
                </div>
                
                <!-- Stats Summary -->
                <div id="ahcStatsSummary" class="ahc-stats-summary">
                    <div class="ahc-stat-item">
                        <span class="ahc-stat-number">1,234</span>
                        <span class="ahc-stat-label">Total Hits</span>
                    </div>
                    <div class="ahc-stat-item">
                        <span class="ahc-stat-number">567</span>
                        <span class="ahc-stat-label">Unique Visitors</span>
                    </div>
                </div>
                
                <!-- Promotional Image Only -->
                <div class="ahc-chart-promo-container">
                    <div class="ahc-chart-promo">
                        <a target="_blank" href="https://www.wp-buy.com/product/visitors-traffic-real-time-statistics-pro/?attribute_license=Single%20License%2029">
                            <img src="' . plugins_url('images/Traffic_by_Title_PRO.jpg', AHCFREE_PLUGIN_MAIN_FILE) . '" alt="Traffic by Title PRO" />
                        </a>
                    </div>
                </div>
            </div>
        </div>';

        echo '<script>
        jQuery(document).ready(function($) {
            var modal = $("#ahcHitsModal");

            // Handle click on stats numbers
            $(document).on("click", ".ahc-stats-number", function(e) {
                e.preventDefault();
                var postId = $(this).data("post-id");
                var pageTitle = $(this).data("page-title");
                
                // Clean the page title
                if (pageTitle.includes("http")) {
                    pageTitle = pageTitle.split("http")[0].trim();
                }
                pageTitle = pageTitle.replace(/[\/\s]*$/, "");
                
                if (!pageTitle || pageTitle.length < 2) {
                    pageTitle = "Page Statistics";
                }
                
                $("#ahcModalTitle").text("Statistics: " + pageTitle);
                
                // Get basic stats and show modal
                fetchBasicStats(postId);
                modal.show();
            });

            // Close modal events
            $(".ahc-modal-close").on("click", function() {
                modal.hide();
            });

            $(window).on("click", function(e) {
                if (e.target === modal[0]) {
                    modal.hide();
                }
            });

            // Fetch basic stats for the header
            function fetchBasicStats(postId) {
                $.ajax({
                    url: ajaxurl,
                    method: "POST",
                    data: {
                        action: "ahcfree_get_basic_stats",
                        post_id: postId,
                        nonce: "' . wp_create_nonce('ahcfree_stats_nonce') . '"
                    },
                    success: function(response) {
                        if (response.success) {
                            updateStatsSummary(response.data);
                        }
                    },
                    error: function() {
                        // Keep default stats if AJAX fails
                    }
                });
            }
            
            function updateStatsSummary(data) {
                var html = `
                    <div class="ahc-stat-item">
                        <span class="ahc-stat-number">${data.total_hits.toLocaleString()}</span>
                        <span class="ahc-stat-label">Total Hits</span>
                    </div>
                    <div class="ahc-stat-item">
                        <span class="ahc-stat-number">${data.unique_visitors.toLocaleString()}</span>
                        <span class="ahc-stat-label">Unique Visitors</span>
                    </div>
                `;
                $("#ahcStatsSummary").html(html);
            }
        });
        </script>';

        $modal_added = true;
    }
}

add_action('manage_posts_custom_column', 'ahcfree_populate_hits_column', 10, 2);
add_action('manage_pages_custom_column', 'ahcfree_populate_hits_column', 10, 2);

// Add CSS for column widths - Free Version
function ahcfree_admin_head()
{
    global $pagenow;
    if ($pagenow == 'edit.php') {
        echo '<style>
            .column-hits {
                width: 120px !important;
                text-align: center !important;
            }
            .fixed .column-hits {
                vertical-align: middle;
            }
            .wp-list-table thead th.column-hits {
                text-align: center !important;
            }
            .wp-list-table thead th.column-hits a {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
            }
        </style>';
    }
}
add_action('admin_head', 'ahcfree_admin_head');

// AJAX handler for getting basic stats - Free Version
add_action('wp_ajax_ahcfree_get_basic_stats', 'ahcfree_get_basic_stats');
function ahcfree_get_basic_stats()
{
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'ahcfree_stats_nonce')) {
        wp_send_json_error('Invalid nonce');
    }

    global $wpdb;

    $post_id = intval($_POST['post_id']);

    // Get basic stats from ahc_hits table
    $results = $wpdb->get_row($wpdb->prepare(
        "SELECT 
            COUNT(*) as total_hits,
            COUNT(DISTINCT hit_ip_address) as unique_visitors
         FROM ahc_hits
         WHERE hit_page_id = %d",
        $post_id
    ));

    $response = [
        'total_hits' => $results ? intval($results->total_hits) : 0,
        'unique_visitors' => $results ? intval($results->unique_visitors) : 0
    ];

    wp_send_json_success($response);
}
