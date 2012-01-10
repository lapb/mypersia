<?php
	if(!defined('__INDEX__'))
		die('Direct access not allowed.');
		
	// -------------------------------------------------------------------------------------------
	//
	// Prepare SQL for User & Group structure.
	//
	$tableUser           = DB_PREFIX . 'User';
	$tableGroup          = DB_PREFIX . 'Group';
	$tableGroupMember    = DB_PREFIX . 'GroupMember';
	$tableArticle		 = DB_PREFIX . 'Article';
	$tableTopic		 	 = DB_PREFIX . 'Topic';
	$tablePost		 	 = DB_PREFIX . 'Post';
	$tableTopicPost		 = DB_PREFIX . 'TopicPost';
	$tableStatistic 	 = DB_PREFIX . 'Statistic';
	
	$spCreateNewArticle  = DB_PREFIX . 'PCreateNewArticle';
	$spUpdateArticle  	 = DB_PREFIX . 'PUpdateArticle';
	$spDisplayArticle 	 = DB_PREFIX . 'PDisplayArticle';
	$spListArticles 	 = DB_PREFIX . 'PListArticles';
	$spListTitles 	 	 = DB_PREFIX . 'PListTitles';
	$spListTopics 		 = DB_PREFIX . 'PListTopics';
	$spGetTopicDetails 	 = DB_PREFIX . 'PGetTopicDetails';
	$spGetPostDetails 	 = DB_PREFIX . 'PGetPostDetails';
	$spGetTopicPosts 	 = DB_PREFIX . 'PGetTopicPosts';
	$spCreateNewPost  	 = DB_PREFIX . 'PCreateNewPost';
	$spCreateNewTopic    = DB_PREFIX . 'PCreateNewTopic';
	$spUpdatePost  	 	 = DB_PREFIX . 'PUpdatePost';
	$spCreateOrUpdatePost = DB_PREFIX . 'PCreateOrUpdatePost';
	$spCreateAccount 	 = DB_PREFIX.'PCreateAccount';
	$spGetAccountDetails = DB_PREFIX.'PGetAccountDetails';
	$spUpdateAccountPassword = DB_PREFIX.'PUpdateAccountPassword';
	$spUpdateAccountEmail = DB_PREFIX.'PUpdateAccountEmail';
	$spUpdateAccountGravatarEmail = DB_PREFIX.'PUpdateAccountGravatarEmail';
	$fGetGravatarLinkFromEmail = DB_PREFIX.'FGetGravatarLinkFromEmail';
	$fCheckFreeUsername  = DB_PREFIX.'FCheckFreeUsername';
	$fCheckUserIsOwnerOrAdmin = DB_PREFIX . 'FCheckUserIsOwnerOrAdmin';
	$tInsertUser 		 = DB_PREFIX . 'TInsertUser';
	$tAddArticle 		 = DB_PREFIX . 'TAddArticle';
	
	$query = '';
	
	$query .= <<<EOD
		DROP TABLE IF EXISTS {$tableTopicPost};
		DROP TABLE IF EXISTS {$tableTopic};
		DROP TABLE IF EXISTS {$tableUser};
		DROP TABLE IF EXISTS {$tableGroup};
		DROP TABLE IF EXISTS {$tableGroupMember};
		DROP TABLE IF EXISTS {$tableArticle};
		DROP TABLE IF EXISTS {$tableStatistic};
		DROP PROCEDURE IF EXISTS {$spCreateNewArticle};
		DROP PROCEDURE IF EXISTS {$spUpdateArticle};
		DROP PROCEDURE IF EXISTS {$spDisplayArticle};
		DROP PROCEDURE IF EXISTS {$spListArticles};
		DROP PROCEDURE IF EXISTS {$spListTopics};
		DROP PROCEDURE IF EXISTS {$spGetTopicDetails};
		DROP PROCEDURE IF EXISTS {$spGetPostDetails};
		DROP PROCEDURE IF EXISTS {$spGetTopicPosts};
		DROP PROCEDURE IF EXISTS {$spCreateNewPost};
		DROP PROCEDURE IF EXISTS {$spCreateNewTopic};
		DROP PROCEDURE IF EXISTS {$spUpdatePost};
		DROP PROCEDURE IF EXISTS {$spCreateOrUpdatePost};
		DROP PROCEDURE IF EXISTS {$spCreateAccount};
		DROP PROCEDURE IF EXISTS {$spGetAccountDetails};
		DROP PROCEDURE IF EXISTS {$spUpdateAccountPassword};
		DROP PROCEDURE IF EXISTS {$spUpdateAccountEmail};
		DROP PROCEDURE IF EXISTS {$spUpdateAccountGravatarEmail};
		DROP FUNCTION IF EXISTS {$fGetGravatarLinkFromEmail};
		DROP FUNCTION IF EXISTS {$fCheckFreeUsername};
		DROP FUNCTION IF EXISTS {$fCheckUserIsOwnerOrAdmin};
		DROP TRIGGER IF EXISTS {$tInsertUser};
		DROP TRIGGER IF EXISTS {$tAddArticle};

		--
		-- Table for the User
		--
		CREATE TABLE {$tableUser} (

		  -- Primary key(s)
		  idUser INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,

		  -- Attributes
		  accountUser CHAR(20) NOT NULL UNIQUE,
		  emailUser CHAR(100) NULL,
		  gravatarUser CHAR(100) NULL,
		  passwordUser CHAR(32) NOT NULL,
		  realName VARCHAR(255) NULL
		) ENGINE = MYISAM CHARSET=utf8 COLLATE=utf8_swedish_ci;


		--
		-- Table for the Group
		--
		CREATE TABLE {$tableGroup} (

		  -- Primary key(s)
		  idGroup CHAR(3) NOT NULL PRIMARY KEY,

		  -- Attributes
		  nameGroup CHAR(40) NOT NULL
		) ENGINE = MYISAM CHARSET=utf8 COLLATE=utf8_swedish_ci;


		--
		-- Table for the GroupMember
		--
		CREATE TABLE {$tableGroupMember} (

		  -- Primary key(s)
		  --
		  -- The PK is the combination of the two foreign keys, see below.
		  --
		  
		  -- Foreign keys
		  GroupMember_idUser INT UNSIGNED NOT NULL,
		  GroupMember_idGroup CHAR(3) NOT NULL,
			
		  FOREIGN KEY (GroupMember_idUser) REFERENCES {$tableUser}(idUser),
		  FOREIGN KEY (GroupMember_idGroup) REFERENCES {$tableGroup}(idGroup),

		  PRIMARY KEY (GroupMember_idUser, GroupMember_idGroup)
		  
		  -- Attributes

		) ENGINE = MYISAM CHARSET=utf8 COLLATE=utf8_swedish_ci;
		
		
		--
		-- Table Article
		--
		CREATE TABLE {$tableArticle}  (
			
			-- Primary key(s)
			id INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
			
			-- Attributes
			title TEXT,
			content TEXT,
			owner INT UNSIGNED,
			created DATETIME,
			updated DATETIME,
			isPublished BOOLEAN,
			
			-- draft columns
			draftTitle TEXT,
			draftContent TEXT,
			draftModified DATETIME,
			
			-- Foreign key(s)
			FOREIGN KEY (owner) REFERENCES {$tableUser}(idUser)
		) ENGINE = MYISAM CHARSET=utf8 COLLATE=utf8_swedish_ci;
	
		--
		-- Table Statistic
		--
		CREATE TABLE {$tableStatistic} (
			
			-- Attributes
			userId INT UNSIGNED,
			noOfArticles INT UNSIGNED DEFAULT 0
			
		) ENGINE = MYISAM CHARSET=utf8 COLLATE=utf8_swedish_ci;
	
		CREATE TABLE {$tableTopic} (
			
			-- Primary key(s)
			id INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
			
			-- Attributes
			firstTopicPostId INT UNSIGNED NOT NULL,
			lastTopicPostUser INT UNSIGNED NOT NULL,
			postCounter INT UNSIGNED NOT NULL,
			lastTopicPostDate DATETIME NOT NULL,
			
			-- Foreign keys
			FOREIGN KEY (firstTopicPostId) REFERENCES $tableArticle(id),
			FOREIGN KEY (lastTopicPostUser) REFERENCES $tableUser(id)
			
		) ENGINE=MYISAM CHARSET=utf8 COLLATE=utf8_swedish_ci;
		
		CREATE TABLE {$tableTopicPost} (
			
			-- Primary key(s)
			id INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
			
			-- Attributes
			topicId INT UNSIGNED NOT NULL,
			postId INT UNSIGNED NOT NULL,
			
			-- Foreign keys
			FOREIGN KEY (topicId) REFERENCES $tableTopic(id),
			FOREIGN KEY (postId) REFERENCES $tableArticle(id)
			
		) ENGINE=MYISAM CHARSET=utf8 COLLATE=utf8_swedish_ci;
	
		--
		-- Permission UDF
		--
		CREATE FUNCTION {$fCheckUserIsOwnerOrAdmin}
		(
			articleId INT UNSIGNED,
			userId INT UNSIGNED
		)
		RETURNS BOOLEAN
		BEGIN
			DECLARE isArticleOwner INT UNSIGNED;
			DECLARE isAdmin INT UNSIGNED;
			
			SELECT owner INTO isArticleOwner FROM {$tableArticle} WHERE id=articleId AND owner=userId;
			SELECT GroupMember_idUser INTO isAdmin FROM {$tableGroupMember} WHERE GroupMember_idUser=userId AND GroupMember_idGroup='adm';
			
			RETURN (isArticleOwner OR isAdmin);
		END;
		
		CREATE FUNCTION {$fGetGravatarLinkFromEmail}
		(
			email VARCHAR(100),
			userId INT UNSIGNED
		)
		RETURNS VARCHAR(200)
		BEGIN
			DECLARE hash VARCHAR(32);
			DECLARE gravatar VARCHAR(200);
			
			IF email IS NOT NULL THEN
				BEGIN
					SET hash = MD5(TRIM(email));
				END;
			ELSE
				BEGIN
					DECLARE username VARCHAR(20);
					SELECT accountUser FROM {$tableUser} WHERE idUser = userId INTO username;
					
					SET hash = MD5(TRIM(CONCAT(userId,'.',username,'@doesnotexist.localhost')));
				END;
			END IF;
		
			SELECT CONCAT('http://www.gravatar.com/avatar/',hash,'.jpg?s=60&amp;d=identicon') INTO gravatar;
		
			RETURN gravatar;
		END;
		
		--
		-- Article Procedures
		--
		CREATE PROCEDURE {$spCreateNewArticle}
		(
			IN aTitle TEXT,
			IN aContent TEXT, 
			IN aOwner INT UNSIGNED, 
			OUT articleId INT UNSIGNED
		)
		BEGIN
			INSERT INTO {$tableArticle}
				(title, content, owner, created, updated) VALUES(aTitle, aContent, aOwner, NOW(), NOW());
			SET articleId = LAST_INSERT_ID();
		END;
		
		CREATE PROCEDURE {$spUpdateArticle}
		(
			IN aTitle TEXT,
			IN aContent TEXT,
			IN aOwner INT UNSIGNED,
			IN articleId INT UNSIGNED
		)
		BEGIN
			UPDATE {$tableArticle} SET title=aTitle, content=aContent, updated=NOW(), 
				draftTitle = NULL, draftContent = NULL, draftModified = NULL 
					WHERE id=articleId AND {$fCheckUserIsOwnerOrAdmin}(articleId, aOwner);
		END;
		
		CREATE PROCEDURE {$spDisplayArticle}
		(
			IN articleId INT UNSIGNED,
			IN userId INT UNSIGNED
		)
		BEGIN
			SELECT t1.id, t1.title, t1.content, t1.created, t1.updated, t2.realName, t2.idUser FROM {$tableArticle} AS t1 JOIN {$tableUser} AS t2 ON t1.owner=t2.idUser WHERE t1.id=articleId AND {$fCheckUserIsOwnerOrAdmin}(articleId, userId);
		END;
		
		CREATE PROCEDURE {$spListArticles}
		(
			IN userId INT UNSIGNED
		)
		BEGIN
			SELECT id, title FROM {$tableArticle} WHERE owner=userId ORDER BY updated LIMIT 20;
		END;
		
		CREATE PROCEDURE {$spListTopics}
		(
		)
		BEGIN
			SELECT T.id AS topicId, T.postCounter, T.lastTopicPostDate, A.id AS postId, A.title, U.idUser AS userId, U.accountUser AS username FROM {$tableTopic} AS T JOIN {$tableArticle} AS A ON A.id = T.firstTopicPostId JOIN {$tableUser} AS U ON U.idUser = T.lastTopicPostUser ORDER BY T.lastTopicPostDate DESC;
		END;
		
		CREATE PROCEDURE {$spGetTopicDetails} 
		(
			IN topicId INT UNSIGNED
		)
		BEGIN
			SELECT T.postCounter, T.lastTopicPostDate, A.title AS topicTitle, AU.accountUser as topicCreator, U.accountUser AS lastPostUsername, A.created AS topicCreationDate FROM {$tableTopic} AS T JOIN {$tableArticle} AS A ON A.id = T.firstTopicPostId JOIN {$tableUser} AS AU ON AU.idUser = A.owner JOIN {$tableUser} AS U ON U.idUser = T.lastTopicPostUser WHERE T.id = topicId; 
		END;
		
		CREATE PROCEDURE {$spGetPostDetails} 
		(
			IN postId INT UNSIGNED,
			IN userId INT UNSIGNED
		)
		BEGIN
			SELECT A.title, A.content, A.draftTitle, a.draftContent, A.isPublished, T.firstTopicPostId AS firstPostId FROM {$tableArticle} AS A JOIN {$tableTopicPost} AS TP ON TP.postId = A.id JOIN {$tableTopic} AS T ON T.id = TP.topicid JOIN {$tableUser} AS U ON U.idUser = userId WHERE A.id=postId AND {$fCheckUserIsOwnerOrAdmin}(postId,userId) LIMIT 1; 
		END;
		
		
		CREATE PROCEDURE {$spGetTopicPosts} 
		(
			IN topicId INT UNSIGNED
		)
		BEGIN
			SELECT A.content, A.created, U.accountUser AS username, U.idUser AS userId, A.id, {$fGetGravatarLinkFromEmail}(U.gravatarUser, U.idUser) AS gravatar FROM {$tableTopicPost} AS TP JOIN {$tableArticle} AS A ON A.id = TP.postId JOIN {$tableUser} AS U ON U.idUser = A.owner WHERE TP.topicId = topicId ORDER BY A.created;
		END;
		
		CREATE PROCEDURE {$spCreateNewPost}
		(
			IN aTopicId INT UNSIGNED,
			IN aContent TEXT,
			IN userId INT UNSIGNED,
			OUT aPostId INT UNSIGNED
		)
		BEGIN
			INSERT INTO {$tableArticle}
				(title, content, owner, created, updated, isPublished) VALUES('', aContent, userId, NOW(), NOW(), TRUE);
			SET aPostId = LAST_INSERT_ID();
			INSERT INTO {$tableTopicPost}
				(topicId, postId) VALUES(aTopicId, aPostId);
			UPDATE {$tableTopic} SET lastTopicPostUser=userId, lastTopicPostDate=NOW(), postCounter=postCounter+1 WHERE id=aTopicId;
		END;
		
		CREATE PROCEDURE {$spCreateNewTopic}
		(
			IN aTitle TEXT,
			IN aContent TEXT, 
			IN userId INT UNSIGNED, 
			OUT aTopicId INT UNSIGNED,
			OUT aPostId INT UNSIGNED
		)
		BEGIN
			INSERT INTO {$tableArticle}
				(title, content, owner, created, updated, isPublished) VALUES(aTitle, aContent, userId, NOW(), NOW(), TRUE);
			SET aPostId = LAST_INSERT_ID();
			INSERT INTO {$tableTopic}
				(firstTopicPostId, lastTopicPostUser, postCounter, lastTopicPostDate) VALUES(aPostId, userId, 1, NOW());
			SET aTopicId = LAST_INSERT_ID();
			INSERT INTO {$tableTopicPost}(topicId, postId) VALUES(aTopicId, aPostId);
		END;
		
		CREATE PROCEDURE {$spUpdatePost}
		(
			IN aTitle TEXT,
			IN aContent TEXT,
			IN aOwner INT UNSIGNED,
			IN postId INT UNSIGNED
		)
		BEGIN
			UPDATE {$tableArticle} SET title=aTitle, content=aContent, updated=NOW() 
				WHERE id=postId AND {$fCheckUserIsOwnerOrAdmin}(postId, aOwner);
		END;
		
		CREATE PROCEDURE {$spCreateOrUpdatePost}
		(
			INOUT aPostId INT UNSIGNED,
			INOUT aTopicId INT UNSIGNED,
			IN aUserId INT UNSIGNED,
			IN aTitle TEXT,
			IN aContent TEXT,
			IN aAction VARCHAR(7),
			OUT isPublished BOOLEAN
		)
		BEGIN
			IF aAction = "draft" THEN
				BEGIN
					UPDATE {$tableArticle} SET draftTitle=aTitle, draftContent=aContent, 
						draftModified=NOW() WHERE id=aPostId 
							AND {$fCheckUserIsOwnerOrAdmin}(aPostId, aUserId);
				END;
			ELSE
				BEGIN
					IF aPostId = 0 THEN
						BEGIN
							IF aTopicId = 0 THEN
								BEGIN
									CALL {$spCreateNewTopic}(aTitle, aContent, aUserId, aTopicId, aPostId);
								END;
							ELSE
								BEGIN	
									CALL {$spCreateNewPost}(aTopicId, aContent, aUserId, aPostId);
								END;
							END IF;
						END;
					ELSE
						BEGIN
							CALL {$spUpdatePost}(aTitle, aContent, aUserId, aPostId);
						END;
					END IF;
				END;
			END IF;
			
			SELECT IF(A.isPublished IS NULL, 0, 1) INTO isPublished FROM {$tableArticle} AS A 
				WHERE id=aPostId;
		END;
		
		CREATE PROCEDURE {$spCreateAccount}
		(
			 IN username VARCHAR(20),
			 IN password VARCHAR(32),
			 OUT status BOOLEAN
		)
		BEGIN
			DECLARE userId INT UNSIGNED;
			
			SELECT idUser INTO userId FROM {$tableUser} WHERE accountUser = username;
			
			IF userId IS NULL THEN 
				BEGIN
					INSERT INTO {$tableUser}(accountUser, passwordUser, emailUser, realname) VALUES(username, password, '', '');
					SET userId = LAST_INSERT_ID();
					
					INSERT INTO {$tableGroupMember}(GroupMember_idUser, GroupMember_idGroup) VALUES(userId, 'usr');
					SET status = TRUE;
				END;
			ELSE
				BEGIN
					SET status = FALSE;
				END;
			END IF;
		END;
		
		CREATE PROCEDURE {$spGetAccountDetails}
		(
			IN userId INT UNSIGNED
		)
		BEGIN
			SELECT U.idUser AS userId, G.idGroup AS groupId, U.accountUser AS username, IFNULL(U.gravatarUser,'') AS gravatarEmail, {$fGetGravatarLinkFromEmail}(U.gravatarUser, U.idUser) AS gravatar, U.passwordUser AS password, IFNULL(U.realName,'') AS name, IFNULL(U.emailUser,'') AS userEmail, G.nameGroup AS groupDescription FROM {$tableUser} AS U JOIN {$tableGroupMember} AS GM ON GM.GroupMember_idUser = U.idUser JOIN {$tableGroup} AS G ON G.idGroup = GM.GroupMember_idGroup WHERE U.idUser = userId LIMIT 1;
		END;
		
		CREATE PROCEDURE {$spUpdateAccountPassword}
		(
			IN userId INT UNSIGNED,
			IN newPassword VARCHAR(32)
		)
		BEGIN
			UPDATE {$tableUser} SET passwordUser = newPassword WHERE idUser = userId;
		END;
		
		CREATE PROCEDURE {$spUpdateAccountEmail}
		(
			IN userId INT UNSIGNED,
			IN email VARCHAR(100)
		)
		BEGIN
			UPDATE {$tableUser} SET emailUser = email WHERE idUser = userId;
		END;
		
		CREATE PROCEDURE {$spUpdateAccountGravatarEmail}
		(
			IN userId INT UNSIGNED,
			IN email VARCHAR(100)
		)
		BEGIN
			UPDATE {$tableUser} SET gravatarUser = email WHERE idUser = userId;
		END;
		
		--
		-- Statistics Triggers
		--
		CREATE TRIGGER {$tInsertUser}
		AFTER INSERT ON {$tableUser}
		FOR EACH ROW
		BEGIN
			INSERT INTO {$tableStatistic}(userId) VALUES(NEW.idUser);
		END;
		
		CREATE TRIGGER {$tAddArticle}
		AFTER INSERT ON {$tableArticle}
		FOR EACH ROW
		BEGIN
			UPDATE {$tableStatistic} SET noOfArticles = noOfArticles+1 WHERE userId=NEW.owner;
		END;
		
		--
		-- Add default user(s) 
		--
		CALL {$spCreateAccount}('admin', MD5('admin'), @created);
		CALL {$spCreateAccount}('doe', MD5('doe'), @created);
		
		--
		-- Add default groups 
		--
		INSERT INTO {$tableGroup} (idGroup, nameGroup) VALUES ('adm', 'Administrators of the site');
		INSERT INTO {$tableGroup} (idGroup, nameGroup) VALUES ('usr', 'Regular users of the site');

		--
		-- Add default groupmembers
		--
		UPDATE {$tableGroupMember} SET GroupMember_idGroup='adm' WHERE GroupMember_idUser=  
		(SELECT idUser FROM {$tableUser} WHERE accountUser = 'admin');
EOD;

	require_once TP_GLOBAL_SOURCEPATH.'CDatabaseController.php';

	$db = CDatabaseController::getInstance();
	
	$db->multiQuery($query) or die($db->error);
	
	$statements = $db->RetrieveAndIgnoreResultsFromMultiQuery();
	
	$query = str_replace("\r\n",'<br />',$query);
	$query = str_replace("\n",'<br />',$query);
	$query = $query.$db->error;
	
	$html = <<<EOD
		<h2>Install database</h2>
		<p>
			Executed queries was:
		</p>
		<p>
			{$query}
		</p>
		<p>
			Number of successful statements: {$statements}/62
		</p>
		<p>
			<a href="?p=index" style="text-decoration: underline;">Home</a>
		</p>
EOD;

	require_once TP_GLOBAL_SOURCEPATH.'CHTMLPage.php';
	
	$chtml = new CHTMLPage();
	
	$chtml->printPage('Installation of the database',$html);
	exit;
?>
