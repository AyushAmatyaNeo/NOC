<?php

namespace Notification\Model;

class LoanRequestNotificationModel extends NotificationModel {

    public $loanId;
    public $loanCode;
    public $loanName;
    public $requestedAmount;
    public $loanDate;
    public $reason;
    public $approvedAmount;
    public $deductOnSalary;
    public $status;

}
