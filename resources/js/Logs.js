import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox";
import "daterangepicker";

/**
 * Represents the Logs class.
 * @extends BaseClass
 */
export default class Logs extends BaseClass {
    /**
     * Constructor for the Logs class.
     * @param {Object} props - The properties for the class.
     *
     */
    constructor(props = null) {
        super(props);
        this.handleOnLoad();
        $(document).on("change", "#usersDropdown", this.handleFilter);
        $(document).on("keyup", "#searchByBookingId", this.handleFilter);
    }

    handleFilter = (startDate, endDate) => {
        try {
            let dateRange = $("#createdDate").val();

            if (startDate && endDate) {
                dateRange = startDate + " - " + endDate;
            }
            const params = {
                dateRange: dateRange,
                userId: $("#usersDropdown").val(),
                searchByBookingId: $("#searchByBookingId").val(),
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterLogs + "?" + queryParams;
            this.handleFilterRequest(url);
        } catch (error) {
            this.handleException(error);
        }
    };
    handleFilterRequest = (url) => {
        $("#loader").show();
        axios
            .get(url)
            .then((response) => {
                const statusCode = response.data.status.code;
                if (statusCode === 200) {
                    const timeLine = $("#timeLine");
                    timeLine.html(response.data.data.html);
                    $("#loader").hide();
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };
    handleOnLoad = () => {
        $("#createdDate").daterangepicker(
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
}
window.service = new Logs(props);
