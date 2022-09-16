
Leave Record Management
*************************

Leave Management system would store the leave consumed by employees of the organization with specific details about leave types. It should consider inclusion of at least the following facts:

*	Leave Type
*	Entitlement
*	Consumed Leave  on particular duration (week, month, year)
*	Earned Balance till yet
*	Claimable Balance till yet
*	Cashable Balance until yet


Leave Configuration
+++++++++++++++++++++

Configuration of each leave type should be parameterized with the following considerations: 

* Applicable Leave Types: Any particular leave type might not be applicable to all employees. Hence, its associated applicable leave types should be always definable. 
* Minimum Avail Duration: Half Day/Full Day
* Carry Forward: Defining whether any particular leave type can be carried forward to another fiscal year or not, if yes, is there any limit to the maximum accumulation or not. 
* Holiday/Week off Exclusion: Yes/No


Leave Setup in HRIS
++++++++++++++++++++++++

You can access the leave option under the setup menu.

.. figure:: img/add-leave-menu.png
   :scale: 50 %

   Firstly open leave setup from setup menu in HRIS then click on New button on top-right.



.. figure:: img/add-leave-panel.png
   :scale: 50 %

   Enter data regarding the leave in HRIS using the below parameters and submit.

*	**Leave Ename:** This field Specify the Name of Leave.
*	**Fiscal Year:** The created leave is applicable for which fiscal year is determined by this field.
*	**Default Days:** The default days for created leave are defined by this field. Actually it is defined by the company’s rule.
*	**Max Accumulate Days:** This filed specify the number of maximum accumulated days for the created leave which  should be carry forward or paid.
*	**Paid:** This field specifies whether the created leave is paid leave or not.
*	**Cashable:** This field specifies whether the max accumulated days is cashable or not at the end of fiscal year or not. 
*	**Carry forward:** This field specifies where the max accumulated days are Carried forward or not at the end of Fiscal Year.
*	**Is Substitute Mandatory:** This field determines whether the substitute Co-worker is compulsory required for the created leave or not.
*	**Allow Half day:** This field determines whether half day leave is allowable for the created leave or not.
*	**Allow Grace Leave:**  This field determines whether quarter leave of created leave is allowed or not i.e. one fourth of total working hour is allowed or not. 
*	**Is Monthly:** This field determines whether the leave is applicable for each month or not.
*	**Assign on Employee setup:** This field determines whether the created leave should be automatically assigned on employee creation time or not.
*	**Is Prorata Basis:** This field determines whether the leave is applicable on pro rata basis or not. For e.g. if casual leave is assigned 12 days for a fiscal year & employee gets enrolled in the mid of fiscal year then he/she is applicable to get 6 days or not.
*	**Remarks:** Remarks can be added on this field.



Leave Filters
+++++++++++++++

Filters for assigning leaves is present below the leave creation form. 

.. figure:: img/add-leave-filters.png
   :scale: 50 %

   Assigning leaves using a filter while creation.


The system allows to create and apply leaves to a filtered group of employees while creating it. You can make a leave specific to any combination of the following:

* Company 
* Branch
* Department
* Designation 
* Position
* Service Type
* Service Event Type
* Employee Type 
* Employee 
* Gender 

Manual Leave Assignment
+++++++++++++++++++++++++++

In case when you want to assign leaves manually to users, you can do it from the assign panel.

First you'll need to navigate to assign tab on the sidebar and click Leaves under this menu.

.. figure:: img/assign-leave-menu.png
   :scale: 50 %

   Assigning leaves to employees.


Then you can apply filters to list emplpyees as desired and then select the leave that you want to assign to them from the filter bar.

.. figure:: img/assign-leave.png
   :scale: 50 %

   Assigning leaves to employees while creation.

You can then tick employees that you want to assign the leave or use bulk select all using the select bar on table header. Once done so, you'll need to enter previous leave balance on the left box appearing below the table and number of leaves to be assigned on the right textbox. Then press submit button to assign the leave details to selected employees.