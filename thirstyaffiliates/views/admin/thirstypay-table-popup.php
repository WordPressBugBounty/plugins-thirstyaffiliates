<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}
/** @var \ThirstyAffiliates\Models\Affiliate_Links_CPT $this */
?>
<div class="wrap ta-blur-wrap">
    <div class="ta-blur">
        <h1 class="wp-heading-inline">ThirstyPay™ Links</h1>

        <a href="post-new.php?post_type=thirstylink&amp;thirstypay=1" class="page-title-action">Add New ThirstyPay™ Link</a>
        <hr class="wp-header-end">

        <h2 class="screen-reader-text">Filter pages list</h2><ul class="subsubsub">
            <li class="all"><a href="edit.php?post_type=thirstylink&amp;thirstypay=1" class="current" aria-current="page">All <span class="count">(10)</span></a></li>
        </ul>

        <form id="posts-filter" method="get" class="thirstypay">
            <p class="search-box">
                <label class="screen-reader-text" for="post-search-input">Search ThirstyPay™ Links:</label>
                <input type="search" id="post-search-input" name="s" value="">
                <input type="submit" id="search-submit" class="button" value="Search ThirstyPay™ Links">
            </p>

            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top">
                        <option value="-1">Bulk actions</option>
                        <option value="edit" class="hide-if-no-js">Edit</option>
                        <option value="trash">Move to Trash</option>
                    </select>
                    <input type="submit" id="doaction" class="button action" value="Apply">
                </div>
                <div class="alignleft actions">
                    <label for="filter-by-date" class="screen-reader-text">Filter by date</label>
                    <select name="m" id="filter-by-date">
                        <option selected="selected" value="0">All dates</option>
                        <option value="202310">October 2023</option>
                    </select>
                    <select name="thirstylink-category" id="thirstylink-category" class="postform">
                        <option value="0">Show Link Categories</option>
                        <option class="level-0" value="5">Uncategorized</option>
                    </select>
                    <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter">
                </div>
                <div class="tablenav-pages one-page"><span class="displaying-num">10 items</span>
                    <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text"> of <span class="total-pages">1</span></span></span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span></div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">Pages list</h2>
            <table class="wp-list-table widefat fixed striped table-view-list pages">
                <thead>
                    <tr>
                        <td id="cb" class="manage-column column-cb check-column">
                            <label class="label-covers-full-cell" for="cb-select-all-1">
                                <span class="screen-reader-text">Select All</span>
                            </label>
                            <input id="cb-select-all-1" type="checkbox">
                        </td>
                        <th scope="col" id="title" class="manage-column column-title column-primary sortable desc" abbr="Title">
                            <a href="#"><span>Title</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span> <span class="screen-reader-text">Sort ascending.</span></a>
                        </th>
                        <th scope="col" id="link_id" class="manage-column column-link_id sortable desc">
                            <a href="#"><span>Link ID</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span> <span class="screen-reader-text">Sort ascending.</span></a>
                        </th>
                        <th scope="col" id="cloaked_url" class="manage-column column-cloaked_url sortable desc">
                            <a href="#"><span>Cloaked URL</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span> <span class="screen-reader-text">Sort ascending.</span></a>
                        </th>
                        <th scope="col" id="taxonomy-thirstylink-category" class="manage-column column-taxonomy-thirstylink-category">Link Categories</th>
                        <th scope="col" id="stats_summary" class="manage-column column-stats_summary">Stats Summary</th>
                        <th scope="col" id="date" class="manage-column column-date sorted desc" aria-sort="descending" abbr="Date">
                            <a href="#"><span>Date</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span></a>
                        </th>
                    </tr>
                </thead>

                <tbody id="the-list">
                    <tr id="post-545" class="iedit author-self level-0 post-545 type-thirstylink status-publish hentry">
                        <th scope="row" class="check-column">
                            <input id="cb-select-545" type="checkbox" name="post[]" value="545">
                        </th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a class="row-title" href="#">Link One</a></strong>
                        </td>
                        <td class="link_id column-link_id" data-colname="Link ID"><span>545</span></td>
                        <td class="cloaked_url column-cloaked_url" data-colname="Cloaked URL">
                            <div class="ta-display-input-wrap">
                                <input style="width:100%;" type="text" value="https://example.com/recommends/link-one" readonly="">
                            </div>
                        </td>
                        <td class="taxonomy-thirstylink-category column-taxonomy-thirstylink-category" data-colname="Link Categories">
                            <a href="#">Products</a>
                        </td>
                        <td class="stats_summary column-stats_summary" data-colname="Stats Summary">
                            <div class="stats-summary-wrap">
                                <span class="total">Total clicks: <strong>100</strong></span>
                                <span class="week">Last 7 days: <strong>3</strong></span>
                                <span class="month">Last 30 days: <strong>10</strong></span>
                            </div>
                        </td>
                        <td class="date column-date" data-colname="Date">Published<br>2023/09/21 at 2:40 pm</td>
                    </tr>
                    <tr id="post-546" class="iedit author-self level-0 post-546 type-thirstylink status-publish hentry">
                        <th scope="row" class="check-column">
                            <input id="cb-select-546" type="checkbox" name="post[]" value="546">
                        </th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a class="row-title" href="#">Link Two</a></strong>
                        </td>
                        <td class="link_id column-link_id" data-colname="Link ID"><span>546</span></td>
                        <td class="cloaked_url column-cloaked_url" data-colname="Cloaked URL">
                            <div class="ta-display-input-wrap">
                                <input style="width:100%;" type="text" value="https://example.com/recommends/link-two" readonly="">
                            </div>
                        </td>
                        <td class="taxonomy-thirstylink-category column-taxonomy-thirstylink-category" data-colname="Link Categories">
                            <a href="#">Products</a>
                        </td>
                        <td class="stats_summary column-stats_summary" data-colname="Stats Summary">
                            <div class="stats-summary-wrap">
                                <span class="total">Total clicks: <strong>100</strong></span>
                                <span class="week">Last 7 days: <strong>3</strong></span>
                                <span class="month">Last 30 days: <strong>10</strong></span>
                            </div>
                        </td>
                        <td class="date column-date" data-colname="Date">Published<br>2023/09/21 at 2:40 pm</td>
                    </tr>
                    <tr id="post-547" class="iedit author-self level-0 post-547 type-thirstylink status-publish hentry">
                        <th scope="row" class="check-column">
                            <input id="cb-select-547" type="checkbox" name="post[]" value="547">
                        </th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a class="row-title" href="#">Link Three</a></strong>
                        </td>
                        <td class="link_id column-link_id" data-colname="Link ID"><span>547</span></td>
                        <td class="cloaked_url column-cloaked_url" data-colname="Cloaked URL">
                            <div class="ta-display-input-wrap">
                                <input style="width:100%;" type="text" value="https://example.com/recommends/link-three" readonly="">
                            </div>
                        </td>
                        <td class="taxonomy-thirstylink-category column-taxonomy-thirstylink-category" data-colname="Link Categories">
                            <a href="#">Products</a>
                        </td>
                        <td class="stats_summary column-stats_summary" data-colname="Stats Summary">
                            <div class="stats-summary-wrap">
                                <span class="total">Total clicks: <strong>100</strong></span>
                                <span class="week">Last 7 days: <strong>3</strong></span>
                                <span class="month">Last 30 days: <strong>10</strong></span>
                            </div>
                        </td>
                        <td class="date column-date" data-colname="Date">Published<br>2023/09/26 at 1:29 pm</td>
                    </tr>
                    <tr id="post-548" class="iedit author-self level-0 post-548 type-thirstylink status-publish hentry">
                        <th scope="row" class="check-column">
                            <input id="cb-select-548" type="checkbox" name="post[]" value="548">
                        </th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a class="row-title" href="#">Link Four</a></strong>
                        </td>
                        <td class="link_id column-link_id" data-colname="Link ID"><span>548</span></td>
                        <td class="cloaked_url column-cloaked_url" data-colname="Cloaked URL">
                            <div class="ta-display-input-wrap">
                                <input style="width:100%;" type="text" value="https://example.com/recommends/link-four" readonly="">
                            </div>
                        </td>
                        <td class="taxonomy-thirstylink-category column-taxonomy-thirstylink-category" data-colname="Link Categories">
                            <a href="#">Products</a>
                        </td>
                        <td class="stats_summary column-stats_summary" data-colname="Stats Summary">
                            <div class="stats-summary-wrap">
                                <span class="total">Total clicks: <strong>100</strong></span>
                                <span class="week">Last 7 days: <strong>3</strong></span>
                                <span class="month">Last 30 days: <strong>10</strong></span>
                            </div>
                        </td>
                        <td class="date column-date" data-colname="Date">Published<br>2023/09/27 at 12:20 pm</td>
                    </tr>
                    <tr id="post-549" class="iedit author-self level-0 post-549 type-thirstylink status-publish hentry">
                        <th scope="row" class="check-column">
                            <input id="cb-select-549" type="checkbox" name="post[]" value="549">
                        </th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a class="row-title" href="#">Link Five</a></strong>
                        </td>
                        <td class="link_id column-link_id" data-colname="Link ID"><span>549</span></td>
                        <td class="cloaked_url column-cloaked_url" data-colname="Cloaked URL">
                            <div class="ta-display-input-wrap">
                                <input style="width:100%;" type="text" value="https://example.com/recommends/link-five" readonly="">
                            </div>
                        </td>
                        <td class="taxonomy-thirstylink-category column-taxonomy-thirstylink-category" data-colname="Link Categories">
                            <a href="#">Products</a>
                        </td>
                        <td class="stats_summary column-stats_summary" data-colname="Stats Summary">
                            <div class="stats-summary-wrap">
                                <span class="total">Total clicks: <strong>100</strong></span>
                                <span class="week">Last 7 days: <strong>3</strong></span>
                                <span class="month">Last 30 days: <strong>10</strong></span>
                            </div>
                        </td>
                        <td class="date column-date" data-colname="Date">Published<br>2023/04/27 at 8:45 am</td>
                    </tr>
                    <tr id="post-550" class="iedit author-self level-0 post-550 type-thirstylink status-publish hentry">
                        <th scope="row" class="check-column">
                            <input id="cb-select-550" type="checkbox" name="post[]" value="550">
                        </th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a class="row-title" href="#">Link Six</a></strong>
                        </td>
                        <td class="link_id column-link_id" data-colname="Link ID"><span>550</span></td>
                        <td class="cloaked_url column-cloaked_url" data-colname="Cloaked URL">
                            <div class="ta-display-input-wrap">
                                <input style="width:100%;" type="text" value="https://example.com/recommends/link-six" readonly="">
                            </div>
                        </td>
                        <td class="taxonomy-thirstylink-category column-taxonomy-thirstylink-category" data-colname="Link Categories">
                            <a href="#">Products</a>
                        </td>
                        <td class="stats_summary column-stats_summary" data-colname="Stats Summary">
                            <div class="stats-summary-wrap">
                                <span class="total">Total clicks: <strong>100</strong></span>
                                <span class="week">Last 7 days: <strong>3</strong></span>
                                <span class="month">Last 30 days: <strong>10</strong></span>
                            </div>
                        </td>
                        <td class="date column-date" data-colname="Date">Published<br>2023/04/27 at 8:45 am</td>
                    </tr>
                    <tr id="post-551" class="iedit author-self level-0 post-551 type-thirstylink status-publish hentry">
                        <th scope="row" class="check-column">
                            <input id="cb-select-551" type="checkbox" name="post[]" value="551">
                        </th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a class="row-title" href="#">Link Seven</a></strong>
                        </td>
                        <td class="link_id column-link_id" data-colname="Link ID"><span>551</span></td>
                        <td class="cloaked_url column-cloaked_url" data-colname="Cloaked URL">
                            <div class="ta-display-input-wrap">
                                <input style="width:100%;" type="text" value="https://example.com/recommends/link-seven" readonly="">
                            </div>
                        </td>
                        <td class="taxonomy-thirstylink-category column-taxonomy-thirstylink-category" data-colname="Link Categories">
                            <a href="#">Products</a>
                        </td>
                        <td class="stats_summary column-stats_summary" data-colname="Stats Summary">
                            <div class="stats-summary-wrap">
                                <span class="total">Total clicks: <strong>100</strong></span>
                                <span class="week">Last 7 days: <strong>3</strong></span>
                                <span class="month">Last 30 days: <strong>10</strong></span>
                            </div>
                        </td>
                        <td class="date column-date" data-colname="Date">Published<br>2023/09/26 at 2:24 pm</td>
                    </tr>
                    <tr id="post-552" class="iedit author-self level-0 post-552 type-thirstylink status-publish hentry">
                        <th scope="row" class="check-column">
                            <input id="cb-select-552" type="checkbox" name="post[]" value="552">
                        </th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a class="row-title" href="#">Link Eight</a></strong>
                        </td>
                        <td class="link_id column-link_id" data-colname="Link ID"><span>552</span></td>
                        <td class="cloaked_url column-cloaked_url" data-colname="Cloaked URL">
                            <div class="ta-display-input-wrap">
                                <input style="width:100%;" type="text" value="https://example.com/recommends/link-eight" readonly="">
                            </div>
                        </td>
                        <td class="taxonomy-thirstylink-category column-taxonomy-thirstylink-category" data-colname="Link Categories">
                            <a href="#">Products</a>
                        </td>
                        <td class="stats_summary column-stats_summary" data-colname="Stats Summary">
                            <div class="stats-summary-wrap">
                                <span class="total">Total clicks: <strong>100</strong></span>
                                <span class="week">Last 7 days: <strong>3</strong></span>
                                <span class="month">Last 30 days: <strong>10</strong></span>
                            </div>
                        </td>
                        <td class="date column-date" data-colname="Date">Published<br>2023/09/26 at 2:24 pm</td>
                    </tr>
                    <tr id="post-553" class="iedit author-self level-0 post-553 type-thirstylink status-publish hentry">
                        <th scope="row" class="check-column">
                            <input id="cb-select-553" type="checkbox" name="post[]" value="553">
                        </th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a class="row-title" href="#">Link Nine</a></strong>
                        </td>
                        <td class="link_id column-link_id" data-colname="Link ID"><span>553</span></td>
                        <td class="cloaked_url column-cloaked_url" data-colname="Cloaked URL">
                            <div class="ta-display-input-wrap">
                                <input style="width:100%;" type="text" value="https://example.com/recommends/link-nine" readonly="">
                            </div>
                        </td>
                        <td class="taxonomy-thirstylink-category column-taxonomy-thirstylink-category" data-colname="Link Categories">
                            <a href="#">Products</a>
                        </td>
                        <td class="stats_summary column-stats_summary" data-colname="Stats Summary">
                            <div class="stats-summary-wrap">
                                <span class="total">Total clicks: <strong>100</strong></span>
                                <span class="week">Last 7 days: <strong>3</strong></span>
                                <span class="month">Last 30 days: <strong>10</strong></span>
                            </div>
                        </td>
                        <td class="date column-date" data-colname="Date">Published<br>2023/10/03 at 11:55 am</td>
                    </tr>
                    <tr id="post-554" class="iedit author-self level-0 post-554 type-thirstylink status-publish hentry">
                        <th scope="row" class="check-column">
                            <input id="cb-select-554" type="checkbox" name="post[]" value="554">
                        </th>
                        <td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
                            <strong><a class="row-title" href="#">Link Ten</a></strong>
                        </td>
                        <td class="link_id column-link_id" data-colname="Link ID"><span>554</span></td>
                        <td class="cloaked_url column-cloaked_url" data-colname="Cloaked URL">
                            <div class="ta-display-input-wrap">
                                <input style="width:100%;" type="text" value="https://example.com/recommends/link-ten" readonly="">
                            </div>
                        </td>
                        <td class="taxonomy-thirstylink-category column-taxonomy-thirstylink-category" data-colname="Link Categories">
                            <a href="#">Products</a>
                        </td>
                        <td class="stats_summary column-stats_summary" data-colname="Stats Summary">
                            <div class="stats-summary-wrap">
                                <span class="total">Total clicks: <strong>100</strong></span>
                                <span class="week">Last 7 days: <strong>3</strong></span>
                                <span class="month">Last 30 days: <strong>10</strong></span>
                            </div>
                        </td>
                        <td class="date column-date" data-colname="Date">Published<br>2023/10/03 at 2:00 pm</td>
                    </tr>
                </tbody>

                <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input id="cb-select-all-2" type="checkbox">
                        </td>
                        <th scope="col" class="manage-column column-title column-primary sortable desc" abbr="Title">
                            <a href="#">
                                <span>Title</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span> <span class="screen-reader-text">Sort ascending.</span>
                            </a>
                        </th>
                        <th scope="col" class="manage-column column-link_id sortable desc">
                            <a href="#">
                                <span>Link ID</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span> <span class="screen-reader-text">Sort ascending.</span>
                            </a>
                        </th>
                        <th scope="col" class="manage-column column-cloaked_url sortable desc">
                            <a href="#"><span>Cloaked URL</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span> <span class="screen-reader-text">Sort ascending.</span>
                            </a>
                        </th>
                        <th scope="col" class="manage-column column-taxonomy-thirstylink-category">Link Categories</th>
                        <th scope="col" class="manage-column column-stats_summary">Stats Summary</th>
                        <th scope="col" class="manage-column column-date sorted desc" aria-sort="descending" abbr="Date">
                            <a href="#">
                                <span>Date</span><span class="sorting-indicators"><span class="sorting-indicator asc" aria-hidden="true"></span><span class="sorting-indicator desc" aria-hidden="true"></span></span>
                            </a>
                        </th>
                    </tr>
                </tfoot>

            </table>
            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label><select name="action2" id="bulk-action-selector-bottom">
                        <option value="-1">Bulk actions</option>
                        <option value="edit" class="hide-if-no-js">Edit</option>
                        <option value="trash">Move to Trash</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action" value="Apply">
                </div>
                <div class="alignleft actions">
                </div>
                <div class="tablenav-pages one-page"><span class="displaying-num">10 items</span>
                    <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
