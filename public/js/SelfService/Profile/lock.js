(function ($, app) {
    'use strict';
    $(document).ready(function () {
        if (document.employeeId == document.selfEmployeeId) {
            app.lockField(true, [
			'employeeCode', 'telephoneNo', 'socialActivity', 'addrTempStreetAddress',
			'genderId', 'extensionNo', 'emailPersonal', 'socialNetwork', 'addrTempZoneId',
			'countryId', 'addrPermWardNo', 'addrPermStreetAddress', 'addrTempWardNo', 'addrTempDistrictId',
			'religionId', 'addrPermVdcMunicipalityId', 'addrPermZoneId', 'addrPermDistrictId',  
			'emailOfficial', 'addrTempVdcMunicipality', 'emergContactName', 'emergContactRelationship', 
			'MobileNo', 'emergContactAddress', 'emergContactNo', 'addrTempHouseNo', 
			'bloodGroupId',
            'addrPermHouseNo',
			'birthdate', 'firstName', 'middleName', 'lastName', 'nameNepali', 'nepaliBirthDate', 'companyId', 'idCardNo', 'idThumbId', 'idLbrf'
                        , 'tab1'
                        , 'tab2'
//                        , 'tab3'
                        , 'tab4'
                        , 'tab5'
                        , 'tab7'
                       // , 'tab8'
                        , 'tab9'
                        // , 'tab7'
                        // , 'tab8'
                        // , 'tab9'
                        , 'tab10'
                    ]);
        }
    });
})(window.jQuery, window.app);


//  below this is only for GWS
//
// (function ($, app) {
//     'use strict';
//     $(document).ready(function () {
//         if (document.employeeId == document.selfEmployeeId) {
//             app.lockField(true, [
//                 'employeeCode'
// //                        ,'telephoneNo', 'socialActivity', 'addrTempStreetAddress',
// //                      'genderId', 'extensionNo', 'emailPersonal', 'socialNetwork', 'addrTempZoneId',
// //                      'countryId', 'addrPermWardNo', 'addrPermStreetAddress', 'addrTempWardNo', 'addrTempDistrictId',
// //                      'religionId', 'addrPermVdcMunicipalityId', 'addrPermZoneId', 'addrPermDistrictId',
// //                      'emailOfficial', 'addrTempVdcMunicipality', 'emergContactName', 'emergContactRelationship',
// //                      'MobileNo', 'emergContactAddress', 'emergContactNo', 'addrTempHouseNo',
// //                      'bloodGroupId',
// //            'addrPermHouseNo',
// //                      'birthdate', 'firstName', 'middleName', 'lastName', 'nameNepali', 'nepaliBirthDate'
//                 , 'companyId', 'birthdate'
//                 , 'idCardNo', 'idThumbId', 'idLbrf' , 'maritualStatus', 'genderId' , 'idAccountId', 'idCitizenshipNo'
//                 , 'idCitizenshipIssueDate', 'idRetirementNo' , 'idPanNo', 'idBarCode', 'idProvidentFundNo'
// //                        , 'tab1'
// //                        , 'tab2'
// //                , 'tab3'
//                 , 'tab4'
// //                        , 'tab5'
// //                        , 'tab6'
// //                , 'tab7'
// //                , 'tab8'
// //                        , 'tab9'
//                 , 'tab10'
//             ]);
//         }
//     });
// })(window.jQuery, window.app);

