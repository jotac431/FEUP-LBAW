const createNewComment = (article_id) => {
    const body = select('#commentTextArea').value;
    if (!body) return;

    sendAjaxRequest(
        'POST', '/comment',
        { body, article_id },
        newCommentHandler('comment_form', 'commentTextArea', select('#comments'), 'afterbegin')
    );
}

const createNewReply = (parent, article_id, parent_comment_id) => {
    const body = select(`#reply_textarea_${parent_comment_id}`).value;
    if (!body) return;

    sendAjaxRequest(
        'POST', '/comment',
        { body, article_id, parent_comment_id },
        newCommentHandler(
            `reply_form_${parent_comment_id}`,
            `reply_textarea_${parent_comment_id}`,
            parent,
            'afterend',
            `reply_${parent_comment_id}`
        )
    );
}

const editComment = (commentId, editBox) => {
    const body = select(`#edit_textarea_${commentId}`).value;
    if (!body) return;

    sendAjaxRequest('PUT', `/comment/${commentId}`, { body }, editCommentHandler(commentId, editBox));
}

const deleteComment = (commentId) =>
    sendAjaxRequest('DELETE', `/comment/${commentId}`, null, deleteCommentHandler(commentId));

const newCommentHandler = (formId, textareaId, parent, position, removeId) => function () {
    const json = JSON.parse(this.responseText);

    const previousError = select(`#${formId} .error`);

    if (this.status != 200) {
        const error = createErrorMessage(json.errors);
        error.classList.add('mt-2');

        if (previousError)
            previousError.replaceWith(error);
        else
            select(`#${textareaId}`).insertAdjacentElement('afterend', error);

        return;
    }

    if (previousError) previousError.remove();

    parent.insertAdjacentHTML(position, json.html);
    select(`#${textareaId}`).value = '';

    if (removeId) select(`#${removeId}`).remove();
}

const deleteCommentHandler = (commentId) => function() {
    const json = JSON.parse(this.responseText);
    const previousError = select(`#comment_${commentId} .error`);

    if (this.status != 200) {
        const error = createErrorMessage(json.errors);
        error.classList.remove('text-center');
        error.classList.add('ms-5');

        if (previousError)
            previousError.replaceWith(error);
        else
            select(`#comment_${commentId}`).appendChild(error);

        return;
    }

    // Remove replies
    let child;
    while (child = select(`#comment_${commentId} + div`)) {
        if (!select(`#comment_${commentId} + div > .child-comment`))
            break;
        child.remove();
    }

    if (previousError) previousError.remove();
    select(`#comment_${commentId}`).remove();
}

const editCommentHandler = (commentId, editBox) => function() {
    const json = JSON.parse(this.responseText);
    const previousError = select(`#comment_${commentId} + .error`);

    if (this.status != 200) {
        const error = createErrorMessage(json.errors);
        error.classList.remove('text-center');
        error.classList.add('ms-5');

        if (previousError)
            previousError.replaceWith(error);
        else
            select(`#comment_${commentId}`).insertAdjacentElement('afterend', error);
        return;
    }

    if (previousError) previousError.remove();

    const commentText = select(`#comment_${commentId} .commentTextContainer`);
    const different = commentText.innerText != json.body;
    commentText.innerText = json.body;

    let editFlag = select(`#comment_${commentId} .editFlag`);
    if (!editFlag && different) {
        editFlag = document.createElement('i');
        editFlag.classList.add('mx-3', 'editFlag');
        editFlag.innerText = "Edited";
        select(`#comment_${commentId} .publishedAt`).insertAdjacentElement('afterend', editFlag);
    }

    const comment = select(`#comment_${commentId}`);
    closeEditBox(comment, editBox);
}

const openReplyBox = (articleId, parentCommentId) => {
    const previousReply = select(`#reply_${parentCommentId}`)
    if (previousReply) {
        previousReply.remove();
        return;
    }

    const parentComment = select(`#comment_${parentCommentId}`);
    const { mainDiv, commentForm, textArea, button, cancelButton } = getCommentTextarea();
    
    commentForm.id = `reply_form_${parentCommentId}`;
    textArea.id = `reply_textarea_${parentCommentId}`;

    button.onclick = () => createNewReply(parentComment, articleId, parentCommentId);
    button.innerText = 'Reply';

    const wrapperDiv = getReplyBox(mainDiv);
    wrapperDiv.id = `reply_${parentCommentId}`;

    cancelButton.onclick = () => wrapperDiv.remove();

    parentComment.insertAdjacentElement('afterend', wrapperDiv);
    textArea.focus();
}

const openEditBox = (commentId, isReply) => {
    const comment = select(`#comment_${commentId}`);
    comment.setAttribute('style', 'display:none !important');

    const { mainDiv, commentForm, textArea, button, cancelButton } = getCommentTextarea();
    textArea.id = `edit_textarea_${commentId}`;
    textArea.value = select(`#comment_${commentId} .commentTextContainer`).innerText;

    button.innerText = 'Save';

    if (!isReply) {
        cancelButton.onclick = () => closeEditBox(comment, mainDiv);
        button.onclick = () => editComment(commentId, mainDiv);
        comment.insertAdjacentElement('beforebegin', mainDiv);
    } else {
        const wrapperDiv = getReplyBox(mainDiv);
        cancelButton.onclick = () => closeEditBox(comment, wrapperDiv);
        button.onclick = () => editComment(commentId, wrapperDiv);
        comment.insertAdjacentElement('beforebegin', wrapperDiv);
    }

    textArea.focus();
}

const closeEditBox = (comment, editBox) => {
    editBox.remove();
    comment.removeAttribute('style');
}

const getCommentTextarea = () => {
    const mainDiv = document.createElement('div');
    mainDiv.classList.add('d-flex', 'flex-row', 'my-3', 'w-75');

    const headerDiv = document.createElement('div');
    headerDiv.classList.add('flex-column', 'h-100', 'commentHeader', 'mx-5');

    const img = select('.commentHeader img').cloneNode();
    headerDiv.appendChild(img);

    const imgText = document.createElement('p');
    imgText.innerText = 'You';
    headerDiv.appendChild(imgText);
    mainDiv.appendChild(headerDiv);

    const commentForm = document.createElement('div');
    commentForm.classList.add('flex-column', 'w-100', 'mb-0');

    const textArea = document.createElement('textarea');
    textArea.classList.add('flex-column', 'border-light', 'm-0', 'p-2');
    textArea.placeholder = 'Type here';
    commentForm.appendChild(textArea);

    const button = document.createElement('button');
    button.classList.add('button', 'button-primary', 'px-4');
    commentForm.appendChild(button);
    mainDiv.appendChild(commentForm);

    const cancelButton = document.createElement('button');
    cancelButton.classList.add('button', 'button-secondary', 'px-4', 'mx-3');
    cancelButton.innerText = 'Cancel';
    commentForm.appendChild(cancelButton);

    return { mainDiv, commentForm, textArea, button, cancelButton };
}

const getReplyBox = (mainDiv) => {
    mainDiv.classList.remove('w-75');

    const childDiv = document.createElement('div');
    childDiv.classList.add('child-comment');
    childDiv.appendChild(mainDiv);

    const wrapperDiv = document.createElement('div');
    wrapperDiv.classList.add('d-flex', 'justify-content-end', 'articleCommentPartial');
    wrapperDiv.appendChild(childDiv);

    return wrapperDiv;
}
