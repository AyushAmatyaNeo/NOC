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
    (select max(menu_id+1) from HRIS_MENUS),
    'Leave Report',
    148,
    NULL,
    'allreport',
    'E',
      TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'leaveReport',
    6,
    NULL,
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
    (select max(menu_id+1) from HRIS_MENUS),
    'Hire&Exit Report',
    148,
    NULL,
    'allreport',
    'E',
      TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'HireAndFireReport',
    7,
    NULL,
    NULL,
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
'Branch Wise Daily',
148,
'allreport',
'E',
TRUNC(SYSDATE),
'fa fa-pencil',
'branchWiseDaily',
(select max(MENU_INDEX)+1 from hris_menus where parent_menu=148),
'Y'
);

DELETE
FROM HRIS_ROLE_PERMISSIONS
WHERE MENU_ID IN
  (SELECT MENU_ID
  FROM HRIS_MENUS
  WHERE LOWER(ROUTE) LIKE LOWER('%trainingapply%')
  );
DELETE
FROM HRIS_MENUS
WHERE MENU_ID IN
  (SELECT MENU_ID
  FROM HRIS_MENUS
  WHERE LOWER(ROUTE) LIKE LOWER('%trainingapply%')
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
    'nto1',
    (SELECT MAX(menu_id)+1 FROM hris_menus
    ),
    'News Type',
    9,
    'News Type',
    'news-type',
    'E',
    TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'index',
    15,NULL,
    NULL,
    'Y'
  );

INSERT
INTO hris_menus
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
    'Report Only',
    4,
    NULL,
    'attendancebyhr',
    'E',
    to_date('07-MAY-18','DD-MON-RR'),
    to_date('07-MAY-18','DD-MON-RR'),
    'fa fa-pencil',
    'report',
    (select max(MENU_INDEX)+1 from hris_menus where parent_menu=4),
    NULL,
    NULL,
    'Y'
  );

UPDATE hris_menus SET route='salarySheet' WHERE route='generate';
UPDATE HRIS_MENUS
SET MENU_NAME          ='Salary Sheet'
WHERE lower(MENU_NAME) =lower('generate') ;



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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    ),
    'Functional Level',
    1,
    NULL,
    'functionalLevels',
    'E',
    to_date('22-JAN-18','DD-MON-RR'),
    NULL,
    'fa fa-square-o',
    'index',
    100,
    NULL,
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    ),
    'Functional Type',
    1,
    NULL,
    'functionalTypes',
    'E',
    to_date('22-JAN-18','DD-MON-RR'),
    to_date('22-JAN-18','DD-MON-RR'),
    'fa fa-square-o',
    'index',
    99,
    NULL,
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    ),
    'Location',
    1,
    NULL,
    'location',
    'E',
    to_date('22-JAN-18','DD-MON-RR'),
    NULL,
    'fa fa-square-o',
    'index',
    89,
    NULL,
    NULL,
    'Y'
  );

BEGIN
    HRIS_INSERT_MENU('Overtime Report','overtime-report','index',280,4,'fa fa-list-alt','Y');
END;
/

BEGIN
  HRIS_INSERT_MENU('Leave Balance(monthly)','leavebalance','monthly',2 ,7,'fa fa-list-alt','Y');
END;
/

BEGIN
    HRIS_INSERT_MENU('Report with Location','attendancebyhr','attendanceReportWithLocation',4,12,'fa fa-list-alt','Y');
END;
/


BEGIN
    HRIS_INSERT_MENU('System Setting','system-setting','index',9,99,'fa fa-square-o','Y');
END;
/

BEGIN
    HRIS_INSERT_MENU('Attendance Log','AttendanceDevice','attendanceLog',337,99,'fa fa-square-o','Y');
END;
/

BEGIN
  HRIS_INSERT_MENU('Not Settled Report','travelStatus','settlement-report','104',5,'fa fa-star-o','Y');
END;
/

BEGIN
  HRIS_INSERT_MENU('Monthly Val Assign(Position)','monthlyValue','position-wise',36,3,'fa fa-file-text-o','Y');
END;
/
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
    364,
    'Overtime Report',
    280,
    NULL,
    'overtime-report',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-list-alt',
    'index',
    4,
    NULL,
    NULL,
    'Y'
  );


--new menus from new fisacl year



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
    'Leave Sub Bypass',
    301,
    NULL,
    'leaveSubBypass',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-list-alt',
    'index',
    8,
    NULL,
    NULL,
    'Y'
  );

