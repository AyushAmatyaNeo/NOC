INSERT
INTO HRIS_MENUS
  (
    MENU_CODE,
    MENU_ID,
    MENU_NAME,
    PARENT_MENU,
    MENU_DESCRIPTION,
    ROUTE,
    STATUS,
    CREATED_DT,
    MODIFIED_DT,
    ICON_CLASS,
    ACTION,
    MENU_INDEX,
    CREATED_BY,
    MODIFIED_BY,
    IS_VISIBLE
  )
  VALUES
  (
    NULL,
    (select max(menu_id)+1 from hris_menus),
    'Payroll Login',
    null,
    NULL,
    'payroll-api',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'index',
    60,
    NULL,
    NULL,
    'Y'
  );


CREATE TABLE HRIS_PAYROLL_API_TOKEN(
TOKEN VARCHAR2(100 BYTE)
);