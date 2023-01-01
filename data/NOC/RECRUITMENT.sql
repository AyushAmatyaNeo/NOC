-- Vacancy TABLE
CREATE TABLE HRIS_REC_OPENINGS
(   
    "OPENING_ID" DECIMAL(7,0),
	"OPENING_NO" VARCHAR(25),
    "VACANCY_NO" NUMBER(7,0) NOT NULL,   
	"START_DATE" DATE NOT NULL ,
	"END_DATE" DATE  NOT NULL ,
	"EXTENDED_DATE" DATE ,
	"INSTRUCTION_EDESC" VARCHAR(5000),
	"INSTRUCTION_NDESC" VARCHAR(5000),
	"STATUS" CHAR(1) DEFAULT 'E',
	"CREATED_BY" DECIMAL(7,0) ,
	"CREATED_DT" DATE ,
	"MODIFIED_BY" DECIMAL(7,0) DEFAULT NULL,
	"MODIFIED_DT" DATE DEFAULT NULL,
	"CHECKED_BY" DECIMAL(7,0) DEFAULT NULL,
	"CHECKED_DT" DATE DEFAULT NULL,
	"APPROVED_BY" DECIMAL(7,0) DEFAULT NULL,
	"APPROVED_DT" DATE DEFAULT NULL ,
	"DELETED_BY" DECIMAL(7,0) DEFAULT NULL ,
	"DELETED_DT" DATE DEFAULT NULL
);
-- ALTER TABLE HRIS_REC_OPENINGS ADD("RESERVATION_NO" NUMBER(7,0) NOT NULL);
-- New Table
CREATE TABLE HRIS_REC_VACANCY
(
    VACANCY_ID NUMBER(7,0)  PRIMARY KEY,
    VACANCY_NO NUMBER(7,0),
    VACANCY_TYPE CHAR(8),
    OPENING_ID NUMBER(7,0) NOT NULL,  --FK
    LEVEL_ID NUMBER(7,0), --FK   
    AD_NO VARCHAR(20),
	RESERVATION_NO NUMBER(7,0) NOT NULL,
    SERVICE_TYPES_ID NUMBER(7,0),  --FK
    SERVICE_EVENTS_ID NUMBER(7,0),  --FK  
    POSITION_ID NUMBER(7,0),  --FK HRIS_DESIGNATIONS (DESIGNATION_ID)
    VACANCY_RESERVATION_NO NUMBER(7,0),    
    QUALIFICATION_ID NUMBER(7,0),
    EXPERIENCE VARCHAR(255),   
    DEPARTMENT_ID NUMBER(7,0) NOT NULL,  --FK
    SKILL_ID VARCHAR(255),
    INCLUSION_ID VARCHAR(255),
    REMARK NVARCHAR(255),
    STATUS CHAR(1) DEFAULT 'E',
    CREATED_BY  NUMBER(7,0),
    CREATED_DT DATE,
    MODIFIED_BY NUMBER(7,0) DEFAULT NULL,
    MODIFIED_DT DATE DEFAULT NULL,
    DELETED_BY NUMBER(7,0) DEFAULT NULL,
    DELETED_DT DATE DEFAULT NULL    
);

