// Draws the maps of `x-map` with MapLibre GL JS. Loaded by app.js only when a map is about to be seen.
import {
    AttributionControl,
    FullscreenControl,
    GeolocateControl,
    LngLatBounds,
    Map as MapLibreMap,
    Marker,
    NavigationControl,
    Popup,
    setWorkerUrl,
} from "maplibre-gl";
import "maplibre-gl/dist/maplibre-gl.css";
// MapLibre runs in a worker, a file it looks for next to its own at runtime. Bundled by Vite, it is no longer there:
// `?url` copies the worker into the build and gives its address, which `setWorkerUrl()` passes to MapLibre.
import workerUrl from "maplibre-gl/dist/maplibre-gl-worker.mjs?url";

setWorkerUrl(workerUrl);

const maps = [];

// The zoom a map shows its point at, and the closest it shows its spots at: wide enough to see what surrounds them.
const zoom = 13;

// How the spots fit in the map: away from its edges, and not closer than `zoom`, as for a single spot.
const fitting = { padding: 40, maxZoom: zoom };

// The font of the numbers of the groups of spots: one of the fonts of the style of the map (OpenFreeMap). With another
// style, it may have to change, or the numbers are not shown.
const font = ["Noto Sans Bold"];

// A button that brings the map back to its view, keeping its direction: its point, or all its spots. Disabled while
// there is nothing to go back to, such as a picker before a point is picked.
class RecenterControl {
    constructor({ label, icon }) {
        this.label = label;
        this.icon = icon;
        this.view = () => null;
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
        this.update();

        return this.container;
    }

    onRemove() {
        this.container.remove();
    }

