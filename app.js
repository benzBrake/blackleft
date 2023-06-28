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
            let val = parseFloat(percentage).toFixed(2);
            if (setUsage) el.find(".usage").text(val + "%");
            el.find('.percentage').css('width', val + "%");
        }
        function setUsage(el, total, used) {
            el.find(".usage").text(used + "/" + total);
        }

    }
});

function fetchData(url, callback, timeOut) {
    timeOut || (timeOut = 2000);
    let sending = false;
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