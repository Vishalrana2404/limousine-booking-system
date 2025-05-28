import ErrorHandler from "./Utility/ErrorHandler.js";

export default class Notification {
    /**
     * Represents the notification limit wich will display on bell icon.
     * @type {number}
     */
    static LIMIT = 10;

    constructor() {
        this.notificationContainer = $("#notificationsContainer");
        this.notifications =
            this.notificationContainer.attr("data-notifications");
        this.notifications =
            this.notifications != null ? JSON.parse(this.notifications) : [];
        this.baseUrl = window.location.origin;
        $(document).on(
            "click",
            ".unreadTopNotificationItem",
            this.handleTopNotification
        );
        $(document).on(
            "click",
            ".unreadNotificationItem",
            this.handleListNotification
        );

        this.render();
    }

    handleNotification = (notification) => {
        // Prepend the new notification to the notifications array
        this.notifications.unshift(notification);
        // Check if the array length exceeds the limit
        if (this.notifications.length > Notification.LIMIT) {
            // Remove the last notification from the end of the array
            this.notifications.splice(Notification.LIMIT - 1, 1);
        }
        this.render();
    };
    handleListNotification = ({ target }) => {
        const readAt = $(target).closest("tr").data("read-at");
        if (!readAt) {
            const dataType = $(target).closest("tr").data("type");
            const dataId = $(target).closest("tr").data("id");
            this.handleMarkAsRead(dataId, dataType);
        }
    };

    handleTopNotification = ({ target }) => {
        const readAt = $(target).data("read-at");
        if (!readAt) {
            const dataType = $(target).data("type");
            const dataId = $(target).data("id");
            this.handleMarkAsRead(dataId, dataType);
        }
    };

    handleMarkAsRead = (dataId, dataType) => {
        const url = window.markAsRead;
        const formData = new FormData();
        formData.append("dataType", dataType);
        formData.append("dataId", dataId);
        $("#loader").show();
        axios
            .post(url, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                if (statusCode === 200) {
                    this.notificationContainer.html(response.data.data.topHtml);
                    const tbody = $("#notificationTable tbody");
                    if (dataType === "mark-single-read") {
                        $("#" + dataId).replaceWith(
                            response.data.data.listHtml
                        );
                    }
                    $("#loader").hide();
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };

    render = () => {
        const notificationCount =
            this.notifications.length > 0
                ? this.notifications.length > 9
                    ? "9+"
                    : this.notifications.length
                : "";
        const notiCountElement =
            notificationCount !== ""
                ? ` <span class="notification notification_count">${notificationCount}</span>`
                : "";
        this.notificationContainer.html(`
 <a class="nav-link px-0" data-bs-toggle="dropdown" href="#" aria-expanded="false" title="Notification(s)">
                <span class="icon notification-icon">
                     ${notiCountElement}
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-xl py-2 notification-dropdown dropdown-menu-end text-xs medium radius-10">
                <span class="dropdown-item text-sm">
                    <i class="fas fa-bell mr-2"></i> Notifications
                     ${
                         this.notifications.length
                             ? `<a href="javascript:void(0);" class="float-right text-primary unreadTopNotificationItem" title="Mark all Read" data-type="mark-all-read" data-id="">Mark all Read</a>`
                             : ""
                     }
                </span>
                <div class="dropdown-divider m-0"></div>
                <div class="nav_unread_notifications" data-notifications="${
                    this.notifications
                }">
                   ${
                       this.notifications.length
                           ? this.notifications
                                 .map((notification) => {
                                     return `<a href="javascript:void(0);"  class="dropdown-item text-dark bold nav-bar-notification-item unreadTopNotificationItem  text-truncate" data-read-at="${notification.read_at}" data-id="${notification.id}" data-type="mark-single-read">
                         ${notification.data.message}  for booking ID #${notification?.data?.data?.booking?.id ? notification.data.data.booking.id :notification.data.booking.id}
                        </a>`;
                                 })
                                 .join("")
                           : `<p class="dropdown-item text-dark">No unread notifications found</p>`
                   }
                </div>
                
                <div class="dropdown-divider m-0"></div>
                <a class="dropdown-item text-center text-primary text-sm" href="${
                    this.baseUrl
                }/notifications" title="View All Notifications">View All Notifications</a>
            </div>`);
    };
}
