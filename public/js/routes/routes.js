// Constants and Configuration
const CONFIG = {
    MAP: {
        DEFAULT_CENTER: { lat: 27.1507245, lng: -81.8185444 },
        DEFAULT_ZOOM: 4,
        MARKER_SCALE: 0.75,
        MARKER_STROKE_WEIGHT: 0,
        TRUCK_ICON_COLOR: '#7D2325'
    },
    API: {
        RETRY_ATTEMPTS: 5,
        RETRY_DELAY: 500,
        LOCATION_UPDATE_DELAY: 600
    },
    INACTIVITY: {
        TIMEOUT: 1800000, // 30 minutes
        CHECK_INTERVAL: 60000 // 1 minute
    }
};

// Utility Class
class RouteUtils {
    static getCurrentRouteName() {
        const urlParts = window.location.href.split('/');
        let routeName = urlParts[urlParts.length - 1];
        return routeName.replace(/#+$/, '').split('?')[0];
    }

    static convertMinutesToHoursMinutes(minutes) {
        if (!minutes) return '0h 0m';
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        return `${hours}h ${remainingMinutes}m`;
    }

    static getRandomDarkColor() {
        const h = Math.random() * 360;
        const s = 50 + Math.random() * 10;
        const v = 50 + Math.random() * 10;
        const rgb = this.hsvToRgb(h, s, v);
        return this.rgbToHex(...rgb);
    }

    static hsvToRgb(h, s, v) {
        // ... existing hsvToRgb implementation ...
    }

    static rgbToHex(r, g, b) {
        // ... existing rgbToHex implementation ...
    }

    static async delayRequest(fn, delay) {
        return new Promise(resolve => setTimeout(() => resolve(fn()), delay));
    }

    static async exponentialBackoff(fn, retries = CONFIG.API.RETRY_ATTEMPTS, delay = CONFIG.API.RETRY_DELAY) {
        try {
            return await fn();
        } catch (error) {
            if (retries === 0) throw error;
            await this.delayRequest(() => {}, delay);
            return this.exponentialBackoff(fn, retries - 1, delay * 2);
        }
    }
}

// Map Manager Class
class MapManager {
    constructor(apiKey) {
        this.apiKey = apiKey;
        this.map = null;
        this.ui = null;
        this.platform = null;
        this.geocoder = null;
        this.markers = {};
        this.infoBubbles = {};
        this.polylines = [];
        this.previousMarker = null;
    }

    initialize() {
        this.platform = new H.service.Platform({ apikey: this.apiKey });
        const defaultLayers = this.platform.createDefaultLayers();

        this.map = new H.Map(
            document.getElementById('map'),
            defaultLayers.vector.normal.map,
            {
                center: CONFIG.MAP.DEFAULT_CENTER,
                zoom: CONFIG.MAP.DEFAULT_ZOOM
            }
        );

        // Initialize map behavior and UI
        const behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(this.map));
        this.ui = H.ui.UI.createDefault(this.map, defaultLayers);
        this.geocoder = this.platform.getGeocodingService();
    }

    async updateLocation(data) {
        try {
            if (!this.validateLocationData(data)) {
                throw new Error('Invalid location data');
            }

            this.clearTrackMarkers(data.route_id);
            const marker = await this.createMarker(data);
            await this.addMarkerToMap(marker, data);
            await this.addReverseGeocoding(marker, data);

        } catch (error) {
            console.error('Error updating location:', error);
            // Implement proper error handling/notification
        }
    }

    validateLocationData(data) {
        return data?.lat && data?.lng && !isNaN(parseFloat(data.lat)) && !isNaN(parseFloat(data.lng));
    }

    createMarker(data) {
        const position = {
            lat: parseFloat(data.lat),
            lng: parseFloat(data.lng)
        };

        const heading = (data.heading !== null && !isNaN(parseFloat(data.heading))) ? parseFloat(data.heading) : 180;
        const icon = this.createTruckIcon(heading);

        return new H.map.Marker(position, {
            icon: icon,
            anchor: new H.math.Point(0, 20)
        });
    }

    createTruckIcon(heading) {
        // ... existing truck icon SVG creation with CONFIG values ...
    }

    async addMarkerToMap(marker, data) {
        if (!this.markers[data.route_id]) {
            this.markers[data.route_id] = [];
        }

        this.markers[data.route_id].push(marker);
        this.previousMarker = marker;
        this.map.addObject(marker);
        this.map.setCenter(marker.getGeometry());
    }

