const notificationContainer = select("#notificationContainer");

const createNotification = (headerHTML, bodyHTML) => {
    const toast = document.createElement("div");
    toast.classList.add("toast");
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "assertive");
    toast.setAttribute("aria-atomic", "true");

    const header = document.createElement("div");
    header.classList.add("toast-header");

    const headerText = document.createElement("strong");
    headerText.classList.add("me-auto");
    headerText.innerHTML = headerHTML;
    header.appendChild(headerText);

    const headerTime = document.createElement("small");
    headerTime.classList.add("text-muted");
    headerTime.innerText = "just now";
    header.appendChild(headerTime);

    const closeButton = document.createElement("button");
    closeButton.classList.add("btn-close");
    closeButton.setAttribute("type", "button");
    closeButton.setAttribute("data-bs-dismiss", "toast");
    closeButton.setAttribute("aria-label", "close");
    header.appendChild(closeButton);

    toast.appendChild(header);

    const body = document.createElement("div");
    body.classList.add("toast-body");
    body.innerHTML = bodyHTML;
    toast.appendChild(body);

    notificationContainer.appendChild(toast);

    bootstrap.Toast.getOrCreateInstance(toast).show();
}

function notificationPanelHandler() {
    if (this.status == 403) {
        window.location = '/login';
        return;
    }
    if (this.status != 200) return; // Keep the panel as it is

    for (let sufix of ["",  "Mobile"]) {
        const panel = select("#notificationPanel" + sufix);
        panel.innerHTML = this.responseText;

        const circle = select("#newNotifications" + sufix);
        if (circle) circle.remove();
    }
}

const fetchNotifications = () => {
    sendAjaxRequest('get', '/api/notifications', null, notificationPanelHandler);

    // Mark notifications as read
    sendAjaxRequest('put', '/notifications', null, null);
}
