

-- from bottlers food/shift/night shift start

insert into hris_menus
(
menu_id,
MENU_NAME,
PARENT_MENU,
route,
status,
created_dt,
icon_class,
action,
menu_index,
is_visible
)values
(
(select max(menu_id)+1 from hris_menus),
'Allowance Report',
148,
'allreport',
'E',
TRUNC(SYSDATE),
'fa fa-pencil',
'monthlyAllowance',
(select max(MENU_INDEX)+1 from hris_menus where parent_menu=148),
'Y'
);



-- from bottlers food/shift/night shift end


-- for bottlers allowance Assign start


insert into hris_menus
(
menu_id,
MENU_NAME,
PARENT_MENU,
route,
status,
created_dt,
icon_class,
action,
menu_index,
is_visible
)values
(
(select max(menu_id)+1 from hris_menus),
'Allowance Assign',
301,
'allowance-assign',
'E',
TRUNC(SYSDATE),
'fa fa-pencil',
'index',
(select max(MENU_INDEX)+1 from hris_menus where parent_menu=301),
'Y'
);

insert into hris_menus
(
menu_id,
MENU_NAME,
PARENT_MENU,
route,
status,
created_dt,
icon_class,
action,
menu_index,
is_visible
)values
(
(select max(menu_id)+1 from hris_menus),
'DailyDeparmentSpecfic',
148,
'allreport',
'E',
TRUNC(SYSDATE),
'fa fa-pencil',
'departmentWiseAttdReport',
(select max(MENU_INDEX)+1 from hris_menus where parent_menu=148),
'Y'
);


-- for bottlers allowance Assign End