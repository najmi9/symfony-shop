
import ToastComponent from '../Components/toast';

const options = (body = null) => {
    return {
        method: 'POST',
        body: body,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        redirect: 'follow', // manual, *follow, error
        referrerPolicy: 'no-referrer', // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
        mode: 'cors', // no-cors, *cors, same-origin
        cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
        credentials: 'same-origin', // include, *same-origin, omit


    }
}

window.createOrder = () => fetch('/paypal/create-order', options())
    .then(res => res.json())
    .then(res => res.id)
    .catch(err => {
        console.log(err)
        const toast = new ToastComponent(
            'Sorry, Unexpected error happend.',
            'toast-danger',
            'bg-danger',
            'fa-times'
        );
        toast.show();
    });

window.onApprove = (data, actions) => fetch("/paypal/capture-payment", options(JSON.stringify(data)))
    .then(res => res.json())
    .then(res => res.id)
    .then(res => {
        const toast = new ToastComponent(
            'Thank you for your payment.',
            'toast-success',
            'bg-primary',
            'fa-check'
        );
        toast.show();
    })
    .catch(err => {
        const toast = new ToastComponent(
            'Sorry, Unexpected error happend.',
            'toast-danger',
            'bg-danger',
            'fa-times'
        );
        toast.show();
    });

window.onCancel = (data) => {
    const toast = new ToastComponent(
        'Payment Canceled.',
        'toast-warning',
        'bg-warning',
        'fa-times'
    );
    toast.show();
}

window.onError = (err) => {
    const toast = new ToastComponent(
        'Sorry. ',
        'toast-danger',
        'bg-danger',
        'fa-times'
    );
    toast.show();
}

