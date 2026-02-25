const getImageFromStorage = (image) => {
    return import.meta.env.VITE_APP_FRONTEND_URL + image;
};

export { getImageFromStorage };
