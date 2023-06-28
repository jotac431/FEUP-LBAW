-----------------------------------------
-- Drop old schema
-----------------------------------------
DROP SCHEMA IF EXISTS lbaw2111 CASCADE;
CREATE SCHEMA lbaw2111;

SET search_path TO lbaw2111;

-----------------------------------------
-- DOMAINS
-----------------------------------------

CREATE DOMAIN VALID_EMAIL AS TEXT CHECK(VALUE LIKE '_%@_%.__%');

-----------------------------------------
-- TYPES
-----------------------------------------

CREATE TYPE PROPOSED_TAG_STATES AS ENUM ('PENDING', 'ACCEPTED', 'REJECTED');
CREATE TYPE NOTIFICATION_TYPE AS ENUM ('MESSAGE', 'FEEDBACK', 'COMMENT');

-----------------------------------------
-- Tables
-----------------------------------------

CREATE TABLE country(
  id SERIAL PRIMARY KEY,
  code TEXT NOT NULL UNIQUE,
  name TEXT NOT NULL UNIQUE
);

-----------------------------------------

CREATE TABLE authenticated_user(
  id SERIAL PRIMARY KEY, 
  name TEXT NOT NULL, 
  email VALID_EMAIL UNIQUE, 
  birth_date TIMESTAMP NOT NULL CHECK (CURRENT_TIMESTAMP >= birth_date),
  is_admin BOOLEAN DEFAULT false,
  description TEXT, 
  password TEXT NOT NULL, 
  avatar TEXT, 
  city TEXT, 
  is_suspended BOOLEAN NOT NULL DEFAULT FALSE,
  reputation INTEGER NOT NULL DEFAULT 0,
  country_id INTEGER NOT NULL REFERENCES country(id) ON DELETE CASCADE ON UPDATE CASCADE,
  remember_token TEXT -- Laravel's remember me functionality
);


-----------------------------------------

CREATE TABLE suspension(
  id SERIAL PRIMARY KEY,
  reason TEXT NOT NULL,
  start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  end_time TIMESTAMP NOT NULL CHECK (end_time >= start_time),
  admin_id INTEGER REFERENCES authenticated_user(id) ON DELETE SET NULL ON UPDATE CASCADE,
  user_id INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE ON UPDATE CASCADE
  CONSTRAINT diff_entities CHECK (admin_id != user_id)
);

-----------------------------------------

CREATE TABLE report(
  id SERIAL PRIMARY KEY, 
  reason TEXT NOT NULL, 
  reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
  is_closed BOOLEAN DEFAULT false, 
  reported_id INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE ON UPDATE CASCADE, 
  reporter_id INTEGER REFERENCES authenticated_user(id) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT different_ids CHECK (reporter_id != reported_id)
);

-----------------------------------------

