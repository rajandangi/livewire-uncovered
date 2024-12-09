
// find all elements with wire:snapshot attribute
document.querySelectorAll('[wire\\:snapshot]').forEach(el => {
    el.__livewire = JSON.parse(el.getAttribute('wire:snapshot'));

    initWireClick(el);
    initWireModel(el);
});

function initWireClick(el) {
    el.addEventListener('click', e => {
        if (!e.target.hasAttribute('wire:click')) return;

        let method = e.target.getAttribute('wire:click');

        sendRequest(el, { callMethod: method });
    });
}


function initWireModel(el) {
    // Set input values
    updateWireModelInputs(el);

    // Send new input values.
    let data = el.__livewire.data;
    // input event fires immediately after the value of an input element is changed
    // change event fires when the element loses focus after its value has changed
    el.addEventListener('change', e => {
        if (!e.target.hasAttribute('wire:model')) return;

        let property = e.target.getAttribute('wire:model');
        let value = e.target.value;

        sendRequest(el, { updateProperty: [property, value] });
    })
}

function updateWireModelInputs(el) {
    let data = el.__livewire.data;
    el.querySelectorAll('[wire\\:model]').forEach(input => {
        // Get the property name from the wire:model attribute.
        let property = input.getAttribute('wire:model');

        input.value = data[property];
    });
}

function sendRequest(el, addToPayload) {
    let snapshot = el.__livewire;

    fetch('/livewire', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            snapshot,
            ...addToPayload,
        })
    })
        .then(response => response.json())
        .then(response => {

            let { html, snapshot } = response;

            el.__livewire = snapshot;
            // el.innerHTML = html;
            Alpine.morph(el.firstElementChild, html);

            updateWireModelInputs(el);
        })
}