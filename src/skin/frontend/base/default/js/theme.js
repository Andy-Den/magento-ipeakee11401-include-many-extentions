/**
 * Created with JetBrains PhpStorm.
 * Date: 6/7/13
 * Time: 8:05 PM
 * To change this template use File | Settings | File Templates.
 */
jQuery(document).ready(function(){

    jQuery('.data-table tr td:first-child,.data-table tr th:first-child').addClass("first");
    jQuery('.data-table tr td:last-child,.data-table tr th:last-child,table tr:last-child,table tbody tr:last-child').addClass("last");
    jQuery('.data-table td:empty,.data-table th:empty').text(".").addClass("empty");

    jQuery(".my-account").parent(".col-main").addClass("col-main-account").siblings(".col-left").addClass("account-sidebar");

})
jQuery(window).load(function(){

    jQuery('.data-table tr td:first-child,.data-table tr th:first-child').addClass("first");
    jQuery('.data-table tr td:last-child,.data-table tr th:last-child,table tr:last-child,table tbody tr:last-child').addClass("last");
    jQuery('.data-table td:empty,.data-table th:empty').text(".").addClass("empty");

})