    // Enables the button while there is a view to go back to.
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

// The primary color of the theme, as `rgb()`: the theme gives it as `oklch()`, which the layers of MapLibre cannot
// read. The browser paints a pixel of it, whose red, green and blue are read back.
const primary = () => {
    const canvas = document.createElement("canvas");
    const context = canvas.getContext("2d", { willReadFrequently: true });

    canvas.width = 1;
    canvas.height = 1;
    context.fillStyle = getComputedStyle(
        document.documentElement,
    ).getPropertyValue("--color-primary");
    context.fillRect(0, 0, 1, 1);

    const [red, green, blue] = context.getImageData(0, 0, 1, 1).data;

    return `rgb(${red}, ${green}, ${blue})`;
};

// The bounds of a GeoJSON collection of points, or null without points.
const boundsOf = ({ features }) =>
    features.length === 0
        ? null
        : features.reduce(
              (bounds, feature) => bounds.extend(feature.geometry.coordinates),
              new LngLatBounds(),
          );

// Shows the spots of a map: the address of their GeoJSON, or the GeoJSON itself. The close ones are grouped, with their
// number, and open when clicked; a spot shows its name, and a link to its page when it has one.
const showSpots = (map, { dataset }, recenter) => {
    const color = primary();
    let bounds = null;

    recenter.view = () => bounds && map.cameraForBounds(bounds, fitting);

    map.on("load", async () => {
        map.addSource("spots", {
            type: "geojson",
            data: dataset.mapSpots.startsWith("{")
                ? JSON.parse(dataset.mapSpots)
                : dataset.mapSpots,
            cluster: true,
            clusterRadius: 50,
        });
        map.addLayer({
            id: "spot-groups",
            type: "circle",
            source: "spots",
            filter: ["has", "point_count"],
            paint: {
                "circle-color": color,
                "circle-radius": [
                    "step",
                    ["get", "point_count"],
                    16,
                    10,
                    20,
                    100,
                    26,
                ],
                "circle-stroke-width": 2,
                "circle-stroke-color": "#ffffff",
            },
        });
        map.addLayer({
            id: "spot-group-counts",
            type: "symbol",
            source: "spots",
            filter: ["has", "point_count"],
            layout: {
                "text-field": ["get", "point_count_abbreviated"],
                "text-font": font,
                "text-size": 13,
            },
            paint: { "text-color": "#ffffff" },
        });
        map.addLayer({
            id: "spot-points",
            type: "circle",
            source: "spots",
            filter: ["!", ["has", "point_count"]],
            paint: {
                "circle-color": color,
                "circle-radius": 8,
                "circle-stroke-width": 2,
                "circle-stroke-color": "#ffffff",
            },
        });

        // With an address, the data is there once loaded.
        bounds = boundsOf(await map.getSource("spots").getData());
        recenter.update();

        if (bounds) {
            map.jumpTo(map.cameraForBounds(bounds, fitting));
        }
    });

    map.on("click", "spot-groups", async ({ features: [group] }) => {
        map.easeTo({
            center: group.geometry.coordinates,
            zoom: await map
                .getSource("spots")
                .getClusterExpansionZoom(group.properties.cluster_id),
        });
    });

    map.on("click", "spot-points", ({ features: [spot] }) => {
        // Dark text, as the box of MapLibre stays white in both themes.
        const content = document.createElement("div");
        const name = document.createElement("p");

        content.className = "flex flex-col gap-1 text-sm text-neutral-900";
        name.className = "font-semibold";
        name.textContent = spot.properties.name;
        content.append(name);

        if (spot.properties.url) {
            const link = document.createElement("a");

            link.className = "link link-primary";
            link.href = spot.properties.url;
            link.textContent = dataset.mapSpotLink;
            content.append(link);
        }

        // Without a close button, which crowds the name: a click elsewhere on the map closes it. Pointing at the edge of
        // the circle, not hidden under it.
        new Popup({ closeButton: false, offset: 14, maxWidth: "16rem" })
            .setLngLat(spot.geometry.coordinates)
            .setDOMContent(content)
            .addTo(map);
    });

    ["spot-groups", "spot-points"].forEach((layer) => {
        map.on("mouseenter", layer, () => {
            map.getCanvas().style.cursor = "pointer";
        });
        map.on("mouseleave", layer, () => {
            map.getCanvas().style.cursor = "";
        });
    });

    // Given a size once its step is shown, the map shows its spots again.
    return () => {
        if (bounds) {
            map.jumpTo(map.cameraForBounds(bounds, fitting));
        }
    };
};

// Picks the point of a picker: by a click on the map, by moving its marker, or by typing its coordinates.
const pickPoint = (map, picker, recenter) => {
    const marker = new Marker({ color: primary(), draggable: true });
    const inputs = {
        latitude: picker.querySelector('[name="latitude"]'),
        longitude: picker.querySelector('[name="longitude"]'),
    };

    recenter.view = () =>
        pickedPoint(picker) && { center: pickedPoint(picker), zoom };

    // Moves the marker to the point, or removes it while there is none.
    const sync = ({ fly = false } = {}) => {
        const current = pickedPoint(picker);

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

    sync();

    // Given a size once its step is shown, the map goes to its point.
    return () => sync({ fly: true });
};

const create = (element) => {
    const { dataset } = element;
    const picker = element.closest("[data-map-picker]");
    const point = picker && pickedPoint(picker);
    // The spots given in the page, to start on them; the ones loaded from an address are fitted once loaded.
    const inline = dataset.mapSpots?.startsWith("{")
        ? boundsOf(JSON.parse(dataset.mapSpots))
        : null;

    element.hidden = false;

    let map;

    try {
        map = new MapLibreMap({
            container: element,
            style: dataset.mapStyle,
            center: point ?? JSON.parse(dataset.mapCenter),
            zoom: point ? zoom : Number(dataset.mapZoom),
            ...(inline && { bounds: inline, fitBoundsOptions: fitting }),
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
    });

    // The position of the person, read again at each click: the map goes there and shows it with a dot, without
    // following it. On a picker, it does not place the point: a click on the map does, on the dot or elsewhere.
    const locate = new GeolocateControl({
        trackUserLocation: false,
        showUserLocation: true,
        fitBoundsOptions: { maxZoom: 15 },
    });

    // The bottom corners stack upwards: the first control added is at the bottom.
    map.addControl(locate, "bottom-right");
    map.addControl(recenter, "bottom-right");
    map.addControl(new FullscreenControl(), "bottom-left");

    const shown = picker
        ? pickPoint(map, picker, recenter)
        : showSpots(map, element, recenter);

    maps.push({ map, shown });
};

document.querySelectorAll("[data-map]").forEach(create);

// A map in a step hidden when it was created has no size: give it one when its step is shown, and show its point or
// its spots again.
document.addEventListener("steps:show", () => {
    maps.forEach(({ map, shown }) => {
        map.resize();
        shown();
    });
});
