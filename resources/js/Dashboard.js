import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import "daterangepicker";
import $ from "jquery";
import "./JqueryCheckBox";
import axios from "axios";
import Chart from "chart.js/auto";


/**
 * Represents the Dashboard class.
 * @extends BaseClass
 */
export default class Dashboard extends BaseClass {
    /**
     * Constructor for the Dashboard class.
     * @param {Object} props - The properties for the class.
     */
    constructor(props = null) {
        super(props);
        this.chartData = this.props?.finalData || {};
        this.handleOnLoad();
        this.handleTypesOfBookingChart();
        this.handleCancellationBookingChart();
        this.handleLineChartForNoOfBookings();
        this.handleLineChartForNoOfCancellation();
    }

    handleTypesOfBookingChart = () => {
        const ctx = document.getElementById('typesOfBookingChart')?.getContext('2d');
        if (!ctx) return;
    
        const finalDataForCharts = this.chartData || {};

        if (this.typesOfBookingChart) {
            this.typesOfBookingChart.destroy();
        }
    
        this.typesOfBookingChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Arrival', 'Transfer', 'Departure', 'Disposal', 'Delivery'],
                datasets: [{
                    label: 'Total Bookings',
                    data: [
                        finalDataForCharts.totalArrivalBookings || 0,
                        finalDataForCharts.totalTransferBookings || 0,
                        finalDataForCharts.totalDepartureBookings || 0,
                        finalDataForCharts.totalDisposalBookings || 0,
                        finalDataForCharts.totalDeliveryBookings || 0
                    ],
                    backgroundColor: [
                        'rgb(235, 192, 60, 92%)',
                        'rgb(147, 196, 125)',
                        'rgb(233, 123, 123, 1)',
                        'rgb(84, 130, 186)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    };
    

    handleCancellationBookingChart = () => {

        const ctx = document.getElementById('cancellationBookingsChart')?.getContext('2d');
        if (!ctx) return;
        const finalDataForCharts = this.chartData || {};

        if (this.cancellationBooking) {
            this.cancellationBooking.destroy();
        }

        this.cancellationBooking = new Chart(ctx, {
            type: 'pie',
            data: {
            labels: ['Cancelled', 'Not Cancelled'],
            datasets: [{
                label: 'Total Bookings Vs Cancellations',
                data: [
                finalDataForCharts.totalCancelledBookings || 0,
                finalDataForCharts.notCandelledBookings || 0,
                ],
                backgroundColor: [
                'rgb(147, 196, 125)',
                'rgba(153, 102, 255, 0.6)'
                ],
                borderWidth: 1
            }]
            },
            options: {
            responsive: true,
            maintainAspectRatio: false
            }
        });
    };

    handleLineChartForNoOfBookings = () => {
        const ctx = document.getElementById('lineChartForNoOfBookings')?.getContext('2d');
        if (!ctx) return;

        // Ensure finalDataForCharts exists and contains valid data
        const finalDataForCharts = this.chartData || {};
        const datesForBookings = finalDataForCharts.datesForBookings || [];
        const bookingsPerDay = finalDataForCharts.bookingsPerDay || [];

        if (this.lineChartForBookings) {
            this.lineChartForBookings.destroy();
        }

        this.lineChartForBookings = new Chart(ctx, {
            type: 'line',
            data: {
                labels: datesForBookings, // Use JavaScript array directly
                datasets: [{
                    label: 'Bookings Per Day',
                    data: bookingsPerDay,
                    borderColor: '#8533ff',
                    borderWidth: 2,
                    fill: false,
                    pointBackgroundColor: 'white',
                    pointBorderColor: '#8533ff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Dates'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Bookings'
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    };

    handleLineChartForNoOfCancellation = () => {
        const ctx = document.getElementById('lineChartForNoOfCancellation')?.getContext('2d');
        if (!ctx) return;

        // Ensure finalData exists and contains valid data
        const finalData = this.chartData || {};
        const datesForCancellation = finalData.datesForCancellation || [];
        const cancellationPerDay = finalData.cancellationPerDay || [];

        if (this.lineChartForCancellation) {
            this.lineChartForCancellation.destroy();
        }

        this.lineChartForCancellation = new Chart(ctx, {
            type: 'line',
            data: {
                labels: datesForCancellation, // Use JavaScript array directly
                datasets: [{
                    label: 'Cancel Per Day',
                    data: cancellationPerDay,
                    borderColor: '#8533ff',
                    borderWidth: 2,
                    fill: false,
                    pointBackgroundColor: 'white',
                    pointBorderColor: '#8533ff',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Dates'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Cancellations'
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    };    

    handleOnLoad = () => {
        let todayEndDate = moment().endOf("day");

        $("#pickupDateBooking").daterangepicker(
            {
                startDate: moment().startOf("day"),
                endDate: todayEndDate,
                parentEl: ".wrapper",
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 1,
                ranges: {
                    Today: [moment().startOf("day"), todayEndDate],
                    Tomorrow: [
                        moment().add(1, "day").startOf("day"),
                        moment().add(1, "day").endOf("day"),
                    ],
                    Yesterday: [
                        moment().subtract(1, "days").startOf("day"),
                        moment().subtract(1, "days").endOf("day"),
                    ],
                    "Last 7 Days": [
                        moment().subtract(6, "days").startOf("day"),
                        moment().endOf("day"),
                    ],
                    "Last 30 Days": [
                        moment().subtract(29, "days").startOf("day"),
                        moment().endOf("day"),
                    ],
                    "This Month": [
                        moment().startOf("month").startOf("day"),
                        moment().endOf("month").endOf("day"),
                    ],
                    "Last Month": [
                        moment().subtract(1, "month").startOf("month").startOf("day"),
                        moment().subtract(1, "month").endOf("month").endOf("day"),
                    ],
                    "Next 7 Days": [
                        moment().startOf("day"),
                        moment().add(6, "days").endOf("day"),
                    ],
                    "Next 30 Days": [
                        moment().startOf("day"),
                        moment().add(29, "days").endOf("day"),
                    ],
                    "Next Week": [
                        moment().add(1, "week").startOf("week").startOf("day"),
                        moment().add(1, "week").endOf("week").endOf("day"),
                    ],
                    "Next Month": [
                        moment().add(1, "month").startOf("month").startOf("day"),
                        moment().add(1, "month").endOf("month").endOf("day"),
                    ],
                    "This Week": [
                        moment().startOf("week").startOf("day"),
                        moment().endOf("week").endOf("day"),
                    ],
                    "Last Week": [
                        moment().subtract(1, "week").startOf("week").startOf("day"),
                        moment().subtract(1, "week").endOf("week").endOf("day"),
                    ],
                },
                locale: {
                    format: "DD/MM/YYYY HH:mm",
                },
            },
            (start, end) => {
                this.handleFilter(
                    start.format("DD/MM/YYYY HH:mm"),
                    end.format("DD/MM/YYYY HH:mm")
                );
            }
        );

        let calendarShown = 0;

        $("#pickupDateBooking").on(
            "showCalendar.daterangepicker",
            function (ev, picker) {
                calendarShown++;

                if (calendarShown === 2) {
                    picker.setStartDate(picker.startDate.clone().startOf("day"));
                    picker.setEndDate(picker.endDate.clone().endOf("day"));
                    $(".drp-calendar.left .calendar-time .hourselect").val(0);
                    $(".drp-calendar.left .calendar-time .minuteselect").val(0);
                    $(".drp-calendar.right .calendar-time .hourselect").val(23);
                    $(".drp-calendar.right .calendar-time .minuteselect").val(59);
                }
            }
        );

        $("#pickupDateBooking").on("hide.daterangepicker", function () {
            calendarShown = 0;
        });
    };

    handleFilter = (startDate, endDate) => {
        try {
            let pickupDateRange = $("#pickupDateBooking").val();

            if (startDate && endDate) {
                pickupDateRange = startDate + " - " + endDate;
            }

            const params = {
                pickupDateRange: pickupDateRange,
            };
            const queryParams = $.param(params);
            const url = this.props.routes.filterDashboardData + "?" + queryParams;
            this.handleFilterRequest(url);
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
                    this.chartData = response.data.data || {}; 

                    this.handleTypesOfBookingChart();
                    this.handleCancellationBookingChart();
                    this.handleLineChartForNoOfBookings();
                    this.handleLineChartForNoOfCancellation();
                    $("#loader").hide();
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error);
            });
    };
}

window.service = new Dashboard(props);
