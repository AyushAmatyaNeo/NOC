INSERT
INTO HRIS_MENUS
  (
    MENU_ID,
    MENU_NAME,
    PARENT_MENU,
    ROUTE,
    STATUS,
    CREATED_DT,
    ICON_CLASS,
    ACTION,
    MENU_INDEX,
    IS_VISIBLE
  )
  VALUES
  (
    (SELECT MAX(menu_id)+1 FROM hris_menus),
    'Utility',
    9,
    'javascript::',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'javascript::',
    (select max(menu_index)+1 from hris_menus where parent_menu=9),
    'Y'
  );



INSERT
INTO HRIS_MENUS
  (
    MENU_ID,
    MENU_NAME,
    PARENT_MENU,
    ROUTE,
    STATUS,
    CREATED_DT,
    ICON_CLASS,
    ACTION,
    MENU_INDEX,
    IS_VISIBLE
  )
  VALUES
  (
    (SELECT MAX(menu_id)+1 FROM hris_menus),
    'Re Attendance',
    (SELECT MAX(menu_id) FROM hris_menus),
    'system-utility',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'reAttendance',
    (select max(menu_index)+1 from hris_menus where parent_menu=9),
    'Y'
  );


INSERT
INTO HRIS_MENUS
  (
    MENU_ID,
    MENU_NAME,
    PARENT_MENU,
    ROUTE,
    STATUS,
    CREATED_DT,
    ICON_CLASS,
    ACTION,
    MENU_INDEX,
    IS_VISIBLE
  )
  VALUES
  (
    (SELECT MAX(menu_id)+1 FROM hris_menus),
    'Database Backup',
    (select menu_id from hris_menus where lower(menu_name) like lower('utility%')),
    'system-utility',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'databaseBackup',
    (select max(menu_index)+1 from hris_menus where parent_menu=(select menu_id from hris_menus where lower(menu_name) like lower('utility%'))),
    'Y'
  );


CREATE TABLE  HRIS_DATABASE_BACKUP(
USER_NAME VARCHAR2(100 BYTE),
PASSWORD VARCHAR2(100 BYTE),
CONNECTION_STRING VARCHAR2(100 BYTE),
ORACLE_USER VARCHAR2(100 BYTE),
DIRECTORY_NAME VARCHAR2(100 BYTE)
);


INSERT
INTO HRIS_MENUS
  (
    MENU_ID,
    MENU_NAME,
    PARENT_MENU,
    ROUTE,
    STATUS,
    CREATED_DT,
    ICON_CLASS,
    ACTION,
    MENU_INDEX,
    IS_VISIBLE
  )
  VALUES
  (
    (SELECT MAX(menu_id)+1 FROM hris_menus),
    'Update Seniority',
    (select menu_id from hris_menus where lower(menu_name) like lower('utility%')),
    'system-utility',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'updateSeniority',
    (select max(menu_index)+1 from hris_menus where parent_menu=(select menu_id from hris_menus where lower(menu_name) like lower('utility%'))),
    'Y'
  );