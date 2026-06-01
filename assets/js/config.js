window.AppConfig = (function () {
    function csrfData() {
        var nameEl  = document.getElementById('csrf_token_name');
        var valueEl = document.getElementById('csrf_token_value');
        if (!nameEl || !valueEl) return {};
        var obj = {};
        obj[nameEl.value] = valueEl.value;
        return obj;
    }

    return {
        base_url: window.base_url,
        csrfData: csrfData
    };
})();