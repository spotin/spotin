// Draws the maps of `x-map` with MapLibre GL JS. Loaded by app.js only when a map is about to be seen.
import {
    AttributionControl,
    FullscreenControl,
    GeolocateControl,
    Map as MapLibreMap,
    Marker,
    NavigationControl,
    setWorkerUrl,
} from "maplibre-gl";
import "maplibre-gl/dist/maplibre-gl.css";
// MapLibre runs in a worker, a file it looks for next to its own at runtime. Bundled by Vite, it is no longer there:
// `?url` copies the worker into the build and gives its address, which `setWorkerUrl()` passes to MapLibre.
import workerUrl from "maplibre-gl/dist/maplibre-gl-worker.mjs?url";

setWorkerUrl(workerUrl);

const maps = [];

// The zoom a map starts at with its point: wide enough to show it with its surroundings.
const zoom = 13;

// A button that brings the map back to its point, at its starting zoom, keeping its direction. Disabled while the map has
// no point, such as a picker before one is picked.
class RecenterControl {
    constructor({ label, icon, view }) {
        this.label = label;
        this.icon = icon;
        this.view = view;
    }

    onAdd(map) {
        this.container = document.createElement("div");
        this.container.className = "maplibregl-ctrl maplibregl-ctrl-group";
        this.button = document.createElement("button");
        this.button.type = "button";
        this.button.title = this.label;
        this.button.setAttribute("aria-label", this.label);
        this.button.append(this.icon);
        this.button.addEventListener("click", () => {
            const view = this.view();

            if (view) {
                map.flyTo(view);
            }
        });
        this.container.append(this.button);

        return this.container;
    }

    onRemove() {
        this.container.remove();
    }

    // Enables the button while the map has a point to go back to.
    update() {
        this.button.disabled = !this.view();
    }
}

// The point of a picker, or null while its coordinates are not valid.
const pickedPoint = (picker) => {
    const latitude = picker.querySelector('[name="latitude"]');
    const longitude = picker.querySelector('[name="longitude"]');

    if (
        latitude.value === "" ||
        longitude.value === "" ||
        !latitude.checkValidity() ||
        !longitude.checkValidity()
    ) {
        return null;
    }

    return [Number(longitude.value), Number(latitude.value)];
};

const primary = () =>
    getComputedStyle(document.documentElement).getPropertyValue(
        "--color-primary",
    );

const create = (element) => {
    const { dataset } = element;
    const picker = element.closest("[data-map-picker]");
    const point = () => {
        if (picker) {
            return pickedPoint(picker);
        }

        return [Number(dataset.longitude), Number(dataset.latitude)];
    };

    element.hidden = false;

    let map;

    try {
        map = new MapLibreMap({
            container: element,
            style: dataset.mapStyle,
            center: point() ?? JSON.parse(dataset.mapCenter),
            zoom: point() ? zoom : Number(dataset.mapZoom),
            // The credits are added at the top left, below.
            attributionControl: false,
            // The map can be turned, but stays flat.
            pitchWithRotate: false,
            touchPitch: false,
            cooperativeGestures: !picker,
            locale: JSON.parse(dataset.mapLocale),
        });
    } catch {
        // No WebGL: the coordinates are still shown or typed in.
        element.hidden = true;

        return;
    }

    // The credits of the style, and of MapLibre, which draws the map.
    map.addControl(
        new AttributionControl({
            compact: true,
            customAttribution:
                '<a href="https://maplibre.org/" target="_blank">MapLibre</a>',
        }),
        "top-left",
    );
    // The compass shows where the map points, and turns it back to the north.
    map.addControl(
        new NavigationControl({ showCompass: true, visualizePitch: false }),
    );

    const recenter = new RecenterControl({
        label: dataset.mapRecenter,
        icon: element
            .querySelector("template[data-map-recenter-icon]")
            .content.firstElementChild.cloneNode(true),
        view: () => point() && { center: point(), zoom },
    });

    const locate = new GeolocateControl({
        trackUserLocation: false,
        showUserLocation: true,
        fitBoundsOptions: { maxZoom: 15 },
    });

    // The bottom corners stack upwards: the first control added is at the bottom.
    map.addControl(locate, "bottom-right");
    map.addControl(recenter, "bottom-right");
    map.addControl(new FullscreenControl(), "bottom-left");

    const marker = new Marker({ color: primary(), draggable: !!picker });

    // Moves the marker to the point, or removes it while there is none.
    const sync = ({ fly = false } = {}) => {
        const current = point();

        recenter.update();

        if (!current) {
            marker.remove();

            return;
        }

        marker.setLngLat(current).addTo(map);

        if (fly) {
            map.easeTo({
                center: current,
                zoom: Math.max(map.getZoom(), zoom),
            });
        }
    };

    sync();
    maps.push({ map, sync });

    if (!picker) {
        return;
    }

    const inputs = {
        latitude: picker.querySelector('[name="latitude"]'),
        longitude: picker.querySelector('[name="longitude"]'),
    };

    // Fills the inputs, as if typed, so that the form knows they changed.
    const pick = ([longitude, latitude]) => {
        inputs.latitude.value = latitude.toFixed(6);
        inputs.longitude.value = longitude.toFixed(6);
        Object.values(inputs).forEach((input) =>
            input.dispatchEvent(new Event("input", { bubbles: true })),
        );
    };

    map.on("click", (event) => {
        pick(event.lngLat.toArray());
        sync();
    });
    marker.on("dragend", () => pick(marker.getLngLat().toArray()));
    Object.values(inputs).forEach((input) =>
        input.addEventListener("change", () => sync({ fly: true })),
    );
};

document.querySelectorAll("[data-map]").forEach(create);

// A map in a step hidden when it was created has no size: give it one when its step is shown, and move it to its
// point.
document.addEventListener("steps:show", () => {
    maps.forEach(({ map, sync }) => {
        map.resize();
        sync({ fly: true });
    });
});
