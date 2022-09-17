git alter table HRIS_EMPLOYEES add (
	 CHECKED SHORTTEXT(1),
	 CHECKED_BY INTEGER,
	 CHECKED_DT DATE,
	 VERIFIED SHORTTEXT(1),
	 VERIFIED_BY INTEGER,
	 VERIFIED_DT DATE
	 );

alter table HRIS_EMPLOYEES add constraint
    checked_flag CHECK ( CHECKED in ( 'Y','N'));

alter table HRIS_EMPLOYEES add constraint
    verified_flag CHECK ( VERIFIED in ( 'Y','N'));

alter table HRIS_ROLES add (
	CHECKER SHORTTEXT(1) DEFAULT 'N' NOT NULL,
	MAKER SHORTTEXT(1) DEFAULT 'N' NOT NULL
);

ALTER TABLE HRIS_ROLES ADD CONSTRAINT checker_flag CHECK(CHECKER IN ('Y','N'));

ALTER TABLE HRIS_ROLES ADD CONSTRAINT maker_flag CHECK(MAKER IN ('Y','N'));

----------noc leave start-----------
alter table HRIS_LEAVE_MASTER_SETUP add (
SERVICE_PERIOD_REQUIRED INTEGER,
DAYS_MATURED INTEGER,
MIN_DAYS_FOR_ENCASH INT,
IMPACT_GRADE SHORTTEXT(1) DEFAULT 'N' ,
COUNT_AS_SERVICE_PERIOD SHORTTEXT(1) DEFAULT 'Y'
);

alter table HRIS_LEAVE_MASTER_SETUP add
    CONSTRAINT gradeImpactCheck CHECK( IMPACT_GRADE IN ('Y', 'N'));

alter table HRIS_LEAVE_MASTER_SETUP
    add CONSTRAINT countService CHECK( COUNT_AS_SERVICE_PERIOD IN ('Y', 'N'));
----------noc leave end-----------

-----------TRAVEL---------
create table hris_position_class_map(
    id number(7,0),
    position_id number(7,0),
    class_id number (7,0),
    STATUS shorttext(1) NOT NULL,
    CONSTRAINT STS_CHK CHECK(STATUS  IN ('E','D')), 
    CREATED_BY NUMBER(7,0),
    APPROVED_BY NUMBER(7,0),
    APPROVED_DATE DATE,
    MODIFIED_BY NUMBER(7,0),
    CREATED_DT DATE,
    CHECKED_BY NUMBER(7,0),
    CHECKED_DT DATE,
    MODIFIED_DT DATE,
    DELETED_BY NUMBER(7,0),
    DELETED_DT DATE,
    constraint id primary key(id)
);

create table hris_classes (
    class_id number(7,0),
    class_name varchar2 (255),
    STATUS shorttext(1) NOT NULL,
    CONSTRAINT STS_CHK_hris_classes CHECK(STATUS  IN ('E','D')), 
    CREATED_BY NUMBER(7,0),
    APPROVED_BY NUMBER(7,0),
    APPROVED_DATE DATE,
    MODIFIED_BY NUMBER(7,0),
    CREATED_DT DATE,
    CHECKED_BY NUMBER(7,0),
    CHECKED_DT DATE,
    MODIFIED_DT DATE,
    DELETED_BY NUMBER(7,0),
    DELETED_DT DATE,
    constraint class_id primary key(class_id)
);

create table hris_class_travel_config (
    config_id NUMBER(7,0),
    class_id number(7,0),
    travel_type varchar2(20) NOT NULL,
    CONSTRAINT travel_type_hris_class_travel_config CHECK(travel_type  IN ('DOMESTIC','INTERNATIONAL')),
    domestic_type varchar2(7),
    CONSTRAINT domestic_type_hris_class_travel_config CHECK(domestic_type  IN ('WALKING','TRAVEL')),
    international_type varchar2(255),
    CONSTRAINT international_type_hris_class_travel_config CHECK(international_type  IN ('LISTED CITIES','OTHER INDIA CITIES','OTHER COUNTRIES')),
    rate number(7,2),
    unit varchar2(5) NOT NULL,
    CONSTRAINT unit_hris_class_travel_config CHECK(unit  IN ('KM','DAY')),
    currency varchar2(10),
    STATUS shorttext(1) NOT NULL,
    CONSTRAINT STS_CHK_hris_class_travel_config CHECK(STATUS  IN ('E','D')), 
    CREATED_BY NUMBER(7,0),
    APPROVED_BY NUMBER(7,0),
    APPROVED_DATE DATE,
    MODIFIED_BY NUMBER(7,0),
    CREATED_DT DATE,
    CHECKED_BY NUMBER(7,0),
    CHECKED_DT DATE,
    MODIFIED_DT DATE,
    DELETED_BY NUMBER(7,0),
    DELETED_DT DATE,
    constraint config_id primary key(config_id)
);

create table hris_travel_expense (
    travel_expense_id NUMBER (7,0),
    departure_DT DATE,
    departure_Place varchar2(255),
    arraival_place varchar2(255),
    arraival_DT DATE,
    travel_id NUMBER(7,0),
    config_id NUMBER(7,0),
    amount NUMBER(10,2),
    other_expense NUMBER(10,2),
    total NUMBER(10,2),
    exchange_rate NUMBER(7,2),
    expense_date DATE,
    remarks varchar2(500),
    STATUS shorttext(1) NOT NULL,
    CONSTRAINT STS_CHK_hris_travel_expense CHECK(STATUS  IN ('E','D')), 
    CREATED_BY NUMBER(7,0),
    APPROVED_BY NUMBER(7,0),
    APPROVED_DATE DATE,
    MODIFIED_BY NUMBER(7,0),
    CREATED_DT DATE,
    CHECKED_BY NUMBER(7,0),
    CHECKED_DT DATE,
    MODIFIED_DT DATE,
    DELETED_BY NUMBER(7,0),
    DELETED_DT DATE,
    constraint travel_expense_id primary key(travel_expense_id)
);

insert into hris_classes (class_id, class_name, STATUS,CREATED_DT) values (1, 'Executive Class', 'E', current_date);
insert into hris_classes (class_id, class_name, STATUS,CREATED_DT) values (2, 'First Class', 'E', current_date);
insert into hris_classes (class_id, class_name, STATUS,CREATED_DT) values (3, 'Second Class', 'E', current_date);
insert into hris_classes (class_id, class_name, STATUS,CREATED_DT) values (4, 'Third Class', 'E', current_date);
insert into hris_classes (class_id, class_name, STATUS,CREATED_DT) values (5, 'Fourth Class', 'E', current_date);

insert into hris_class_travel_config(config_id, class_id, travel_type, domestic_type, rate, unit, status, created_dt, currency) values (1, 1, 'DOMESTIC', 'WALKING', 125, 'KM', 'E', current_date, 'NPR');
insert into hris_class_travel_config(config_id, class_id, travel_type, domestic_type, rate, unit, status, created_dt, currency) values (2, 2, 'DOMESTIC', 'WALKING', 78.13, 'KM', 'E', current_date, 'NPR');
insert into hris_class_travel_config(config_id, class_id, travel_type, domestic_type, rate, unit, status, created_dt, currency) values (3, 3, 'DOMESTIC', 'WALKING', 78.13, 'KM', 'E', current_date, 'NPR');
insert into hris_class_travel_config(config_id, class_id, travel_type, domestic_type, rate, unit, status, created_dt, currency) values (4, 4, 'DOMESTIC', 'WALKING', 62.5, 'KM', 'E', current_date, 'NPR');
insert into hris_class_travel_config(config_id, class_id, travel_type, domestic_type, rate, unit, status, created_dt, currency) values (5, 5, 'DOMESTIC', 'WALKING', 62.5, 'KM', 'E', current_date, 'NPR');

