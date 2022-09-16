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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Customer Location',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower ('customer setup%')
    ),
    'customer-location',
    'E',
    TRUNC(SYSDATE),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Customer Location',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower ('customer setup%')
    ),
    'customer-location',
    'E',
    TRUNC(SYSDATE),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Customer Location',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower ('customer setup%')
    ),
    'customer-location',
    'E',
    TRUNC(SYSDATE),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Contract Details',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('customer contract%')
    ),
    'customer-contract-details',
    'E',
    TRUNC(SYSDATE),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Contract Employee Assign',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('customer%')
    AND parent_menu IS NULL
    ),
    'contract-employees',
    'E',
    TRUNC(SYSDATE),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Contract Attendance',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('customer%')
    AND parent_menu IS NULL
    ),
    'contract-attendance',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'index',
    (SELECT NVL(MAX(menu_index),0)+1
    FROM hris_menus
    WHERE parent_menu=
      (SELECT menu_id
      FROM hris_menus
      WHERE lower(menu_name) LIKE lower('customer%')
      AND parent_menu IS NULL
      )
    ),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Employee Absent Details',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('customer%')
    AND parent_menu IS NULL
    ),
    'contract-absent-details',
    'E',
    TRUNC(SYSDATE),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Employee Added Details',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('customer%')
    AND parent_menu IS NULL
    ),
    'contract-added-details',
    'E',
    TRUNC(SYSDATE),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Bill',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('customer%')
    AND parent_menu IS NULL
    ),
    'contract-attendance',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'billPrint',
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Duty Type',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('customer%')
    AND parent_menu IS NULL
    ),
    'duty-type',
    'E',
    TRUNC(SYSDATE),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'add',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('Duty Type%')
    ),
    'duty-type',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'add',
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'edit',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('Duty Type%')
    ),
    'duty-type',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'edit',
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'add Customer',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower ('customer setup%')
    ),
    'customer-setup',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'add',
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'edit Customer',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower ('customer setup%')
    ),
    'customer-setup',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'edit',
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'add Customer Location',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower ('customer setup%')
    ),
    'customer-location',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'add',
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'edit Customer Location',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower ('customer setup%')
    ),
    'customer-location',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'edit',
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'add',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('customer contract%')
    ),
    'customer-contract',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'add',
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'edit',
    (SELECT menu_id
    FROM hris_menus
    WHERE lower(menu_name) LIKE lower('customer contract%')
    ),
    'customer-contract',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'edit',
    1,
    'N'
  );

UPDATE HRIS_MENUS
SET ROUTE='javascript::',
  ACTION ='javascript::'
WHERE LOWER(MENU_NAME) LIKE LOWER('CONTRACT ATT%ENDANCE');
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Attendance',
    (SELECT MENU_ID
    FROM HRIS_MENUS
    WHERE LOWER(MENU_NAME) LIKE LOWER('CONTRACT ATTENDANCE%')
    ),
    'contract-attendance',
    'E',
    TRUNC(SYSDATE),
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Report',
    (SELECT MENU_ID
    FROM HRIS_MENUS
    WHERE LOWER(MENU_NAME) LIKE LOWER('CONTRACT ATTENDANCE%')
    ),
    'contract-attendance',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'report',
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    )
    ,
    'Employee Wise Report',
    (SELECT MENU_ID
    FROM HRIS_MENUS
    WHERE LOWER(MENU_NAME) LIKE LOWER('CONTRACT ATTENDANCE%')
    ),
    'contract-attendance',
    'E',
    TRUNC(SYSDATE),
    'fa fa-pencil',
    'emp-wise-report',
    3,
    'Y'
  );
