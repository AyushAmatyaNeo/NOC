Travel balance and SAP integration
***************************************

Those companies that are using  HRIS with SAP, can have functionality to view ledger impact on  SAP table.All the amount of travel advance and travel expenses requested are stored and calculated appropriately in SAP table.

There are three conditions in which debit and credit are balanced in SAP table.

1.Expense higher than advance requested(Excess condition)

for eg:advance requested:16,000

       expense requested:20,000

.. figure:: img/sap_excess.png
   :scale: 50%

   Excess condition Ledger impact on SAP table.


2.Expense equal to advance requested(Exact condition)

for eg:advance requested:9,000

       expense requested:9,000

.. figure:: img/sap_exact.png
   :scale: 50%

   Exact condition Ledger impact on SAP table.


3.Expense less than advance requested(less condition)

for eg:advance requested:20,000

       expense requested:13,000

.. figure:: img/sap_less.png
   :scale: 50%

   Less condition Ledger impact on SAP table.
