import Dropzone from 'dropzone';
import '../../css/admin/product.css'
import ToastComponent from '../Components/toast';

const id = document.querySelector('.product-id').dataset.id;

Dropzone.autoDiscover = false;

const url =  `/admin/products/upload-image/${id}`;

const coverImgUrl = `/admin/products/upload-cover-image/${id}`;

const options = (url, maxFiles) => ({
        url, // Set the url for your upload script location
        paramName: 'file', // The name that will be used to transfer the file
        maxFiles,
        maxFilesize: 10, // MB
        addRemoveLinks: true,
        acceptedFiles: 'image/*',

        error: () => {
            const toast = new ToastComponent('Error happed during the uploading of the picture, refresh and try again.', 'error', 'danger', 'warn');
            toast.show();
        }
});

new Dropzone("#js-dropzone", options(url, 10));
new Dropzone("#js-cover-dropzone", options(coverImgUrl, 1));