-- do not insert it start
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
    'Apply',
    (select menu_id from hris_menus where lower(menu_name) like 'training%' and parent_menu=302 and route='trainingStatus'),
    NULL,
    'trainingApply',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil',
    'add',
    4,
    NULL,
    NULL,
    'Y'
  );
-- do not insert it end


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
    'Leave Cancel To Approve',
    (select menu_id from hris_menus where lower(menu_name) like 'approval%'),
    NULL,
    'leaveapprove',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'cancelList',
    1,
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'view',
    (select menu_id from hris_menus where lower(menu_name) like 'leave cancel%'),
    NULL,
    'leaveapprove',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'cancelView',
    1,
    NULL,
    NULL,
    'N'
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
    (select max(menu_id)+1 from hris_menus),
    'File Type',
    (select menu_id from hris_menus where lower(menu_name) like 'setup%' and parent_menu is null),
    NULL,
    'fileType',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'index',
    (select max(menu_index)+1 from hris_menus where parent_menu=(select menu_id from hris_menus where lower(menu_name) like 'setup%'
and parent_menu is null)),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'ADD',
    (select menu_id from hris_menus where menu_name like 'File Type%'),
    NULL,
    'fileType',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'add',
    1,
    NULL,
    NULL,
    'N'
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
    (select max(menu_id)+1 from hris_menus),
    'EDIT',
    (select menu_id from hris_menus where menu_name like 'File Type%'),
    NULL,
    'fileType',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'edit',
    1,
    NULL,
    NULL,
    'N'
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
    (select max(menu_id)+1 from hris_menus),
    'Medical Reimbursement',
    (select menu_id from hris_menus where menu_id=302 and route='javascript::' and Action='javascript::'),
    NULL,
    'javascript::',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'javascript::',
    (select max(Menu_Index)+1 from hris_menus where parent_menu=302),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Entry',
    (select menu_id from hris_menus where menu_name like 'Medical Reimbursement' 
    and route='javascript::' and action='javascript::'),
    NULL,
    'medicalEntry',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'index',
    1,
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Verify',
    (select menu_id from hris_menus where menu_name like 'Medical Reimbursement' 
    and route='javascript::' and action='javascript::'),
    NULL,
    'medicalVerify',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'index',
    2,
    NULL,
    NULL,
    'Y'
  );




Insert into HRIS_MENUS (MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) values
 (null,(select max(menu_id+1) from HRIS_MENUS),'Employee Birthhday Report',148,null,'allreport','E',trunc(sysdate),null,null,'birthdayReport',
(select max(menu_index)+1 from hris_menus where Parent_Menu=148),
null,null,'Y');

Insert into HRIS_MENUS (MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) values 
(null,(select max(menu_id+1) from HRIS_MENUS),'Job Duration Report',148,null,'allreport','E',trunc(sysdate),null,null,'jobDurationReport',
(select max(menu_index)+1 from hris_menus where Parent_Menu=148),
null,null,'Y');

Insert into HRIS_MENUS (MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,MODIFIED_DT,ICON_CLASS,ACTION,MENU_INDEX,CREATED_BY,MODIFIED_BY,IS_VISIBLE) values
 (null,(select max(menu_id+1) from HRIS_MENUS),'Weekly Work Report',148,null,'allreport','E',trunc(sysdate),null,null,'weeklyWorkingHoursReport',
(select max(menu_index)+1 from hris_menus where Parent_Menu=148),
null,null,'Y');


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
    'Settlement',
    (select menu_id from hris_menus where menu_name like 'Medical Reimbursement' 
    and route='javascript::' and action='javascript::'),
    NULL,
    'medicalSettlement',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'index',
    3,
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Balance',
    (select menu_id from hris_menus where menu_name like 'Medical Reimbursement' 
    and route='javascript::' and action='javascript::'),
    NULL,
    'medicalReport',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'index',
    4,
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Transaction',
    (select menu_id from hris_menus where menu_name like 'Medical Reimbursement' 
    and route='javascript::' and action='javascript::'),
    NULL,
    'medicalReport',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'transactionRep',
    5,
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Leave Count Date Wise',
    2,
    NULL,
    'leavebalance',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'betweenDates',
    (select max(menu_index)+1 from hris_menus where parent_menu=2),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Leave Report Card',
    2,
    NULL,
    'leavereportcard',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'index',
    (select max(menu_index)+1 from hris_menus where Parent_Menu=2),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Leave Carry Forward',
    2,
    NULL,
    'leavecarryforward',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'index',
    (select max(menu_index)+1 from hris_menus where Parent_Menu=2),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'add',
    (select menu_id from hris_menus where menu_name='Leave Carry Forward'),
    NULL,
    'leavecarryforward',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'add',
    (select max(menu_index)+1 from hris_menus where Parent_Menu=(select menu_id from hris_menus where menu_name='Leave Carry Forward')),
    NULL,
    NULL,
    'N'
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
    (select max(menu_id)+1 from hris_menus),
    'edit',
    (select menu_id from hris_menus where menu_name='Leave Carry Forward'),
    NULL,
    'leavecarryforward',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'edit',
    (select max(menu_index)+1 from hris_menus where Parent_Menu=(select menu_id from hris_menus where menu_name='Leave Carry Forward')),
    NULL,
    NULL,
    'N'
  );

  INSERT INTO hris_menus (
    menu_code,
    menu_id,
    menu_name,
    parent_menu,
    menu_description,
    route,
    status,
    created_dt,
    modified_dt,
    icon_class,
    action,
    menu_index,
    created_by,
    modified_by,
    is_visible
) VALUES (
    NULL,
    (select max(menu_id)+1 from hris_menus),
    'Basic Report',
    85,
    NULL,
    'loanReport',
    'E',
    TO_DATE('07-MAY-19','DD-MON-RR'),
    NULL,
    'fa fa-star-o',
    'index',
    910,
    12,
    NULL,
    'Y'
);


