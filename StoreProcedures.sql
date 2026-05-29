-- =========================================
-- VIEW: CATALOG
-- =========================================

CREATE OR REPLACE VIEW view_catalog AS
SELECT
    m.media_id,
    m.title,
    m.img,
    m.format,
    m.year,
    g.genre,
    mt.category
FROM Media m
JOIN Genres g
    ON m.genre_id = g.genre_id
JOIN Media_Types mt
    ON m.media_types_id = mt.media_types_id;



-- -- =========================================
-- -- VIEW: RANDOM
-- -- =========================================

-- CREATE OR REPLACE VIEW view_random AS
-- SELECT
--     media_id,
--     title,
--     category,
--     img
-- FROM view_catalog
-- ORDER BY RAND()
-- LIMIT 4;



-- =========================================
-- VIEW: ITEM DETAIL
-- =========================================

CREATE OR REPLACE VIEW view_item_detail AS
SELECT
    m.media_id,
    m.title,
    m.img,
    m.format,
    m.year,
    mt.category,
    g.genre,
    b.publisher,
    b.isbn,
    p.fullname,
    r.role
FROM Media m

JOIN Genres g
    ON m.genre_id = g.genre_id

JOIN Media_Types mt
    ON m.media_types_id = mt.media_types_id

LEFT JOIN Books b
    ON m.media_id = b.media_id

LEFT JOIN Media_People mp
    ON m.media_id = mp.media_id

LEFT JOIN People p
    ON mp.people_id = p.people_id

LEFT JOIN Role r
    ON mp.role_id = r.role_id;



-- =========================================
-- PROCEDURE: GET FORMATS BY CATEGORY
-- =========================================

DROP PROCEDURE IF EXISTS sp_get_formats_by_category;

DELIMITER $$

CREATE PROCEDURE sp_get_formats_by_category (
    IN p_category VARCHAR(100)
)
BEGIN
    SELECT DISTINCT
        LOWER(category) AS category,
        format
    FROM view_catalog
    WHERE
        p_category IS NULL
        OR LOWER(category) = LOWER(p_category)
    ORDER BY category, format;
END$$

DELIMITER ;



-- =========================================
-- PROCEDURE: GET GENRES BY CATEGORY
-- =========================================

DROP PROCEDURE IF EXISTS sp_get_genres_by_category;

DELIMITER $$

CREATE PROCEDURE sp_get_genres_by_category (
    IN p_category VARCHAR(100)
)
BEGIN
    SELECT DISTINCT
        LOWER(category) AS category,
        genre
    FROM view_catalog
    WHERE
        p_category IS NULL
        OR LOWER(category) = LOWER(p_category)
    ORDER BY category, genre;
END$$

DELIMITER ;



-- =========================================
-- PROCEDURE: GET FULL CATALOG
-- =========================================

DROP PROCEDURE IF EXISTS sp_get_full_catalog;

DELIMITER $$

CREATE PROCEDURE sp_get_full_catalog (
    IN p_limit  INT,
    IN p_offset INT
)
BEGIN
    DECLARE v_limit  BIGINT;
    DECLARE v_offset INT;

    SET v_limit  = IFNULL(p_limit, 18446744073709551615);
    SET v_offset = IFNULL(p_offset, 0);

    SELECT
        media_id,
        title,
        category,
        img
    FROM view_catalog
    ORDER BY
        REPLACE(
            REPLACE(
                REPLACE(title, 'The ', ''),
            'An ', ''),
        'A ', '')
    LIMIT v_limit OFFSET v_offset;
END$$

DELIMITER ;



-- =========================================
-- PROCEDURE: GET CATALOG
-- =========================================

DROP PROCEDURE IF EXISTS sp_get_catalog;

DELIMITER $$

CREATE PROCEDURE sp_get_catalog (
    IN p_category VARCHAR(100),
    IN p_limit    INT,
    IN p_offset   INT
)
BEGIN
    SELECT
        media_id,
        title,
        category,
        img
    FROM view_catalog
    WHERE
        (
            p_category IS NULL
            OR LOWER(category) = LOWER(p_category)
        )
    ORDER BY
        REPLACE(
            REPLACE(
                REPLACE(title, 'The ', ''),
            'An ', ''),
        'A ', '')
    LIMIT p_limit OFFSET p_offset;
END$$

DELIMITER ;



-- =========================================
-- PROCEDURE: SEARCH CATALOG
-- =========================================

DROP PROCEDURE IF EXISTS sp_search_catalog;

DELIMITER $$

CREATE PROCEDURE sp_search_catalog (
    IN p_search   VARCHAR(255),
    IN p_category VARCHAR(100),
    IN p_limit    INT,
    IN p_offset   INT
)
BEGIN
    SELECT DISTINCT
        vc.media_id,
        vc.title,
        vc.category,
        vc.img
    FROM view_catalog vc
    WHERE
        (
            p_search IS NULL
            OR p_search = ''
            OR vc.title LIKE CONCAT('%', p_search, '%')
            OR EXISTS (
                SELECT 1
                FROM Media_People mp
                JOIN People p
                    ON p.people_id = mp.people_id
                WHERE
                    mp.media_id = vc.media_id
                    AND p.fullname LIKE CONCAT('%', p_search, '%')
            )
        )
        AND (
            p_category IS NULL
            OR LOWER(vc.category) = LOWER(p_category)
        )
    ORDER BY
        REPLACE(
            REPLACE(
                REPLACE(vc.title, 'The ', ''),
            'An ', ''),
        'A ', '')
    LIMIT p_limit OFFSET p_offset;
