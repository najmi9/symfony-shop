import ToastComponent from '../Components/toast';

document.querySelectorAll('#js-add-product').forEach(btn => {
    btn.onclick = (e) => {
        const id = btn.dataset.product;
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
            .catch(err => {
                const toast = new ToastComponent(
                    ' Sorry. Error happend try again.',
                    'toast-error',
                    'bg-danger',
                    'fa-times'
                );
                toast.show();
            });
    }
});

document.querySelectorAll('#js-like').forEach(btn => {
    btn.onclick = e => {
        const id = btn.dataset.product;
        if (btn.dataset.user) {
            const toast = new ToastComponent(
                'You should be authenticated to like the products.',
                'toast-warning',
                'bg-warning',
                'fa-times'
            );
            toast.show();
            return;
        }

        const url = "/like";
        fetch(url, {
            method: 'POST',
            body: JSON.stringify(id)
        })
            .then(res => res.json())
            .then(res => {
                document.querySelector('span#js-likes-' + id).innerHTML = res.likes;
                const icon = btn.querySelector("i");
                if (icon.classList.contains('fas')) {
                    icon.classList.replace('fas', 'far')
                } else {
                    icon.classList.replace('far', 'fas');
                }
            })
            .catch(err => {
                const toast = new ToastComponent(
                    'Sorry. Error happend try again.',
                    'toast-error',
                    'bg-danger',
                    'fa-times'
                );
                toast.show();
            });
    }
});
