jQuery(document).ready(function ($) {
    function fetchNotifications() {
        $.ajax({
            url: shopwice_ajax.ajaxurl,
            type: 'POST',
            data: { action: 'shopwice_get_recent_sales_and_orders' },
            success: function (response) {
                if (response.length > 0) {
                    response.forEach(notification => {
                        let notificationHTML = `<div class="shopwice-notification">
                            <img src="${notification.image}" alt="Product Image" />
                            <p>${notification.message}</p>
                        </div>`;
                        $("body").append(notificationHTML);
                        setTimeout(() => { $(".shopwice-notification").fadeOut(); }, 5000);
                    });
                }
            }
        });
    }

    setInterval(fetchNotifications, 15000); // Fetch notifications every 15 seconds
});