CREATE TABLE HRIS_REC_VACANCY_OPTIONS
(
    VACANCY_OPTION_ID NUMBER(7,0) PRIMARY KEY,
    VACANCY_ID NUMBER(7,0) NOT NULL,  --FK
    OPTION_ID NUMBER(7,0) NOT NULL, --FK
    QUOTA NUMBER(2,0) NOT NULL,
    OPEN_INTERNAL CHAR(10),
    NORMAL_AMT NUMBER(7,2), --PRICE
    LATE_AMT NUMBER(7,2), --PRICE
    REMARKS NVARCHAR(255),
    STATUS CHAR(1) NOT NULL,
    CREATED_BY  NUMBER(7,0),
    CREATED_DT DATE,
    MODIFIED_BY NUMBER(7,0) DEFAULT NULL,
    MODIFIED_DT DATE DEFAULT NULL,
    CHECKED_BY NUMBER (7,0) DEFAULT NULL,
    CHECKED_DT DATE DEFAULT NULL,
    APPROVED_BY NUMBER (7,0) DEFAULT NULL,
    APPROVED_DT DATE DEFAULT NULL,
    DELETED_BY NUMBER(7,0) DEFAULT NULL,
    DELETED_DT DATE DEFAULT NULL
);
--  Inclusion
CREATE TABLE HRIS_REC_OPTIONS
(
    OPTION_ID NUMBER(7,0) PRIMARY KEY,
    OPTION_EDESC VARCHAR(255) NOT NULL,
    OPTION_NDESC VARCHAR(255),
    REMARKS VARCHAR(255),
    STATUS CHAR(1) NOT NULL,
    CREATED_BY  NUMBER(7,0),
    CREATED_DT DATE,
    MODIFIED_BY NUMBER(7,0) DEFAULT NULL,
    MODIFIED_DT DATE DEFAULT NULL,
    DELETED_BY NUMBER(7,0) DEFAULT NULL,
    DELETED_DT DATE DEFAULT NULL
);
CREATE TABLE HRIS_REC_OPENINGS_DOCUMENTS
(  
    FILE_ID NUMBER(7,0) PRIMARY KEY, 
    FILE_NAME VARCHAR(255), 
    FILE_IN_DIR_NAME VARCHAR(2000), 
    UPLOADED_DATE DATE DEFAULT CURRENT_DATE, 
    OPENING_ID NUMBER(7,0), --FK
    STATUS CHAR(1) DEFAULT 'E'
);
CREATE TABLE HRIS_REC_STAGES
(
    REC_STAGE_ID NUMBER(2,0) PRIMARY KEY,
    STAGE_EDESC NVARCHAR(255),
    STAGE_NDESC NVARCHAR(255),
    ORDER_NO NUMBER(3,1),
    IS_FINAL CHAR(1) DEFAULT 'N',
    STATUS CHAR(1) DEFAULT 'E',
    CREATED_BY  NUMBER(7,0),
    CREATED_DT DATE,
    MODIFIED_BY NUMBER(7,0) DEFAULT NULL,
    MODIFIED_DT DATE DEFAULT NULL,
    CHECKED_BY NUMBER (7,0) DEFAULT NULL,
    CHECKED_DT DATE DEFAULT NULL,
    APPROVED_BY NUMBER (7,0) DEFAULT NULL,
    APPROVED_DT DATE DEFAULT NULL,
    DELETED_BY NUMBER(7,0) DEFAULT NULL,
    DELETED_DT DATE DEFAULT NULL
);

