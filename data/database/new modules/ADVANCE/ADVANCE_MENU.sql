-- to crate advance menus

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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
    'Advance',
    1,
    'advance-setup',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'index',
    (select max(menu_index)+1 from hris_menus where parent_menu=1),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
    'add',
    (select menu_id from hris_menus where route='advance-setup' and action='index'),
    'advance-setup',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'add',
    (select nvl(max(menu_index),0)+1 from hris_menus where parent_menu=(select menu_id from hris_menus where route='advance-setup' and action='index')),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
    'edit',
    (select menu_id from hris_menus where route='advance-setup' and action='index'),
    'advance-setup',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'edit',
    (select nvl(max(menu_index),0)+1 from hris_menus where parent_menu=(select menu_id from hris_menus where route='advance-setup' and action='index')),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
    'Advance Request',
    6,
    'advance-request',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'index',
    (select max(menu_index)+1 from hris_menus where parent_menu=6),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
    'Advance',
    302,
    'javascript',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'index',
    (select max(menu_index)+1 from hris_menus where parent_menu=302),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
    'Status',
    (select max(menu_id) from hris_menus where parent_menu=302),
    'advanceStatus',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'index',
    1,
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
    'Apply',
    (  select max(menu_id) from hris_menus where parent_menu=302),
    'advanceApply',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'add',
    2,
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
    'Advance to Approve',
    304,
    'advance-approve',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'index',
    (select max(menu_index) from HRIS_MENUS where parent_menu=304),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
    'Advance Status',
    305,
    'advance-approve',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'status',
    (select max(menu_index) from HRIS_MENUS where parent_menu=305),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS), 
    'View',
    (select menu_id from hris_menus where lower(menu_name) like 'advance to approve%'),
    'advance-approve',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'view',
    1,
    'N'
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS), 
    'View',
    (select menu_id from hris_menus where lower(menu_name) like 'advance status%'),
    'advance-approve',
    'E',
    trunc(sysdate),
    'fa fa-pencil',
    'view',
    1,
    'N'
  );
  