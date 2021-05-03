
DROP DATABASE IF EXISTS minddit;
CREATE DATABASE IF NOT EXISTS minddit;
USE minddit;

DELIMITER $$
DROP PROCEDURE IF EXISTS insert_awards$$
CREATE PROCEDURE insert_awards (IN userID INT, IN awardType ENUM('gold','silver'), IN contentID INT, IN contentType ENUM('post','comment'), OUT status TINYINT(1))  BEGIN
	IF contentType = 'post' THEN
			IF NOT EXISTS (SELECT * FROM awards, post_awards WHERE awards.user_id = userID AND post_awards.post_id = contentID AND post_awards.award_id = awards.id and awards.award = awardType) THEN
			START TRANSACTION;
			INSERT INTO awards (user_id, award) VALUES (userID, awardType);
			INSERT INTO post_awards (award_id, post_id) VALUES (LAST_INSERT_ID(), contentID);
			SET status = 1;
			COMMIT;
			ELSE
				SET status = 0;
			END IF;
	ELSEIF contentType = 'comment' THEN
			IF NOT EXISTS (SELECT * FROM awards, comment_awards WHERE awards.user_id = userID AND comment_awards.comment_id = contentID AND comment_awards.award_id = awards.id and awards.award = awardType) THEN
			START TRANSACTION;
			INSERT INTO awards (user_id, award) VALUES (userID, awardType);
			INSERT INTO comment_awards (award_id, comment_id) VALUES (LAST_INSERT_ID(), contentID);
			SET status = 1;
			COMMIT;
			ELSE
				SET status = 0;
			END IF;
	END IF;
END$$

DROP PROCEDURE IF EXISTS insert_likes$$
CREATE PROCEDURE insert_likes (userID INT, likeType ENUM('upvote','downvote'), contentID INT, contentType ENUM('post','comment'))  BEGIN
	IF contentType = 'post' THEN
 			IF NOT EXISTS (SELECT * FROM likes, post_likes WHERE likes.user_id = userID and post_likes.post_id = contentID and post_likes.like_id = likes.id) THEN
			START TRANSACTION;
			INSERT INTO likes (user_id, vote) VALUES (userID, likeType);
			INSERT INTO post_likes (like_id, post_id) VALUES (LAST_INSERT_ID(), contentID);
			COMMIT;
            ELSE
            	UPDATE likes SET vote = likeType where id = (select id from (select l.id from likes l inner join post_likes pl on pl.post_id = contentID and pl.like_id = l.id where l.user_id = userID) as t);
			END IF;
	ELSEIF contentType = 'comment' THEN
			IF NOT EXISTS (SELECT * FROM likes, comment_likes WHERE likes.user_id = userID AND comment_likes.comment_id = contentID and  comment_likes.like_id = likes.id) THEN
			START TRANSACTION;
			INSERT INTO likes (user_id, vote) VALUES (userID, likeType);
			INSERT INTO comment_likes (like_id, comment_id) VALUES (LAST_INSERT_ID(), contentID);
			COMMIT;
            ELSE
            	UPDATE likes SET vote = likeType where id = (select id from (select l.id from likes l inner join comment_likes cl on cl.comment_id = contentID and cl.like_id = l.id where l.user_id = userID) as t);
			END IF;
	END IF;
END$$

DELIMITER ;

DROP TABLE IF EXISTS awards;
CREATE TABLE awards (
  id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  award enum('gold','silver') NOT NULL
);


INSERT INTO awards (id, user_id, award) VALUES
(1, 2, 'silver'),
(2, 2, 'gold'),
(3, 2, 'gold'),
(4, 2, 'silver'),
(5, 2, 'silver'),
(6, 3, 'gold'),
(7, 3, 'silver'),
(8, 3, 'silver'),
(9, 3, 'silver'),
(10, 3, 'gold');

DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
  id int(11) NOT NULL,
  parent_id int(11) DEFAULT NULL,
  user_id int(11) DEFAULT NULL,
  post_id int(11) DEFAULT NULL,
  body longtext,
  created_at int(30) DEFAULT NULL,
  updated_at int(30) DEFAULT NULL
);