INSERT INTO hris_menus (
    menu_code,
    menu_id,
    menu_name,
    parent_menu,
    menu_description,
    route,
    status,
    created_dt,
    modified_dt,
    icon_class,
    action,
    menu_index,
    created_by,
    modified_by,
    is_visible
) VALUES (
    NULL,
    (select max(menu_id)+1 from hris_menus),
    'Employee Statement',
    85,
    NULL,
    'loanReport',
    'E',
    TO_DATE('07-MAY-19','DD-MON-RR'),
    NULL,
    'fa fa-star-o',
    'loanVoucher',
    911,
    12,
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
    (select max(menu_id)+1 from hris_menus),
    'Query Helper',
    (select menu_id from hris_menus where lower(menu_name) like '%utility%'),
    NULL,
    'system-utility',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'query',
    (select max(menu_index)+1  from hris_menus where lower(menu_name) like '%utility%'),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Overall Overtime Report',
    280,
    NULL,
    'overtime-report',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-list-alt',
    'overtimeReport',
    (select max(menu_index)+1  from hris_menus where lower(ROUTE) like '%overtime-report%'),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Roster Report',
    (select menu_id from hris_menus where lower(menu_name) like '%assign%' and PARENT_MENU is null),
    NULL,
    'allreport',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil',
    'rosterReport',
    (select max(menu_index)+1  from hris_menus where lower(menu_name) like '%assign%' and PARENT_MENU is null),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Age Report',
    (select menu_id from hris_menus where lower(menu_name) = 'report' AND PARENT_MENU = 302),
    NULL,
    'allreport',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil',
    'ageReport',
    (select max(menu_index)+1  from hris_menus where PARENT_MENU = 148),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Contract Expiry Report',
    (select menu_id from hris_menus where lower(menu_name) = 'report' AND PARENT_MENU = 302),
    NULL,
    'allreport',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil',
    'contractExpiryReport',
    (select max(menu_index)+1  from hris_menus where PARENT_MENU = 148),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Best Case Shift Group',
    (select menu_id from hris_menus where lower(menu_name) like 'setup%' and parent_menu is null),
    NULL,
    'shiftGroup',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil-square-o',
    'index',
    (select max(menu_index)+1 from hris_menus where parent_menu=(select menu_id from hris_menus where lower(menu_name) like 'setup%'
and parent_menu is null)),
    NULL,
    NULL,
    'Y'
  );


