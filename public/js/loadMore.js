const SEARCH_LIMIT = 10;
const ARTICLE_USER_LIMIT = 5;

loadMoreHandler = (containerId) => function () {
    const container = select(`#${containerId}`);
    const json = JSON.parse(this.responseText);

    const previousError = select(`#${containerId} .error`);

    if (this.status == 400) {
        const error = createErrorMessage(json.errors);
        error.classList.add('mb-2');

        if (previousError)
            previousError.replaceWith(error);
        else
            container.appendChild(error);

        return;
    }

    if (previousError) previousError.remove();

    const html = json.html;
    const canLoadMore = json.canLoadMore;
  
    container.insertAdjacentHTML('beforeend', html);
    if (!canLoadMore) select('#load-more').style.display = "none";
  };

const loadMoreSearch = (type, value) => {
    const numResults = select(`#${type}`).childElementCount;
    const url = `/api/search/${type}?value=${value}&offset=${numResults}&limit=${SEARCH_LIMIT}`;
    sendAjaxRequest('get', url, null, loadMoreHandler(type));
};

const loadMoreHome = () => {
    const numArticles = select('#articles').childElementCount;

    const url = getFilterUrl(numArticles);
    sendAjaxRequest('get', url, null, loadMoreHandler('articles'));
};

const loadMoreUser = (userId) => {
    const numResults = select('#articles').childElementCount;
    const url = `/api/user/${userId}/articles?offset=${numResults}&limit=${ARTICLE_USER_LIMIT}`;
    sendAjaxRequest('get', url, null, loadMoreHandler('articles'));
};

const loadMoreComments = (articleId) => {
    const numResults = select('#comments').childElementCount;
    const url = `/api/article/${articleId}/comments?offset=${numResults}&limit=10`;
    sendAjaxRequest('get', url, null, loadMoreHandler('comments'));
};
