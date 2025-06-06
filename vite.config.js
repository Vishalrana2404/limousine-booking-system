import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/js/app.js",
                "resources/css/app.css",
                "resources/js/BaseClass.js",
                "resources/js/bootstrap.bundle.min.js",
                "resources/js/Login.js",
                "resources/js/Dashboard.js",
                "resources/js/Users.js",
                "resources/js/Settings.js",
                "resources/js/Clients.js",
                "resources/js/VehicleClass.js",
                "resources/js/Vehicle.js",
                "resources/js/Drivers.js",
                "resources/js/DriverOffDay.js",
                "resources/js/Hotels.js",
                "resources/js/Events.js",
                "resources/js/Bookings.js",
                "resources/js/BookingsArchive.js",
                "resources/js/Charts.js",
                "resources/js/Dashboard.js",
                "resources/js/EditBookings.js",
                "resources/js/Logs.js",
                "resources/js/PeakPeriod.js",
                "resources/js/CitySurcharge.js",
                "resources/js/DriverSchedule.js",
                "resources/js/Notification.js",
                "resources/js/NotificationList.js",
                "resources/js/Reports.js",
                "resources/js/EmailTemplates.js",
            ],
            refresh: true,
        }),
    ],
});
