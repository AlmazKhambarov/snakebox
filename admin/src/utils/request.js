import axios from "axios";
import Cookies from "js-cookie";

const defaultHeaders = {
    'Content-Type': 'application/json',
};

axios.defaults.baseURL = import.meta.env.VITE_API_URL;

export async function request(type, uri, data = {}, headers = {}) {
    if (Cookies.get('token')) {
        defaultHeaders['Authorization'] = `Bearer ${Cookies.get('token')}`;
    }

    if (!(data instanceof FormData)) {
        headers = { ...defaultHeaders, ...headers };
    } else {
        headers = { ...headers };
        if (Cookies.get('token')) {
            headers['Authorization'] = `Bearer ${Cookies.get('token')}`;
        }
    }

    try {
        let response;
        if (type === 'GET') {
            response = await axios.get(uri, { params: data, headers: headers });
        } else {
            response = await axios.post(uri, data, { headers: headers });
        }
        return response;
    } catch (error) {
        if (error.response && error.response.status === 401) {
            const isLoginRequest = error.config?.url?.includes('/admin/login');

            if (!isLoginRequest) {
                Cookies.remove("token", { path: "/" });
                // If in production, also try to remove from subdomains
                if (import.meta.env.VITE_APP_PRODUCTION === "on") {
                    const domain = import.meta.env.VITE_APP_FRONTEND_URL?.replace(/^https?:\/\//, '').split('/')[0];
                    if (domain) Cookies.remove("token", { path: "/", domain: `.${domain}` });
                }
                window.location.href = '/login';
            }
        }
        return error.response || error;
    }
}
