giveFeedback = (elem, content_id, is_like, is_comment) => {
    const url = '/content/' + content_id;
    sendAjaxRequest('put', url, { is_like }, giveFeedbackHandler(elem, content_id, is_like, is_comment));
}

removeFeedback = (elem, content_id, is_like, is_comment) => {
    const url = '/content/' + content_id;
    sendAjaxRequest('delete', url, { is_like }, removeFeedbackHandler(elem, content_id, is_like, is_comment));
}

const giveFeedbackHandler = (elem, content_id, is_like, is_comment) => function() {
    if (this.status == 403) {
        window.location = '/login';
        return;
    }

    const previousError = elem.querySelector(`#${elem.id} .error`);
    console.log(this.responseText);
    if (this.status != 200) {
        const error = createErrorMessage(JSON.parse(this.responseText).errors);
        error.classList.add('mt-3');

        if (previousError)
            previousError.replaceWith(error);
        else
            elem.append(error);

        return;
    }

    elem.classList.add("purpleLink");
    elem.onclick = () => removeFeedback(elem, content_id, is_like, is_comment);

    const counter = elem.lastElementChild;
    counter.innerHTML = is_like ? JSON.parse(this.responseText).likes : JSON.parse(this.responseText).dislikes;

    const likeSelector = is_comment ? `#likes_${content_id}` : '#articleLikes';
    const dislikeSelector = is_comment ? `#dislikes_${content_id}` : '#articleDislikes';

    const oppositeFeedback = is_like ? select(dislikeSelector) : select(likeSelector);
    const oppositeCounter = oppositeFeedback.lastElementChild;
    oppositeCounter.innerHTML = is_like ? JSON.parse(this.responseText).dislikes : JSON.parse(this.responseText).likes;

    oppositeFeedback.classList = is_like ? dislikeClasses(is_comment) : likeClasses(is_comment);
    oppositeFeedback.onclick = () => giveFeedback(oppositeFeedback, content_id, !is_like, is_comment);

    if (previousError) previousError.remove();
}

const removeFeedbackHandler = (elem, content_id, is_like, is_comment) => function() {
    if (this.status == 403) {
        window.location = '/login';
        return;
    }

    const previousError = elem.querySelector(`#${elem.id} .error`);

    if (this.status != 200) {
        const error = createErrorMessage(JSON.parse(this.responseText).errors);
        error.classList.add('mt-3');

        if (previousError)
            previousError.replaceWith(error);
        else
            elem.appendChild(error);

        return;
    }

    elem.classList = is_like ? likeClasses(is_comment) : dislikeClasses(is_comment);
    elem.onclick = () => giveFeedback(elem, content_id, is_like, is_comment);
    
    const counter = elem.lastElementChild;
    counter.innerHTML = is_like ? JSON.parse(this.responseText).likes : JSON.parse(this.responseText).dislikes;

    if (previousError) previousError.remove();
}

const likeClasses = is_comment => is_comment ? 
    ["fa fa-thumbs-up feedbackIcon"] : ["fas fa-thumbs-up ps-5 feedbackIcon"];

const dislikeClasses = is_comment => is_comment ? 
    ["fa fa-thumbs-down ps-3 pe-3 feedbackIcon"] : ["fas fa-thumbs-down ps-3 feedbackIcon"];