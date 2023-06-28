const saveFavoriteTags = (userTags, userId) => {
    const prevTags = userTags.map(tag => tag.id);

    const tagsArray = selectAll('.tagContainer');
    tagsArray.forEach(tagElem => {
        const isPrevTag = prevTags.includes(parseInt(tagElem.id));

        if (isPrevTag && !tagElem.classList.contains('selectedTag')) {  // Tag removed from favorites
                sendAjaxRequest('put', `/tags/${tagElem.id}/remove_favorite`, null, null);
        } else if (!isPrevTag && tagElem.classList.contains('selectedTag')) {  // Tag added to favorites
                sendAjaxRequest('put', `/tags/${tagElem.id}/add_favorite`, null, null);
        } 
    });

    window.location.replace(`/user/${userId}`);
}

const toggleSelected = (elem) => {
    elem.classList.toggle('selectedTag');
}