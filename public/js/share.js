const showSocials = () => {
    const currentURL = window.location.href;

    const url = `/api/share_socials`;
    sendAjaxRequest('post', url, {url: currentURL}, handleShowSocials(currentURL));
}

const handleShowSocials = (currentURL) => function(){
    const socialLinks = JSON.parse(this.responseText).links;

    select('#fbIcon').href = socialLinks.facebook;
    select('#twitterIcon').href = socialLinks.twitter;
    select('#linkedInIcon').href = socialLinks.linkedin;
    select('#redditIcon').href = socialLinks.reddit;
    select('#shareLinkInput').value = currentURL;

    select('#socialsPopup').classList.remove('d-none');
}

const hideSocials = () => {
    const popup = select('#socialsPopup');
    popup.classList.add('d-none');
}

