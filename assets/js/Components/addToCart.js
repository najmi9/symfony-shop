import ToastComponent from "./toast";

export default function addToCart(id) {
    const url = "/cart/add-product";
    fetch(url, {
        method: 'POST',
        body: JSON.stringify(id)
    })
    .then(res => res.json())
    .then(res => {
        document.querySelector('#cart-products').innerHTML = res.products;
        const toast = new ToastComponent(
            'The product added to the cart.',
            'toast-success',
            'bg-primary',
            'fa-check'
        );
        toast.show();
    })
    .catch(() => {
        const toast = new ToastComponent(
            ' Sorry. Error happend try again.',
            'toast-error',
            'bg-danger',
            'fa-times'
        );
        toast.show();
    });
}