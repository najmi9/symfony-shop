import addToCart from "../Components/addToCart";
import '../../styles/product.css';

(() => {
    const btn = document.querySelector('#js-add-product');
    if (!btn) {
        return;
    }
    btn.onclick = async (e) => {
        e.preventDefault();
        const id = btn.dataset.product;
        await addToCart(id);
    }

    document.querySelectorAll('.product-img').forEach(img => {
        function callback(){
            img.parentElement.classList.remove('popup-box');
            img.classList.remove('popup');
            img.parentElement.firstElementChild.classList.remove('close-icon');
            img.setAttribute('width', '100%');
            img.setAttribute('height', '100%');
        }

        img.onclick = (e) => {
            const icon = img.parentElement.firstElementChild;
            icon.classList.add('close-icon');
            img.parentElement.classList.add('popup-box');
            img.classList.add('popup');
            img.removeAttribute('width')
            img.removeAttribute('height')

            icon.onclick = callback
        }
    });
})();


