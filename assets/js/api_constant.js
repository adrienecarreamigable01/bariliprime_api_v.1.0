var origin  = window.location.origin;
var protocol = `${window.location.protocol}//`;
var baseApi = `${protocol}bariliprime-api-v1.doitcebu.com`;

// auth api
var loginApi                    = `${baseUrl}auth/authenticate`;
var deauthApi                   = `${baseUrl}auth/logout`;
var getScheduleByDateApi        = `${baseUrl}schedule/get_available_date`;
var getAvailableTimeApi         = `${baseUrl}schedule/get_available_time`;
var addScheduleApi              = `${baseUrl}schedule/add`;
var updateScheduleApi           = `${baseUrl}schedule/update`;
var getSingleScheduleApi        = `${baseUrl}schedule/info`;
var checkifHasScheduleApi       = `${baseUrl}schedule/check_schedule`;
// var checkifHasScheduleApi   = `${baseUrl}schedule/check_schedule`;
var getScheduleAllApi           = `${baseUrl}schedule/getAll`;
var getBorrowerInfoApi          = `${baseUrl}borrower/get_borrower`;
var getBorrowerScheduleApi      = `${baseUrl}borrower/getBorrowerSchedule`;
var checkIfAvailableApi         = `${baseUrl}schedule/checkIfAvailable`;
var getAllScheduleHolidaysApi   = `${baseUrl}schedule/getAllScheduleHolidays`;
var checkScheduleHolidayApi     = `${baseUrl}schedule/checkScheduleHoliday`;
var sendEmailApi                = `${baseUrl}email/send`;
var getPasswordApi              = `${baseUrl}email/getPassword`;

// API

var changePasswordApi          = `${baseApi}/borrower/changePassword`;
var changeUserNameApi          = `${baseApi}/borrower/changeUserName`;