Insert into HRIS_MENUS 
(MENU_CODE,MENU_ID,MENU_NAME,PARENT_MENU,
MENU_DESCRIPTION,ROUTE,STATUS,CREATED_DT,
MODIFIED_DT,ICON_CLASS,ACTION,MENU_INDEX,
IS_VISIBLE)
values (null,
(select max(menu_id)+1 from hris_menus)
,'Weekly Roster',
(select menu_id from hris_menus where lower(menu_name) like '%assign%' and PARENT_MENU is null),
null,
'roaster',
'E',
trunc(sysdate),
null,
'fa fa-square-o',
'weeklyRoster',
(select max(menu_index)+1  from hris_menus where lower(menu_name) like '%assign%' and PARENT_MENU is null),
'Y');


    INSERT INTO hris_menus (
        menu_code,
        menu_id,
        menu_name,
        parent_menu,
        menu_description,
        route,
        status,
        created_dt,
        modified_dt,
        icon_class,
        action,
        menu_index,
        created_by,
        modified_by,
        is_visible
    ) VALUES (
        NULL,
        (select max(menu_id)+1 from hris_menus),
        'Group Shift Assign',
        (select menu_id from hris_menus where lower(menu_name) like 'assign' and parent_menu is null),
        NULL,
        'groupshiftassign',
        'E',
        TO_DATE('12-SEP-19', 'DD-MON-RR'),
        TO_DATE('12-SEP-19', 'DD-MON-RR'),
        'fa fa-users',
        'index',
        2,
        12,
        12,
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
        (select max(menu_id)+1 from hris_menus),
        'Branch Wise Daily IN OUT',
        148,
        NULL,
        'allreport',
        'E',
        trunc(sysdate),
        NULL,
        'fa fa-pencil',
        'branchWiseDailyInOut',
        (select max(menu_index)+1 from hris_menus where parent_menu=148),
        NULL,
        NULL,
        'Y'
      );

        INSERT INTO HRIS_MENUS
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
            'Leave Addition Report',
            2,
            NULL,
            'leavebalance',
            'E',
            trunc(sysdate),
            NULL,
            'fa fa-pencil-square-o',
            'leaveAdditionReport',
            (select max(menu_index)+1 from hris_menus where parent_menu=2),
            NULL,
            NULL,
            'Y'
          );


        INSERT INTO HRIS_MENUS
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
            'Resigned or Retired Employees',
            302,
            NULL,
            'employee',
            'E',
            trunc(sysdate),
            NULL,
            'fa fa-pencil-square-o',
            'resignedOrRetired',
            (select max(menu_index)+1 from hris_menus where parent_menu=302),
            NULL,
            NULL,
            'Y'
          );

INSERT INTO HRIS_MENUS
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
    'Leave Cancel',
    6,
    NULL,
    'leaverequest',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-times',
    'cancel',
    (select max(menu_index)+1 from hris_menus where parent_menu=6),
    NULL,
    NULL,
    'Y'
);



INSERT INTO hris_menus (
    menu_code,
    menu_id,
    menu_name,
    parent_menu,
    menu_description,
    route,
    status,
    created_dt,
    modified_dt,
    icon_class,
    action,
    menu_index,
    created_by,
    modified_by,
    is_visible
) VALUES (
    NULL,
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS),
    'Salary Update',
    (select menu_id from hris_menus where lower(menu_name) like 'utility'),
    NULL,
    'excelUpload',
    'E',
    TO_DATE('07-NOV-19', 'DD-MON-RR'),
    NULL,
    'fa fa-edit',
    'updateEmployeeSalary',
    3,
    1000445,
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
    (select max(menu_id)+1 from hris_menus),
    'Overtime / WOH Bulk Update',
    301,
    NULL,
    'overtime-bulk-setup',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-list-alt',
    'index',
    9,
    NULL,
    NULL,
    'Y'
);

INSERT
INTO hris_menus
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
    'Attendance SCP Report',
    4,
    NULL,
    'attendancebyhr',
    'E',
    TRUNC(SYSDATE),
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'reportOnly',
    2,
    NULL,
    NULL,
    'Y'
);

INSERT
INTO hris_menus
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
    'Whereabout Assign',
    301,
    NULL,
    'whereabouts',
    'E',
    TRUNC(SYSDATE),
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'index',
    2,
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Whereabout Report',
    (select menu_id from hris_menus where lower(menu_name) = 'report' AND PARENT_MENU = 302),
    NULL,
    'allreport',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil',
    'whereabouts',
    (select max(menu_index)+1  from hris_menus where PARENT_MENU = 148),
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Travel Itnary',
    (select menu_id from hris_menus where lower(menu_name) = 'self service'),
    NULL,
    'travelItnary',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil',
    'index',
    12,
    NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Leave Deduction',
    2,
    NULL,
    'leavededuction',
    'E',
    trunc(sysdate),
    trunc(sysdate),
    'fa fa-pencil',
    'index',
    6,NULL,NULL,
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
    (select max(menu_id)+1 from hris_menus),
    'Travel Itnary',
    (select menu_id from hris_menus where lower(menu_name) = 'self service'),
    NULL,
    'travelItnary',
    'E',
    trunc(sysdate),
    NULL,
    'fa fa-pencil',
    'view',
    12,
    NULL,
    NULL,
    'N'
);
