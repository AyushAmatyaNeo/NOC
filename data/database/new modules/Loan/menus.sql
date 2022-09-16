Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Reports',
(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Loan' AND PARENT_MENU = 302),
null,
'javascript::'
,'E',
trunc(sysdate),null,'fa fa-star',
'javascript::',
3,null,null,'Y');


Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Basic Report',
(SELECT menu_id FROM HRIS_MENUS where Menu_Name='Reports' and 
Parent_Menu=(SELECT menu_id FROM HRIS_MENUS where Menu_Name='Loan' 
and parent_menu = 302)),
null,
'loanReport'
,'E',
trunc(sysdate),null,'fa fa-star',
'index',
1,null,null,'Y');


Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Voucher Report',
(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Reports' and 
Parent_Menu=(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Loan'
and parent_menu = 302)),
null,
'loanReport'
,'E',
trunc(sysdate),null,'fa fa-star',
'loanVoucher',
2,null,null,'Y');


Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,
MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) 
values 
(null,
(SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
'Cash Payment Report',
(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Reports' and 
Parent_Menu=(SELECT menu_id FROM HRIS_MENUS  where Menu_Name='Loan'
and parent_menu = 302)),
null,
'loanReport'
,'E',
trunc(sysdate),null,'fa fa-star',
'cashPaymentReport',
3,null,null,'Y');

