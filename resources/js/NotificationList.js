import BaseClass from "./BaseClass.js";
import "daterangepicker";

/**
 * Represents the NotificationList class.
 * @extends BaseClass
 */
export default class NotificationList extends BaseClass {
    /**
     * Constructor for the NotificationList class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        this.handleOnLoad();
        $(document).on(
            "click",
            "#sortMessage, #sortDate, #sortTime,#sortUser,#sortDescription",
            this.handleSorting
        );
    }

    handleOnLoad = () => {
        $("#notificationDateRange").daterangepicker(
            {
                startDate: moment(),
                endDate: moment(),
                parentEl: ".wrapper",
                ranges: {
                    Today: [moment(), moment()],
                    Yesterday: [
                        moment().subtract(1, "days"),
                        moment().subtract(1, "days"),
                    ],
                    "Last 7 Days": [moment().subtract(6, "days"), moment()],
                    "Last 30 Days": [moment().subtract(29, "days"), moment()],
                    "This Month": [
                        moment().startOf("month"),
                        moment().endOf("month"),
                    ],
                    "Last Month": [
                        moment().subtract(1, "month").startOf("month"),
                        moment().subtract(1, "month").endOf("month"),
                    ],
                    "Next 7 Days": [moment(), moment().add(6, "days")],
                    "Next 30 Days": [moment(), moment().add(29, "days")],
                    "Next Week": [
                        moment().add(1, "week").startOf("week"),
                        moment().add(1, "week").endOf("week"),
                    ],
                    "Next Month": [
                        moment().add(1, "month").startOf("month"),
                        moment().add(1, "month").endOf("month"),
                    ],
                    "This Week": [
                        moment().startOf("week"),
                        moment().endOf("week"),
                    ],
                    "Last Week": [
                        moment().subtract(1, "week").startOf("week"),
                        moment().subtract(1, "week").endOf("week"),
                    ],
                },
                locale: {
                    format: "DD/MM/YYYY", // Format of the date displayed in the input
                },
            },
            (start, end) => {
                this.handleFilter(
                    start.format("DD/MM/YYYY"),
                    end.format("DD/MM/YYYY")
                );
            }
        );
    };

    handleFilter = (startDate, endDate) => {
        try {
            let notificationDateRange = $("#notificationDateRange").val();

            if (startDate && endDate) {
                notificationDateRange = startDate + " - " + endDate;
            }

            const params = {
                notificationDateRange: notificationDateRange,
            };
            const queryParams = $.param(params);
            const url = window.filterNotification + "?" + queryParams;
            this.handleFilterRequest(url);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleSorting = ({ target }) => {
        try {
            const sortOrder = $("#sortOrder").val() === "asc" ? "desc" : "asc";
            let notificationDateRange = $("#notificationDateRange").val();
            const params = {
                notificationDateRange: notificationDateRange,
                sortField: $(target).attr("id"),
                sortDirection: sortOrder,
            };
            const queryParams = $.param(params);
            const url = window.filterNotification + "?" + queryParams;
            this.handleFilterRequest(url, sortOrder);
        } catch (error) {
            this.handleException(error);
        }
    };

    handleFilterRequest = (url, sortOrder = null) => {
        $("#loader").show();
        axios
            .get(url)
            .then((response) => {
                const statusCode = response.data.status.code;
                if (statusCode === 200) {
                    const tbody = $("#notificationTable tbody");
                    tbody.html(response.data.data.html);
                    if (sortOrder) {
                        $("#sortOrder").val(sortOrder);
                    }
                    $("#loader").hide();
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };
}
window.service = new NotificationList(props);
