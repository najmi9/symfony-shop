import fetchOptions from "../../../Components/fetchOptions";

/**
 * @param {HTMLFormElement} form the form
 * @param {string} createUrl fetch url
 * @param {function} editUrl take res and return edit url
 * @param {HTMLDivElement} card form container
 */
export default async function handleSubmitForm(form, createUrl, editUrl, card) {
    const data = Object.fromEntries(new FormData(form));
    data.zip = parseInt(data.zip, 10);
    try {
        const res = await fetch(createUrl, fetchOptions(JSON.stringify(data)));
        const json = await res.json();
        if (res.ok) {
            displayData(form, json, editUrl, card);
            const customerId = document.querySelector('span#customerId');
            customerId.setAttribute('data-id', json.id);
            card.style.boxShadow = 'lightgray 5px 5px 5px 5px';
        } else {
            const error = [];
            json.violations?.forEach(violation => {
                error.push({ field: violation.propertyPath, value: violation.title });
            });
            displayErrors(error, form);
            card.style.boxShadow = 'rgba(255, 0, 0, 0.5) 5px 5px 5px 5px';
        }
    } catch (error) {
        console.log(error);
    }
}

/**
 * @param {object} error 
 * @param {HTMLElement} form 
 */
function displayErrors(error, form) {
    error.forEach(err => {
        const inp = form.querySelector(`input[name="${err.field}"]`);
        if (!inp.classList.contains('is-invalid')) {
            inp.classList.add('is-invalid');
        }
        let alreadyExist = false
        inp.parentElement.children.forEach(child => {
            if (child.innerText === err.value) {
                alreadyExist = true;
            }
        });

        if (!alreadyExist) {
            const span = document.createElement('span');
            span.classList.add('invalid-feedback');
            span.innerHTML = err.value;
            inp.parentElement.appendChild(span);
        }
    })
}

function displayData(form, data, editUrl, card) {
    form.querySelectorAll('input').forEach(input => {
        input.setAttribute('readonly', true);
        if (input.classList.contains('is-invalid')) {
            input.classList.remove('is-invalid');
            input.parentElement.lastChild.remove();
            card.style.boxShadow = 'lightgray 5px 5px 5px 5px';
            card.querySelector('small.text-danger')?.remove();
        }
        input.style.backgroundColor = 'lightgray';
    });

    form.querySelector('button').style.display = 'none';
    const h3 = document.querySelector('#shipping-title');

    const btn = document.createElement('button');
    btn.classList.add('btn', 'btn-sm', 'btn-primary');
    btn.innerHTML = '<i class="fas fa-edit"></i>'

    btn.onclick = (e) => {
        e.preventDefault();
        form.querySelectorAll('input').forEach(input => {
            input.removeAttribute('readonly');
            input.style.backgroundColor = '';
        });

        const submitButton = form.querySelector('button');

        submitButton.style.display = 'inline';

        submitButton.onclick = async (e) => {
            e.preventDefault();
            await handleSubmitForm(form, editUrl(data), editUrl, card)
        }

        btn.remove();
    }

    h3.appendChild(btn);
}