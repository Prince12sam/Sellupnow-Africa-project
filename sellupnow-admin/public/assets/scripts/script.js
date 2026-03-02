if ($(window).outerWidth() > 1199) {
    $("nav.side-navbar").removeClass("shrink");
}

function onlyLetter(evt) {
    var chars = String.fromCharCode(evt.which);
    if (!/[a-z,A-Z]/.test(chars)) {
        evt.preventDefault();
    }
}

const tooltipTriggerList = document.querySelectorAll(
    '[data-bs-toggle="tooltip"]'
);
const tooltipList = [...tooltipTriggerList].map(
    (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
);

function onlyNumber(evt) {
    var chars = String.fromCharCode(evt.which);
    if (!/[0-9.]/.test(chars)) {
        evt.preventDefault();
    }
}

// document.addEventListener("DOMContentLoaded", function () {
//     var root = document.documentElement;

//     // Get the value of --theme-color
//     var themeColor = getComputedStyle(root).getPropertyValue("--theme-color");

//     var svgImages = document.querySelectorAll(".menu.active .menu-icon");

//     svgImages.forEach(function (svgImage) {
//         var svgPath = svgImage.getAttribute("src");
//         var xhr = new XMLHttpRequest();
//         xhr.onreadystatechange = function () {
//             if (xhr.readyState === 4 && xhr.status === 200) {
//                 var svgContent = xhr.responseText;
//                 svgContent = svgContent.replace(
//                     /stroke="#25314C"/g,
//                     'stroke="' + themeColor + '"'
//                 );
//                 svgContent = svgContent.replace(
//                     /fill="#25314C"/g,
//                     'fill="' + themeColor + '"'
//                 );
//                 svgImage.src =
//                     "data:image/svg+xml;charset=utf-8," +
//                     encodeURIComponent(svgContent);
//             }
//         };
//         xhr.open("GET", svgPath, true);
//         xhr.send();
//     });
// });

// visitor messge
$(".visitorMessage").on("click", function (e) {
    Swal.fire(
        "Access Denied!",
        "You don't have permission to access update/edit this admin.",
        "warning"
    );
});

var previewFile = (event, previewID) => {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById(previewID);
        output.src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
};

var gridView = document.getElementById("gridView");
var listView = document.getElementById("listView");

var gridItem = document.getElementById("gridItem");
var listItem = document.getElementById("listItem");

if (
    gridView !== null &&
    listView !== null &&
    gridItem !== null &&
    listItem !== null
) {
    gridView.addEventListener("click", function () {
        gridView.classList.add("active");
        listView.classList.remove("active");
        localStorage.setItem("view", "grid");

        gridItem.classList.add("d-flex");
        gridItem.classList.remove("d-none");
        listItem.classList.remove("d-block");
        listItem.classList.add("d-none");
    });

    listView.addEventListener("click", function () {
        gridView.classList.remove("active");
        listView.classList.add("active");
        localStorage.setItem("view", "list");

        gridItem.classList.remove("d-flex");
        gridItem.classList.add("d-none");
        listItem.classList.add("d-block");
        listItem.classList.remove("d-none");
    });

    $(document).ready(function () {
        var view = localStorage.getItem("view");
        if (view === "grid") {
            gridView.classList.add("active");
            listView.classList.remove("active");

            gridItem.classList.add("d-flex");
            gridItem.classList.remove("d-none");
            listItem.classList.add("d-none");
        } else if (view === "list") {
            gridView.classList.remove("active");
            listView.classList.add("active");

            gridItem.classList.add("d-none");
            listItem.classList.add("d-block");
            listItem.classList.remove("d-none");
        } else {
            gridView.classList.add("active");
            listView.classList.remove("active");

            gridItem.classList.add("d-flex");
            gridItem.classList.remove("d-none");
            listItem.classList.add("d-none");
        }
    });
}

$(document).ready(function () {
    // select2
    $('.select2').select2();

    $("tr:not(:first-child)").not('.ui-datepicker-calendar tr').each(function (index) {
        $(this).css("animation-delay", index * 0.1 + "s");
        $(this).css("display", "table-row");
    });

    setTimeout(function () {
        $("table").not('.ui-datepicker-calendar table').css("overflow", "auto");
    }, $("table tr").not('.ui-datepicker-calendar tr').length * 200);
});

document.body.classList.remove("loading");

// manage theme dark mode and light mode
const theme = localStorage.getItem("theme");
var appContent = document.getElementById('appContent');
const simplePagination = document.getElementById('simplePagination');

if (theme == "dark") {
    appContent.classList.remove("app-theme-white");
    appContent.classList.add("app-theme-dark");

    // pos simple pagination
    if (simplePagination) {
        simplePagination.classList.add("dark-theme");
        simplePagination.classList.remove("light-theme");
    }
}else {
    appContent.classList.remove("app-theme-dark");
    appContent.classList.add("app-theme-white");

    // pos simple pagination
    if (simplePagination) {
        simplePagination.classList.add("light-theme");
        simplePagination.classList.remove("dark-theme");
    }
}

const switchTheme = () => {
    if (appContent.classList.contains("app-theme-white")) {
        appContent.classList.remove("app-theme-white");
        appContent.classList.add("app-theme-dark");
        localStorage.setItem("theme", "dark");

        // pos simple pagination
        if (simplePagination) {
            simplePagination.classList.add("dark-theme");
            simplePagination.classList.remove("light-theme");
        }
    } else {
        appContent.classList.remove("app-theme-dark");
        appContent.classList.add("app-theme-white");
        localStorage.setItem("theme", "light");

        // pos simple pagination
        if (simplePagination) {
            simplePagination.classList.add("light-theme");
            simplePagination.classList.remove("dark-theme");
        }
    }
};

document.getElementById("searchBtn").addEventListener("click", function () {
    $(this).hide();
    const searchBox = $('.searchingBox');
    searchBox.addClass('visible');
    document.getElementById("searchInput").focus();
});

// search menu bar
$('#searchInput').on('input', function () {
    var search = $(this).val().toLowerCase();
    var searchResultPane = $('.search-list');
    $(searchResultPane).html('');

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function safeUrl(url) {
        url = String(url || '');
        if (url.startsWith('/')) return url;
        if (url.startsWith('http://') || url.startsWith('https://')) return url;
        return '#';
    }

    // debounce for global search
    if (window.__adminGlobalSearchTimer) {
        clearTimeout(window.__adminGlobalSearchTimer);
    }

    if (search.length == 0) {
        searchResultPane.slideUp();
        return;
    }

    // search
    var match = $('.menu, .subMenu').filter(function (idx, elem) {
        return $(elem).text().trim().toLowerCase().indexOf(search) >= 0 ? elem : null;
    }).sort();

    // search found
    if (match.length) {
        match.each(function (idx, elem) {
            var item_url = safeUrl($(elem).attr('href'));
            var item_text = escapeHtml($(elem).text().trim());
            $(searchResultPane).append(`<li><a href="${item_url}">${item_text}</a></li>`);
        });
    } else {
        $(searchResultPane).append('<li class="text-muted p-2">No menu result found.</li>');
    }

    // global search (Customer Web users/listings/ads) - only for >= 2 chars
    if (search.length >= 2) {
        window.__adminGlobalSearchTimer = setTimeout(function () {
            try {
                $.getJSON('/admin/global-search', { q: search })
                    .done(function (res) {
                        var items = (res && res.items) ? res.items : [];
                        if (!items.length) {
                            return;
                        }

                        $(searchResultPane).append('<li class="text-muted p-2">Quick results</li>');

                        items.forEach(function (it) {
                            var url = safeUrl(it.url || '#');
                            var label = escapeHtml(it.label || '');
                            var type = escapeHtml(it.type || '');
                            if (!label) return;
                            $(searchResultPane).append(
                                `<li><a href="${url}"><span class="text-muted" style="font-size:12px;">${type}</span> — ${label}</a></li>`
                            );
                        });
                    })
                    .fail(function () {
                        // ignore
                    });
            } catch (e) {
                // ignore
            }
        }, 250);
    }

    // show search result
    searchResultPane.slideDown();
});

$(document).on('click', function (e) {
    var searchBtn = $('#searchBtn');
    var searchBox = $('.searchingBox');
    if (!searchBtn.is(e.target) && searchBtn.has(e.target).length === 0 && !searchBox.is(e.target) && searchBox.has(e.target).length === 0) {
        $('.search-list').slideUp();
        $('#searchInput').val('');

        setTimeout(()=> {
            searchBox.removeClass('visible');
            setTimeout(() => {
                searchBtn.show();
            }, 400);
        }, 500);
    }
});