CREATE TABLE HRIS_REC_VACANCY_STAGES
(
    REC_VACANCY_STAGE_ID NUMBER(7,0) PRIMARY KEY,
    REC_STAGE_ID NUMBER(2,0), --FK
    VACANCY_ID NUMBER(7,0) NOT NULL,  --FK
    REMARKS VARCHAR(255),
    STATUS CHAR(1) NOT NULL,
    CREATED_BY  NUMBER(7,0),
    CREATED_DT DATE,
    MODIFIED_BY NUMBER(7,0) DEFAULT NULL,
    MODIFIED_DT DATE DEFAULT NULL,
    CHECKED_BY NUMBER (7,0) DEFAULT NULL,
    CHECKED_DT DATE DEFAULT NULL,
    APPROVED_BY NUMBER (7,0) DEFAULT NULL,
    APPROVED_DT DATE DEFAULT NULL,
    DELETED_BY NUMBER(7,0) DEFAULT NULL,
    DELETED_DT DATE DEFAULT NULL
);
-- Level
CREATE TABLE HRIS_REC_VACANCY_LEVELS
(
    VACANCY_LEVEL_ID NUMBER (7,0) PRIMARY KEY,
    FUNCTIONAL_LEVEL_ID NUMBER(7,0) NOT NULL, --FK
    POSITION_ID NUMBER(7,0), -- FK
    OPENING_ID NUMBER(7,0), --FK
    EFFECTIVE_DATE DATE DEFAULT CURRENT_DATE,
    NORMAL_AMOUNT NUMBER(7,0) NOT NULL,
    LATE_AMOUNT NUMBER(7,0),
    INCLUSION_AMOUNT NUMBER(7,0),
    MIN_AGE NUMBER(2,0),
    MAX_AGE NUMBER(2,0),
    STATUS CHAR(1) DEFAULT 'E',
    CREATED_BY NUMBER(7,0) NOT NULL,
    CREATED_DATE DATE DEFAULT CURRENT_DATE,
    MODIFIED_BY NUMBER(7,0) NULL,
    MODIFIED_DATE DATE DEFAULT NULL,
    DELETED_BY NUMBER(7,0) NULL,
    DELETED_DATE DATE DEFAULT NULL
);
CREATE TABLE HRIS_REC_VACANCY_INCLUSION
(
    VACANCY_INCLUSION_ID NUMBER (7,0) PRIMARY KEY,
    INCLUSION_ID VARCHAR(20), --FK
    VACANCY_ID NUMBER (7,0),
    STATUS CHAR(1) DEFAULT 'E',
    CREATED_BY NUMBER(7,0) NOT NULL,
    CREATED_DATE DATE DEFAULT CURRENT_DATE,
    MODIFIED_BY NUMBER(7,0) NULL,
    MODIFIED_DATE DATE DEFAULT NULL,
    DELETED_BY NUMBER(7,0) NULL,
    DELETED_DATE DATE DEFAULT NULL
);
-- TABLE FOR STAGES OF APPLICATION IN USER APPLICATION
CREATE TABLE HRIS_REC_APPLICATION_STAGES_STATUS
(
    STAGE_STATUS_ID NUMBER(2,0) PRIMARY KEY,
    STAGE_ID NUMBER(7,0), -- FK
    APPLICATION_ID NUMBER(20,0),
    REMARKS NVARCHAR(1500),
    STATUS CHAR(1) DEFAULT 'E',
    CREATED_BY  NUMBER(7,0),
    CREATED_DT DATE,
    MODIFIED_BY NUMBER(7,0) DEFAULT NULL,
    MODIFIED_DT DATE DEFAULT NULL,
    DELETED_BY NUMBER(7,0) DEFAULT NULL,
    DELETED_DT DATE DEFAULT NULL
);
ALTER TABLE HRIS_REC_VACANCY_APPLICATION add (REMARKS NVARCHAR(1500));

-- truncate table HRIS_REC_OPENINGS;
-- truncate table HRIS_REC_VACANCY;
-- truncate table HRIS_REC_VACANCY_OPTIONS;
-- truncate table HRIS_REC_OPTIONS;
-- truncate table HRIS_REC_OPENINGS_DOCUMENTS;
-- truncate table HRIS_REC_STAGES;
-- truncate table HRIS_REC_VACANCY_STAGES;
-- truncate table HRIS_REC_VACANCY_LEVELS;
-- truncate table HRIS_REC_VACANCY_INCLUSION;

HRIS_REC_OPENINGS
HRIS_REC_VACANCY
HRIS_REC_VACANCY_OPTIONS
HRIS_REC_OPTIONS
HRIS_REC_OPENINGS_DOCUMENTS
HRIS_REC_STAGES
HRIS_REC_VACANCY_STAGES
HRIS_REC_VACANCY_LEVELS
HRIS_REC_VACANCY_INCLUSION

-- NOT REQUIRED

CREATE TABLE HRIS_REC_EXAM_CENTERS
(
    EXAM_CENTER_ID NUMBER(7,0) PRIMARY KEY,
    EXAM_CENTER_CODE VARCHAR(10),
    EXAM_CENTER_NAME VARCHAR(100) NOT NULL,
    STATUS CHAR(1) DEFAULT 'E',
    CREATED_DATE DATE DEFAULT CURRENT_DATE
);
CREATE TABLE HRIS_REC_EXAM_TYPES
(
    EXAM_TYPE_ID NUMBER(7,0) PRIMARY KEY,
    EXAM_TYPE_CODE VARCHAR(10),
    EXAM_TYPE_NAME VARCHAR(100) NOT NULL,
    STATUS CHAR(1) DEFAULT 'E',
    CREATED_DATE DATE DEFAULT CURRENT_DATE 
);


-- Add FOREIGN KEY
ALTER TABLE HRIS_REC_VACANCY ADD CONSTRAINT FK_DEPARTMENT_ID FOREIGN KEY (DEPARTMENT_ID) REFERENCES HRIS_DEPARTMENTS (DEPARTMENT_ID);
ALTER TABLE HRIS_REC_VACANCY_OPTIONS ADD CONSTRAINT FK_OPTION_ID FOREIGN KEY (OPTION_ID) REFERENCES HRIS_REC_OPTIONS (OPTION_ID);