INSERT INTO comments (id, parent_id, user_id, post_id, body, created_at, updated_at) VALUES
(1, NULL, 2, 1, 'WOOOWW!', 1578523520, 1578593316),
(2, 1, 2, 1, 'Cute', 1578523528, 1578597640),
(3, 2, 2, 1, 'i agree', 1578597652, 1578597652),
(4, NULL, 1, 2, 'nice', 1578614367, 1578614367),
(5, NULL, 2, 3, 'wow\n', 1578682289, 1578682289),
(6, 4, 2, 2, 'nice comment', 1578692985, 1578692985);

-- --------------------------------------------------------

DROP TABLE IF EXISTS comment_awards;
CREATE TABLE comment_awards (
  award_id int(11) NOT NULL,
  comment_id int(11) NOT NULL
);

--
-- Dumping data for table comment_awards
--

INSERT INTO comment_awards (award_id, comment_id) VALUES
(8, 1);

DROP TABLE IF EXISTS comment_likes;
CREATE TABLE comment_likes (
  like_id int(11) DEFAULT NULL,
  comment_id int(11) DEFAULT NULL
);

INSERT INTO comment_likes (like_id, comment_id) VALUES
(5, 4),
(8, 5),
(9, 1),
(10, 2);

DROP TABLE IF EXISTS details;
CREATE TABLE details (
  user_id int(11) DEFAULT NULL,
  profile_picture text,
  gender enum('male','female','other') DEFAULT NULL,
  country varchar(30) DEFAULT NULL,
  age int(11) DEFAULT NULL,
  description text
);