CREATE TABLE tag(
  id SERIAL PRIMARY KEY,
  name TEXT NOT NULL UNIQUE,
  proposed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  state PROPOSED_TAG_STATES NOT NULL DEFAULT 'PENDING',
  user_id INTEGER REFERENCES authenticated_user(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-----------------------------------------

CREATE TABLE area_of_expertise(
  user_id INTEGER REFERENCES authenticated_user(id) ON DELETE CASCADE ON UPDATE CASCADE,
  tag_id  INTEGER REFERENCES tag(id) ON DELETE CASCADE ON UPDATE CASCADE,
  reputation INTEGER NOT NULL,
  PRIMARY KEY (user_id, tag_id)
);

-----------------------------------------

CREATE TABLE favorite_tag(
  user_id INTEGER REFERENCES authenticated_user(id) ON DELETE CASCADE ON UPDATE CASCADE,
  tag_id  INTEGER REFERENCES tag(id) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY (user_id, tag_id)
);


-----------------------------------------

CREATE TABLE message(
  id SERIAL PRIMARY KEY,
  body TEXT NOT NULL,
  published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  sender_id INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE ON UPDATE CASCADE,
  receiver_id INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE ON UPDATE CASCADE,
  is_read BOOLEAN DEFAULT false
);

-----------------------------------------

CREATE TABLE follow(
  follower_id INTEGER REFERENCES authenticated_user(id) ON DELETE CASCADE ON UPDATE CASCADE,
  followed_id INTEGER REFERENCES authenticated_user(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT own_follows CHECK (follower_id != followed_id),
  PRIMARY KEY(follower_id, followed_id)
);

-----------------------------------------

CREATE TABLE content(
  id SERIAL PRIMARY KEY,
  body TEXT NOT NULL,
  published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  is_edited BOOLEAN DEFAULT false,
  likes INTEGER DEFAULT 0 CHECK (likes >= 0),
  dislikes INTEGER DEFAULT 0 CHECK (dislikes >= 0),
  author_id INTEGER REFERENCES authenticated_user(id) ON DELETE SET NULL ON UPDATE CASCADE
);

-----------------------------------------

CREATE TABLE article(
  content_id INTEGER PRIMARY KEY REFERENCES content(id) ON DELETE CASCADE ON UPDATE CASCADE, 
  title TEXT NOT NULL, 
  thumbnail TEXT
);

-----------------------------------------

CREATE TABLE comment(
  content_id INTEGER PRIMARY KEY REFERENCES content(id) ON DELETE CASCADE ON UPDATE CASCADE,
  article_id INTEGER NOT NULL REFERENCES article(content_id) ON DELETE CASCADE ON UPDATE CASCADE,
  parent_comment_id INTEGER REFERENCES comment(content_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-----------------------------------------

CREATE TABLE feedback(
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES authenticated_user(id) ON DELETE SET NULL ON UPDATE CASCADE, 
  content_id INTEGER NOT NULL REFERENCES content(id) ON DELETE CASCADE ON UPDATE CASCADE, 
  is_like BOOLEAN NOT NULL
);

-----------------------------------------

CREATE TABLE article_tag(
  article_id INTEGER REFERENCES article(content_id) ON DELETE CASCADE ON UPDATE CASCADE,
  tag_id INTEGER REFERENCES tag(id) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY(article_id, tag_id)
);


-----------------------------------------

CREATE TABLE notification(
  id SERIAL PRIMARY KEY,
  receiver_id INTEGER NOT NULL REFERENCES authenticated_user(id) ON DELETE CASCADE ON UPDATE CASCADE,
  date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  is_read BOOLEAN DEFAULT false,
  msg INTEGER REFERENCES message(id) ON DELETE CASCADE ON UPDATE CASCADE,
  fb_giver INTEGER REFERENCES authenticated_user(id) ON DELETE CASCADE ON UPDATE CASCADE,
  rated_content INTEGER REFERENCES content(id) ON DELETE CASCADE ON UPDATE CASCADE,
  new_comment INTEGER REFERENCES comment(content_id) ON DELETE CASCADE ON UPDATE CASCADE,
  type NOTIFICATION_TYPE NOT NULL
);


-----------------------------------------
-- PERFORMANCE INDICES
-----------------------------------------

CREATE INDEX content_author ON content USING hash (author_id);

CREATE INDEX user_messages ON message USING btree (receiver_id, sender_id);

CREATE INDEX notification_receiver ON notification USING hash (receiver_id);


-----------------------------------------
-- FULL-TEXT SEARCH INDICES
-----------------------------------------

ALTER TABLE article ADD COLUMN tsvectors TSVECTOR;

CREATE FUNCTION article_search_update() RETURNS TRIGGER AS $$
DECLARE new_body text = (select body from content where id = NEW.content_id);
DECLARE old_body text = (select body from content where id = OLD.content_id);
BEGIN
  IF TG_OP = 'INSERT' THEN
    NEW.tsvectors = (
      setweight(to_tsvector('english', NEW.title), 'A') ||
      setweight(to_tsvector('english', new_body), 'B')
    );
  END IF;

  IF TG_OP = 'UPDATE' THEN
      IF (NEW.title <> OLD.title OR new_body <> old_body) THEN
        NEW.tsvectors = (
          setweight(to_tsvector('english', NEW.title), 'A') ||
          setweight(to_tsvector('english', new_body), 'B')
        );
      END IF;
  END IF;

  RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER article_search_update
  BEFORE INSERT OR UPDATE ON article
  FOR EACH ROW
  EXECUTE PROCEDURE article_search_update();

CREATE INDEX article_search ON article USING GIST (tsvectors);

-----------------------------------------

ALTER TABLE authenticated_user ADD COLUMN tsvectors TSVECTOR;

CREATE FUNCTION user_search_update() RETURNS TRIGGER AS $$
BEGIN
  IF TG_OP = 'INSERT' THEN
    NEW.tsvectors = (
      setweight(to_tsvector('english', NEW.name), 'A') ||
      setweight(to_tsvector('english', NEW.email), 'B')
    );
  END IF;

  IF TG_OP = 'UPDATE' THEN
      IF (NEW.name <> OLD.name OR NEW.email <> OLD.email) THEN
        NEW.tsvectors = (
          setweight(to_tsvector('english', NEW.name), 'A') ||
          setweight(to_tsvector('english', NEW.email), 'B')
        );
      END IF;
  END IF;

  RETURN NEW;
END $$
LANGUAGE plpgsql;

CREATE TRIGGER user_search_update
  BEFORE INSERT OR UPDATE ON authenticated_user
  FOR EACH ROW
  EXECUTE PROCEDURE user_search_update();

CREATE INDEX user_search ON authenticated_user USING GIST (tsvectors);

-----------------------------------------
-- TRIGGERS
-----------------------------------------

/*
Trigger to update likes/dislikes of a content when feedback is given,
creates a notification on that feedback and updates user reputation, as well as its areas of expertise.
*/
CREATE FUNCTION feedback_content() RETURNS TRIGGER AS
$BODY$
DECLARE author_id authenticated_user.id%type = (
  SELECT author_id FROM content INNER JOIN authenticated_user ON (content.author_id = authenticated_user.id)
  WHERE content.id = NEW.content_id
);
DECLARE feedback_value INTEGER = 1;
BEGIN
    IF (NOT NEW.is_like)
        THEN feedback_value = -1;
    END IF;

    IF (NEW.is_like) THEN
        UPDATE content SET likes = likes + 1 WHERE id = NEW.content_id;

        IF (author_id IS NOT NULL) THEN
          INSERT INTO notification(receiver_id, is_read, msg, fb_giver, rated_content, new_comment, type)
          VALUES (author_id, FALSE, NULL, NEW.user_id, NEW.content_id, NULL, 'FEEDBACK');
        END IF;
    ELSE 
        UPDATE content SET dislikes = dislikes + 1 WHERE id = NEW.content_id;
    END IF;
    
    UPDATE authenticated_user SET reputation = reputation + feedback_value
    WHERE id = author_id;

    UPDATE area_of_expertise SET reputation = reputation + feedback_value
    WHERE 
        user_id = author_id AND 
        tag_id IN (
			SELECT tag_id FROM article_tag
    		WHERE article_id=NEW.content_id
		);

    RETURN NULL;
END
$BODY$
LANGUAGE plpgsql;

CREATE TRIGGER feedback_content
    AFTER INSERT ON feedback
    FOR EACH ROW
    EXECUTE PROCEDURE feedback_content();

-----------------------------------------

-- Trigger to remove like/dislike of a content when feedback on it is removed and to update authenticated user reputation, as well as its areas of expertise
CREATE FUNCTION remove_feedback() RETURNS TRIGGER AS
$BODY$
DECLARE author_id authenticated_user.id%type = (SELECT author_id FROM content INNER JOIN authenticated_user ON (content.author_id = authenticated_user.id) WHERE content.id = OLD.content_id);
DECLARE feedback_value INTEGER = -1;
BEGIN
    IF (NOT OLD.is_like)
        THEN feedback_value = 1;
    END IF;

    IF (OLD.is_like) THEN
        UPDATE content SET likes = likes - 1 WHERE id = OLD.content_id;
    ELSE 
        UPDATE content SET dislikes = dislikes - 1 WHERE id = OLD.content_id;
    END IF;
    
    UPDATE authenticated_user SET reputation = reputation + feedback_value
    WHERE id = author_id;

    UPDATE area_of_expertise SET reputation = reputation + feedback_value
    WHERE 
        user_id = author_id AND 
        tag_id IN (
			SELECT tag_id FROM article_tag
    		WHERE article_id=OLD.content_id
		);

    RETURN NULL;
END
$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER remove_feedback
    AFTER DELETE ON feedback
    FOR EACH ROW
    EXECUTE PROCEDURE remove_feedback();

-----------------------------------------

-- Trigger to prevent users from liking or disliking his own content (articles or comments)
CREATE FUNCTION check_feedback() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF (NEW.user_id in (
        SELECT content.author_id 
        FROM content 
        WHERE content.id = NEW.content_id)) THEN
            RAISE EXCEPTION 'You cannot give feedback on your own content';
    END IF;
    RETURN NEW;
END;
$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER check_feedback
    BEFORE INSERT ON feedback
    FOR EACH ROW
    EXECUTE PROCEDURE check_feedback();

-----------------------------------------

-- Trigger to add notification when a message is sent form an user to another or to remove, in case of being read
CREATE FUNCTION message_sent_notification() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF (NEW.is_read) THEN
        DELETE FROM notification WHERE msg = NEW.id;
    ELSE 
        INSERT INTO notification(receiver_id, is_read, msg, fb_giver, rated_content, new_comment, type) 
            VALUES (NEW.receiver_id, FALSE, NEW.id, NULL, NULL, NULL, 'MESSAGE');
    END IF;
    RETURN NULL;
END
$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER message_sent_notification
    AFTER INSERT ON message
    FOR EACH ROW
    EXECUTE PROCEDURE message_sent_notification();


-----------------------------------------

/*
Trigger to delete all the information about an article that was deleted
it just needs to delete the content represented by that article 
since its that deletion is cascaded to the comments and other elements of the article
*/
CREATE FUNCTION delete_article() RETURNS TRIGGER AS
$BODY$
BEGIN 
    DELETE FROM content WHERE content.id = OLD.content_id;
    RETURN OLD;
END
$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER delete_article
    AFTER DELETE ON article
    FOR EACH ROW
    EXECUTE PROCEDURE delete_article();


-----------------------------------------

/*
Trigger to delete the respective content of a comment when a comment
is deleted. */
CREATE FUNCTION delete_comment() RETURNS TRIGGER AS
$BODY$
BEGIN 
    DELETE FROM content WHERE content.id = OLD.content_id;
    RETURN OLD;
END
$BODY$

LANGUAGE plpgsql;


CREATE TRIGGER delete_comment
    AFTER DELETE ON comment
    FOR EACH ROW
    EXECUTE PROCEDURE delete_comment();

-----------------------------------------

-- Trigger to prevent an article from having an unaccepted tag or more than 3 tags
CREATE FUNCTION add_article_tag_check() RETURNS TRIGGER AS
$BODY$
BEGIN
    IF ((SELECT state FROM tag WHERE NEW.tag_id = tag.id) <> 'ACCEPTED')
    THEN
        RAISE EXCEPTION 'You cannot associate an article to an Unaccepted tag';
    END IF;
    
    IF ((SELECT COUNT(*) FROM article_tag WHERE article_id = NEW.article_id)) >= 3
    THEN
        RAISE EXCEPTION 'You cannot associate anymore tags to this article';
    END IF;
    RETURN NEW;
END
$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER add_article_tag_check
    BEFORE INSERT ON article_tag
    FOR EACH ROW
    EXECUTE PROCEDURE add_article_tag_check();

-----------------------------------------

/*
Trigger to create an area of expertise when an article_tag is inserted,
in case the author the article doesn’t have it yet
*/
CREATE FUNCTION create_area_expertise() RETURNS TRIGGER AS
$BODY$
DECLARE author_id INTEGER = (
    SELECT author_id FROM content WHERE id = NEW.article_id
);
BEGIN
    IF NEW.tag_id NOT IN (
        SELECT tag_id FROM area_of_expertise where user_id = author_id
    )
    THEN
        INSERT INTO area_of_expertise VALUES(author_id, NEW.tag_id, 0);
	END IF;
	RETURN NULL;
END
$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER create_area_expertise
    AFTER INSERT ON article_tag
    FOR EACH ROW
    EXECUTE PROCEDURE create_area_expertise();

-----------------------------------------

-- Triggers to update the *is_edited* flag when a content's body or an article's title is updated
CREATE FUNCTION set_content_is_edited() RETURNS TRIGGER AS
$BODY$
BEGIN
    UPDATE content SET is_edited = TRUE
    WHERE id = NEW.id;
	RETURN NULL;
END
$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER set_content_is_edited
    AFTER UPDATE ON content
    FOR EACH ROW
    WHEN (OLD.body IS DISTINCT FROM NEW.body)
    EXECUTE PROCEDURE set_content_is_edited();

-----------------------------------------

-- Trigger to mark the content as edited when an article's title is changed
CREATE FUNCTION set_article_is_edited() RETURNS TRIGGER AS
$BODY$
BEGIN
    UPDATE content SET is_edited = TRUE
    WHERE id = NEW.content_id;
	RETURN NULL;
END
$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER set_article_is_edited
    AFTER UPDATE ON article
    FOR EACH ROW
    WHEN (OLD.title IS DISTINCT FROM NEW.title)
    EXECUTE PROCEDURE set_article_is_edited();
  
-----------------------------------------

-- Trigger to put authenticated_user flag to true if a suspension on him is created
CREATE FUNCTION is_suspended_flag_true() RETURNS TRIGGER AS
$BODY$
BEGIN
    UPDATE authenticated_user SET is_suspended = true
    WHERE id = NEW.user_id;
	RETURN NEW;
END
$BODY$

LANGUAGE plpgsql;

CREATE TRIGGER is_suspended_flag_true
    AFTER INSERT ON suspension
    FOR EACH ROW
    EXECUTE PROCEDURE is_suspended_flag_true();


-----------------------------------------

-- Trigger to create a notification when a comment is created

CREATE FUNCTION create_comment_notification() RETURNS TRIGGER AS
$BODY$
DECLARE article_author INTEGER = (
  SELECT author_id FROM content WHERE id = NEW.article_id
);
DECLARE parent_author INTEGER = (
  SELECT author_id FROM content WHERE id = NEW.parent_comment_id
);
DECLARE commenter INTEGER = (
  SELECT author_id FROM content WHERE id = NEW.content_id
);
BEGIN
  IF NEW.parent_comment_id IS NOT NULL THEN
    IF parent_author IS NOT NULL AND parent_author <> commenter THEN
      INSERT INTO notification(receiver_id, is_read, msg, fb_giver, rated_content, new_comment, type) 
        VALUES (parent_author, FALSE, NULL, NULL, NULL, NEW.content_id, 'COMMENT');
    END IF;
  ELSE IF article_author IS NOT NULL AND article_author <> commenter THEN
    INSERT INTO notification(receiver_id, is_read, msg, fb_giver, rated_content, new_comment, type) 
        VALUES (article_author, FALSE, NULL, NULL, NULL, NEW.content_id, 'COMMENT');
  END IF;
  END IF;
  RETURN NULL;
END
$BODY$

LANGUAGE plpgsql;
CREATE TRIGGER create_comment_notification
    AFTER INSERT ON comment
    FOR EACH ROW
    EXECUTE PROCEDURE create_comment_notification();
