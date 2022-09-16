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
    (select max(menu_id) from HRIS_MENUS),
    'Employee Wise Group Sheet',
    (select menu_id from HRIS_MENUS where lower(MENU_NAME) like '%payroll report%'),
    NULL,
    'payrollReport',
    'E',
    current_date,
    NULL,
    'fa fa-pencil-square-o',
    'employeeWiseGroupSheet',
    5,
    141,
    NULL,
    'Y'
);


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
    (select max(menu_id) + 1 from HRIS_MENUS),
    'Employee File Setup',
    1,
    NULL,
    'fileSetup',
    'E',
    current_date,
    NULL,
    'fa fa-pencil',
    'index',
    14,
    1,
    NULL,
    'Y'
);