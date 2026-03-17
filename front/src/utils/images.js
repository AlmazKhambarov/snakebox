const getImageFromStorage = (image) => {
    if (image && typeof image === 'string' && image.startsWith('http')) {
        return image;
    }
    return (import.meta.env.VITE_APP_BACKEND_URL || '') + (image || '');
};

export { getImageFromStorage };
