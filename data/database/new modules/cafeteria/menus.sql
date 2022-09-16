Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Cafeteria',null,null,'javascript::','E',
trunc(sysdate),null,'fa fa-edit',
'javascript::',
(select max(menu_index)+1 from  hris_menus where Route='javascript::'
and action='javascript::' and parent_menu is null)
,null,null,'Y');



Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Setup',
(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Cafeteria'),
null,'javascript::','E',
trunc(sysdate),null,'fa fa-edit',
'javascript::',
1,null,null,'Y');


Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Menu',
(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Setup' and Parent_Menu=(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Cafeteria')),
null,'cafeteriasetup','E',
trunc(sysdate),null,'fa fa-edit',
'menu',
1,null,null,'Y');


Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Time',
(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Setup' and Parent_Menu=(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Cafeteria')),
null,'cafeteriasetup','E',
trunc(sysdate),null,'fa fa-edit',
'schedule',
2,null,null,'Y');

Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Menu Time Mapping',
(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Setup' and Parent_Menu=(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Cafeteria')),
null,'cafeteriasetup','E',
trunc(sysdate),null,'fa fa-edit',
'map',
3,null,null,'Y');

Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Cafe Entry',
(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Cafeteria'),
null,
'cafeteria-activity'
,'E',
trunc(sysdate),null,'fa fa-edit',
'activity',
2,null,null,'Y');


Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Reports',
(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Cafeteria'),
null,
'cafeteriareports'
,'E',
trunc(sysdate),null,'fa fa-edit',
'canteenReport',
3,null,null,'Y');



