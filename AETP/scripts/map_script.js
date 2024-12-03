const filterItems = document.querySelectorAll('.filter-item');
const clearFilterBtn = document.getElementById('clearFilter');
let activeFilter = null;

filterItems.forEach(item => {
    if (item.id !== 'clearFilter') {
        item.addEventListener('click', () => {
            // If there's an active filter, deactivate it
            if (activeFilter) {
                activeFilter.classList.remove('active');
            }

            // If the clicked filter is the same as the active one, deactivate it
            if (activeFilter === item) {
                activeFilter = null;
            } else {
                // Activate the clicked filter
                item.classList.add('active');
                activeFilter = item;
            }

            updateFilters();
        });
    }
});

clearFilterBtn.addEventListener('click', () => {
    // Clear active filter
    if (activeFilter) {
        activeFilter.classList.remove('active');
        activeFilter = null;
    }
    updateFilters();
});

// Adjust the markers based on the pan offset and scale (keeping position fixed)
function adjustMarkers() {
    const markers = document.querySelectorAll('.map-marker');
    markers.forEach(marker => {
        const mapX = parseFloat(marker.dataset.mapX);
        const mapY = parseFloat(marker.dataset.mapY);

        // Calculate the position of the marker based on the pan offset and original map coordinates
        const x = (mapX * scale) + offset.x;
        const y = (mapY * scale) + offset.y;

        // Set the position of the marker
        marker.style.left = `${x}px`;
        marker.style.top = `${y}px`;

        // Set a fixed size for the marker
        const fixedMarkerSize = 50;
        marker.style.width = `${fixedMarkerSize}px`;
        marker.style.height = `${fixedMarkerSize}px`;
    });
}

function filterMapElements(filter) {
    const mapElements = document.querySelectorAll('.map-element');
    mapElements.forEach(element => {
        if (element.dataset.type === filter) {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    });
}

function showAllMapElements() {
    const mapElements = document.querySelectorAll('.map-element');
    mapElements.forEach(element => {
        element.style.display = 'block';
    });
}

// Initial filter setup
updateFilters();

//Map Rendering

const canvas = document.getElementById('map-canvas');
const ctx = canvas.getContext('2d');
const zoomInBtn = document.getElementById('zoom-in');
const zoomOutBtn = document.getElementById('zoom-out');

let scale = 1.3;
let offset = { x: 0, y: 0 };
let isDragging = false;
let startPan = { x: 0, y: 0 };
let lastX = 0;
let lastY = 0;
let dragSpeed = 0.4;
let velocity = { x: 0, y: 0 };

const image = new Image();
image.src = 'AETP_MAP.png';
image.crossOrigin = 'anonymous';

image.onload = () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    offset = { x: 0, y: 0 };
    drawMap();
};

function drawMap() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.save();
    ctx.translate(offset.x, offset.y);
    ctx.scale(scale, scale);
    ctx.drawImage(image, 0, 0);
    ctx.restore();

    // Adjust markers after re-render
    adjustMarkers();
}

// Function to add location markers and attach modal event
function addLocationMarkers(locationsArray) {
    locationsArray.forEach(location => {
        const marker = document.createElement('div');
        marker.classList.add('map-marker');
        marker.style.position = 'absolute';
        marker.dataset.mapX = location.x;
        marker.dataset.mapY = location.y;

        // Create the location pin shape
        const pinShape = document.createElement('div');
        pinShape.classList.add('location-pin');

        const pinCircle = document.createElement('div');
        pinCircle.classList.add('pin-circle');
        pinCircle.innerHTML = `<div class="marker-icon">${activeFilter.querySelector('svg').outerHTML}</div>`;

        const pinCone = document.createElement('div');
        pinCone.classList.add('pin-cone');

        pinShape.appendChild(pinCircle);
        pinShape.appendChild(pinCone);
        marker.appendChild(pinShape);

        marker.addEventListener('click', () => {
            showModal(location.label, location.image);
        });

        canvas.parentElement.appendChild(marker);
        
    });
}

// Show Modal Function
function showModal(title, imageUrl) {
    const modal = document.getElementById('location-modal');
    const modalImage = document.getElementById('modal-image');
    const modalTitle = document.getElementById('modal-title');
    const closeBtn = modal.querySelector('.close-btn');

    // Set the content
    modalImage.src = imageUrl;
    modalTitle.textContent = title;

    // Show the modal
    modal.style.display = 'block';

    // Close modal on click of the close button
    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    // Close modal on outside click
    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
}

// Function to clear existing markers before adding new ones
function clearMarkers() {
    const existingMarkers = document.querySelectorAll('.map-marker');
    existingMarkers.forEach(marker => marker.remove());
}

function updateFilters() {
    // Clear existing markers
    clearMarkers();

    // If a filter is active, apply it
    if (activeFilter) {
        const filter = activeFilter.dataset.filter;

        // Apply filter and show relevant map elements
        filterMapElements(filter);

        // Add the corresponding location markers based on the filter
        addLocationMarkers(locations[filter]);

        // Trigger a re-render of the map to update markers positioning
        drawMap();  // Ensure the map is re-rendered immediately

        // Display clear filter button if any filter is active
        clearFilterBtn.style.display = 'flex';
    } else {
        // If no filter is active, show all map elements
        showAllMapElements();
        clearFilterBtn.style.display = 'none';
    }
}

