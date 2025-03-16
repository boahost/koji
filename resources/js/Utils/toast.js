import { toast as toastify } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

export const toast = {
    success(message) {
        toastify(message, {
            type: 'success',
            position: 'top-right',
            autoClose: 3000,
            hideProgressBar: false,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
            progress: undefined,
            theme: 'light',
        });
    },
    error(message) {
        toastify(message, {
            type: 'error',
            position: 'top-right',
            autoClose: 3000,
            hideProgressBar: false,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
            progress: undefined,
            theme: 'light',
        });
    }
};
