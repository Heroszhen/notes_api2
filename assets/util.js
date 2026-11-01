import Toastify from "toastify-js";

/**
 *
 * @param {File} file
 * @returns {Promise<string>}
 */
export const readFile = (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
        reader.readAsDataURL(file);
    });
};

/**
 *
 * @param {string} message
 * @param {string} type
 * @param {number} duration
 */
export const alertMessage = (message, type, duration = 2000) => {
    Toastify({
        text: message,
        duration: duration,
        gravity: "top",
        position: "right",
        close: true,
        className: `toast-${type}`,
    }).showToast();
};