const locations = {
    Restrooms: [
        { x: 400, y: 500, label: "Restroom 1", image: "../header-image.png" },
        { x: 500, y: 600, label: "Restroom 2", image: "restroom2.jpg" }
    ],
    Wash_Area: [
        { x: 800, y: 100, label: "Wash Area 1", image: "washarea1.jpg" }
    ],
    Facilities: [
        { x: 470, y: 485, label: "Information Center", image: "header-image.jpg" },
        { x: 910, y: 430, label: "Bee Hive Center", image: "header-image.jpg" }
    ],
    cafeteria: [
        { x: 700, y: 500, label: "Cafeteria 1", image: "cafeteria1.jpg" }
    ]
};

// Apply locations when filters are updated
filterItems.forEach(item => {
    item.addEventListener('click', () => {
        const filter = item.dataset.filter;
        addLocationMarkers(locations[filter]);
    });
});

// Zoom control and other events
canvas.addEventListener('wheel', (e) => {
    e.preventDefault();

    // Get mouse position relative to the canvas
    const mouseX = e.clientX - canvas.offsetLeft;
    const mouseY = e.clientY - canvas.offsetTop;

    // Convert mouse position to map coordinates
    const mapX = (mouseX - offset.x) / scale;
    const mapY = (mouseY - offset.y) / scale;

    // Adjust scale
    const delta = -e.deltaY;
    const newScale = Math.min(Math.max(scale + delta * 0.002, 0.8), 2.2);

    // Adjust offset to zoom towards cursor
    offset.x -= (mapX * newScale - mapX * scale);
    offset.y -= (mapY * newScale - mapY * scale);

    // Update scale and redraw map
    scale = newScale;
    drawMap();
    adjustMarkers(); // Adjust markers after zooming
});

// Handle mouse down for panning
canvas.addEventListener('mousedown', (e) => {
    isDragging = true;
    startPan = { x: e.clientX - offset.x, y: e.clientY - offset.y };
    lastX = e.clientX;
    lastY = e.clientY;
    velocity = { x: 0, y: 0 };
});

canvas.addEventListener('mouseup', () => {
    isDragging = false;
    applyInertia()
});

canvas.addEventListener('mouseleave', () => {
    isDragging = false;
    applyInertia()
});

// Handle mouse move for smooth panning
canvas.addEventListener('mousemove', (e) => {
    if (!isDragging) return;

    const deltaX = (e.clientX - lastX) * dragSpeed;
    const deltaY = (e.clientY - lastY) * dragSpeed;

    velocity.x = deltaX;
    velocity.y = deltaY;

    offset.x += deltaX;
    offset.y += deltaY;

    // Limit the dragging within the image boundaries
    const imageWidth = image.width * scale;
    const imageHeight = image.height * scale;
    const maxOffsetX = Math.min(0, canvas.width - imageWidth); // Left boundary
    const maxOffsetY = Math.min(0, canvas.height - imageHeight); // Top boundary
    const minOffsetX = Math.max(0, canvas.width - imageWidth); // Right boundary
    const minOffsetY = Math.max(0, canvas.height - imageHeight); // Bottom boundary

    offset.x = Math.min(Math.max(offset.x, maxOffsetX), minOffsetX);
    offset.y = Math.min(Math.max(offset.y, maxOffsetY), minOffsetY);

    lastX = e.clientX;
    lastY = e.clientY;

    drawMap();
    adjustMarkers(); // Adjust markers after panning
});

// Zoom In / Zoom Out controls
zoomInBtn.addEventListener('click', () => {
    scale = Math.min(scale + 0.1, 2.2);
    drawMap();
    adjustMarkers(); // Adjust markers after zooming
});

zoomOutBtn.addEventListener('click', () => {
    scale = Math.max(scale - 0.1, 0.8);
    drawMap();
    adjustMarkers(); // Adjust markers after zooming
});

// Apply inertia for smooth drag effect
function applyInertia() {
    // Smooth inertia effect
    if (Math.abs(velocity.x) > 0.1 || Math.abs(velocity.y) > 0.1) {
        offset.x += velocity.x;
        offset.y += velocity.y;
        velocity.x *= 0.95;
        velocity.y *= 0.95;

        // Limit the dragging within the image boundaries during inertia
        const imageWidth = image.width * scale;
        const imageHeight = image.height * scale;
        const maxOffsetX = Math.min(0, canvas.width - imageWidth); // Left boundary
        const maxOffsetY = Math.min(0, canvas.height - imageHeight); // Top boundary
        const minOffsetX = Math.max(0, canvas.width - imageWidth); // Right boundary
        const minOffsetY = Math.max(0, canvas.height - imageHeight); // Bottom boundary

        offset.x = Math.min(Math.max(offset.x, maxOffsetX), minOffsetX);
        offset.y = Math.min(Math.max(offset.y, maxOffsetY), minOffsetY);

        requestAnimationFrame(applyInertia);
        drawMap();
        adjustMarkers(); // Adjust markers during inertia
    }
}

drawMap();