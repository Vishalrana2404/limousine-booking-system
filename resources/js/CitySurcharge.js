import loader from "./bootstrap.js";
import BaseClass from "./BaseClass.js";
import ErrorHandler from "./Utility/ErrorHandler.js";
import axios from "axios"; // Import axios for HTTP requests

/**
 * Represents the CitySurcharge class.
 * @extends BaseClass
 */
export default class CitySurcharge extends BaseClass {
    /**
     * Constructor for the CitySurcharge class.
     * @param {Object} props - The properties for the class.
     */
    constructor(props = null) {
        super(props);
        this.handleOnLoad();
        this.savedPolygons = [];
        this.map = null;
        this.drawnPolygons = {};
        $(document).on("contextmenu",this.desableContextMenu);
    }
    desableContextMenu = (e) =>{
        e.preventDefault();
    }

    handleOnLoad = () => {
        this.initializeGoogleMap("map");
    };

    initializeGoogleMap = (elementId) => {
        loader
            .load()
            .then(() => {
                const mapElement = document.getElementById(elementId);
                if (!mapElement) {
                    console.error(`Element with id ${elementId} not found`);
                    return;
                }
                const singaporeBounds = new google.maps.LatLngBounds(
                    new google.maps.LatLng(1.17807, 103.57781), // Southwest corner
                    new google.maps.LatLng(1.48492, 104.08868) // Northeast corner
                );
                // Initialize the map centered on Singapore with bounds and disable zoom controls
                this.map = new google.maps.Map(mapElement, {
                    center: singaporeBounds.getCenter(), // Center the map within the bounds
                    zoom: 11,
                    zoomControl: true, // Hides zoom control buttons
                    restriction: {
                        latLngBounds: singaporeBounds,
                        strictBounds: true,
                    },
                    gestureHandling: "auto", // Disable zooming with gestures
                    keyboardShortcuts: true, // Disable zooming with keyboard
                });

                const markers = [];
                let currentPolygon = null;

                // Add click event listener to the map
                this.map.addListener("click", (event) => {
                    const clickedLatLng = event.latLng;

                    if (!currentPolygon) {
                        currentPolygon = new google.maps.Polygon({
                            strokeColor: "#FF0000",
                            strokeOpacity: 0.8,
                            strokeWeight: 2,
                            fillColor: "#FF0000",
                            fillOpacity: 0.35,
                            editable: true, // Allow vertices to be edited
                            map: this.map,
                        });
                    }

                    // Add clicked point to the current polygon
                    currentPolygon.getPath().push(clickedLatLng);
                });

                // Add right-click event listener to finish drawing the polygon
                google.maps.event.addListener(this.map, "rightclick", () => {
                    if (currentPolygon) {
                        // Close the current polygon
                        currentPolygon.setMap(null); // Remove the temporary polygon from the map
                        const polygonCoordinates = currentPolygon
                            .getPath()
                            .getArray()
                            .map((coord) => ({
                                lat: coord.lat(),
                                lng: coord.lng(),
                            }));
                        // Send polygonCoordinates to your backend server
                        this.savePolygonCoordinates(polygonCoordinates);
                        currentPolygon = null;
                    }
                });
                this.drawSavedCoordinates();
               
            })
            .catch((e) => {
                console.error("Error loading Google Maps API", e);
            });
    };

    drawPolygon = (coordinates) => {
        if (!this.map) return; // Make sure map is initialized

        // Create a polygon with the saved coordinates
        const savedPolygon = new google.maps.Polygon({
            paths: coordinates,
            strokeColor: "#FF0000",
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: "#FF0000",
            fillOpacity: 0.35,
            editable: true, // Allow vertices to be edited
            map: this.map,
        });

        // Optionally, you can store the polygon in an array to keep track of it
        this.savedPolygons.push(savedPolygon);

        // Return the polygon in case you need to manipulate it further
        return savedPolygon;
    };

