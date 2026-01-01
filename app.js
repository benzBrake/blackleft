$(document).ready(function () {
    // 下拉菜单
    $('.sub-toggle').click(function (e) {
        e.stopPropagation();
        var subMenu = $(this).next('.sub-menu');
        if (subMenu.hasClass('active')) {
            subMenu.removeClass('active');
            $(document).off('click', clickOutside);
        } else {
            subMenu.addClass('active');
            $(document).on('click', clickOutside);
        }

        function clickOutside(event) {
            if (!subMenu.is(event.target) && subMenu.has(event.target).length === 0) {
                subMenu.removeClass('active');
                $(document).off('click', clickOutside);
            }
        }
    });

    var statusWidget = $('#status-widget');
    if (statusWidget.length) {
        var cpu = $('#cpu-usage');
        var mem = $('#ram-usage');
        var disk = $('#disk-usage');

        if (statusWidget.attr('url')) {
            fetchData(statusWidget.attr('url'), function (obj) {
                setPercentage(cpu, obj.cpu_usage, true);
                setPercentage(mem, obj.mem_usage);
                setUsage(mem, obj.mem_total, obj.mem_used);
                setPercentage(disk, obj.disk_usage);
                setUsage(disk, obj.disk_total, obj.disk_used);
            });
        }

        function setPercentage(el, percentage, setUsage) {
            var val = parseFloat(percentage).toFixed(2);
            if (setUsage) el.find(".usage").text(val + "%");
            el.find('.percentage').css('width', val + "%");
        }

        function setUsage(el, total, used) {
            if (typeof total === "undefined" || typeof used === "undefined") return;
            el.find(".usage").text(used + "/" + total);
        }

    }

    // 如果是 IE，则替换小写字母为大写字母
    if (navigator.userAgent.indexOf("MSIE") > -1 || navigator.userAgent.indexOf("Trident") > -1) {
        $('#topper_logo h1, #topper_nav li a, #sidebar h3').each(function () {
            $(this).html($(this).html().toUpperCase());
        });
    }
});

function fetchData(url, callback, timeOut) {
    timeOut || (timeOut = 2000);
    var sending = false;
    setInterval(function () {
        if (!sending) {
            sending = true;
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    if (typeof callback === 'function') {
                        callback(response);
                        sending = false;
                    }
                }
            });
        }
    }, timeOut);
}