    async addReverseGeocoding(marker, data) {
        try {
            const response = await RouteUtils.exponentialBackoff(() =>
                fetch(`https://revgeocode.search.hereapi.com/v1/revgeocode?at=${data.lat},${data.lng}&apikey=${this.apiKey}`)
            );

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const geocodeData = await response.json();
            if (geocodeData.items?.[0]) {
                this.addInfoBubble(marker, data, geocodeData.items[0].address);
            }
        } catch (error) {
            console.error('Reverse geocoding failed:', error);
        }
    }

    addInfoBubble(marker, data, address) {
        const addressString = this.formatAddress(address);
        
        marker.addEventListener('tap', () => {
            if (!this.infoBubbles[data.route_id]) {
                this.infoBubbles[data.route_id] = [];
            }

            const infoBubble = new H.ui.InfoBubble(marker.getGeometry(), {
                content: addressString
            });

            this.ui.addBubble(infoBubble);
            this.infoBubbles[data.route_id].push(infoBubble);
        });
    }

    formatAddress(address) {
        const parts = [
            address.houseNumber,
            address.street,
            address.city,
            address.state,
            address.postalCode,
            address.country
        ];
        return parts.filter(Boolean).join(', ');
    }

    clearTrackMarkers(routeId) {
        if (this.markers[routeId]) {
            this.markers[routeId].forEach(marker => this.map.removeObject(marker));
            delete this.markers[routeId];
        }

        if (this.infoBubbles[routeId]) {
            this.infoBubbles[routeId].forEach(bubble => bubble.close());
            delete this.infoBubbles[routeId];
        }
    }
}

// Route Manager Class
class RouteManager {
    constructor(mapManager) {
        this.mapManager = mapManager;
        this.currentRoutes = new Map();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Setup WebSocket listeners
        setTimeout(() => {
            window.Echo.private('location')
                .listen('.client-SendLocation', this.handleLocationUpdate.bind(this))
                .listen('SendLocation', this.handleSendLocation.bind(this));
        }, CONFIG.API.LOCATION_UPDATE_DELAY);

        // Setup UI event listeners
        $(document).on('click', '.download-multiple-routes', this.handleMultipleDownload.bind(this));
        // ... other event listeners ...
    }

    async handleLocationUpdate(data) {
        try {
            await this.sendLocationToServer(data);
            await this.mapManager.updateLocation(data);
        } catch (error) {
            console.error('Error handling location update:', error);
        }
    }

    async sendLocationToServer(data) {
        const form = new FormData();
        form.append("lat", data.lat);
        form.append("lng", data.lng);
        form.append("route_id", data.route_id);

        const response = await RouteUtils.exponentialBackoff(() =>
            $.ajax({
                url: "/api/sendlocation",
                method: "POST",
                headers: { Accept: "application/json" },
                processData: false,
                contentType: false,
                data: form
            })
        );

        return response;
    }

    handleSendLocation(data) {
        console.log("SendLocation", data);
        // Implement send location logic
    }

    async handleMultipleDownload(e) {
        const selectedRouteIds = $(e.target).data('routeids') || [];
        // Implement download logic
    }

    // ... other route management methods ...
}

// Activity Monitor Class
class ActivityMonitor {
    constructor() {
        this.inactivityTimer = null;
        this.setupEventListeners();
    }

    setupEventListeners() {
        ['mousedown', 'keydown', 'mousemove', 'touchstart'].forEach(event => {
            document.addEventListener(event, this.resetInactivityTimer.bind(this));
        });
    }

    startMonitoring() {
        this.resetInactivityTimer();
        setInterval(() => this.checkActivity(), CONFIG.INACTIVITY.CHECK_INTERVAL);
    }

    resetInactivityTimer() {
        clearTimeout(this.inactivityTimer);
        this.inactivityTimer = setTimeout(() => {
            this.handleInactivity();
        }, CONFIG.INACTIVITY.TIMEOUT);
    }

    handleInactivity() {
        // Implement inactivity handling (logout, notification, etc.)
    }
}

// Initialize Application
$(document).ready(() => {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const mapManager = new MapManager(apiKey);
    mapManager.initialize();

    const routeManager = new RouteManager(mapManager);
    const activityMonitor = new ActivityMonitor();
    activityMonitor.startMonitoring();
});

// ... existing route-specific functions that haven't been refactored yet ...
