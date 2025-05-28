import ErrorHandler from "./Utility/ErrorHandler";
import $ from "jquery";
import { TempusDominus } from "@eonasdan/tempus-dominus";
import loader from "./bootstrap.js";

export default class BaseClass {
    constructor(props) {
        this.props = props;
        this.cropper = null;
        this.timePickerInstances = {};
        /*** Valdation Meessage */
        const customValidationMessagesMeta = $(
            'meta[name="validation-messages"]'
        );
        this.languageMessage = customValidationMessagesMeta.length
            ? JSON.parse(customValidationMessagesMeta.attr("content"))
            : null;
    }

    handleException = (error) => {
        if (!(error instanceof ErrorHandler)) {
            error = new ErrorHandler(500);
        }
        error.flashMessage();
    };

    getDefaultHeaders = () => {
        const token = $('meta[name="csrf-token"]').attr("content");

        return {
            "X-CSRF-TOKEN": token,
            "X-Requested-With": "XMLHttpRequest",
        };
    };

    isValidSize = (file) => {
        if (file.size > 5242880) {
            return false;
        }
        return true;
    };
    openModal = (modalId) => {
        $("#" + modalId).modal("show");
    };

    closeModal = (modalId) => {
        $("#bulkAction").val("");
        $("#deleteBulkConfirmButton").attr("id", "deleteConfirmButton");
        $("#" + modalId).modal("hide");
    };
    initializeGoogleMapAutoComplete = (elementId) => {
        loader
            .load()
            .then(() => {
                const input = document.getElementById(elementId);

                // Create a new Autocomplete object
                const autocomplete = new google.maps.places.Autocomplete(
                    input,
                    {
                        bounds: new google.maps.LatLngBounds(
                            new google.maps.LatLng(1.130475, 103.692035), // Southwest corner of Singapore
                            new google.maps.LatLng(1.450475, 104.092035) // Northeast corner of Singapore
                        ),
                        componentRestrictions: { country: "SG" },
                    }
                );

                // Add a listener for the place_changed event
                autocomplete.addListener("place_changed", () => {
                    const place = autocomplete.getPlace();
                    input.dispatchEvent(new Event("change", { bubbles: true }));
                    $(input).valid();
                });
            })
            .catch((e) => {
                console.error("Error loading Google Maps API", e);
            });
    };

    // Initialize time picker for a specific element
    initializeTimePicker = (elementId, minTime = null) => {
        this.disposeTimePicker(elementId); // Dispose existing instance for this element
        const options = {
            localization: {
                format: "HH:mm",
                hourCycle: "h23",
            },
            display: {
                viewMode: "clock",
                icons: {
                    time: "fa fa-clock",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                },
                components: {
                    calendar: false,
                    date: false,
                    clock: true,
                    decades: true,
                },
            },
        };

        if (minTime) {
            options.restrictions = { minDate: minTime };
        }
        this.timePickerInstances[elementId] = new TempusDominus(
            document.getElementById(elementId),
            options
        );
    };

    // Initialize date picker for a specific element
    initializeDatePicker = (
        elementId,
        timeElementId = false,
        callback = false
    ) => {
        const datePicker = new TempusDominus(
            document.getElementById(elementId),
            {
                localization: {
                    format: "dd/MM/yyyy",   
                    hourCycle: "h23",
                },
                display: {
                    viewMode: "calendar",
                    icons: {
                        type: "icons",
                        date: "fa fa-calendar",
                        up: "fa fa-arrow-up",
                        down: "fa fa-arrow-down",
                        previous: "fa fa-chevron-left",
                        next: "fa fa-chevron-right",
                        today: "fa fa-calendar-check",
                        clear: "fa fa-trash",
                        close: "fa fa-xmark",
                    },
                    components: {
                        calendar: true,
                        date: true,
                        clock: false,
                        month: true,
                        year: true,
                        decades: false,
                        hours: false,
                        minutes: false,
                        seconds: false,
                    },
                },
            }
        );

        // Add event listener for date change
        if (timeElementId) {
            document
                .getElementById(elementId)
                .addEventListener("change.td", (e) =>
                    this.onDateChange(e, timeElementId)
                );
        }
        if (callback) {
            document
                .getElementById(elementId)
                .addEventListener("change.td", () => {
                    callback();
                });
        }
    };

    // Dispose existing time picker instance for a specific element
    disposeTimePicker = (elementId) => {
        if (this.timePickerInstances[elementId]) {
            this.timePickerInstances[elementId].dispose();
            delete this.timePickerInstances[elementId];
        }
    };

    // Handle date change event
    onDateChange = (e, elementId) => {
        const selectedDate = new Date(e.detail.date);
        const now = new Date();
        now.setSeconds(0, 0);
        this.disposeTimePicker(elementId); // Dispose existing instance for this element

        // Initialize time picker based on selected date
        if (selectedDate.toDateString() === now.toDateString()) {
            this.initializeTimePicker(elementId, now);
        } else {
            this.initializeTimePicker(elementId);
        }
    };

    initializeDateTimePicker = (elementId, minDateTime = null) => {
        const element = document.getElementById(elementId);

        const options = {
            localization: {
                format: "dd/MM/yyyy HH:mm",
                hourCycle: "h23",
            },
            display: {
                viewMode: "calendar",
                icons: {
                    type: "icons",
                    date: "fa fa-calendar",
                    time: "fa fa-clock",
                    up: "fa fa-arrow-up",
                    down: "fa fa-arrow-down",
                    previous: "fa fa-chevron-left",
                    next: "fa fa-chevron-right",
                    today: "fa fa-calendar-check",
                    clear: "fa fa-trash",
                    close: "fa fa-xmark",
                },
                components: {
                    calendar: true,
                    date: true,
                    clock: true,
                    month: true,
                    year: true,
                    hours: true,
                    minutes: true,
                    seconds: false,
                },
            },
            useCurrent: false,
        };
        if (minDateTime) {
            options.restrictions = { minDate: minDateTime };
        }
        const datePicker = new TempusDominus(element, options);
    };
    convertDate = (dateStr) => {
        const parts = dateStr.split("/");
        if (parts.length === 3) {
            const day = parts[0];
            const month = parts[1];
            const year = parts[2];
            return year + "-" + month + "-" + day;
        } else {
            return null;
        }
    };
    checkDateBetweenPeakPeriods = (pickUpdate) => {
        if (!pickUpdate) {
            return false;
        }
        // Convert pickUpdate to a Date object
        let [day, month, year] = pickUpdate.split("/");
        let pickUpdateDate = new Date(year, month - 1, day); // months are 0-based in JS Date objects
        const peakPeriods = this.props.peakPeriods;
        // Check if pickUpdateDate lies between any peak period
        let isBetween = peakPeriods.some((period) => {
            let startDate = new Date(period.start_date);
            let endDate = new Date(period.end_date);
            return pickUpdateDate >= startDate && pickUpdateDate <= endDate;
        });
        return isBetween;
    };
}