    savePolygonCoordinates = (coordinates, id = null) => {
        $("#loader").show();
        const saveUrl = this.props.routes.saveCitySurcharge;
        const formData = new FormData();
        formData.append("coordinates", JSON.stringify(coordinates));
        formData.append("id", id);
        axios
            .post(saveUrl, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const existingId = response.data.data.id;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    // Update or draw polygon for the saved location
                    this.updateOrDrawPolygon(existingId, coordinates);
                    $("#loader").hide();
                    throw flash;
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error); // Handle exceptions appropriately
            });
    };

    updateOrDrawPolygon = (id, coordinates) => {
        if (this.drawnPolygons[id]) {
            // Update existing polygon
            this.drawnPolygons[id].setPath(
                coordinates.map(
                    (coord) => new google.maps.LatLng(coord.lat, coord.lng)
                )
            );
            
        } else {
            // Draw new polygon
            const newPolygon = this.drawPolygon(coordinates);
            this.drawnPolygons[id] = newPolygon;
            // Add event listener to the polygon to handle clicks
            google.maps.event.addListener(newPolygon, "rightclick", () => {
                // Trigger update request when the saved polygon is clicked
                this.handlePolygonClick(newPolygon, id, coordinates);
            });
            google.maps.event.addListener(newPolygon, "dblclick", () => {
            // Delete the double-clicked polygon
            this.deleteSavedPolygon(newPolygon);
        });
        }
    };

    handlePolygonClick = (polygon, id, originalCoordinates) => {
        const updatedCoordinates = polygon
            .getPath()
            .getArray()
            .map((coord) => ({
                lat: coord.lat(),
                lng: coord.lng(),
            }));

        // Check if coordinates have changed
        if (
            !this.areCoordinatesEqual(updatedCoordinates, originalCoordinates)
        ) {
            this.savePolygonCoordinates(updatedCoordinates, id);
        }
    };

    areCoordinatesEqual = (coordinates1, coordinates2) => {
        if (coordinates1.length !== coordinates2.length) {
            return false;
        }
        for (let i = 0; i < coordinates1.length; i++) {
            if (
                coordinates1[i].lat !== coordinates2[i].lat ||
                coordinates1[i].lng !== coordinates2[i].lng
            ) {
                return false;
            }
        }
        return true;
    };

    drawSavedCoordinates = () => {
        const savedCities = this.props.savedCities;
        if (savedCities) {
            savedCities.forEach((row) => {
                const coordinates = JSON.parse(row.coordinates);
                const existingId = row.id;
                this.updateOrDrawPolygon(existingId, coordinates);
            });
        }
    };
    deleteSavedPolygon = (polygon) => {
        // Find the ID of the polygon to delete
        let polygonId = null;
        Object.entries(this.drawnPolygons).forEach(([id, drawnPolygon]) => {
            if (drawnPolygon === polygon) {
                polygonId = id;
                return;
            }
        });
        // Remove the polygon from the map
        polygon.setMap(null);
        // Optionally, you may also want to update your backend to remove the polygon
        if (polygonId) {
            this.deletePolygonFromBackend(polygonId);
        }
    };

    deletePolygonFromBackend = (polygonId) => {
        $("#loader").show();
        const deleteUrl = this.props.routes.deleteCitySurcharge;
        const formData = new FormData();
        formData.append("id", polygonId);
        axios
            .post(deleteUrl, formData)
            .then((response) => {
                const statusCode = response.data.status.code;
                const message = response.data.status.message;
                const flash = new ErrorHandler(statusCode, message);
                if (statusCode === 200) {
                    // Remove the polygon from the drawnPolygons object
                    delete this.drawnPolygons[polygonId];
                    $("#loader").hide();
                    throw flash;
                }
            })
            .catch((error) => {
                $("#loader").hide();
                this.handleException(error); // Handle exceptions appropriately
            });
    };
}

window.service = new CitySurcharge(props);
