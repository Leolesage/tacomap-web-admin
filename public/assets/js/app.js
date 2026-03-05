(function () {
  function debounce(fn, delay) {
    var timer;
    return function () {
      var args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        fn.apply(null, args);
      }, delay);
    };
  }

  window.initTacosPlaceSearch = function (inputId, targetId) {
    var input = document.getElementById(inputId);
    var target = document.getElementById(targetId);
    if (!input || !target) return;

    var url = input.getAttribute('data-search-url');
    if (!url) return;

    function bindPagination() {
      var buttons = target.querySelectorAll('.ajax-page-btn');
      buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (btn.hasAttribute('disabled')) return;
          var page = btn.getAttribute('data-page') || '1';
          runSearch(page);
        });
      });
    }

    function fetchAndRender(page) {
      var q = input.value || '';
      var fullUrl = url + '?q=' + encodeURIComponent(q) + '&page=' + encodeURIComponent(page);
      fetch(fullUrl, { headers: { 'X-Requested-With': 'fetch' } })
        .then(function (res) {
          if (!res.ok) throw new Error('search_error');
          return res.text();
        })
        .then(function (html) {
          target.innerHTML = html;
          bindPagination();
        })
        .catch(function () {});
    }

    var runSearch = debounce(function (page) {
      fetchAndRender(page || 1);
    }, 250);

    input.addEventListener('keyup', function () {
      runSearch(1);
    });

    bindPagination();
  };

  window.initTacosPlaceMap = function (mapId, latId, lngId) {
    if (!window.L) return;

    var mapEl = document.getElementById(mapId);
    var latEl = document.getElementById(latId);
    var lngEl = document.getElementById(lngId);
    if (!mapEl || !latEl || !lngEl) return;

    var lat = parseFloat(latEl.value) || 48.8566;
    var lng = parseFloat(lngEl.value) || 2.3522;

    var map = L.map(mapEl).setView([lat, lng], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([lat, lng], { draggable: false }).addTo(map);

    function updateInputs(latlng) {
      latEl.value = latlng.lat.toFixed(7);
      lngEl.value = latlng.lng.toFixed(7);
    }

    map.on('click', function (e) {
      marker.setLatLng(e.latlng);
      updateInputs(e.latlng);
    });

    function syncFromInputs() {
      var newLat = parseFloat(latEl.value);
      var newLng = parseFloat(lngEl.value);
      if (isNaN(newLat) || isNaN(newLng)) return;
      var latlng = L.latLng(newLat, newLng);
      marker.setLatLng(latlng);
      map.setView(latlng);
    }

    latEl.addEventListener('change', syncFromInputs);
    lngEl.addEventListener('change', syncFromInputs);
  };

  window.initTacosPlaceMarkerMap = function (mapId, lat, lng) {
    if (!window.L) return;
    var mapEl = document.getElementById(mapId);
    if (!mapEl) return;

    var latitude = parseFloat(lat);
    var longitude = parseFloat(lng);
    if (isNaN(latitude) || isNaN(longitude)) return;

    var map = L.map(mapEl).setView([latitude, longitude], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([latitude, longitude]).addTo(map);
  };
})();
