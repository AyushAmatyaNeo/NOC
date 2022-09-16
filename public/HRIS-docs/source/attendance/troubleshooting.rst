.. _attendance troubleshooting:

Troubleshooting Attendance 
****************************
Since attendance is a essential component of daily operations, you can face issues in the attendance from time to time. We have built various tools that can assist the users to troubleshoot such problems easily. 


ZKTeco Attendance Manager
------------------------------

Our compatiable attendance devices come up with a software built separately to track individual attendance data stored on the devices. In case you see some attendance data not appearing in the attendance report, this is the first place you should look at. If the employee has made a thumb/card punch, it should be present in this software. If the data doesnt exist in this software, it will not be sent to the software either. This means the attendance in question was not registered by the device itself.

You can view attendance in this software by obtaining the thumb-id assigned for the employee from their profile, and IP Address of the attendance device.


If you see the attendance data in the ZKT software, but not on HRIS, you should follow other troubleshooting methods described  below.    


Re Attendance
--------------------

The reattendance tool makes it easy to re run the attendance procedure for selected employees. This is useful when you want the system to reload attendance after changing certain user parameters while troubleshooting. 

.. figure:: img/trouble-reattendance.png
   :scale: 50 %

   Reattendance menu. 


.. figure:: img/trouble-reattendance-tool.png
   :scale: 50 %

   Reattendance tool. 

For selecting employees in bulk using a filter, you can click on the |filter|  button.
This allows you to quickly select employees from a company, branch, designation etc. as shown in the following screen.

.. figure:: img/trouble-reattendance-tool-filters.png
   :scale: 50 %

   Using filters to select employees in bulk using the Reattendance tool. 

After selecting the filters, click on search |search| button to list out all employees in the reattendance tool.

.. figure:: img/trouble-reattendance-tool-filters-apply.png
   :scale: 50 %

   All employees matching the filters are now visible on the reattendance tool. 


You'll have to specify the date range, and select employees for whom you want to redo the attendance procedure. Once done so, click on submit and the tool does the attendance procedure.

You can verify the re attendance using the attendance report.


Common Issues
---------------------

Some of the common scenarios where attendance issues are explained below.


Invalid Join Date 
+++++++++++++++++++++

Another common case is when an employee is assigned an invalid joining date e.g empty value or a date in the future. As our system logs attendance for an user only after the joining date, having invalid joining date results in no attendance data.

If you have this problem and changed the joining date, you'll need to run reattendance for the user to obtain past attendance records.


Duplicate / Empty Thumb ID
++++++++++++++++++++++++++++++

Another common case is when an employee is assigned empty/duplicate Thumb ID. Having duplicate thumb id causes conflict with other data, and having no thumb id means the system wouldnt be able to identify who has punched in.

Once fixing this problem, you'll need to run reattendance for the user to view past attendance data.



