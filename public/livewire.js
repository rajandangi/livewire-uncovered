
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
            morph(el.firstElementChild, html);

            updateWireModelInputs(el);
        })
}

// CUSTOM MORPH FUNCTION
function morph(from, to) {
    if (typeof to === 'string') {
        let temp = document.createElement('div');
        temp.innerHTML = to;
        to = temp.firstElementChild;
    }

    if (from.tagName !== to.tagName) {
        from.replaceWith(to.cloneNode(true));
    }

    patchText(from, to);
    patchAttributes(from, to);
    patchChildren(from, to);
}

function patchChildren(from, to) {
    let childFrom = from.firstElementChild;
    let childTo = to.firstElementChild;

    // If there are more children in the to node, add them
    while (childTo) {
        if (!childFrom) {
            childFrom = from.appendChild(childTo.cloneNode(true));
        } else {
            morph(childFrom, childTo);
        }

        childFrom = childFrom.nextElementSibling;
        childTo = childTo.nextElementSibling;
    }

    // If there are more children in the from node, remove them
    while (childFrom) {
        let toRemove = childFrom;
        childFrom = childFrom.nextElementsibling;
        toRemove.remove();
    }
}

function patchAttributes(from, to) {
    // for (let attributes of to.attributes) {
    //     from.setAttribute(attributes.name, attributes.value);
    // }
    for (let { name, value } of to.attributes) {
        from.setAttribute(name, value);
    }

    for (let { name, value } of from.attributes) {
        if (!to.hasAttribute(name)) {
            from.removeAttribute(name)
        }
    }
}

function patchText(from, to) {
    if (to.children.length > 0) return;
    from.textContent = to.textContent;
}