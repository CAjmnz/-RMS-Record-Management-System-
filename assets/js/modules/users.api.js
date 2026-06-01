window.UsersAPI = (function () {

    function create(data, success, fail) {
        $.post(AppConfig.base_url + "users/store",
            Object.assign(AppConfig.csrfData(), data),
            success,
            "json"
        ).fail(fail);
    }

    function update(data, success, fail) {
        $.post(AppConfig.base_url + "users/update",
            Object.assign(AppConfig.csrfData(), data),
            success,
            "json"
        ).fail(fail);
    }

    function get(id, success) {
        $.get(AppConfig.base_url + "users/get/" + id, success, "json");
    }

    function remove(id, success) {
        $.post(AppConfig.base_url + "users/delete/" + id,
            AppConfig.csrfData(),
            success,
            "json"
        );
    }

    return {
        create,
        update,
        get,
        remove
    };

})();