INSERT INTO details (user_id, profile_picture, gender, country, age, description) VALUES
(1, 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/a0/a03a37a6472fbd7243dcec2ac9891c4c847881e6_full.jpg', 'male', 'United Kingdom', 18, 'Welcome to my profile. I am the admin of this site'),
(2, 'http://www.gamasutra.com/db_area/images/news/2018/Jun/320213/supermario64thumb1.jpg', 'male', 'United Kingdom', 18, 'Hello my people!'),
(3, 'https://58southmoltonstreet.co.uk/wp-content/uploads/58PhotographyLogo_Guide.png', 'male', 'United Kingdom', 18, 'Welcome to my profile!');


DROP TABLE IF EXISTS likes;
CREATE TABLE likes (
  id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  vote enum('upvote','downvote') DEFAULT NULL
);

INSERT INTO likes (id, user_id, vote) VALUES
(1, 1, 'upvote'),
(2, 1, 'upvote'),
(3, 1, 'upvote'),
(4, 1, 'downvote'),
(5, 2, 'upvote'),
(6, 2, 'upvote'),
(7, 2, 'downvote'),
(8, 2, 'upvote'),
(9, 3, 'upvote'),
(10, 3, 'upvote'),
(11, 1, 'upvote'),
(12, 3, 'upvote'),
(13, 2, 'upvote');

DROP TABLE IF EXISTS posts;
CREATE TABLE posts (
  id int(11) NOT NULL,
  title varchar(255) DEFAULT NULL,
  user_id int(11) NOT NULL,
  is_image tinyint(1) DEFAULT NULL,
  body text,
  created_at int(30) DEFAULT NULL,
  updated_at int(30) DEFAULT NULL,
  subreddit_name varchar(20) NOT NULL
);

INSERT INTO posts (id, title, user_id, is_image, body, created_at, updated_at, subreddit_name) VALUES
(1, 'Butterfly', 2, 1, 'https://live.staticflickr.com/4561/38054606355_26429c884f_b.jpg', 1578522766, 1578522768, 'pics'),
(2, 'Cool website', 2, 0, 'wow', 1578601411, 1578682142, 'random'),
(3, 'lorem ipsum', 2, 0, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ac ut consequat semper viverra nam. Pretium aenean pharetra magna ac placerat. Urna id volutpat lacus laoreet non curabitur gravida arcu ac. Sagittis id consectetur purus ut faucibus pulvinar elementum. Vel risus commodo viverra maecenas accumsan lacus. Curabitur gravida arcu ac tortor dignissim convallis aenean et tortor. Ut venenatis tellus in metus vulputate eu. Ut eu sem integer vitae justo eget magna fermentum. Vitae sapien pellentesque habitant morbi tristique senectus et netus. Pharetra et ultrices neque ornare aenean euismod elementum nisi quis. Dolor purus non enim praesent elementum.\r\n\r\nUrna nunc id cursus metus aliquam eleifend mi. Vel pharetra vel turpis nunc. Ornare quam viverra orci sagittis eu. Sed elementum tempus egestas sed sed risus pretium quam vulputate. Urna duis convallis convallis tellus id interdum velit laoreet. Mi eget mauris pharetra et ultrices neque ornare aenean euismod. Eget nulla facilisi etiam dignissim. Bibendum enim facilisis gravida neque convallis. In eu mi bibendum neque egestas congue quisque egestas. Volutpat ac tincidunt vitae semper quis lectus nulla at volutpat. Libero enim sed faucibus turpis in eu mi. Sed sed risus pretium quam vulputate dignissim. Vestibulum sed arcu non odio euismod. Amet mauris commodo quis imperdiet. Porta lorem mollis aliquam ut porttitor. Enim sed faucibus turpis in eu.\r\n\r\nSed viverra ipsum nunc aliquet bibendum. Cras semper auctor neque vitae tempus quam. Tristique et egestas quis ipsum suspendisse. Eget nullam non nisi est sit amet. Egestas quis ipsum suspendisse ultrices gravida dictum fusce ut. Tristique sollicitudin nibh sit amet commodo nulla facilisi. Lorem dolor sed viverra ipsum nunc aliquet. Tempor nec feugiat nisl pretium fusce id velit ut. Odio euismod lacinia at quis risus sed vulputate. Quis enim lobortis scelerisque fermentum dui faucibus in ornare. Quam viverra orci sagittis eu. Eget est lorem ipsum dolor sit amet consectetur adipiscing. Venenatis cras sed felis eget velit aliquet sagittis id consectetur. Tellus cras adipiscing enim eu. Egestas pretium aenean pharetra magna ac placerat vestibulum lectus.\r\n\r\nVitae congue eu consequat ac felis donec et. Libero enim sed faucibus turpis in eu. Aliquet nibh praesent tristique magna sit amet purus. Nunc pulvinar sapien et ligula ullamcorper malesuada proin libero nunc. Semper feugiat nibh sed pulvinar proin gravida. Semper eget duis at tellus. Blandit volutpat maecenas volutpat blandit aliquam etiam erat velit scelerisque. Convallis convallis tellus id interdum. Nunc lobortis mattis aliquam faucibus purus. Purus viverra accumsan in nisl nisi scelerisque eu ultrices vitae. Nisl pretium fusce id velit ut tortor. Proin fermentum leo vel orci porta non pulvinar.\r\n\r\nSed cras ornare arcu dui vivamus arcu. Natoque penatibus et magnis dis parturient montes nascetur. Eget gravida cum sociis natoque penatibus et magnis dis parturient. Eleifend donec pretium vulputate sapien nec sagittis aliquam. Tincidunt praesent semper feugiat nibh sed. Mattis pellentesque id nibh tortor id. Faucibus purus in massa tempor. Augue eget arcu dictum varius duis. Aliquet enim tortor at auctor urna nunc id cursus metus. Mi quis hendrerit dolor magna eget est. Et malesuada fames ac turpis. Habitasse platea dictumst quisque sagittis purus sit amet volutpat. Sit amet facilisis magna etiam tempor. Sed faucibus turpis in eu mi bibendum neque. Quam viverra orci sagittis eu volutpat odio facilisis. Pulvinar etiam non quam lacus suspendisse faucibus interdum. Nisl suscipit adipiscing bibendum est ultricies integer. Magna fermentum iaculis eu non diam phasellus vestibulum lorem. Iaculis eu non diam phasellus vestibulum lorem.', 1578604219, 1578604219, 'random'),
(4, 'nice', 1, 0, 'nice', 1578618560, 1578618560, 'random'),
(5, 'Final Test??', 2, 0, 'computer science lol', 1578693357, 1578693357, 'computerscience'),
(6, 'computer', 2, 1, 'https://cdn.britannica.com/77/170477-050-1C747EE3/Laptop-computer.jpg', 1578693402, 1578693402, 'computerscience'),
(7, 'Nice', 1, 0, 'wow', 1578780170, 1578780170, 'computerscience'),
(8, 'nice 2', 1, 0, 'the sequel', 1578780209, 1578780209, 'random'),
(9, 'new 3', 1, 0, 'third times the charm', 1578780228, 1578780228, 'random'),
(10, 'The most liked post', 1, 0, 'Please like this post', 1578893134, 1578893134, 'random'),
(11, 'Mario', 3, 1, 'http://www.gamasutra.com/db_area/images/news/2018/Jun/320213/supermario64thumb1.jpg', 1578895049, 1578895049, 'pics');

DROP TABLE IF EXISTS post_awards;
CREATE TABLE post_awards (
  award_id int(11) NOT NULL,
  post_id int(11) NOT NULL
);

INSERT INTO post_awards (award_id, post_id) VALUES
(1, 1),
(3, 1),
(2, 2),
(6, 2),
(7, 2),
(4, 3),
(9, 3),
(10, 3),
(5, 5);

DROP TABLE IF EXISTS post_likes;
CREATE TABLE post_likes (
  like_id int(11) NOT NULL,
  post_id int(11) DEFAULT NULL
);

INSERT INTO post_likes (like_id, post_id) VALUES
(1, 1),
(6, 1),
(2, 2),
(7, 2),
(3, 3),
(4, 4),
(11, 10),
(12, 10),
(13, 10);

DROP TABLE IF EXISTS subreddits;
CREATE TABLE subreddits (
  name varchar(20) NOT NULL,
  creator_id int(11) DEFAULT NULL,
  description text,
  created_at int(11) DEFAULT NULL
);

INSERT INTO subreddits (name, creator_id, description, created_at) VALUES
('computerscience', 2, 'this subreddit is for computer science!!!!', 1578693338),
('pics', 2, 'Post pictures here', 1578516782),
('random', 2, 'post random stuff here', 1578516797);

DROP TABLE IF EXISTS subreddit_subscriptions;
CREATE TABLE subreddit_subscriptions (
  subreddit_name varchar(20) NOT NULL,
  user_id int(11) NOT NULL
);

INSERT INTO subreddit_subscriptions (subreddit_name, user_id) VALUES
('computerscience', 2),
('pics', 2),
('random', 2);

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id int(11) NOT NULL,
  username varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  created_at int(30) NOT NULL DEFAULT '0'
);

INSERT INTO users (id, username, password, created_at) VALUES
(1, 'lava1234567890', 'udeshya', 1578489686),
(2, 'YoganLava', 'udeshya', 1578499620),
(3, 'NewPerson', 'new', 1578787951);

DROP TRIGGER IF EXISTS create_details;
DELIMITER $$
CREATE TRIGGER create_details AFTER INSERT ON users FOR EACH ROW BEGIN
	INSERT INTO details (user_id,profile_picture,gender,country,age,description) VALUES (NEW.id,null,'other','none',0,null);
END
$$
DELIMITER ;

DROP VIEW IF EXISTS mostliked;

CREATE VIEW mostliked AS
SELECT  p.id             AS id
       ,p.title          AS title
       ,p.user_id        AS user_id
       ,p.is_image       AS is_image
       ,p.body           AS body
       ,p.created_at     AS created_at
       ,p.updated_at     AS updated_at
       ,p.subreddit_name AS subreddit_name
       ,l3.likes         AS likes
       ,l3.dislikes      AS dislikes
FROM 
((
	SELECT  posts.id             AS id
	       ,posts.title          AS title
	       ,posts.user_id        AS user_id
	       ,posts.is_image       AS is_image
	       ,posts.body           AS body
	       ,posts.created_at     AS created_at
	       ,posts.updated_at     AS updated_at
	       ,posts.subreddit_name AS subreddit_name
	FROM posts) p
	LEFT JOIN 
	(
		SELECT  coalesce(COUNT(distinct l.id),0)  AS likes
		       ,coalesce(COUNT(distinct l2.id),0) AS dislikes
		       ,pl.post_id                        AS post_id
		FROM 
		((post_likes pl
			LEFT JOIN likes l on
			(((l.id = pl.like_id) AND (l.vote = 'upvote'))
			))
			LEFT JOIN likes l2 on
			(((l2.id = pl.like_id) AND (l2.vote = 'downvote'))
			)
		)
		GROUP BY  pl.post_id
	) l3 on((p.id = l3.post_id))
)
ORDER BY (l3.likes - l3.dislikes) desc; 

ALTER TABLE awards
  ADD PRIMARY KEY (id,user_id,award),
  ADD KEY user_id (user_id);

ALTER TABLE comments
  ADD PRIMARY KEY (id),
  ADD KEY user_id (user_id),
  ADD KEY post_id (post_id);

ALTER TABLE comment_awards
  ADD PRIMARY KEY (award_id,comment_id),
  ADD KEY comment_id (comment_id);

ALTER TABLE comment_likes
  ADD KEY like_id (like_id),
  ADD KEY comment_id (comment_id);

ALTER TABLE details
  ADD KEY user_id (user_id);

ALTER TABLE likes
  ADD PRIMARY KEY (id),
  ADD KEY likes_ibfk_1 (user_id);

ALTER TABLE posts
  ADD PRIMARY KEY (id),
  ADD KEY user_id (user_id),
  ADD KEY subreddit_name (subreddit_name);

ALTER TABLE post_awards
  ADD PRIMARY KEY (award_id,post_id),
  ADD KEY post_id (post_id);

ALTER TABLE post_likes
  ADD PRIMARY KEY (like_id),
  ADD KEY post_id (post_id);

ALTER TABLE subreddits
  ADD PRIMARY KEY (name),
  ADD KEY creator_id (creator_id);

ALTER TABLE subreddit_subscriptions
  ADD PRIMARY KEY (subreddit_name,user_id),
  ADD KEY user_id (user_id);

ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY username (username);

ALTER TABLE awards
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE comments
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE likes
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE posts
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE users
  MODIFY id int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE awards
  ADD CONSTRAINT awards_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id);

