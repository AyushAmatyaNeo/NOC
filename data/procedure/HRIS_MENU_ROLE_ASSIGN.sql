CREATE OR REPLACE PROCEDURE HRIS_MENU_ROLE_ASSIGN(
    P_MENU_ID     NUMBER,
    P_ROLE_ID     NUMBER,
    P_ASSIGN_FLAG CHAR )
AS
  V_EXIST_FLAG CHAR(1);
  V_PARENT_MENU NUMBER;
  V_TEMP_MENU_ID NUMBER;
BEGIN
  IF P_ASSIGN_FLAG ='Y' THEN
  DECLARE CURSOR child for
    (SELECT 
        node_id, 
        menu_id, 
        menu_name,
        route,
        action,
        icon_class,
        parent_id
     FROM HIERARCHY ( 
     SOURCE ( SELECT menu_id, menu_name, route, action, icon_class, menu_id as node_id, 
     parent_menu as parent_id 
                 FROM hris_menus WHERE status = 'E' 
                 ORDER BY node_id ) start where menu_id = :P_MENU_ID
                      --DEPTH 4
                      ORPHAN IGNORE
     CACHE FORCE ) 
     ORDER BY node_id
    );
    for childs as child do
      SELECT (
        CASE
          WHEN COUNT(*) >0
          THEN 'Y'
          ELSE 'N'
        END)
      INTO V_EXIST_FLAG
      FROM HRIS_ROLE_PERMISSIONS
      WHERE MENU_ID   = childs.MENU_ID
      AND ROLE_ID     = P_ROLE_ID;
      IF(V_EXIST_FLAG = 'N') THEN
        INSERT
        INTO HRIS_ROLE_PERMISSIONS
          (
            ROLE_ID,
            MENU_ID,
            STATUS,
            CREATED_DT
          )
          VALUES
          (
            P_ROLE_ID,
            childs.MENU_ID,
            'E',
            current_date
          );
      END IF;
    END for;
    
    V_TEMP_MENU_ID = P_MENU_ID;
    WHILE 1 = 1 DO
    	SELECT PARENT_MENU INTO V_PARENT_MENU FROM HRIS_MENUS WHERE MENU_ID = V_TEMP_MENU_ID;
    	V_TEMP_MENU_ID = V_PARENT_MENU;
    	IF V_PARENT_MENU IS NULL THEN
    		BREAK;
    	END IF;
    	SELECT (
        CASE
          WHEN COUNT(*) >0
          THEN 'Y'
          ELSE 'N'
        END)
      INTO V_EXIST_FLAG
      FROM HRIS_ROLE_PERMISSIONS
      WHERE MENU_ID   = V_PARENT_MENU
      AND ROLE_ID     = P_ROLE_ID;
      IF(V_EXIST_FLAG = 'N') THEN
        INSERT
        INTO HRIS_ROLE_PERMISSIONS
          (
            ROLE_ID,
            MENU_ID,
            STATUS,
            CREATED_DT
          )
          VALUES
          (
            P_ROLE_ID,
            V_PARENT_MENU,
            'E',
            current_date
          );
      END IF;
    END WHILE;
    
    
  ELSE
    DECLARE CURSOR child for
    (SELECT 
        node_id, 
        menu_id, 
        menu_name,
        route,
        action,
        icon_class,
        parent_id
     FROM HIERARCHY ( 
     SOURCE ( SELECT menu_id, menu_name, route, action, icon_class, menu_id as node_id, 
     parent_menu as parent_id 
                 FROM hris_menus WHERE status = 'E' 
                 ORDER BY node_id ) start where menu_id = :P_MENU_ID
                      --DEPTH 4
                      ORPHAN IGNORE
     CACHE FORCE ) 
     ORDER BY node_id
    );
    for childs as child do
      DELETE
      FROM HRIS_ROLE_PERMISSIONS
      WHERE ROLE_ID =P_ROLE_ID
      AND MENU_ID   = childs.MENU_ID;
    END for;
  END IF;
END;