ALTER TABLE "HRISVISMA2"."HRIS_REC_VACANCY" ADD CONSTRAINT FK_LEVEL_ID FOREIGN KEY (LEVEL_ID) REFERENCES HRIS_FUNCTIONAL_LEVELS (FUNCTIONAL_LEVEL_ID);

ALTER TABLE "HRISVISMA2"."HRIS_REC_VACANCY" ADD CONSTRAINT FK_SERVICE_TYPES_ID 
FOREIGN KEY (SERVICE_TYPES_ID) REFERENCES HRIS_SERVICE_TYPES (SERVICE_TYPE_ID);

ALTER TABLE "HRISVISMA2"."HRIS_REC_VACANCY" ADD CONSTRAINT FK_INCLUSION_ID 
FOREIGN KEY (INCLUSION_ID) REFERENCES HRIS_REC_OPTIONS (OPTION_ID);


-- Add primary KEY
ALTER TABLE HRIS_DESIGNATIONS  ADD primary key ("DESIGNATION_ID");

ALTER TABLE "HRISVISMA2"."HRIS_SERVICE_TYPES" ADD primary key ("SERVICE_TYPE_ID");

ALTER TABLE "HRISVISMA2"."HRIS_REC_SERVICE_EVENTS_TYPES" ADD primary key ("SERVICE_EVENT_ID");


-- Add Table
ALTER TABLE <tablename> ADD (<columnname> <datatype>);
ALTER TABLE "HRISVISMA2"."HRIS_REC_VACANCY" ADD (SERVICE_TYPES_ID NUMBER(7,0));
ALTER TABLE "HRISVISMA2"."HRIS_REC_VACANCY" ADD (SERVICE_EVENTS_ID NUMBER(7,0));

ALTER TABLE "HRISVISMA2"."HRIS_REC_VACANCY_USERS" ADD (MODIFIED_DT DATE DEFAULT NULL);

MODIFIED_DT DATE DEFAULT NULL

-- NEW CHANGES

ALTER TABLE HRIS_FUNCTIONAL_LEVELS ADD(DESIGNATION_ID VARCHAR(40));
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '1' WHERE FUNCTIONAL_LEVEL_ID = 16;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '2' WHERE FUNCTIONAL_LEVEL_ID = 1;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '3' WHERE FUNCTIONAL_LEVEL_ID = 2;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '4' WHERE FUNCTIONAL_LEVEL_ID = 3;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '5' WHERE FUNCTIONAL_LEVEL_ID = 4;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '6' WHERE FUNCTIONAL_LEVEL_ID = 5;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '7' WHERE FUNCTIONAL_LEVEL_ID = 6;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '8' WHERE FUNCTIONAL_LEVEL_ID = 7;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '9' WHERE FUNCTIONAL_LEVEL_ID = 8;

UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '10,11,12,13,14,15,16,17,18,19' WHERE FUNCTIONAL_LEVEL_ID = 10;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '10,11,12,13,14,15,16,17,18,19' WHERE FUNCTIONAL_LEVEL_ID = 11;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '10,11,12,13,14,15,16,17,18,19' WHERE FUNCTIONAL_LEVEL_ID = 12;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '10,11,12,13,14,15,16,17,18,19' WHERE FUNCTIONAL_LEVEL_ID = 13;
UPDATE HRIS_FUNCTIONAL_LEVELS SET DESIGNATION_ID = '10,11,12,13,14,15,16,17,18,19' WHERE FUNCTIONAL_LEVEL_ID = 14;




CREATE TABLE HRIS_REC_ADMIT_SETUP (
    "ADMIT_SETUP_ID" DECIMAL(7) NOT NULL,
    "DECLARATION_TEXT" NVARCHAR(1000),
    "TERMS" NVARCHAR(1400),
    "FILE_NAME" NVARCHAR(200),
    "STATUS" CHAR(1) DEFAULT 'E',
    "CREATED_BY" DECIMAL(4),
    "CREATED_DATE" DATE DEFAULT CURRENT_DATE,
    "MODIFIED_BY" DECIMAL(4),
    "MODIFIED_DT" DATE,
    PRIMARY KEY ("ADMIT_SETUP_ID")
);