insert into hris_class_travel_config(config_id, class_id, travel_type, domestic_type, rate, unit, status, created_dt, currency) values (6, 1, 'DOMESTIC', 'TRAVEL', 2500, 'DAY', 'E', current_date, 'NPR');
insert into hris_class_travel_config(config_id, class_id, travel_type, domestic_type, rate, unit, status, created_dt, currency) values (7, 2, 'DOMESTIC', 'TRAVEL', 2000, 'DAY', 'E', current_date, 'NPR');
insert into hris_class_travel_config(config_id, class_id, travel_type, domestic_type, rate, unit, status, created_dt, currency) values (8, 3, 'DOMESTIC', 'TRAVEL', 2000, 'DAY', 'E', current_date, 'NPR');
insert into hris_class_travel_config(config_id, class_id, travel_type, domestic_type, rate, unit, status, created_dt, currency) values (9, 4, 'DOMESTIC', 'TRAVEL', 1200, 'DAY', 'E', current_date, 'NPR');
insert into hris_class_travel_config(config_id, class_id, travel_type, domestic_type, rate, unit, status, created_dt, currency) values (10, 5, 'DOMESTIC', 'TRAVEL', 1000, 'DAY', 'E', current_date, 'NPR');

insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (11, 1, 'INTERNATIONAL', 'LISTED CITIES', 255, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (12, 2, 'INTERNATIONAL', 'LISTED CITIES', 175, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (13, 3, 'INTERNATIONAL', 'LISTED CITIES', 175, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (14, 4, 'INTERNATIONAL', 'LISTED CITIES', 125, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (15, 5, 'INTERNATIONAL', 'LISTED CITIES', 100, 'DAY', 'E', current_date, 'USD');

insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (16, 1, 'INTERNATIONAL', 'OTHER INDIA CITIES', 112.5, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (17, 2, 'INTERNATIONAL', 'OTHER INDIA CITIES', 87.5, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (18, 3, 'INTERNATIONAL', 'OTHER INDIA CITIES', 87.5, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (19, 4, 'INTERNATIONAL', 'OTHER INDIA CITIES', 62.5, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (20, 5, 'INTERNATIONAL', 'OTHER INDIA CITIES', 50, 'DAY', 'E', current_date, 'USD');

insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (21, 1, 'INTERNATIONAL', 'OTHER COUNTRIES', 225, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (22, 2, 'INTERNATIONAL', 'OTHER COUNTRIES', 175, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (23, 3, 'INTERNATIONAL', 'OTHER COUNTRIES', 175, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (24, 4, 'INTERNATIONAL', 'OTHER COUNTRIES', 125, 'DAY', 'E', current_date, 'USD');
insert into hris_class_travel_config(config_id, class_id, travel_type, international_type, rate, unit, status, created_dt, currency) values (25, 5, 'INTERNATIONAL', 'OTHER COUNTRIES', 100, 'DAY', 'E', current_date, 'USD');


---edit in hana database for WORKFORCE-----
create table hris_service_group as (select * from hris_rec_service_events_types);

ALTER TABLE hris_employees ADD (service_group_id NUMBER(4));

CREATE TABLE HRIS_WORKFORCE
   (	
    WORKFORCE_ID NUMBER(7),
	COMPANY_ID NUMBER(7), 
	LOCATION_ID NUMBER(7) NOT NULL, 
	DEPARTMENT_ID NUMBER(7),
	POSITION_ID NUMBER(7) NOT NULL, 
	service_group_ID NUMBER(7), 
	service_type_ID NUMBER(7),
	DESIGNATION_ID NUMBER(7), 
	QUOTA NUMBER(5) NOT NULL, 
	REMARKS VARCHAR(2000) DEFAULT NULL, 
	CREATED_BY NUMBER(7), 
	CREATED_DT DATE NOT NULL,
    MODIFIED_BY NUMBER(7),
	MODIFIED_DT DATE,
	CHECKED_BY NUMBER(7) DEFAULT NULL,
	CHECKED_DT DATE,
	APPROVED_BY NUMBER(7) DEFAULT NULL,
	APPROVED_DT DATE,
	STATUS SHORTTEXT(1) DEFAULT 'E' NOT NULL ,
	constraint pk_hris_workforce  PRIMARY KEY (WORKFORCE_ID),
	constraint HRIS_WORKFORCE_status CHECK (STATUS IN ('E','D')),
	CONSTRAINT UNQ_WORKFORCE UNIQUE (COMPANY_ID, LOCATION_ID,POSITION_ID),
	CONSTRAINT FK_WORKFORCE1 FOREIGN KEY (COMPANY_ID) REFERENCES HRIS_COMPANY(COMPANY_ID),
	CONSTRAINT FK_WORKFORCE3 FOREIGN KEY (POSITION_ID) REFERENCES HRIS_POSITIONS(POSITION_ID),
	CONSTRAINT FK_WORKFORCEA FOREIGN KEY (CREATED_BY) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID),
	CONSTRAINT FK_WORKFORCEM FOREIGN KEY (MODIFIED_BY) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID),
	CONSTRAINT FK_WORKFORCEC FOREIGN KEY (CHECKED_BY) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID),
	CONSTRAINT FK_WORKFORCED FOREIGN KEY (APPROVED_BY) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID)
   );
alter table hris_functional_levels add (roles varchar(5000));
alter table hris_positions add (roles varchar(5000));
alter table hris_job_history add (roles varchar(5000));

DROP TABLE HRIS_GL_ENTRY;
DROP TABLE HRIS_FINANCE_DATA;

create table HRIS_FINANCE_DATA (
    FINANCE_DATA_ID NUMBER(7) PRIMARY KEY,
    MODULE_CODE varchar(2) NOT NULL,
    MASTER_ID NUMBER(7),
    REQUEST_ID NUMBER(7) NOT NULL,
    AMOUNT NUMBER(12,2),
    BRANCH_ID NUMBER(7),
    OFFICE_ID NUMBER(7),
    department_id number(7)
);

create table HRIS_GL_ENTRY (
   GL_ENTRY_ID NUMBER(10) not null,
   FINANCE_DATA_ID NUMBER(10),
   --FOREIGN KEY (FINANCE_DATA_ID) REFERENCES HRIS_FINANCE_DATA(FINANCE_DATA_ID),
   EMPLOYEE_ID NUMBER(7) not null,
   EMPLOYEE_CODE VARCHAR(255),
   VOUCHER_NO VARCHAR(30), --MODULE_CODE/MASTER_ID/BRANCH_ID/OFFICE_ID/LOCATION_ID/LOT_ID
   CREATED_DATE DATE not null,
   CREATED_BY NUMBER(7) not null,
   AMOUNT NUMBER(12,2),
   DR_ACC_Code VARCHAR(10),
   CR_ACC_Code VARCHAR(10),
   BRANCH_ID NUMBER(7),
   CC1 NUMBER(7),
   CC2 NUMBER(7),
   CC3 VARCHAR(2) default 'NA',
   CC4 VARCHAR(2) default 'NA',
   SYNC VARCHAR(1) default 'N',
   SYNCED_DATE DATE
);


alter table HRIS_LOAN_MASTER_SETUP add(
 market_interest_rate float,
 DR_ACC_Code VARCHAR(10),
 CR_ACC_Code VARCHAR(10)
);

alter table HRIS_EMPLOYEE_LOAN_REQUEST add(
 sync  VARCHAR(1)
);

CREATE TABLE HRIS_PAY_SETUP_SPECIAL(
    PAY_ID INT,
    SALARY_TYPE_ID INT,
    FORMULA varchar(5000),
    STATUS CHAR(1) NOT NULL ,
    FLAG CHAR(1) ,
    CREATED_DT DATE DEFAULT CURRENT_DATE,
    CREATED_BY NUMBER(7,0),
    MODIFIED_BY NUMBER(7,0),
    MODIFIED_DT DATE,
    CONSTRAINT SETUP_SPECIAL_STATUS_CHECK CHECK (STATUS IN('D','E')),
    CONSTRAINT SETUP_SPECIAL_FLAG_CHECK CHECK (FLAG IN ('Y', 'N'))
   );

ALTER TABLE HRIS_PAY_SETUP_SPECIAL ADD PRIMARY KEY (PAY_ID, SALARY_TYPE_ID);

ALTER TABLE HRIS_PAY_SETUP ADD 
PRIMARY KEY (PAY_ID);
  
ALTER TABLE HRIS_PAY_SETUP_SPECIAL ADD 
FOREIGN KEY (PAY_ID) REFERENCES HRIS_PAY_SETUP(PAY_ID);

ALTER TABLE HRIS_SALARY_TYPE ADD 
PRIMARY KEY (SALARY_TYPE_ID);


 ALTER TABLE HRIS_SALARY_SHEET_GROUP ADD PRIMARY KEY (GROUP_ID);
  
ALTER TABLE HRIS_PAY_SETUP_SPECIAL ADD 
FOREIGN KEY (SALARY_TYPE_ID) REFERENCES HRIS_SALARY_TYPE(SALARY_TYPE_ID);

ALTER TABLE hris_ss_pay_value_modified ADD (remarks varchar(600));

create table hris_employee_grade_info(
  employee_id number,
  opening_grade number,
  ADDITIONAL_GRADE number,
  GRADE_VALUE number,
  GRADE_DATE date,
  FISCAL_YEAR_ID number,
  REMARKS varchar(500),
  created_by number,
  created_date date,
  modified_by number,
  modified_date date
  
);

ALTER TABLE hris_employee_grade_info ADD PRIMARY KEY (employee_id, FISCAL_YEAR_ID);
ALTER TABLE hris_employee_grade_info ADD FOREIGN KEY (employee_id) REFERENCES hris_employees(employee_id);
ALTER TABLE HRIS_FISCAL_YEARS ADD PRIMARY KEY ( FISCAL_YEAR_ID);


ALTER TABLE hris_employee_grade_info ADD FOREIGN KEY (FISCAL_YEAR_ID) REFERENCES HRIS_FISCAL_YEARS(FISCAL_YEAR_ID);

CREATE TABLE HRIS_EMPLOYEE_FILE_SETUP
(	FILE_ID NUMBER(7,0),
     FILE_NAME VARCHAR2(255),
     STATUS CHAR(1),
	FILE_TYPE_CODE CHAR(3),
	CREATED_DT DATE,
	CREATED_BY NUMBER(7,0),
	MODIFIED_DT DATE,
	MODIFIED_BY NUMBER(7,0)
   );

RENAME COLUMN HRIS_EMPLOYEE_FILE.FILE_NAME TO FILE_ID;


----hris_employees_loan_request with file path----
create table HRIS_EMPLOYEE_LOAN_REQUEST_BKP as (select * from HRIS_EMPLOYEE_LOAN_REQUEST);

ALTER TABLE HRIS_EMPLOYEE_LOAN_REQUEST ADD (FILE_PATH VARCHAR(1000));

---------------------------------------------------

--------KARYA SAMPADAN FEATURE ON DARTACHALANI--------
ALTER TABLE dc_registration_draft ADD (KS_FISCAL_YR INT);
ALTER TABLE dc_registration_draft ADD (EMP_ID INT);

ALTER TABLE dc_registration ADD (KS_FISCAL_YR INT);
ALTER TABLE dc_registration ADD (EMP_ID INT);
----------------------------------------------------------
-- Employee Profile file upload - file type -- START
-- insert into HRIS_FILE_TYPE values(004,'JPG','E',current_date,'','');  -- not required as JPEG & JPG are same for dropzone.
insert into HRIS_FILE_TYPE values(005,'PDF','E',current_date,'','');
insert into HRIS_FILE_TYPE values(006,'PNG','E',current_date,'','');

-- Employee Profile file upload - file type  -- END

--Adding acting_functional_level_id
ALTER TABLE hris_employees_bk add (ACTING_FUNCTIONAL_LEVEL_ID DECIMAL(7) DEFAULT NULL);
----

--TRAVEL NEW FLOW------------
alter table HRIS_TRAVEL_EXPENSE
add (other_expense_detail varchar(500));

alter table hris_travel_expense
add (transportation VARCHAR(255),
transportation_class VARCHAR(255),
Rate1 DECIMAL(10,2),
miles DECIMAL(7,2),
Rate2 DECIMAL (10,2),
purpose VARCHAR(500),
Jv_Number varchar(255),
Cheque_Number varchar(255),
Bank_id Decimal(7));

alter table hris_employee_travel_request
add (Jv_Number varchar(255),
Cheque_Number varchar(255),
Bank_id Decimal(7));
-----------------------------

-----------------FOR transport alternate recommender and approver-----
alter table hris_employee_travel_request add (recommender_id decimal(7),
approver_id decimal(7));
---------------------------

--------------FOR SPECIAL ATTENDANCE--------------------
CREATE TABLE HRIS_SPECIAL_ATTENDANCE_ASSIGN 
   (	"ID" DECIMAL(10,0), 
	"SP_ID" DECIMAL(7,0) NOT NULL, 
	"EMPLOYEE_ID" DECIMAL(7,0) NOT NULL, 
	"ATTENDANCE_DT" DATE NOT NULL, 
	"DISPLAY_IN_OUT" CHAR(1) NOT NULL, 
	"STATUS" CHAR (2) NOT NULL, 
	"CREATED_BY" DECIMAL(7,0), 
	"CREATED_DT" DATE DEFAULT CURRENT_DATE, 
	"MODIFIED_BY" DECIMAL(7,0), 
	"MODIFIED_DT" DATE, 
	 CHECK (DISPLAY_IN_OUT IN ('Y', 'N')), 
	  PRIMARY KEY ("ID")) ;

CREATE TABLE HRIS_SPECIAL_ATTENDANCE_SETUP
   (	"ID" DECIMAL(7,0), 
	"SP_CODE" VARCHAR(8), 
	"SP_EDESC" VARCHAR(100), 
	"STATUS" CHAR(1) NOT NULL, 
	"REMARKS" VARCHAR(500), 
	"CREATED_BY" DECIMAL(7,0), 
	"CREATED_DT" DATE DEFAULT CURRENT_DATE, 
	"MODIFIED_BY" DECIMAL(7,0), 
	"MODIFIED_DT" DATE, 
	 PRIMARY KEY ("ID"))  ;

alter table hris_attendance_detail
add(
SP_ID DECIMAL(7,0));
----------------------------------

---------FOR TRAVEL EXPENSE TEAM LEAD-------------
alter table hris_employee_travel_request
add(
IS_TEAM_LEAD CHAR(1));
-----------------------------------

------------FOR ADDRESS IN EXPERIENCE------------

alter table HRIS_EMPLOYEE_EXPERIENCES add(address varchar(255));
----------------------------

----------FOR DIVISION IN EMPLOYEE QUALIFICAITON-----------
alter table hris_employee_qualifications alter
(RANK_VALUE VARCHAR(20));
-----------------------------------------

----for travel revice and request amount------
alter table hris_employee_travel_request add (initial_advance_amount DOUBLE);
---------------------------------------------

------------for include in salary in employee table------------
alter table hris_employees add (include_in_payroll char(1));
----------------------------------------------------

-----------for travel_allowance_class in employee table-----------
alter table hris_employees add (travel_allowance_class decimal(7));
--------------------------------

---------for half day flag in travel expense--------
alter table hris_travel_expense add(HALF_DAY_FLAG char(1) default 'N');
------------------------------

--------
-- for employees recruitment
Alter table HRIS_EMPLOYEES add (INCLUSION varchar(255))

alter table hris_Job_history add PRIMARY KEY (Job_history_id);

alter table HRIS_EMPLOYEES add PRIMARY KEY (EMPLOYEE_ID);

--------FOR TRANSFER SETTLEMENT--------------
CREATE TABLE HRIS_TRANSFER_SETTLEMENT (
TRANSFER_SETTLEMENT_ID DECIMAL(7),
Job_history_id DECIMAL(7),
EMPLOYEE_ID DECIMAL(7),
REQUESTED_DATE DATE,
From_date DATE,
To_date DATE,
DEPARTURE VARCHAR(255),
DESTINATION VARCHAR(255),
ADDRESS VARCHAR(255),
TRANSFER_REASON VARCHAR(255),
TRAVELLED_DAYS DOUBLE,
TOTAL_TADA_AMT DOUBLE,
FAMILY_NO_TRAVLLED_WITH DECIMAL(5),
FAMILY_TADA_AMT DOUBLE,
WEIGHT DOUBLE,
EXTRA_WEIGHT_AMT DOUBLE,
YEARLY_SETTTLEMENT_REQ_AMT DOUBLE,
YEARLY_SETTTLEMENT_AP_AMT DOUBLE,
PLANE_EXPENSE_REQ_AMT DOUBLE,
PLANE_EXPENSE_AP_AMT DOUBLE,
PLANE_EXPENSE_FILE VARCHAR(255),
VEHICLE_EXPENSE_REQ_AMT DOUBLE, 
VEHICLE_EXPENSE_AP_AMT DOUBLE,
VEHICLE_EXPENSE_FILE VARCHAR(255),
MISC_EXPENSE_REQ_AMT DOUBLE,
MISC_EXPENSE_AP_AMT DOUBLE,
MISC_EXPENSE_FILE VARCHAR(255),
HOURS DOUBLE,
CHECKED_DT DATE,
CHECKED_BY DECIMAL(7),
APPROVED_DT DATE,
APPROVED_BY DECIMAL(7),
STATUS CHAR(2),
REMARKS VARCHAR(255),
CREATED_DT DATE,
CREATED_BY DECIMAL(7),
MODIFIED_DT DATE,
MODIFIED_BY DECIMAL(7),
DELETED_DT DATE,
DELETED_BY DECIMAL(7),
PRIMARY KEY (TRANSFER_SETTLEMENT_ID),
CONSTRAINT FK_JOB_HISTORY_ID FOREIGN KEY (Job_history_id) REFERENCES hris_job_history(Job_history_id),
CONSTRAINT FK_EMPLOYEE_ID FOREIGN KEY (EMPLOYEE_ID) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID)
);


alter table HRIS_TRANSFER_SETTLEMENT add constraint check_status_flag CHECK ( STATUS in ( 'RQ','RC','R' ,'AP','C' ,'CP','CR'));

alter table HRIS_TRANSFER_SETTLEMENT add(FOR_FAMILY char(1) default 'N');

alter table HRIS_TRANSFER_SETTLEMENT add(MILES Decimal(7,2),
misc_expense_detail varchar(500),
purpose varchar(255),
transport_class varchar(255),
expense_category varchar(255));

alter table HRIS_TRANSFER_SETTLEMENT add(transportation varchar(255));


create table HRIS_TRANSFER_SETTLEMENT_CONFIG(
CONFIG_ID DECIMAL(7),
CLASS_ID DECIMAL(7),
TADA_AMT DOUBLE,
MAX_FAMILY_ALLOWED DECIMAL(7),
MAX_ALLOWED_WEIGHT DECIMAL(7),
EXTRA_PER_KG_AMT DECIMAL(7),
YEARLY_SETTTLEMENT_AMT DOUBLE,
CHECKED_DT DATE,
CHECKED_BY DECIMAL(7),
APPROVED_DT DATE,
APPROVED_BY DECIMAL(7),
STATUS CHAR(2),
REMARKS VARCHAR(255),
CREATED_DT DATE,
CREATED_BY DECIMAL(7),
MODIFIED_DT DATE,
MODIFIED_BY DECIMAL(7),
DELETED_DT DATE,
DELETED_BY DECIMAL(7),
PRIMARY KEY (CONFIG_ID),
CONSTRAINT FK_CLASS_ID FOREIGN KEY (CLASS_ID) REFERENCES hris_classes(CLASS_ID)
);


alter table HRIS_TRANSFER_SETTLEMENT_CONFIG add(MAX_ALLOWED_WEIGHT_AMT DOUBLE);

alter table HRIS_TRANSFER_SETTLEMENT add(WEIGHT_REQ_AMT DOUBLE, WEIGHT_AP_AMT DOUBLE);


alter table HRIS_TRANSFER_SETTLEMENT add(FAMILY_NAME VARCHAR(255));
alter table HRIS_TRANSFER_SETTLEMENT add(serial_number decimal(7));

CREATE TABLE HRIS_TRANSFER_SETTLEMENT_FILES (FILE_ID DECIMAL(7,0),
	 FILE_NAME VARCHAR(255),
	 FILE_IN_DIR_NAME VARCHAR(4000),
	 UPLOADED_DATE DATE DEFAULT CURRENT_DATE,
	 JOB_HISTORY_ID DECIMAL(7,0),
	 STATUS CHAR(2),
	 REMARKS VARCHAR(255),
	CREATED_DT DATE,
	CREATED_BY DECIMAL(7),
	MODIFIED_DT DATE,
	MODIFIED_BY DECIMAL(7),
	DELETED_DT DATE,
	DELETED_BY DECIMAL(7),
    SERIAL_NUMBER DECIMAL (7),
	PRIMARY KEY (FILE_ID));

alter table HRIS_TRANSFER_SETTLEMENT add (APPROVER_REMARKS VARCHAR(255));


alter table HRIS_TRANSFER_SETTLEMENT add (
JV_NUMBER VARCHAR(255),
BANK_ID DECIMAL(7,0),
CHEQUE_NUMBER VARCHAR(255));
------------------------------

-------------FOR LOCATION ID AND ACTING_LOCATION_ID in SERVIE status update----------
alter table HRIS_JOB_HISTORY add(FROM_LOCATION_ID decimal(7),
TO_LOCATION_ID decimal(7),
FROM_ACTING_POSITION_ID decimal(7),
TO_ACTING_POSITION_ID decimal(7));
----------------------

--- SQL Script for Weekly Roaster management --
CREATE TABLE HRIS_WEEKLY_ROASTER
(
EMPLOYEE_ID NUMBER(7,0) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID),
SUN NUMBER(7,0),
MON NUMBER(7,0),
TUE NUMBER(7,0),
WED NUMBER(7,0),
THU NUMBER(7,0),
FRI NUMBER(7,0),
SAT NUMBER(7,0),
STATUS CHAR(1) DEFAULT 'E' NOT NULL,
CREATED_DT DATE DEFAULT current_date NOT NULL,
CREATED_BY NUMBER(7,0),
MODIFIED_DT DATE,
MODIFIED_BY NUMBER(7,0),
DELETED_DT DATE,
DELETED_BY NUMBER(7,0)
);


CREATE PROCEDURE hris_weekly_ros_assign (
    p_employee_id   NUMBER,
    p_sun           NUMBER,
    p_mon           NUMBER,
    p_tue           NUMBER,
    p_wed           NUMBER,
    p_thu           NUMBER,
    p_fri           NUMBER,
    p_sat           NUMBER
) AS

    v_update   NUMBER default 1;
    v_sun      NUMBER;
    v_mon      NUMBER;
    v_tue      NUMBER;
    v_wed      NUMBER;
    v_thu      NUMBER;
    v_fri      NUMBER;
    v_sat      NUMBER;
    cnt		   NUMBER;
BEGIN
    --dbms_output.put_line('TEST');
    BEGIN
        SELECT
            sun,
            mon,
            tue,
            wed,
            thu,
            fri,
            sat
        INTO
            v_sun,v_mon,v_tue,v_wed,v_thu,v_fri,v_sat DEFAULT NULL, NULL, NULL, NULL, NULL, NULL, NULL
        FROM
            hris_weekly_roaster
        WHERE
            employee_id = p_employee_id;
            
            
            
            SELECT count(*) into cnt
            
        FROM
            hris_weekly_roaster
        WHERE
            employee_id = p_employee_id;

    IF cnt < 1 THEN
            INSERT INTO hris_weekly_roaster VALUES (
                p_employee_id,
                p_sun,
                p_mon,
                p_tue,
                p_wed,
                p_thu,
                p_fri,
                p_sat,
                'E',
                current_date,
                NULL,
                NULL,
                NULL,
                NULL,
                NULL
            );
        END IF;

            --v_update default 0;
    END;

    IF
        ( v_update = 1 )
    THEN
        UPDATE hris_weekly_roaster
            SET
                sun = p_sun,
                mon = p_mon,
                tue = p_tue,
                wed = p_wed,
                thu = p_thu,
                fri = p_fri,
                sat = p_sat
        WHERE
            employee_id = p_employee_id;

    END IF;

END;

--------for location in dartachalani---------

alter table DC_DEPARTMENTS_USERS add (location_id decimal(7));

alter table DC_REGISTRATION_DRAFT  add(
from_location_id decimal(7),
location_id decimal(7));


alter table dc_user_assign add (location_id decimal(7));


alter table DC_DISPATCH_DRAFT add (from_location_id decimal(7));

alter table DC_DISPATCH add (from_location_id decimal(7));

alter table DC_REGISTRATION add(
location_id decimal(7));
-----------

----for organogram----
alter table hris_branches add (parent_department decimal(7) default null);

alter table hris_locations add (branch_id decimal(7) default null);

alter table hris_departments add(department_ndesc varchar(5000));
alter table hris_branches add (branch_ndesc varchar(5000));
alter table hris_locations add (location_ndesc varchar(5000));



create table hris_organogram_setup(
department_id decimal(7),
is_main_parent char(1),
ir_right_side char(1),
has_twins char(1),
skip_level char(1),
twins_department decimal(7),
status char(1),
created_by decimal(7),
created_dt date);


insert into hris_organogram_setup values(16,'Y','N','N','N',null,'E',456,current_date);

insert into hris_organogram_setup values(17,'N','Y','N','N',null,'E',456,current_date);

insert into hris_organogram_setup values(18,'N','Y','N','N',null,'E',456,current_date);

insert into hris_organogram_setup values(19,'N','Y','N','N',null,'E',456,current_date);

insert into hris_organogram_setup values(7,'N','N','Y','N',3,'E',456,current_date);

insert into hris_organogram_setup values(5,'N','N','N','Y',null,'E',456,current_date);

insert into hris_organogram_setup values(14,'N','N','N','Y',null,'E',456,current_date);



drop table hris_organogram_setup;

create table hris_organogram_setup(
hris_org_id decimal(7) primary key,
department_id decimal(7),
is_main_parent char(1) default 'N',
is_right_side char(1) default 'N',
has_twins char(1) default 'N',
skip_level char(1) default 'N',
twins_department decimal(7),
status char(1) default 'E',
created_by decimal(7),
created_dt date,
modified_by decimal(7),
modified_date date);


insert into hris_organogram_setup values(1, 16,'Y','N','N','N',null,'E',456,current_date, null, null);

insert into hris_organogram_setup values(2, 17,'N','Y','N','N',null,'E',456,current_date, null, null);

insert into hris_organogram_setup values(3, 18,'N','Y','N','N',null,'E',456,current_date, null, null);

insert into hris_organogram_setup values(4, 19,'N','Y','N','N',null,'E',456,current_date, null, null);

insert into hris_organogram_setup values(5, 7,'N','N','Y','N',3,'E',456,current_date, null, null);

insert into hris_organogram_setup values(6, 5,'N','N','N','Y',null,'E',456,current_date, null, null);

insert into hris_organogram_setup values(7, 14,'N','N','N','Y',null,'E',456,current_date, null, null);

-----------------------------darta chalani to_other_office-----------------------------------------------------------

alter table DC_DISPATCH_DRAFT add(to_other_office varchar(5000));

alter table DC_DISPATCH add(to_other_office varchar(5000));

alter table dc_registration_draft add(from_other_office varchar(5000));

alter table dc_registration add(from_other_office varchar(5000));


ALTER TABLE dc_dispatch alter ( remarks varchar(5000));


ALTER TABLE dc_registration alter ( remarks varchar(5000));

ALTER TABLE dc_dispatch_draft alter ( remarks varchar(5000));


ALTER TABLE dc_registration_draft alter ( remarks varchar(5000));

-----------------WORKFORCE ALTER QUERY-----------------------------

alter table hris_workforce add(branch_id decimal(7));


------------GRATUITY---------
CREATE COLUMN TABLE "HRIS_GRATUITY_GROUP" (
	 "GROUP_ID" DECIMAL(7,0) CS_FIXED,
	 "GROUP_CODE" VARCHAR(10),
	 "GROUP_EDESC" VARCHAR(255) NOT NULL ,
	 "GROUP_NDESC" VARCHAR(5000),
	 "EXPENSE_ACC_CODE" VARCHAR(255) NOT NULL,
	 "CLEARING_ACC_CODE" VARCHAR(255) NOT NULL,
	 "STATUS" SHORTTEXT(1)NOT NULL ,
	 "REMARKS" VARCHAR(255),
	 "CREATED_BY" DECIMAL(7,0) CS_FIXED,
	 "CREATED_DT" DATE DEFAULT CURRENT_DATE,
	 "CHECKED_BY" DECIMAL(7,	0) CS_FIXED,
	 "CHECKED_DATE" DATE,
	 "APPROVED_BY" DECIMAL(7,	0) CS_FIXED,
	 "APPROVED_DATE" DATE,
	 "MODIFIED_BY" DECIMAL(7,	0) CS_FIXED,
	 "MODIFIED_DT" DATE,
	 "DELETED_BY" DECIMAL(7,	0) CS_FIXED,
	 "DELETED_DT" DATE,
	 CONSTRAINT "PK_GRATUITY_GROUP_ID" PRIMARY KEY ("GROUP_ID"),
	 CONSTRAINT "GRAG_STATUS_FLAG_CHECK" CHECK ("STATUS" IN ( 'E', 'D' ) ));

CREATE COLUMN TABLE "HRIS_GRATUITY" (
	 "GRATUITY_ID" DECIMAL(7,0) CS_FIXED,
	 "EMPLOYEE_ID" DECIMAL(7,0) CS_FIXED,
	 "EXTRA_SERVICE_YR" DECIMAL(4,2) CS_FIXED,
	 "TOTAL_AMOUNT" DECIMAL(14,2) NOT NULL,
	 "APPROVED_AMOUNT" DECIMAL(14,2),
	 "VOUCHER_NO" VARCHAR(100) NOT NULL,
	 "STATUS" SHORTTEXT(1)NOT NULL ,
	 "REMARKS" VARCHAR(255),
	 "CREATED_BY" DECIMAL(7,0) CS_FIXED,
	 "CREATED_DT" DATE DEFAULT CURRENT_DATE,
	 "CHECKED_BY" DECIMAL(7,	0) CS_FIXED,
	 "CHECKED_DATE" DATE,
	 "APPROVED_BY" DECIMAL(7,	0) CS_FIXED,
	 "APPROVED_DATE" DATE,
	 "MODIFIED_BY" DECIMAL(7,	0) CS_FIXED,
	 "MODIFIED_DT" DATE,
	 "DELETED_BY" DECIMAL(7,	0) CS_FIXED,
	 "DELETED_DT" DATE,
	 CONSTRAINT "PK_GRATIUTY_ID" PRIMARY KEY ("GRATUITY_ID"),
	 FOREIGN KEY ("EMPLOYEE_ID") REFERENCES "HRIS_EMPLOYEES"("EMPLOYEE_ID") ON DELETE CASCADE,
	 CONSTRAINT "GRA_STATUS_FLAG_CHECK" CHECK ("STATUS" IN ( 'E', 'D' ) ));

CREATE COLUMN TABLE "HRIS_GRATUITY_DETAIL" (
	 "GRA_DETAIL_ID" DECIMAL(7,0) CS_FIXED,
	 "GRATUITY_ID" DECIMAL(7,0) CS_FIXED,	 
	 "GROUP_ID" DECIMAL(7,0) CS_FIXED,
	 "CALCULATED_AMOUNT" DECIMAL(14,2) NOT NULL,
	 "APPROVED_AMOUNT" DECIMAL(14,2),
	 "STATUS" SHORTTEXT(1)NOT NULL ,
	 "REMARKS" VARCHAR(255),
	 "CREATED_BY" DECIMAL(7,0) CS_FIXED,
	 "CREATED_DT" DATE DEFAULT CURRENT_DATE,
	 "CHECKED_BY" DECIMAL(7,	0) CS_FIXED,
	 "CHECKED_DATE" DATE,
	 "APPROVED_BY" DECIMAL(7,	0) CS_FIXED,
	 "APPROVED_DATE" DATE,
	 "MODIFIED_BY" DECIMAL(7,	0) CS_FIXED,
	 "MODIFIED_DT" DATE,
	 "DELETED_BY" DECIMAL(7,	0) CS_FIXED,
	 "DELETED_DT" DATE,
	 CONSTRAINT "PK_GRATIUTY_DTL_ID" PRIMARY KEY ("GRA_DETAIL_ID"),
	 FOREIGN KEY ("GRATUITY_ID") REFERENCES "HRIS_GRATUITY"("GRATUITY_ID") ON DELETE CASCADE,
	 FOREIGN KEY ("GROUP_ID") REFERENCES "HRIS_GRATUITY_GROUP"("GROUP_ID") ON DELETE CASCADE,
	 CONSTRAINT "GRAD_STATUS_FLAG_CHECK" CHECK ("STATUS" IN ( 'E', 'D' ) ));
	 

alter table HRIS_GRATUITY_DETAIL add (DESCRIPTION varchar(5000));
----------CREATE SCRIPT FOR SERVICES AND SERVICE_SUBGROUP TABLE-----

CREATE TABLE HRIS_SERVICE_SUBGROUP
	("SERVICE_SUBGROUP_ID" DECIMAL(7,
	0) CS_FIXED,
	 "SERVICE_NAME" VARCHAR(255),
	 "REMARKS" VARCHAR(255),
	 "STATUS" CHAR(1) CS_FIXEDSTRING DEFAULT 'E',
	 "CREATED_DT" DATE  DEFAULT CURRENT_DATE,
	 "MODIFIED_DT" DATE ,
	 "CREATED_BY" DECIMAL(7,
	0) CS_FIXED,
	 "MODIFIED_BY" DECIMAL(7,
	0) CS_FIXED,
	 "COMPANY_ID" DECIMAL(7,
	0) CS_FIXED,
	 CONSTRAINT "PK_HRIS_SERVICE_SUBGROUP" PRIMARY KEY ("SERVICE_SUBGROUP_ID"));


CREATE TABLE HRIS_SERVICES
	("SERVICE_ID" DECIMAL(7,
	0) CS_FIXED,
	 "SERVICE_NAME" VARCHAR(255),
	 "REMARKS" VARCHAR(255),
	 "STATUS" CHAR(1) CS_FIXEDSTRING DEFAULT 'E',
	 "CREATED_DT" DATE  DEFAULT CURRENT_DATE,
	 "MODIFIED_DT" DATE ,
	 "CREATED_BY" DECIMAL(7,
	0) CS_FIXED,
	 "MODIFIED_BY" DECIMAL(7,
	0) CS_FIXED,
	 "COMPANY_ID" DECIMAL(7,
	0) CS_FIXED,
	 CONSTRAINT "PK_HRIS_SERVICES" PRIMARY KEY ("SERVICE_ID"));

ALTER TABLE HRIS_EMPLOYEES ADD(
SERVICE_SUBGROUP_ID DECIMAL(7),
SERVICE_ID DECIMAL(7));



alter table hris_service_subgroup add( service_id decimal(7));


INSERT INTO HRIS_SERVICE_SUBGROUP VALUES (1,'Electrical','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Computer','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Civil','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Industrial','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'CA','','E',current_date,current_date,456,456,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Mechanical','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Law','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Petroleum','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Environmental','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Chemical','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Electronics and Communications','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Mistri-Mechanical','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Telephone Operator','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'Mistri-Electrical','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'General','','E',current_date,current_date,456,456,1,null);

INSERT INTO HRIS_SERVICE_SUBGROUP VALUES ((select max(service_subgroup_id) + 1 from hris_service_subgroup),'CA','','E',current_date,current_date,456,456,1,null);


update hris_service_subgroup set service_id = 3 where service_subgroup_id in (1,2,3,4,5,7,8,9,10,11,13);


update hris_service_subgroup set service_id = 5 where service_subgroup_id in (6,12);


insert into hris_services values(1,'NA',null,'E','2022-2-16','2022-2-16',456,456,1);
insert into hris_services values(2,'Administration',null,'E','2022-2-16','2022-2-16',456,456,1);
insert into hris_services values(3,'Engineer',null,'E','2022-2-16','2022-2-16',456,456,1);
insert into hris_services values(4,'Lab',null,'E','2022-2-16','2022-2-16',456,456,1);
insert into hris_services values(5,'Miscellenous',null,'E','2022-2-16','2022-2-16',456,456,1);
insert into hris_services values(6,'Finance',null,'E','2022-2-16','2022-2-16',456,456,1);


alter table hris_workforce add(
service_id decimal(7),
service_subgroup_id decimal(7))


alter table hris_gratuity add (retirement_date date);


----------------------job history----------------------
alter table hris_job_history add(
to_acting_functional_level_id decimal(7));


alter table hris_employees_grade_ceiling_master_setup 
add (service_type_id decimal(7), functional_level_id decimal(7));

update hris_employees_grade_ceiling_master_setup set service_type_id=2 where position_type = 'Non-Technical';

update hris_employees_grade_ceiling_master_setup set service_type_id=1 where position_type = 'Technical';

update hris_employees_grade_ceiling_master_setup set functional_level_id=10 where functional_level_edesc = 'IV';
update hris_employees_grade_ceiling_master_setup set functional_level_id=11 where functional_level_edesc = 'V';
update hris_employees_grade_ceiling_master_setup set functional_level_id=12 where functional_level_edesc = 'II';
update hris_employees_grade_ceiling_master_setup set functional_level_id=13 where functional_level_edesc = 'I';
update hris_employees_grade_ceiling_master_setup set functional_level_id=14 where functional_level_edesc = 'III';
update hris_employees_grade_ceiling_master_setup set functional_level_id=9 where functional_level_edesc = '2';
update hris_employees_grade_ceiling_master_setup set functional_level_id=15 where functional_level_edesc = '1';
update hris_employees_grade_ceiling_master_setup set functional_level_id=16 where functional_level_edesc = '12';
update hris_employees_grade_ceiling_master_setup set functional_level_id=1 where functional_level_edesc = '11';
update hris_employees_grade_ceiling_master_setup set functional_level_id=2 where functional_level_edesc = '10';
update hris_employees_grade_ceiling_master_setup set functional_level_id=3 where functional_level_edesc = '9';
update hris_employees_grade_ceiling_master_setup set functional_level_id=4 where functional_level_edesc = '8';
update hris_employees_grade_ceiling_master_setup set functional_level_id=5 where functional_level_edesc = '7';
update hris_employees_grade_ceiling_master_setup set functional_level_id=6 where functional_level_edesc = '6';
update hris_employees_grade_ceiling_master_setup set functional_level_id=7 where functional_level_edesc = '5';
update hris_employees_grade_ceiling_master_setup set functional_level_id=8 where functional_level_edesc = '4';

CREATE TABLE HRIS_SETTLEMENT_CHECK (ID DECIMAL CS_DECIMAL_FLOAT,
	 FROM_OFFICE_ID DECIMAL(7,
	0) CS_FIXED,
	 TO_OFFICE_ID DECIMAL(7,
	0) CS_FIXED,
	 REMARKS CHAR(2) CS_FIXEDSTRING,
	 STATUS CHAR(1) CS_FIXEDSTRING,
	 CREATED_DT DATE CS_DAYDATE,
	 CREATED_BY VARCHAR(255),
	 MODIFIED_BY DECIMAL(7,
	0) CS_FIXED,
	 MODIFIED_DT DATE);


-------update position----------------
alter table hris_positions add(
parent_functional_level_id decimal(7));

update hris_positions set parent_functional_level_id = 8 where position_id =9;
update hris_positions set parent_functional_level_id = 6 where position_id =7;
update hris_positions set parent_functional_level_id = 3 where position_id =4;
update hris_positions set parent_functional_level_id = 1 where position_id =2;
update hris_positions set parent_functional_level_id = 5 where position_id =6;
update hris_positions set parent_functional_level_id = 2 where position_id =3;
update hris_positions set parent_functional_level_id = 16 where position_id =1;
update hris_positions set parent_functional_level_id = 4 where position_id =5;
update hris_positions set parent_functional_level_id = 7 where position_id =8;



--------settlement----------------
insert into hris_settlement_check values(1,26,18,'NC','E', null,null,null,null);
insert into hris_settlement_check values(2,26,3,'NC','E', null,null,null,null);
insert into hris_settlement_check values(3,26,28,'NC','E', null,null,null,null);
insert into hris_settlement_check values(4,26,26,'NC','E', null,null,null,null);
insert into hris_settlement_check values(5,1,10,'NS','E', null,null,null,null);
insert into hris_settlement_check values(6,1,14,'NC','E', null,null,null,null);
insert into hris_settlement_check values(7,2,23,'NC','E', null,null,null,null);
insert into hris_settlement_check values(8,2,11,'NC','E', null,null,null,null);
insert into hris_settlement_check values(9,8,16,'NC','E', null,null,null,null);
insert into hris_settlement_check values(10,4,19,'NC','E', null,null,null,null);
insert into hris_settlement_check values(11,5,25,'NC','E', null,null,null,null);
insert into hris_settlement_check values(12,5,20,'NC','E', null,null,null,null);
insert into hris_settlement_check values(13,9,12,'NS','E', null,null,null,null);
insert into hris_settlement_check values(14,9,21,'NC','E', null,null,null,null);
insert into hris_settlement_check values(15,9,21,'NC','E', null,null,null,null);
insert into hris_settlement_check values(16,6,27,'NC','E', null,null,null,null);
insert into hris_settlement_check values(17,7,22,'NC','E', null,null,null,null);
insert into hris_settlement_check values(18,7,13,'NS','E', null,null,null,null);


----------------job history---------------------------


alter table hris_job_history add(
to_service_group_id decimal(7),
to_service_sub_group_id decimal(7));

<<<<<<< HEAD
<<<<<<< HEAD
=======

>>>>>>> 4554561073661868960e71f3f793f69e4d08ed22
=======

>>>>>>> origin/ayush-nepal

------------loan request-----------------

alter table hris_employee_loan_request add(     
EMPLOYEE_CODE varchar(15),
BASIC_SALARY decimal(7),
BASIC_GRADE decimal(7),
NET_AMOUNT decimal(7),
SALARY_GRADE decimal(7),
APPLIED_LOAN decimal(7),
PERIOD decimal(3),
EPF decimal(7),
INCOME_TAX decimal(7),
SST decimal(7),
CIT decimal(7),
EWF decimal(7),
LAND_LOAN decimal(7),
MOTORCYCLE_LOAN decimal(7),
HML decimal(7),
SOCIAL_LOAN decimal(7),
VEHICLE_PURCHASE_LOAN decimal(7),
MEDICAL_LOAN decimal(7),
CYCLE_LOAN decimal(7),
EDUCATION_LOAN decimal(7),
FAMILY_INSURANCE_LOAN decimal(7),
MODERN_TECHNOLOGY decimal(7),
REPAYMENT_INSTALLMENTS decimal(7),
MONTHLY_INSTALLMENT_AMOUNT decimal(7),
MONTHLY_INSTALLMENT_RATE decimal(7));
----------------OVERTIME CLAIM NEW FLOW----------------
CREATE TABLE HRIS_EMPLOYEE_OVERTIME_CLAIM_REQUEST
	(OVERTIME_CLAIM_ID DECIMAL(7,0),
	 EMPLOYEE_ID DECIMAL(7),
	 MONTH_ID DECIMAL(7),
	 TOTAL_REQ_OT_HOURS DECIMAL(7,2),
	 TOTAL_REQ_SUBSTITUTE_LEAVE DECIMAL(7,2),
	 TOTAL_REQ_DASHAIN_TIHAR_LEAVE DECIMAL(7,2),
	 TOTAL_REQ_GRAND_TOTAL_LEAVE DECIMAL(7,2),
	 TOTAL_REQ_LUNCH_ALLOWANCE DECIMAL(7,2),
	 TOTAL_REQ_NIGHT_ALLOWANCE DECIMAL(7,2),
	 TOTAL_REQ_LOCKING_ALLOWANCE DECIMAL(7,2),
	 TOTAL_REQ_OT_DAYS DECIMAL(7,2),
	 TOTAL_APP_OT_HOURS DECIMAL(7,2),
	 TOTAL_APP_SUBSTITUTE_LEAVE DECIMAL(7,2),
	 TOTAL_APP_DASHAIN_TIHAR_LEAVE DECIMAL(7,2),
	 TOTAL_APP_GRAND_TOTAL_LEAVE DECIMAL(7,2),
	 TOTAL_APP_LUNCH_ALLOWANCE DECIMAL(7,2),
	 TOTAL_APP_NIGHT_ALLOWANCE DECIMAL(7,2),
	 TOTAL_APP_LOCKING_ALLOWANCE DECIMAL(7,2),
	 TOTAL_APP_OT_DAYS DECIMAL(7,2),
	 REMARKS VARCHAR(255),
	 STATUS CHAR(2),
	 RECOMMENDED_BY DECIMAL(7),
	 RECOMMENDED_DATE DATE,
	RECOMMENDED_REMARKS VARCHAR(255),
	APPROVED_BY DECIMAL(7),
	APPROVED_DATE DATE,
	APPROVED_REMARKS VARCHAR(255),
	 CREATED_DT DATE  DEFAULT CURRENT_DATE,
	 MODIFIED_DT DATE ,
	 CREATED_BY DECIMAL(7,
	0),
	 MODIFIED_BY DECIMAL(7,
	0));

CREATE TABLE HRIS_EMPLOYEE_OVERTIME_CLAIM_DETAIL
	(OVERTIME_CLAIM_DETAIL_ID DECIMAL(7,0),
		OVERTIME_CLAIM_ID DECIMAL(7,0),
		ATTENDANCE_DT DATE,
		DAY_CODE CHAR(1),
		IN_TIME TIMESTAMP,
		OUT_TIME TIMESTAMP,
		TOTAL_HOUR DECIMAL(7,2),
		OT_HOUR DECIMAL(7,2),
		LUNCH_ALLOWANCE DECIMAL(7,2),
		LOCKING_ALLOWANCE DECIMAL(7,2),
		NIGHT_ALLOWANCE DECIMAL(7,2),
		LEAVE_REWARD DECIMAL(7,2),
		DASHAIN_TIHAR_LEAVE_REWARD DECIMAL(7,2),
		TOTAL_LEAVE_REWARD DECIMAL(7,2),
		TYPE_FLAG CHAR(1),
		CANCELED_BY_RA CHAR(1),
	 STATUS CHAR(2),
	 CREATED_DT DATE  DEFAULT CURRENT_DATE,
	 MODIFIED_DT DATE ,
	 CREATED_BY DECIMAL(7,
	0),
	 MODIFIED_BY DECIMAL(7,
	0));



<<<<<<< HEAD
	-- Recruitment--
	1. ALTER TABLE HRIS_REC_VACANCY_APPLICATION ADD (APPLICATION_TYPE varchar(255));
	2. CREATE TABLE HRIS_REC_INSTRUCTIONS (
		INSTRUCTION_ID INTEGER NOT NULL,
		INSTRUCTION_CODE VARCHAR(255),
		DESCRIPTION_EDESC VARCHAR(5000),
		DESCRIPTION_NDESC VARCHAR(5000),
		STATUS VARCHAR(6),
		CREATED_BY DECIMAL(7),
		CREATED_DT DATE DEFAULT CURRENT_DATE,
		MODIFIED_BY DECIMAL(7),
		MODIFIED_DT DATE DEFAULT CURRENT_DATE,
		CHECKED_BY DECIMAL(7),
		CHECKED_DT DATE DEFAULT CURRENT_DATE,
		APPROVED_BY DECIMAL(7),
		APPROVED_DT DATE DEFAULT CURRENT_DATE,
		DELETED_BY DECIMAL(7),
		DELETED_DT DATE DEFAULT CURRENT_DATE
		);
	3. ALTER TABLE hris_job_history ADD (INCLUSION varchar(255));
	4.CREATE TABLE HRIS_REC_TEMP_PAYMENT (
		ID INTEGER NOT NULL,
		MERCHANT_ID VARCHAR(255),
		APP_ID VARCHAR(255),
		APP_NAME VARCHAR(255),
		TXN_ID VARCHAR(5000),
		TXN_DATE VARCHAR(255),
		TXN_CUR VARCHAR(255),
		AMOUNT DECIMAL(7,2),
		REFERENCE_ID VARCHAR(5000),
		REMARKS VARCHAR(5000),
		PARTICULARS VARCHAR(5000),
		TOKEN VARCHAR(5000),
		STATUS VARCHAR(5000),
		CREATED_DT DATE DEFAULT CURRENT_DATE,
		DELETED_BY DECIMAL(7),
		DELETED_DT DATE DEFAULT CURRENT_DATE
		);

	
=======
>>>>>>> origin/ayush-nepal
alter table HRIS_EMPLOYEE_OVERTIME_CLAIM_DETAIL add(OT_REMARKS varchar(5000));


alter table hris_Employee_leave_addition add (OT_DETAIL_ID DECIMAL(10));
	

----------------LOAN EMI TABLE---------------------------------

CREATE TABLE HRIS_EMPLOYEE_EMI_DETAIL(
EMI_ID DECIMAL(7),
LOAN_REQUEST_ID DECIMAL(7),
TENURE DECIMAL(7),
LOAN_AMOUNT DECIMAL(7,2),
INSTALLMENT DECIMAL(7,2),
INTEREST DECIMAL(7,2),
PRINCIPAL_REPAID DECIMAL(7,2),
RENAINING_PRINCIPAL DECIMAL(7,2),
STATUS CHAR(2),
CREATED_DT DATE,
CREATED_BY DECIMAL(7),
MODIFIED_DT DATE,
MODIFIED_BY DECIMAL(7));


alter table hris_employee_emi_detail add(
employee_id decimal(7));



alter table hris_employee_emi_detail add(
repayment_installments decimal(7),
remaining_principal decimal(7));


alter table HRIS_EMPLOYEE_EMI_DETAIL alter(
LOAN_AMOUNT decimal(9,2));

<<<<<<< HEAD
=======
alter table hris_employee_loan_request add(
MONTH_ID decimal(7),
FISCAL_YEAR_ID decimal(7));


rename column hris_employee_loan_request.monthly_installment_rate to monthly_interest_rate;


alter table hris_employee_loan_request alter(
MONTHLY_INTEREST_RATE decimal(7,2));


>>>>>>> origin/ayush-nepal
---------------------------------------------------------------------------------------------------------