ALTER TABLE comments
  ADD CONSTRAINT comments_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id),
  ADD CONSTRAINT comments_ibfk_2 FOREIGN KEY (post_id) REFERENCES posts (id),
  ADD CONSTRAINT comments_ibfk_3 FOREIGN KEY (user_id) REFERENCES users (id),
  ADD CONSTRAINT comments_ibfk_4 FOREIGN KEY (post_id) REFERENCES posts (id),
  ADD CONSTRAINT comments_ibfk_5 FOREIGN KEY (user_id) REFERENCES users (id),
  ADD CONSTRAINT comments_ibfk_6 FOREIGN KEY (post_id) REFERENCES posts (id),
  ADD CONSTRAINT comments_ibfk_7 FOREIGN KEY (user_id) REFERENCES users (id),
  ADD CONSTRAINT comments_ibfk_8 FOREIGN KEY (post_id) REFERENCES posts (id);

ALTER TABLE comment_awards
  ADD CONSTRAINT comment_awards_ibfk_1 FOREIGN KEY (award_id) REFERENCES awards (id),
  ADD CONSTRAINT comment_awards_ibfk_2 FOREIGN KEY (comment_id) REFERENCES comments (id);

ALTER TABLE comment_likes
  ADD CONSTRAINT comment_likes_ibfk_1 FOREIGN KEY (like_id) REFERENCES likes (id),
  ADD CONSTRAINT comment_likes_ibfk_2 FOREIGN KEY (comment_id) REFERENCES comments (id);

