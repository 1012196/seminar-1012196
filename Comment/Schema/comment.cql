CREATE KEYSPACE bcore WITH REPLICATION =
{ 'class' : 'SimpleStrategy', 'replication_factor' : 1 };

//Use the keyspace
USE bcore;


CREATE TABLE comment_by_article (
    article_id int,
    comment_id timeuuid,
    user_id int,
    username text,
    comment_text text,
    user_avatar text,
    user_full_name text,
    PRIMARY KEY (article_id, comment_id, user_id)
);
// SELECT * FROM comment_by_article WHERE article_id = $article_id;

CREATE TABLE comment_not_approve (
    article_id int,
    comment_id timeuuid,
    user_id int,
    username text,
    comment_text text,
    user_avatar text,
    user_full_name text,
    PRIMARY KEY (article_id, comment_id, user_id)
);
// SELECT * FROM comment_by_article WHERE article_id = $article_id;

CREATE TABLE comment_by_user (
    user_id int,
    comment_id timeuuid,
    username text,
    comment_text text,
    user_avatar text,
    user_full_name text,
    PRIMARY KEY (user_id, comment_id)
);
// SELECT * FROM comment_by_user WHERE user_id = $user_id;

CREATE TABLE reply_by_comment (
    article_id int,
    parent_id timeuuid,
    user_id int,
    reply_id timeuuid,
    reply_text text,
    username text,
    user_avatar text,
    user_full_name text,
    PRIMARY KEY (article_id, parent_id, reply_id, user_id)
);
// SELECT * FROM reply_by_comment WHERE article_id = $article_id;

CREATE TABLE reply_not_approve (
    article_id int,
    parent_id timeuuid,
    user_id int,
    reply_id timeuuid,
    reply_text text,
    username text,
    user_avatar text,
    user_full_name text,
    PRIMARY KEY (article_id, parent_id, reply_id, user_id)
);
// SELECT * FROM reply_by_comment WHERE article_id = $article_id;

CREATE TABLE like_by_article (
    article_id int,
    comment_id timeuuid,
    user_id int,
    username text,
    user_full_name text,
    PRIMARY KEY (article_id, comment_id, user_id)
);
// SELECT * FROM like_by_article WHERE article_id = $article_id;

CREATE TABLE like_by_owner (
    owner_id int,
    comment_id timeuuid,
    article_id int,
    user_id int,
    username text,
    user_full_name text,
    PRIMARY KEY (owner_id, comment_id, article_id)
);
// SELECT * FROM like_by_user WHERE owner_id = $owner_id ORDER BY comment_id DESC;

CREATE TABLE count_reply_by_comment (
    article_id int,
    comment_id timeuuid,
    counter_reply counter,
    PRIMARY KEY (article_id, comment_id)
);
// SELECT * FROM count_reply_by_comment WHERE article_id = $article_id AND comment_id = $comment_id;

