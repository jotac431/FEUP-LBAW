proposeTag = (e) => {
    e.preventDefault();
    const tagName = e.target.elements.tagName.value;
    sendAjaxRequest('post', '/tags/new', { tagName }, proposeTagHandler);
}

function proposeTagHandler() {
    if (this.status == 403) {
        window.location = '/login';
        return;
    }

    const previousError = select('#proposeTag .error');

    if (this.status == 400) {
        const error = createErrorMessage(JSON.parse(this.responseText).errors);

        if (previousError)
            previousError.replaceWith(error);
        else
            select('#proposeTag').appendChild(error);

        return;
    }

    if (previousError) previousError.remove();

    const confirmation = document.createElement('h4');
    confirmation.classList.add('mb-0');
    confirmation.innerHTML = 'Thank you for your contribution!';

    select('#proposeTagForm').replaceWith(confirmation);
}


acceptTag = (elem, id) => {
    const url = '/tags/'+ id + '/accept';
    sendAjaxRequest('put', url, null, tagHandler(elem, id, "accept"));
}

rejectTag = (elem, id) =>  {
    const url = '/tags/'+ id + '/reject';
    sendAjaxRequest('put', url, null, tagHandler(elem, id, "reject"));
}

removeTag = (elem, id) =>  {
    const url = '/tags/'+ id;
    sendAjaxRequest('delete', url, null, tagHandler(elem, id, "remove"));
}

const tagHandler = (elem, id, action) => function() {
    if (this.status == 403) {
        window.location = '/login';
        return;
    }

    const previousError = elem.querySelector('.error');

    if (this.status == 400) {
        const error = createErrorMessage(JSON.parse(this.responseText).errors);

        if (previousError)
            previousError.replaceWith(error);
        else
            elem.appendChild(error);

        return;
    }

    if (action !== "remove") {
        const accept = (btn, id) => { removeTag(btn, id); };
        const remove = (btn, id) => { acceptTag(btn, id); };
        let icon, color, containerId, func;
        if (action === "accept") {
            icon = "fa-trash";
            color = "text-danger";
            containerId = `#acceptedTagsContainer`;
            func = accept;
        } else {
            icon = "fa-check";
            color = "text-success";
            containerId = `#rejectTagsContainer`
            func = remove;
        }

        replaceTagContainer(this.responseText, func, id, icon, color, containerId);
    }

    if (previousError) previousError.remove();

    const confirmation = document.createElement('h4');
    confirmation.classList.add('mb-0');
    confirmation.innerHTML = JSON.parse(this.responseText).msg;

    elem.parentElement.replaceWith(confirmation);
}


function replaceTagContainer(responseText, btnFunction, id, icon, iconColor, containerId) {
    const tagContainer = document.createElement('div');
    tagContainer.classList.add("y-5", "pb-3", "pt-5", "bg-dark", "manageTagContainer");

    const subdiv = document.createElement('div');
    subdiv.id = "stateButton";
    subdiv.classList.add("d-flex", "align-items-center");
    
    const tag = document.createElement("h5");
    tag.classList.add("mx-3", "my-0", "py-0", "w-75");
    tag.innerHTML = JSON.parse(responseText).tag_name;

    const btn = document.createElement("button");
    btn.type = "button";
    btn.onclick = () => { btnFunction(btn, id);};
    btn.classList.add("my-0", "py-0", "btn", "btn-lg", "btn-transparent");

    const iter = document.createElement("i");
    iter.classList.add("fas", icon, "fa-2x", "mb-2", iconColor);

    btn.appendChild(iter);
    subdiv.appendChild(tag);
    subdiv.appendChild(btn);
    tagContainer.appendChild(subdiv);

    const container = select(containerId);
    container.appendChild(tagContainer);

    return container;
}
