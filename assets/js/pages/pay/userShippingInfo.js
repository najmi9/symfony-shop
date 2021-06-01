import handleSubmitForm from "./functions/handleSubmit";

export default function userInfo() {
    const btn = document.querySelector('button#edit-user-info');
    if (!btn) {
        return;
    }

    btn.onclick = (e) => {
        e.preventDefault();
        const form = document.querySelector('form[name="customer"]');

        form.querySelectorAll('input').forEach(inp => {
            inp.removeAttribute('readonly');
            inp.style.backgroundColor = 'white';
        });

        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.style.display = 'inline';

        form.onsubmit = async (e) => {
            e.preventDefault();

            const editUrl = data => `/profile/edit-shipping-data`;
            const card = document.querySelector('.shipping-card');

            await handleSubmitForm(form, `/profile/edit-shipping-data`, editUrl, card);
        }

        btn.remove();
    }
}