ALTER TABLE details
  ADD CONSTRAINT details_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id);

ALTER TABLE likes
  ADD CONSTRAINT likes_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id);

ALTER TABLE posts
  ADD CONSTRAINT posts_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id),
  ADD CONSTRAINT posts_ibfk_2 FOREIGN KEY (subreddit_name) REFERENCES subreddits (name);

ALTER TABLE post_awards
  ADD CONSTRAINT post_awards_ibfk_1 FOREIGN KEY (award_id) REFERENCES awards (id),
  ADD CONSTRAINT post_awards_ibfk_2 FOREIGN KEY (post_id) REFERENCES posts (id);

ALTER TABLE post_likes
  ADD CONSTRAINT post_likes_ibfk_1 FOREIGN KEY (like_id) REFERENCES likes (id),
  ADD CONSTRAINT post_likes_ibfk_2 FOREIGN KEY (post_id) REFERENCES posts (id);

ALTER TABLE subreddits
  ADD CONSTRAINT subreddits_ibfk_1 FOREIGN KEY (creator_id) REFERENCES users (id);

ALTER TABLE subreddit_subscriptions
  ADD CONSTRAINT subreddit_subscriptions_ibfk_1 FOREIGN KEY (subreddit_name) REFERENCES subreddits (name),
  ADD CONSTRAINT subreddit_subscriptions_ibfk_2 FOREIGN KEY (user_id) REFERENCES users (id);