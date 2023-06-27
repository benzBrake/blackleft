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
                setValue(cpu, obj.cpu_usage);
                setValue(mem, obj.mem_usage);
                setValue(disk, disk.disk_usage);
            });
        }

        function setValue(el, percentage) {
            let val = parseInt(percentage, 10);
            el.find('.usage').text(val + "%");
            el.find('.percentage').css('width', val + "%");
        }
    }
});

function fetchData(url, callback, timeOut) {
    timeOut || (timeOut = 2000);
    setInterval(function () {
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (typeof callback === 'function') {
                    callback(response);
                }
            }
        });
    }, timeOut);
}