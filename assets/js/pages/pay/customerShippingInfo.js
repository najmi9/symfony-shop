import handleSubmitForm from "./functions/handleSubmit";

export default function customerInfo() {
    const form = document.querySelector('form[name="customer"]');

    if (form) {
        form.onsubmit = async (e) => {
            e.preventDefault();
            const editUrl = data => `/customers/${data.id}/edit`;
            const card = document.querySelector('.shipping-card');

            await handleSubmitForm(form, '/customers/new', editUrl, card);
        }
    }
}