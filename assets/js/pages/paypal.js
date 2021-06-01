
import fetchOptions from '../Components/fetchOptions';
import ToastComponent from '../Components/toast';

(() => {
    const span = document.querySelector('span#customerId');

    if (!span.dataset.user) {
        import('./pay/customerShippingInfo')
        .then(res => res.default())
        ;
    } else {
        import('./pay/userShippingInfo')
        .then(callback=> callback.default());
    }

    window.createOrder = () => {
        if (!span.dataset.user && !span.dataset.id) {
            span.parentElement.parentElement.style.boxShadow = '5px 5px 5px 5px rgba(300, 0, 0, 0.5)';
            span.innerHTML = '<small class="text-danger">Shipping Information Required!!</small>'
            return;
        }

        const url = '/paypal/create-order' + (span.dataset.id ? `/${span.dataset.id}` : '')

        return fetch(url, fetchOptions(null))
            .then(res => res.json())
            .then(res => res.id)
            .catch(() => {
                const toast = new ToastComponent(
                    'Sorry, Unexpected error happend.',
                    'toast-danger',
                    'bg-danger',
                    'fa-times'
                );
                toast.show();
            });
    }

    window.onApprove = (data) => fetch("/paypal/capture-payment", fetchOptions(JSON.stringify(data)))
        .then(res => res.json())
        .then(res => res.id)
        .then(() => {
            const toast = new ToastComponent(
                'Thank you for your payment.',
                'toast-success',
                'bg-primary',
                'fa-check'
            );
            toast.show();
        })
        .catch(() => {
            const toast = new ToastComponent(
                'Sorry, Unexpected error happend.',
                'toast-danger',
                'bg-danger',
                'fa-times'
            );
            toast.show();
        });

    window.onCancel = async (data) => {
        const toast = new ToastComponent(
            'Payment Canceled.',
            'toast-warning',
            'bg-warning',
            'fa-times'
        );
        toast.show();

        await fetch(`/paypal/${data.orderID}/order-cancled`, fetchOptions());
    }

    window.onError = async (err) => {
        // post request to see that the order is canceled
        const toast = new ToastComponent(
            'Sorry, An expected error. ',
            'toast-danger',
            'bg-danger',
            'fa-times'
        );
        toast.show();

        await fetch(`/paypal/order-error`, fetchOptions(err));
    }
})()