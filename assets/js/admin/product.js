import Dropzone from 'dropzone';
import { Toast } from 'bootstrap';

import '../../css/admin/product.css'

const id = document.querySelector('.product-id').dataset.id;

const url = `/upload-image/${id}`;

Dropzone.autoDiscover = false;

const options =  {
        url: url, // Set the url for your upload script location
        paramName: 'file', // The name that will be used to transfer the file
        maxFiles: 10,
        maxFilesize: 10, // MB
        addRemoveLinks: true,
        acceptedFiles: 'image/*',

        accept: function (file, done) {
            done();
        },
        error: () => {
            document.querySelector('div.toasts').innerHTML = `
                <div style="position: fixed; bottom: 60px; right: 20px; z-index: 99;" 
                    class="toast toast-error bg-danger text-white" data-bs-delay="10000"
                     role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="fas fas fa-times text-secondary fa-2x"></i>
                        <strong class="me-auto">Symfony App</strong>
                        <small class="text-muted">1 mins ago</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Error happed during the uploading of the picture, refresh and try again.
                    </div>
                </div>
            `;
            const toastDiv = document.querySelector('.toast-error');
            const toast = new Toast(toastDiv);
            toast.show();
        }
}

const myDropzone = new Dropzone("#js-dropzone", options);
console.log(myDropzone)
//Dropzone.options.myDropzone = options;