END$$

DELIMITER ;

-- =========================================
-- PROCEDURE: GET ITEM FULL DETAIL
-- =========================================

DROP PROCEDURE IF EXISTS sp_get_item_full_detail;

DELIMITER $$

CREATE PROCEDURE sp_get_item_full_detail (
    IN p_media_id INT
)
BEGIN
    SELECT
        media_id,
        title,
        category,
        img,
        format,
        year,
        genre,
        publisher,
        isbn
    FROM view_item_detail
    WHERE media_id = p_media_id
    LIMIT 1;

    SELECT
        fullname,
        role
    FROM view_item_detail
    WHERE media_id = p_media_id;
END$$

DELIMITER ;



-- =========================================
-- PROCEDURE: SEARCH CATALOG COUNT
-- =========================================

DROP PROCEDURE IF EXISTS sp_search_catalog_count;

DELIMITER $$

CREATE PROCEDURE sp_search_catalog_count (
    IN p_search   VARCHAR(255),
    IN p_category VARCHAR(100)
)
BEGIN
    SELECT COUNT(DISTINCT vc.media_id) AS total
    FROM view_catalog vc
    WHERE
        (
            p_search IS NULL
            OR p_search = ''
            OR vc.title LIKE CONCAT('%', p_search, '%')
            OR EXISTS (
                SELECT 1
                FROM Media_People mp
                JOIN People p
                    ON p.people_id = mp.people_id
                WHERE
                    mp.media_id = vc.media_id
                    AND p.fullname LIKE CONCAT('%', p_search, '%')
            )
        )
        AND (
            p_category IS NULL
            OR LOWER(vc.category) = LOWER(p_category)
        );
END$$

DELIMITER ;



-- =========================================
-- INDEXES
-- =========================================

CREATE INDEX idx_media_title
ON Media(title);

CREATE INDEX idx_people_fullname
ON People(fullname);

CREATE INDEX idx_media_types_category
ON Media_Types(category);

-- =========================================
-- PROCEDURE: GET RANDOM CATALOG
-- =========================================

DROP PROCEDURE IF EXISTS sp_get_random_catalog;

DELIMITER $$

CREATE PROCEDURE sp_get_random_catalog()
BEGIN
    SELECT
        media_id,
        title,
        category,
        img
    FROM view_catalog
    ORDER BY RAND()
    LIMIT 4;
END$$

DELIMITER ;

-- =========================================
-- PROCEDURE: GET ALL USERS
-- =========================================

DROP PROCEDURE IF EXISTS sp_get_users;

DELIMITER $$

CREATE PROCEDURE sp_get_users (
    IN p_limit INT,
    IN p_offset INT
)
BEGIN
    SELECT
        userid,
        username,
        email,
        password
    FROM users
    LIMIT p_limit OFFSET p_offset;
END$$

DELIMITER ;

-- =========================================
-- PROCEDURE: GET USER BY ID
-- =========================================

DROP PROCEDURE IF EXISTS sp_get_user_by_id;

DELIMITER $$

CREATE PROCEDURE sp_get_user_by_id (
    IN p_userid INT
)
BEGIN
    SELECT
        userid,
        username,
        email,
        password
    FROM users
    WHERE userid = p_userid
    LIMIT 1;
END$$

DELIMITER ;

-- =========================================
-- PROCEDURE: FIND USER BY EMAIL
-- =========================================

DROP PROCEDURE IF EXISTS sp_find_user_by_email;

DELIMITER $$

CREATE PROCEDURE sp_find_user_by_email (
    IN p_email VARCHAR(255)
)
BEGIN
    SELECT
        userid,
        username,
        email,
        password
    FROM users
    WHERE LOWER(email) = LOWER(TRIM(p_email))
    LIMIT 1;
END$$

DELIMITER ;

-- =========================================
-- PROCEDURE: CREATE USER
-- =========================================

DROP PROCEDURE IF EXISTS sp_create_user;

DELIMITER $$

CREATE PROCEDURE sp_create_user (
    IN p_username VARCHAR(100),
    IN p_email VARCHAR(255),
    IN p_password VARCHAR(255)
)
BEGIN
    INSERT INTO users (
        username,
        email,
        password
    )
    VALUES (
        p_username,
        p_email,
        p_password
    );
END$$

DELIMITER ;

-- =========================================
-- PROCEDURE: UPDATE USER
-- =========================================

DROP PROCEDURE IF EXISTS sp_update_user;

DELIMITER $$

CREATE PROCEDURE sp_update_user (
    IN p_userid INT,
    IN p_username VARCHAR(100),
    IN p_email VARCHAR(255)
)
BEGIN
    UPDATE users
    SET
        username = p_username,
        email = p_email
    WHERE userid = p_userid;
END$$

DELIMITER ;

-- =========================================
-- PROCEDURE: DELETE USER
-- =========================================

DROP PROCEDURE IF EXISTS sp_delete_user;

DELIMITER $$

CREATE PROCEDURE sp_delete_user (
    IN p_userid INT
)
BEGIN
    DELETE FROM users
    WHERE userid = p_userid;
END$$

DELIMITER ;

