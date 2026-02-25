const removeHttp = (url) => {
    if (url.startsWith("http://")) {
        url = url.replace("http://", "");
    }
    if (url.startsWith("https://")) {
        url = url.replace("https://", "");
    }

    return url;
};

export { removeHttp };
