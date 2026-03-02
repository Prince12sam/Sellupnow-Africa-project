var route_prefix = "/admin/laravel-filemanager";

// Prevent SetUrl from firing more than once per selection (the backup script.js
// calls the callback twice: once via parent[callback] and once via parent.lfmSetUrl).
var _lfmCallbackGuard = false;

// Normalize a LFM item URL to a storage-relative path (e.g. "photos/22/file.jpg")
function normalizeStoragePath(fileUrl) {
    if (!fileUrl) return "";
    try {
        var parsed = new URL(fileUrl, window.location.origin);
        // decodeURIComponent so filenames with spaces/commas survive Storage::exists()
        var pathname = decodeURIComponent(parsed.pathname);
        // Strip leading /storage/ or /public/storage/
        var idx = pathname.toLowerCase().indexOf("/storage/");
        if (idx !== -1) {
            return pathname.substring(idx + "/storage/".length);
        }
        return pathname.replace(/^\/+/, "");
    } catch (e) {
        var s = String(fileUrl).replace(window.location.origin, "");
        var idx2 = s.toLowerCase().indexOf("/storage/");
        if (idx2 !== -1) {
            return decodeURIComponent(s.substring(idx2 + "/storage/".length));
        }
        return decodeURIComponent(s.replace(/^\/+/, ""));
    }
}

// open filemanager
document.querySelectorAll(".lfm").forEach(button => {
    button.addEventListener("click", function (e) {
        e.preventDefault();

        const container = this.closest(".image-container");
        const iframe = document.getElementById("lfmIframe");

        // save references in iframe dataset
        iframe.dataset.containerId = container.dataset.containerId;

        // reset the guard so the next selection can fire
        _lfmCallbackGuard = false;

        // open filemanager
        iframe.src = route_prefix + "?type=image&callback=SetUrl";
        $("#lfmModal").modal("show");
    });
});

// callback after selecting file(s)
window.SetUrl = function (items) {
    // Guard against the double-call from the backup script.js
    if (_lfmCallbackGuard) return;
    _lfmCallbackGuard = true;

    const iframe = document.getElementById("lfmIframe");

    // get the right container
    const container = document.querySelector(
        `.image-container[data-container-id="${iframe.dataset.containerId}"]`
    );

    if (!container) return;

    const target_input = container.querySelector(".thumbnailAdd");
    const target_preview = container.querySelector(".holder");
    const target_thumbnailPath = container.querySelector(".thumbnailPath");

    if (target_preview) {
        target_preview.innerHTML = "";
    }

    const files = items
        .filter(item => item && item.url)
        .map(item => ({
            name: item.name || "",
            path: normalizeStoragePath(item.url),
            url: item.url,
        }));


    if (target_thumbnailPath) {
        const parent = target_thumbnailPath.parentNode;

        // remove old hidden inputs (for multiple mode only)
        parent.querySelectorAll('input[data-generated="true"]').forEach(el =>
            el.remove()
        );

        if (target_thumbnailPath.hasAttribute("multiple")) {
            // multiple mode
            const frag = document.createDocumentFragment();

            files.forEach(file => {
                const hidden = document.createElement("input");
                hidden.type = "hidden";
                hidden.name = target_thumbnailPath.name;
                hidden.value = file.path;
                hidden.dataset.generated = "true";
                frag.appendChild(hidden);

                if (target_preview) {
                    const img = document.createElement("img");
                    img.src = file.url;
                    target_preview.appendChild(img);
                }
            });

            parent.appendChild(frag);
            if (target_input) target_input.value = files.map(f => f.name).join(",");
        } else {
            // single mode
            if (files[0]) {
                target_thumbnailPath.value = files[0].path;

                if (target_preview) {
                    const img = document.createElement("img");
                    img.src = files[0].url;
                    target_preview.appendChild(img);
                }

                if (target_input) target_input.value = files[0].name;
            } else {
                if (target_input) target_input.value = "";
                target_thumbnailPath.value = "";
            }
        }
    }

    if (target_input) target_input.dispatchEvent(new Event("change"));
    if (target_preview) target_preview.dispatchEvent(new Event("change"));
    $("#lfmModal").modal("hide");
};


// alias for filemanager fallback
window.lfmSetUrl = window.SetUrl;

