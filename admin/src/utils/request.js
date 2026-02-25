import axios from "axios";
import Cookies from "js-cookie";

const defaultHeaders = {
    'Content-Type': 'application/json',
};

axios.defaults.baseURL = import.meta.env.VITE_API_URL;

export async function request(type, uri, data = {}, headers = {}) {
    if(Cookies.get('token')) {
        defaultHeaders['Authorization'] = `Bearer ${Cookies.get('token')}`;
    }

    headers = {...defaultHeaders, ...headers};

    try {
        let response;
        if (type === 'GET') {
            response = await axios.get(uri, {params: data, headers: headers});
        } else {
            response = await axios.post(uri, data, {headers: headers});
        }
        return response;
    } catch (error) {
        // Если получили 401 от /api/user или /user, удаляем токен из куки
        if (error.response && error.response.status === 401) {
            const url = error.config?.url || uri;
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
        return error.response || error;
    }
}