<span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">1 of <span class="total-pages">1</span></span></span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span></div>
                <br class="clear">
            </div>
    </div>
</div>

<div class="ta-blur-popup">
    <div class="ta-blur-popup-wrap">
        <div class="ta-blur-popup-content">
            <div class="ta-blur-popup-logo">
                <img src="<?php echo $this->_constants->IMAGES_ROOT_URL() . 'payments/thirstypay-logo.svg'; ?>" alt="">
            </div>
            <h2>Elevate Your Earnings with Stripe!</h2>
            <p><em>Why wait around for your money?</em> Connect your site to Stripe and get paid the second customers click on your ThirstyPay™ links. Make your cash flow as smooth as your customer journey.</p>
            <h4>Boost Your Bucks with Stripe's Epic Extras</h4>
            <ul class="features">
                <li>Seamless &amp; Secure Payment Processing</li>
                <li>Global Currency Support</li>
                <li>Multi-Channel Sales</li>
                <li>Immediate Access to Funds</li>
                <li>Enhanced Tracking & Analytics</li>
                <li>No Coding Required</li>
            </ul>
            <p>Integrate with Stripe today and watch your revenue soar, all while giving your customers an exceptional shopping experience.</p>
        </div>
        <div class="ta-blur-popup-cta">
            <a href="<?php echo esc_url($stripe_connect_url); ?>" id="ta_cta_upgrade_link" class="ta-cta-button">Connect to Stripe</a>
        </div>
    </div>
</div>
