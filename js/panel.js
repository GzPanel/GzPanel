/**
 * Created by Samer on 2015-07-09.
 */

/**
 * Submit settings change via POST to a PHP script.
 * Handled via PHP to keep API info (API Key) private.
 * @param dataName
 * @param dataValue
 */
function submitSettingsChange(dataName, dataValue, functionOnSuccess) {
    $.ajax({
        type: "POST",
        url: 'updateSetting.php',
        contentType: 'application/x-www-form-urlencoded',
        data: {name: dataName, value: dataValue},
        success: function (data) {
            if (functionOnSuccess !== undefined)
                functionOnSuccess(data);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            alert("500 - Failed to update - Contact Support.");
        }
    });
}

$(document).ready(function () {

    /**
     * Create popovers with HTML content.
     * Title - Popover header
     * Content - Popover body
     */
    $("[data-toggle=popover]").popover({
        html: true,
        content: function () {
            var content = $(this).attr("data-popover-content");
            return $(content).children(".popover-body").html();
        },
        title: function () {
            var title = $(this).attr("data-popover-content");
            return $(title).children(".popover-heading").html();
        }
    });
});