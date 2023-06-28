const ARTICLE_FILTER_LIMIT = 10;
let minDate, maxDate;

function replaceArticles() {
  const json = JSON.parse(this.responseText);
  const previousError = select('#filterError');

  if (this.status == 400) {
    const error = createErrorMessage(json.errors);
    error.id = 'filterError';
    error.classList.add('mb-2');

    if (previousError)
        previousError.replaceWith(error);
    else
        select('#filterSection').after(error);

    return;
  }

  if (previousError) previousError.remove();

  const html = json.html;
  const canLoadMore = json.canLoadMore;

  const section = select('#articles');
  while (section.firstChild)
    section.removeChild(section.firstChild);

  if (html === "")
    section.appendChild(notFoundMessage());
  else
    section.insertAdjacentHTML('afterbegin', html);

  loadMoreButton = select('#load-more');
  if (loadMoreButton.style.display === "none") {
    if (canLoadMore) loadMoreButton.style.display = "block";
  } else {
    if (!canLoadMore) loadMoreButton.style.display = "none";
  }
}

const getFilterUrl = (offset = 0) => {
  const type = select('input[name="filterType"]:checked').id;
  let url = `/api/article/filter?type=${type}&offset=${offset}&limit=${ARTICLE_FILTER_LIMIT}`;

  const tags = Array.from(select("#filterTags").selectedOptions)
    .map((elem) => parseInt(elem.value));

  if (minDate && minDate !== "")
    url += `&minDate=${minDate}`;

  if (maxDate && maxDate !== "")
    url += `&maxDate=${maxDate}`;

  for (let tag of tags)
    url += `&tags[]=${tag}`;

  return url;
}

const notFoundMessage = () => {
  const msg = document.createElement('div');
  msg.classList.add('alert', 'alert-custom', 'mb-4', 'text-center');
  msg.setAttribute('role', 'alert');

  const h3 = document.createElement('h3');
  h3.classList.add('my-3');
  h3.innerText = "No articles found. Please review your criteria";
  msg.appendChild(h3);

  return msg;
}
