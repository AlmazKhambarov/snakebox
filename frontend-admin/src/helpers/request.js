import axios from "axios";
import Cookies from "js-cookie";

export function request(type, uri, data = {}, headers = {}) {
    if (Cookies.get("token")) {
        axios.defaults.headers.common["Authorization"] = `Bearer ${Cookies.get(
            "token"
        )}`;
        axios.defaults.headers.common["Accept"] = "application/json";
    }

    return new Promise(async (res, rej) => {
        try {
            let result;

            if (type === "POST") {
                result = await axios.post(uri, data, headers);
            } else {
                result = await axios.get(uri, {
                    params: data,
                    headers: headers,
                });
            }

            return res(result);
        } catch (e) {
            // Если получили 401 от /api/user или /user, удаляем токен из куки
            if (e.response && e.response.status === 401) {
                const url = e.config?.url || uri;
                const fullUrl = typeof url === 'string' ? url : uri;
                // Проверяем, что это запрос к /api/user или /user (но не к /users)
                const isUserEndpoint = (fullUrl.includes('/api/user') || fullUrl.match(/\/user(\/|$)/)) && !fullUrl.includes('/users');
                if (isUserEndpoint) {
                    // Удаляем токен из куки
                    const options = {
                        path: "/",
                    };
                    
                    if (import.meta.env.VITE_APP_PRODUCTION === "on") {
                        const domain = import.meta.env.VITE_APP_FRONTEND_URL?.replace(/^https?:\/\//, '').split('/')[0];
                        if (domain) {
                            options.domain = `.${domain}`;
                        }
                    } else {
                        options.domain = "localhost";
                    }
                    
                    Cookies.remove("token", options);
                }
            }
            rej(e.response);
        }
    });
}
