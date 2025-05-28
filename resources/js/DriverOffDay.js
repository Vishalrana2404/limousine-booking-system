import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "./JqueryCheckBox";

/**
 * Represents the DriverOffDay class.
 * @extends BaseClass
 */
export default class DriverOffDay extends BaseClass {
    /**
     * Represents the WEEK value.
     * @type {string}
     */
    static WEEK = "WEEK";
    /**
     * Represents the WEEK value.
     * @type {string}
     */
    static MONTH = "MONTH";
    /**
     * Constructor for the Drivers class.
     * @param {Object} props - The properties for the class.
     */
    constructor(props = null) {
        super(props);
        this.selectedDates = [];
        this.currentDate = new Date();
        $(document).on("change", ".driverCheckBox", this.handleCheckedAction);
        this.initializeCalendarTable();
        $(document).on("change", "#timeRangeSelect", this.handleWeekMonth);
        $(document).on("click", ".weekButton, .monthButton", this.handleWeeks);
    }
    handleWeeks = ({ target }) => {
        switch ($(target).attr("id")) {
            case "nextWeek":
                this.currentDate.setDate(this.currentDate.getDate() + 7);
                this.initializeCalendarTable();
                break;
            case "pastWeek":
                this.currentDate.setDate(this.currentDate.getDate() - 7);
                this.initializeCalendarTable();
                break;
            case "pastMonth":
                this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                this.initializeCalendarTable();
                break;
            case "nextMonth":
                this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                this.initializeCalendarTable();
                break;
            default:
                break;
        }
    };
    initializeCalendarTable = () => {
        const timeRange = $("#timeRangeSelect").val();
        let dates;
        if (timeRange === "WEEK") {
            dates = this.generateDates(this.currentDate, DriverOffDay.WEEK);
        } else {
            dates = this.generateDates(this.currentDate, DriverOffDay.MONTH);
        }
        this.getDriverData().then((driverData) => {
            this.handleHtmlTable(dates, driverData);
        });
    };
    getDriverData = () => {
        const url = this.props.routes.getDriverOffDays;
        $("#loader").show();
        return axios
            .get(url)
            .then((response) => {
                const statusCode = response.data.status.code;
                if (statusCode === 200) {
                    $("#loader").hide();
                    return response.data.data;
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };

    generateDates = (baseDate, range) => {
        let dates = [];
        let date = new Date(baseDate);
        if (range === DriverOffDay.WEEK) {
            date.setDate(date.getDate() - date.getDay()); // set to the start of the week
            for (let i = 0; i < 7; i++) {
                dates.push(date.toISOString().split("T")[0]);
                date.setDate(date.getDate() + 1);
            }
        } else {
            date.setDate(1); // set to the start of the month
            const options = { day: "numeric", month: "short" };
            while (date.getMonth() === baseDate.getMonth()) {
                dates.push(date.toISOString().split("T")[0]);
                date.setDate(date.getDate() + 1);
            }
        }

        return dates;
    };
    handleHtmlTable = (dates, drivers) => {
        let tableRowHtml = "";
        let tableHeaderHtml = "<th>Drivers</th>";
        dates.forEach((date) => {
            tableHeaderHtml += `<th>${this.formatDate(date)}</th>`;
        });
        // Data for drivers
        // const drivers = this.props.driverData;
        // Generate table rows
        drivers.forEach((driver, index) => {
            const offDays = driver.driver_off_day.map(function (item) {
                return item.off_date;
            });
            tableRowHtml += `<tr data-driver-id="${driver.id}"><td>${driver.name}</td>`;
            dates.forEach((date, dateIndex) => {
                let isChecked = "";
                if ($.inArray(date, offDays) !== -1) {
                    isChecked = "checked";
                }
                let isDisabled = this.getDesableDate(date);
                let checkboxId = `checkbox_${dateIndex + 1}_tr_${index + 1}`;
                tableRowHtml += `
                    <td data-off-date="${date}">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input driverCheckBox" type="checkbox" id="${checkboxId}" ${isDisabled} ${isChecked}>
                            <label for="${checkboxId}" class="custom-control-label"></label>
                        </div>
                    </td>`;
            });
            tableRowHtml += `</tr>`;
        });
        $("#driverOffDayTable thead tr").html(tableHeaderHtml);
        $("#driverOffDayTable tbody").html(tableRowHtml);
    };
    getDesableDate = (date) => {
        let dateToCompare = new Date(date);
        let today = new Date();
        today.setHours(0, 0, 0, 0); // Set the time to the beginning of the day for an accurate comparison
        return dateToCompare < today ? "disabled" : "";
    };
    formatDate = (dateString) => {
        // Parse the input date string
        const date = new Date(dateString);
        // Define an array of month abbreviations
        const months = [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "May",
            "Jun",
            "Jul",
            "Aug",
            "Sep",
            "Oct",
            "Nov",
            "Dec",
        ];
        // Extract the day and month
        const day = ("0" + date.getDate()).slice(-2); // ensures two-digit format
        const month = months[date.getMonth()]; // getMonth() returns 0-11
        // Format the date as 'DD MMM'
        const formattedDate = day + " " + month;
        return formattedDate;
    };
    handleWeekMonth = ({ target }) => {
        if ($(target).val() == DriverOffDay.MONTH) {
            $(".weekButton").hide();
            $(".monthButton").show();
        } else {
            $(".weekButton").show();
            $(".monthButton").hide();
        }
        this.currentDate = new Date();
        this.initializeCalendarTable();
    };

    handleCheckedAction = ({ target }) => {
        try {
            const driverId = $(target).closest("tr").data("driver-id");
            const offDate = $(target).closest("td").data("off-date");
            const isChecked = $(target).is(":checked") ? true : false;
            if (driverId != "") {
                const formData = new FormData();
                formData.append("driver_id", driverId);
                formData.append("off_date", offDate);
                formData.append("checked", isChecked);
                const url = this.props.routes.saveOffDays;
                axios
                    .post(url, formData)
                    .then((response) => {
                        const statusCode = response.data.status.code;
                        const message = response.data.status.message;
                        const flash = new ErrorHandler(statusCode, message);
                        throw flash;
                    })
                    .catch((error) => {
                        $("#loader").hide();
                        this.handleException(error);
                    });
            } else {
                throw new ErrorHandler(
                    422,
                    this.languageMessage.select_driver.required
                );
            }
        } catch (error) {
            this.handleException(error);
        }
    };
}

window.service = new DriverOffDay(props);
