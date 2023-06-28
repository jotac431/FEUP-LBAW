const userId = select("meta[name='user-id']").getAttribute('content');

const articleLikeNotification = (data) => {
    const { username, avatar, user_id, article_id, article_title } = data;

    const header = "New like in one of your articles";
    const body = `
        <a href="/user/${user_id}">${username}</a> 
        liked your article 
        <a href="/article/${article_id}">${article_title}</a>
    `;

    createNotification(header, body);
}

const commentLikeNotification = (data) => {
    const { username, avatar, user_id, article_id, comment_body, article_title } = data;

    const header = "New like in one of your comments";

    const trimmedBody = comment_body.length > 100 ?
        comment_body.substring(0, 100) + "..."
        : comment_body;

    const body = `
        <a href="/user/${user_id}">${username}</a> 
        liked your comment in 
        <a href="/article/${article_id}">${article_title}</a>
        <br><i class="text-light">${trimmedBody}</i>
    `;

    createNotification(header, body);
}

const commentNotification = (data) => {
    const { username, avatar, user_id, article_id, article_title, comment_body } = data;

    const header = "New comment in one of your articles";

    const trimmedBody = comment_body.length > 100 ?
        comment_body.substring(0, 100) + "..."
        : comment_body;

    const body = `
        <a href="/user/${user_id}">${username}</a> 
        commented in your article 
        <a href="/article/${article_id}">${article_title}</a>
        <br><i class="text-light">${trimmedBody}</i>
    `;

    createNotification(header, body);
}

const replyNotification = (data) => {
    const { username, avatar, user_id, article_id, article_title, comment_body } = data;

    const header = "New reply to one of your comments";

    const trimmedBody = comment_body.length > 100 ?
        comment_body.substring(0, 100) + "..."
        : comment_body;

    const body = `
        <a href="/user/${user_id}">${username}</a> 
        replied to your comment in 
        <a href="/article/${article_id}">${article_title}</a>
        <br><i class="text-light">${trimmedBody}</i>
    `;

    createNotification(header, body);
}

if (userId) {

    const pusher = new Pusher('26ec0b6978ee54d90d56', {
        encrypted: true,
        cluster: 'eu'
    });

    const notificationChannel = pusher.subscribe(`notifications.${userId}`);

    notificationChannel.bind('article-like', articleLikeNotification);
    notificationChannel.bind('comment-like', commentLikeNotification);
    notificationChannel.bind('comment', commentNotification);
    notificationChannel.bind('comment-reply', replyNotification);
}
