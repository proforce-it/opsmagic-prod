<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// clear route cache
Route::get('/clear-route-cache', function () {
    Artisan::call('route:cache');
    return 'Routes cache has clear successfully !';
});

//clear config cache
Route::get('/clear-config-cache', function () {
    Artisan::call('config:cache');
    return 'Config cache has clear successfully !';
});

// clear application cache
Route::get('/clear-app-cache', function () {
    Artisan::call('cache:clear');
    return 'Application cache has clear successfully!';
});

// clear view cache
Route::get('/clear-view-cache', function () {
    Artisan::call('view:clear');
    return 'View cache has clear successfully!';
});

Route::get('/queue-work', function () {
    Artisan::call('queue:work');
    return 'Queue successfully work!';
});

Route::get('/create-payroll-week-date-entries', [App\Http\Controllers\Dashboards\CreatePayrollWeekDateEntries::class, 'index']);

// UPDATE COST CENTRES NAME TO ID IN DATABASE
Route::get('/update-nationality-id-in-worker-table', [App\Http\Controllers\HomeController::class, 'updateNationalityIdInWorkerTable']);

Route::get('/', function () { return view('theme.auth.partials.login'); });
//Route::get('login', function () { return view('theme.auth.partials.login'); });

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('custom.forgot-password.form');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('custom.forgot-password.email');
Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('custom.password.reset');
Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('custom.password.update');
Route::get('/user-password', [App\Http\Controllers\User\UserController::class, 'userPassword']);
Route::post('/update-user-password', [App\Http\Controllers\User\UserController::class, 'updateUserPassword']);

Route::get('/confirm-worker-email/{id}', [App\Http\Controllers\Workers\WorkerController::class, 'confirmWorkerEmail']);
Route::get('/confirm-client-job-worker/{id}/{confirm_by}/{status}', [App\Http\Controllers\Clients\ClientController::class, 'confirmClientJobWorker']);
Route::get('/confirm-job-shift-worker/{id}/{status}', [App\Http\Controllers\Job\JobController::class, 'confirmJobShiftWorker']);

//Worker
Route::get('reset-worker-password', [App\Http\Controllers\Workers\WorkerController::class, 'resetWorkerPassword']);
Route::post('reset-worker-password-action', [App\Http\Controllers\Workers\WorkerController::class, 'resetWorkerPasswordAction']);

Route::middleware('auth')->group(function(){
    Route::get('/dashboard', [App\Http\Controllers\Dashboards\DashboardController::class, 'newIndex'])->name('dashboard_home');
    Route::post('/get-shift-with-space-in-next-7-days', [App\Http\Controllers\Dashboards\DashboardController::class, 'getShiftWithSpaceInNextSevenDays']);
    Route::post('/get-dashboard-data', [App\Http\Controllers\Dashboards\DashboardController::class, 'getDashboardData']);
    Route::post('/get-state-city-option', [App\Http\Controllers\Location\CountryStateCityController::class, 'getStateCityOption']);

    Route::post('/get-new-dashboard-data', [App\Http\Controllers\Dashboards\DashboardController::class, 'getNewDashboardData']);
    Route::post('/quick-dashboard-search-worker-client-job', [App\Http\Controllers\Dashboards\DashboardController::class, 'quickDashboardSearchWorkerClientJob']);
    Route::post('/get-booking-invitation-to-chase', [App\Http\Controllers\Dashboards\DashboardController::class, 'getBookingInvitationToChase']);
    Route::post('/get-total-bookings', [App\Http\Controllers\Dashboards\DashboardController::class, 'getTotalBookings']);
    Route::post('/get-total-job-shift', [App\Http\Controllers\Dashboards\DashboardController::class, 'getTotalJobShift']);
    Route::post('/get-total-job', [App\Http\Controllers\Dashboards\DashboardController::class, 'getTotalJob']);
    Route::post('/get-total-site', [App\Http\Controllers\Dashboards\DashboardController::class, 'getTotalSite']);
    Route::post('/get-total-client', [App\Http\Controllers\Dashboards\DashboardController::class, 'getTotalClient']);
    Route::post('/booking-invitation-action', [App\Http\Controllers\Job\JobController::class, 'bookingInvitationConfirm']);


    /*--- Worker management ---*/
    Route::get('/worker-management', [App\Http\Controllers\Workers\WorkerController::class, 'workerManagement'])->name('dashboard_worker-management');
    Route::post('/list-of-workers', [App\Http\Controllers\Workers\WorkerController::class, 'listOfWorkers']);
    Route::get('/view-worker-details/{id}', [App\Http\Controllers\Workers\WorkerController::class, 'viewWorker'])->name('worker-management_worker-details');
    Route::get('/create-worker', [App\Http\Controllers\Workers\WorkerController::class, 'createWorker'])->name('worker-management_add-worker');
    Route::post('/check-worker-validation/{section}', [App\Http\Controllers\Workers\WorkerController::class, 'checkWorkerValidation']);
    Route::post('/create-worker-action', [App\Http\Controllers\Workers\WorkerController::class, 'createWorkerAction']);
    //Route::post('/get-document-id-details', [App\Http\Controllers\Workers\WorkerController::class, 'getDocumentIdDetails']);
    //Route::post('/update-document-id-details',[\App\Http\Controllers\Workers\WorkerController::class,'updateDocumentIdDetails']);

    /*Route::post('/add-new-section-for-work-experience', [App\Http\Controllers\Workers\WorkerController::class, 'addNewSectionForWorkExperience']);
    Route::post('/add-new-section-for-interview-record', [App\Http\Controllers\Workers\WorkerController::class, 'addNewSectionForInterviewRecord']);
    Route::post('/add-new-section-for-worker-note', [App\Http\Controllers\Workers\WorkerController::class, 'addNewSectionForWorkerNote']);
    Route::post('/add-new-section-for-document', [App\Http\Controllers\Workers\WorkerController::class, 'addNewSectionForDocument']);
    Route::post('/add-new-section-for-right-to-work', [App\Http\Controllers\Workers\WorkerController::class, 'addNewSectionForRightToWork']);*/

    Route::get('/delete-worker-action/{id}', [App\Http\Controllers\Workers\WorkerController::class, 'deleteWorkerAction']);
    Route::post('/update-worker-status', [App\Http\Controllers\Workers\WorkerController::class, 'updateWorkerStatus']);
    Route::get('/delete-rtw-action/{id}', [App\Http\Controllers\Workers\WorkerController::class, 'deleteRtwAction']);
    Route::post('/insert-rights-to-work', [App\Http\Controllers\Workers\WorkerController::class, 'insertRightsToWork']);
    Route::post('/list-of-absence', [App\Http\Controllers\Workers\WorkerController::class, 'listOfAbsence']);
    Route::post('/create-absence', [App\Http\Controllers\Workers\WorkerController::class, 'createAbsence']);
    Route::get('/delete-absence-action/{id}', [App\Http\Controllers\Workers\WorkerController::class, 'deleteAbsenceAction']);
    Route::get('/delete-document-action/{id}', [App\Http\Controllers\Workers\WorkerController::class, 'deleteDocumentAction']);
    Route::post('/get-worker-assigned-jobs', [App\Http\Controllers\Workers\WorkerController::class, 'getWorkerAssignedJobs']);
    Route::post('/get-worker-shifts-booked', [App\Http\Controllers\Workers\WorkerController::class, 'getWorkerShiftsBooked']);
    Route::post('/get-next-14-day-shift', [App\Http\Controllers\Workers\WorkerController::class, 'getNext14DayShift']);
    Route::post('/get-worker-shifts-worked', [App\Http\Controllers\Workers\WorkerController::class, 'getWorkerShiftsWorked']);
    Route::post('/worker-status-bulk-action', [App\Http\Controllers\Workers\WorkerController::class, 'workerStatusBulkAction']);
    Route::post('/upload-worker-profile-pic', [App\Http\Controllers\Workers\WorkerController::class, 'uploadWorkerProfilePic']);
    Route::get('/send-mail-for-worker-email-conformation/{id}', [App\Http\Controllers\Workers\WorkerController::class, 'sendMailForWorkerEmailConformation']);
    Route::post('/update-worker-document-scan',[App\Http\Controllers\Workers\WorkerController::class,'updateRtwDocumentScan']);

    Route::post('/update-worker-basic-details', [App\Http\Controllers\Workers\WorkerController::class, 'updateWorkerBasicDetails']);
    Route::post('/update-worker-uk-address', [App\Http\Controllers\Workers\WorkerController::class, 'updateWorkerUkAddress']);
    Route::post('/update-worker-addresses', [App\Http\Controllers\Workers\WorkerController::class, 'updateWorkerAddresses']);
    Route::post('/update-worker-other-details', [App\Http\Controllers\Workers\WorkerController::class, 'updateWorkerOtherDetails']);
    Route::post('/update-worker-bank-details', [App\Http\Controllers\Workers\WorkerController::class, 'updateWorkerBankDetails']);
    //Route::post('/update-worker-skill-details', [App\Http\Controllers\Workers\WorkerController::class, 'updateWorkerSkillDetails']);
    Route::post('/update-worker-document-details', [App\Http\Controllers\Workers\WorkerController::class, 'updateWorkerDocumentDetails']);
    Route::post('/update-worker-incomplete-document-details',[App\Http\Controllers\Workers\WorkerController::class,'updateWorkerIncompleteDocumentDetails']);
    Route::post('/update-worker-other-document-details', [App\Http\Controllers\Workers\WorkerController::class, 'updateWorkerOtherDocumentDetails']);
    Route::post('/update-worker-note', [App\Http\Controllers\Workers\WorkerController::class, 'updateWorkerNote']);
    Route::post('/list-of-activity-logs', [App\Http\Controllers\Activity\ActivityLogController::class, 'listOfActivityLogs']);
    Route::post('/update-leaving-status',[App\Http\Controllers\Workers\WorkerController::class,'update_leaving_status']);
    Route::post('/update-bulk-leaving-status',[App\Http\Controllers\Workers\WorkerController::class,'update_bulk_leaving_status']);

    /*--- Clients management*/
    Route::get('/client-management', [App\Http\Controllers\Clients\ClientController::class, 'clientManagement'])->name('dashboard_client-management');
    Route::post('/list-of-client', [App\Http\Controllers\Clients\ClientController::class, 'listOfClient']);
    Route::get('/create-client', [App\Http\Controllers\Clients\ClientController::class, 'createClient'])->name('client-management_create-client');
    Route::post('/check-client-validation/{section}', [App\Http\Controllers\Clients\ClientController::class, 'checkClientValidation']);
    Route::post('/store-client', [App\Http\Controllers\Clients\ClientController::class, 'storeClient']);
    Route::get('/view-client-details/{id}', [App\Http\Controllers\Clients\ClientController::class, 'viewClient'])->name('client-management_client-details');
    Route::post('/get-client-dashboard-data', [App\Http\Controllers\Clients\ClientController::class, 'getDashboardData']);
    Route::get('/delete-client-action/{id}', [App\Http\Controllers\Clients\ClientController::class, 'deleteClientAction']);
    Route::post('/upload-client-logo-pic', [App\Http\Controllers\Clients\ClientController::class, 'uploadClientLogoPic']);
    Route::post('/update-client-status', [App\Http\Controllers\Clients\ClientController::class, 'updateClientStatus']);
    //Route::post('/add-new-section-for-location', [App\Http\Controllers\Clients\ClientController::class, 'addNewSectionForLocation']);

    Route::post('/update-client-basic-details', [App\Http\Controllers\Clients\ClientController::class, 'updateClientBasicDetails']);

    Route::post('/get-client-site', [App\Http\Controllers\Clients\ClientController::class, 'getClientSite']);
    Route::get('/view-site/{id}', [App\Http\Controllers\Clients\ClientController::class, 'siteDetails'])->name('client-management_site-details');
    Route::post('/store-client-site-details', [App\Http\Controllers\Clients\ClientController::class, 'storeClientSiteDetails']);
    Route::get('/delete-client-site-action/{id}', [App\Http\Controllers\Clients\ClientController::class, 'deleteClientSiteAction']);
    Route::post('/store-site-direction-details', [App\Http\Controllers\Clients\ClientController::class, 'storeSiteDirectionDetails']);

    Route::post('/get-client-contact', [App\Http\Controllers\Clients\ClientController::class, 'getClientContact']);
    Route::post('/get-site-for-contact', [App\Http\Controllers\Clients\ClientController::class, 'getSiteForContact']);
    Route::post('/store-client-contact-details', [App\Http\Controllers\Clients\ClientController::class, 'storeClientContactDetails']);
    Route::get('/view-client-contact/{id}', [App\Http\Controllers\Clients\ClientController::class, 'viewClientContact'])->name('client-management_client-details_contact');
    Route::get('/delete-client-contact-action/{id}', [App\Http\Controllers\Clients\ClientController::class, 'deleteClientContactAction']);

    Route::post('/get-client-jobs', [App\Http\Controllers\Clients\ClientController::class, 'getClientJobs']);
    Route::post('/store-client-job-details', [App\Http\Controllers\Clients\ClientController::class, 'storeClientJobDetails']);
    Route::get('/archive-client-job-action/{id}/{status}', [App\Http\Controllers\Clients\ClientController::class, 'ArchiveClientJobAction']);
    Route::get('/view-client-job/{id}', [App\Http\Controllers\Clients\ClientController::class, 'viewClientJob'])->name('client-management_view-job');
    Route::post('/update-client-job-basic-details', [App\Http\Controllers\Clients\ClientController::class, 'updateClientJobBasicDetails']);
    //Route::post('/update-client-job-pay-rate-details', [App\Http\Controllers\Clients\ClientController::class, 'updateClientJobPayRateDetails']);
    Route::post('/search-client-job-worker', [App\Http\Controllers\Clients\ClientController::class, 'searchClientJobWorker']);
    Route::post('/store-client-job-worker', [App\Http\Controllers\Clients\ClientController::class, 'storeClientJobWorker']);
    Route::post('/store-client-job-worker-multiple', [App\Http\Controllers\Clients\ClientController::class, 'storeClientJobWorkerMultiple']);
    Route::post('/get-client-job-worker', [App\Http\Controllers\Clients\ClientController::class, 'getClientJobWorker']);
    Route::post('/get-client-job-worker-transport', [App\Http\Controllers\Clients\ClientController::class, 'getClientJobWorkerTransport']);
    Route::post('/update-worker-transport-details', [App\Http\Controllers\Clients\ClientController::class, 'updateWorkerTransportDetails']);
    Route::get('/archive-client-job-worker/{id}', [App\Http\Controllers\Clients\ClientController::class, 'archiveClientJobWorker']);
    Route::get('/un-archive-client-job-worker/{id}', [App\Http\Controllers\Clients\ClientController::class, 'unArchiveClientJobWorker']);
    Route::get('/confirm-client-job-worker-admin/{id}/{confirm_by}/{status}', [App\Http\Controllers\Clients\ClientController::class, 'confirmClientJobWorkerAdmin']);
    Route::get('/worker-availability/{id}', [App\Http\Controllers\Clients\ClientController::class, 'workerAvailability'])->name('client-management_view-worker-availability');
    Route::post('/get-job-worker-availability', [App\Http\Controllers\Clients\ClientController::class, 'getJobWorkerAvailability']);
    Route::post('/action-on-worker-availability', [App\Http\Controllers\Clients\ClientController::class, 'actionOnWorkerAvailability']);
    Route::post('/get-client-job-worker-future-confirm-and-invitation-shift', [App\Http\Controllers\Clients\ClientController::class, 'getClientJobWorkerFutureConfirmAndInvitationShift']);

    Route::post('/update-client-document-details', [App\Http\Controllers\Clients\ClientController::class, 'updateClientDocumentDetails']);
    Route::post('/update-client-other-document-details', [App\Http\Controllers\Clients\ClientController::class, 'updateClientOtherDocumentDetails']);
    Route::get('/delete-client-document-action/{id}', [App\Http\Controllers\Clients\ClientController::class, 'deleteClientDocumentAction']);

    Route::post('/store-client-note-details', [App\Http\Controllers\Clients\ClientController::class, 'storeClientNoteDetails']);
    Route::post('/get-client-notes', [App\Http\Controllers\Clients\ClientController::class, 'getClientNote']);

    Route::post('/get-booking-data',[App\Http\Controllers\Clients\ClientController::class,'getClientBooking'])->name('client.bookings');
    Route::post('/get-timesheet-data',[App\Http\Controllers\Clients\ClientController::class,'getClientTimesheet'])->name('client.timesheet');
    Route::get('/assignment-management', [App\Http\Controllers\Job\JobController::class, 'index'])->name('dashboard_booking-calendar');
    Route::post('/get-site-using-client', [App\Http\Controllers\Job\JobController::class, 'getSiteUsingClient']);
    Route::post('/get-job-using-site', [App\Http\Controllers\Job\JobController::class, 'getJobUsingSite']);
    Route::post('/create-job-shift', [App\Http\Controllers\Job\JobController::class, 'createJobShift']);
    Route::post('/get-job-shift-data', [App\Http\Controllers\Job\JobController::class, 'getJobShiftData']);
    Route::get('/view-job-shift/{shift_id}', [App\Http\Controllers\Job\JobController::class, 'viewJobShift'])->name('assignment-management_view-bookings');
    Route::post('/update-job-shift-basic-details', [App\Http\Controllers\Job\JobController::class, 'updateJobShiftBasicDetails']);
    Route::post('/add-worker-to-job-shift', [App\Http\Controllers\Job\JobController::class, 'addWorkerToJobShift']);
    Route::post('/selected-worker-action-to-job-shift', [App\Http\Controllers\Job\JobController::class, 'selectedWorkerActionToJobShift']);
    Route::post('/manage-slot-action', [App\Http\Controllers\Job\JobController::class, 'manageSlotAction']);
    Route::post('/delete-shift-action', [App\Http\Controllers\Job\JobController::class, 'deleteShiftAction']);
    Route::get('/export-booking-calendar-sheet-confirm-worker/{job_shift_id}', [App\Http\Controllers\Job\JobController::class, 'exportBookingCalendarSheetConfirmWorker']);
    Route::post('/bulk-export-booking-calendar-sheet-confirm-worker', [App\Http\Controllers\Job\JobController::class, 'bulkExportBookingCalendarSheetConfirmWorker']);
    Route::post('/restore-declined-cancelled-worker', [App\Http\Controllers\Job\JobController::class, 'restoreDeclinedCancelledWorker']);
    Route::post('/linked-to-client-worker-add-into-job', [App\Http\Controllers\Job\JobController::class, 'linkedToClientWorkerAddIntoJob']);
    Route::post('/copy-job-shift', [App\Http\Controllers\Job\JobController::class, 'copyJobShift']);
    Route::post('/copy-job-shift-in-worker-availability', [App\Http\Controllers\Job\JobController::class, 'copyJobShiftInWorkerAvailability']);


    Route::get('/job-management', [App\Http\Controllers\Job\JobController::class, 'jobManagement'])->name('dashboard_job-management');

    Route::get('/job-shift-uploader', [App\Http\Controllers\Job\JobShiftUploadController::class, 'index'])->name('dashboard_bookings-uploader');
    Route::post('/upload-job-shift', [App\Http\Controllers\Job\JobShiftUploadController::class, 'shiftUploader']);

    Route::get('/worker-search', [App\Http\Controllers\Workers\WorkerSearchController::class, 'index'])->name('dashboard_worker-search');
    Route::post('/worker-search-action', [App\Http\Controllers\Workers\WorkerSearchController::class, 'workerSearchAction']);
    Route::post('/store-worker-search-request', [App\Http\Controllers\Workers\WorkerSearchController::class, 'storeWorkerSearchRequest']);
    Route::get('/delete-worker-search-request-data/{id}', [App\Http\Controllers\Workers\WorkerSearchController::class, 'deleteWorkerSearchRequestData']);

    Route::get('/timesheet-uploader', [App\Http\Controllers\Timesheet\TimeSheetUploaderController::class, 'index'])->name('dashboard_timesheet-uploader');
    Route::post('/upload-timesheet', [App\Http\Controllers\Timesheet\TimeSheetUploaderController::class, 'timesheetUploader']);
    Route::get('/timesheet-editor', [App\Http\Controllers\Timesheet\TimeSheetUploaderController::class, 'timesheetEditor'])->name('dashboard_timesheet-editor');
    Route::post('/get-timesheet-editor-shift-line-item-data', [App\Http\Controllers\Timesheet\TimeSheetUploaderController::class, 'getTimesheetEditorShiftLineItemData']);
    Route::post('/get-timesheet-editor-total-hour-per-worker-data', [App\Http\Controllers\Timesheet\TimeSheetUploaderController::class, 'getTimesheetEditorTotalHourPerWorkerData']);
    Route::post('/edit-timesheet-action', [App\Http\Controllers\Timesheet\TimeSheetUploaderController::class, 'editTimesheetAction']);
    Route::get('/delete-timesheet-entry/{id}', [App\Http\Controllers\Timesheet\TimeSheetUploaderController::class, 'deleteTimesheetEntry']);
    Route::get('/export-timesheet-entry', [App\Http\Controllers\Timesheet\TimeSheetUploaderController::class, 'exportTimesheetEntry']);
    Route::post('/get-client-job-using-worker', [App\Http\Controllers\Timesheet\TimeSheetUploaderController::class, 'getClientJobUsingWorker']);
    Route::post('/single-timesheet-entry-create-action', [App\Http\Controllers\Timesheet\TimeSheetUploaderController::class, 'singleTimesheetEntryCreateAction']);

    Route::get('/user-management', [App\Http\Controllers\User\UserController::class, 'index'])->name('dashboard_user-management');
    Route::post('/get-user', [App\Http\Controllers\User\UserController::class, 'getUser']);
    Route::get('/get-single-user/{id}', [App\Http\Controllers\User\UserController::class, 'getSingleUser']);
    Route::post('/store-user-action', [App\Http\Controllers\User\UserController::class, 'storeUserAction']);
    Route::post('/edit-user-action', [App\Http\Controllers\User\UserController::class, 'editUserAction']);
    Route::post('/update-user-status', [App\Http\Controllers\User\UserController::class, 'updateUserStatus']);

    Route::get('/bonus-uploader', [App\Http\Controllers\Bonus\BonusUploaderController::class, 'index'])->name('dashboard_bonus-uploader');
    Route::post('/upload-bonus', [App\Http\Controllers\Bonus\BonusUploaderController::class, 'bonusUploader']);
    Route::get('/bonus-editor', [App\Http\Controllers\Bonus\BonusUploaderController::class, 'bonusEditor'])->name('dashboard_bonus-editor');
    Route::post('/get-bonus-editor-line-item-data', [App\Http\Controllers\Bonus\BonusUploaderController::class, 'getBonusEditorLineItemData']);
    Route::post('/get-bonus-editor-worker-summary-data', [App\Http\Controllers\Bonus\BonusUploaderController::class, 'getBonusEditorWorkerSummaryData']);
    Route::post('/edit-bonus-action', [App\Http\Controllers\Bonus\BonusUploaderController::class, 'editBonusAction']);
    Route::get('/delete-bonus-entry/{id}', [App\Http\Controllers\Bonus\BonusUploaderController::class, 'deleteBonusEntry']);
    Route::get('/export-bonus-entry', [App\Http\Controllers\Bonus\BonusUploaderController::class, 'exportBonusEntry']);
    Route::post('/single-bonus-entry-create-action', [App\Http\Controllers\Bonus\BonusUploaderController::class, 'singleBonusEntryCreateAction']);

    Route::post('/create-payroll-action', [App\Http\Controllers\Payroll\PayrollController::class, 'createPayrollAction']);
    Route::get('/view-payroll-report', [App\Http\Controllers\Payroll\PayrollController::class, 'viewPayrollReport'])->name('dashboard_view-payroll-report');
    Route::post('/get-payroll-data', [App\Http\Controllers\Payroll\PayrollController::class, 'getPayrollData']);
    Route::get('/export-payroll-item', [App\Http\Controllers\Payroll\PayrollController::class, 'exportPayrollItem']);
    Route::post('/delete-payroll-report', [App\Http\Controllers\Payroll\PayrollController::class, 'deleteReport']);

    Route::get('/timesheet-and-bonus-editor', [App\Http\Controllers\TimesheetAndBonus\TimesheetAndBonusController::class, 'index'])->name('dashboard_timesheet-and-bonus-editor');
    Route::post('/delete-ignored-entry', [App\Http\Controllers\TimesheetAndBonus\TimesheetAndBonusController::class, 'deleteIgnoredEntry']);

    Route::get('/financial-report', [App\Http\Controllers\Reports\FinancialReportController::class, 'financialReport'])->name('dashboard_financial-report');
    Route::post('/get-financial-site-summary-report', [App\Http\Controllers\Reports\FinancialReportController::class, 'getFinancialSiteSummaryReport']);
    Route::post('/get-financial-job-summary-report', [App\Http\Controllers\Reports\FinancialReportController::class, 'getFinancialJobSummaryReport']);
    Route::post('/get-financial-worker-summary-report', [App\Http\Controllers\Reports\FinancialReportController::class, 'getFinancialWorkerSummaryReport']);
    Route::post('/get-financial-payroll-summary-report', [App\Http\Controllers\Reports\FinancialReportController::class, 'getFinancialPayrollSummaryReport']);

    Route::get('/my-profile',[App\Http\Controllers\Dashboards\ProfileController::class,'index'])->name('dashboard_my-profile');
    Route::post('/update-dashboard-tab',[App\Http\Controllers\Dashboards\ProfileController::class,'updateDashboardTab']);
    Route::post('/upload-user-profile-pic', [App\Http\Controllers\Dashboards\ProfileController::class, 'uploadUserProfilePic']);
    Route::get('/update-password-tab',[App\Http\Controllers\Dashboards\ProfileController::class,'updatePasswordTab']);
    Route::post('/update-profile-password',[App\Http\Controllers\Dashboards\ProfileController::class,'updateUserPassword']);

    Route::post('/create-flat-pay-rate-details',[App\Http\Controllers\Clients\PayRateController::class,'createFlatPayRateDetails']);
    Route::post('/edit-flat-pay-rate-details',[App\Http\Controllers\Clients\PayRateController::class,'editFlatPayRateDetails']);
    Route::get('/delete-flat-pay-rate-action/{id}',[App\Http\Controllers\Clients\PayRateController::class,'deleteFlatPayRateAction']);

    Route::post('/create-pay-rate-map-details',[App\Http\Controllers\Clients\PayRateController::class,'createPayRateMapDetails']);
    Route::get('/pay-rate-map-step-2/{id}',[App\Http\Controllers\Clients\PayRateController::class,'payRateMapStepTwo'])->name('client-management_pay-rate-step-2');
    Route::post('/store-prm-calendar-event',[App\Http\Controllers\Clients\PayRateController::class,'storePrmCalendarEvent']);
    Route::get('/prm-read-only-pay-map/{id}',[App\Http\Controllers\Clients\PayRateController::class,'payRateReadOnlyMap'])->name('client-management_pay-rate-map-read-only');

    Route::post('/edit-default-pay-rate-map-action',[App\Http\Controllers\Clients\PayRateController::class,'editDefaultPayRateMapAction']);
    Route::post('/add-extra-pay-rate-map-action',[App\Http\Controllers\Clients\PayRateController::class,'addExtraPayRateMapAction']);
    Route::post('/get-extra-pay-rate-map-details',[App\Http\Controllers\Clients\PayRateController::class,'getExtraPayRateMapDetails']);
    Route::post('/edit-extra-pay-rate-map-action',[App\Http\Controllers\Clients\PayRateController::class,'editExtraPayRateMapAction']);
    Route::post('/delete-extra-pay-rate-map-action',[App\Http\Controllers\Clients\PayRateController::class,'deleteExtraPayRateMapAction']);

    Route::post('/create-temporary-upcoming-prm-entry',[App\Http\Controllers\Clients\PayRateController::class,'createTemporaryUpcomingPrmEntry']);
    Route::get('/create-upcoming-pay-rate-map/{id}',[App\Http\Controllers\Clients\PayRateController::class,'createUpcomingPayRateMap'])->name('client-management_upcoming-pay-rate-map');
    Route::post('/store-upcoming-prm-calendar-event',[App\Http\Controllers\Clients\PayRateController::class,'storeUpcomingPrmCalendarEvent']);
    Route::get('/delete-upcoming-pay-rate-map-action/{id}',[App\Http\Controllers\Clients\PayRateController::class,'deleteUpcomingPayRateMapAction']);
    Route::post('/update-primary-contact',[App\Http\Controllers\Clients\ClientController::class,'updateClientPrimaryContact']);
    Route::post('/update-client-pay-details', [App\Http\Controllers\Clients\ClientController::class, 'updateClientPayDetails']);

    Route::get('/shift-overview', [App\Http\Controllers\Job\ShiftOverviewController::class, 'index'])->name('dashboard_shift-overview');
    Route::post('/change-week', [App\Http\Controllers\Job\ShiftOverviewController::class, 'changeWeek']);
    Route::post('/get-job-shift-overview', [App\Http\Controllers\Job\ShiftOverviewController::class, 'getJobShiftOverview']);

    Route::get('/booking-overview', [App\Http\Controllers\Job\BookingOverviewController::class, 'index'])->name('dashboard_booking-overview');
    Route::post('/change-week-booking-overview', [App\Http\Controllers\Job\BookingOverviewController::class, 'changeWeekBookingOverview']);
    Route::post('/get-booking-overview', [App\Http\Controllers\Job\BookingOverviewController::class, 'getBookingOverview']);

    Route::get('/booking-overview-by-client/{id}', [App\Http\Controllers\Job\BookingOverviewController::class, 'bookingOverviewByClient'])->name('dashboard_booking-overview-by-client');
    Route::post('/get-booking-overview-by-client', [App\Http\Controllers\Job\BookingOverviewController::class, 'getBookingOverviewByClient']);

    Route::get('/accommodation-list', [App\Http\Controllers\Accommodation\AccommodationController::class, 'index'])->name('dashboard_accommodation-list');
    Route::post('/get-accommodation', [App\Http\Controllers\Accommodation\AccommodationController::class, 'getAccommodation']);
    Route::get('/create-accommodation', [App\Http\Controllers\Accommodation\AccommodationController::class, 'createAccommodation'])->name('accommodation-list_add-accommodation');
    Route::post('/store-accommodation', [App\Http\Controllers\Accommodation\AccommodationController::class, 'storeAccommodation']);
    Route::get('/view-accommodation/{id}', [App\Http\Controllers\Accommodation\AccommodationController::class, 'viewAccommodation'])->name('accommodation-list_accommodation-details');
    Route::post('/archive-accommodation', [App\Http\Controllers\Accommodation\AccommodationController::class, 'archiveAccommodation']);

    Route::get('/pick-up-point-list', [App\Http\Controllers\PickupPoint\PickupPointController::class, 'index'])->name('dashboard_pick-up-point-list');
    Route::post('/get-pick-up-point', [App\Http\Controllers\PickupPoint\PickupPointController::class, 'getPickUpPoint']);
    Route::get('/create-pick-up-point', [App\Http\Controllers\PickupPoint\PickupPointController::class, 'createPickUpPoint'])->name('pick-up-point-list_add-pick-up-point');
    Route::post('/store-pick-up-point', [App\Http\Controllers\PickupPoint\PickupPointController::class, 'storePickUpPoint']);
    Route::get('/view-pick-up-point/{id}', [App\Http\Controllers\PickupPoint\PickupPointController::class, 'viewPickUpPoint'])->name('pick-up-point-list_pick-up-point-details');
    Route::post('/archive-pick-up-point', [App\Http\Controllers\PickupPoint\PickupPointController::class, 'archivePickUpPoint']);

    Route::get('/worker-uploader', [App\Http\Controllers\Workers\WorkerUploaderController::class, 'index'])->name('dashboard_worker-uploader');
    Route::post('/worker-upload-action', [App\Http\Controllers\Workers\WorkerUploaderController::class, 'workerUploadAction']);

    Route::get('/view-draft-timesheet-entries/{job_shift_id}', [App\Http\Controllers\Timesheet\DraftTimesheetController::class, 'index'])->name('assignment-management_view-draft-timesheet');
    Route::post('/get-draft-timesheet-entries', [App\Http\Controllers\Timesheet\DraftTimesheetController::class, 'getDraftTimesheetEntries']);
    Route::post('/create-draft-timesheet-entries', [App\Http\Controllers\Timesheet\DraftTimesheetController::class, 'createDraftTimesheetEntries']);
    Route::post('/add-draft-timesheet-action', [App\Http\Controllers\Timesheet\DraftTimesheetController::class, 'addDraftTimesheetAction']);
    Route::post('/delete-draft-timesheet-entry/{timesheet_id}', [App\Http\Controllers\Timesheet\DraftTimesheetController::class, 'deleteDraftTimesheetEntry']);
    Route::post('/edit-draft-timesheet-action', [App\Http\Controllers\Timesheet\DraftTimesheetController::class, 'editDraftTimesheetAction']);
    Route::post('/check-timesheet-entries-validation', [App\Http\Controllers\Timesheet\DraftTimesheetController::class, 'checkTimesheetEntriesValidation']);
    Route::post('/create-timesheet-using-draft-timesheet-entries', [App\Http\Controllers\Timesheet\DraftTimesheetController::class, 'createTimesheetUsingDraftTimesheetEntries']);

    Route::post('/get-job-line', [App\Http\Controllers\Job\JobLineController::class, 'index']);
    Route::post('/store-job-line', [App\Http\Controllers\Job\JobLineController::class, 'storeJobLine']);
    Route::post('/action-job-line', [App\Http\Controllers\Job\JobLineController::class, 'actionJobLine']);

    Route::get('/cost-centres-management', [App\Http\Controllers\Group\CostCentreController::class, 'index'])->name('dashboard_cost-centres-management');
    Route::post('/get-cost-centre', [App\Http\Controllers\Group\CostCentreController::class, 'getCostCentre']);
    Route::post('/store-cost-centre-action', [App\Http\Controllers\Group\CostCentreController::class, 'storeCostCentreAction']);
    Route::get('/get-single-cost-centre/{id}', [App\Http\Controllers\Group\CostCentreController::class, 'getSingleCostCentre']);
    Route::post('/edit-cost-centre-action', [App\Http\Controllers\Group\CostCentreController::class, 'editCostCentreAction']);
    Route::post('/archived-un-archived-cost-centre-action', [App\Http\Controllers\Group\CostCentreController::class, 'deleteCostCentreAction']);

    Route::get('/teams-management', [App\Http\Controllers\Group\TeamsController::class, 'index'])->name('dashboard_teams-management');
    Route::post('/get-teams', [App\Http\Controllers\Group\TeamsController::class, 'getTeams']);
    Route::post('/store-teams-action', [App\Http\Controllers\Group\TeamsController::class, 'storeTeamsAction']);
    Route::get('/get-single-teams/{id}', [App\Http\Controllers\Group\TeamsController::class, 'getSingleTeams']);
    Route::post('/edit-team-action', [App\Http\Controllers\Group\TeamsController::class, 'editTeamAction']);

    Route::get('/groups', [App\Http\Controllers\Group\GroupController::class, 'index'])->name('dashboard_groups');
    Route::post('/get-group', [App\Http\Controllers\Group\GroupController::class, 'getGroup']);
    Route::post('/store-group-action', [App\Http\Controllers\Group\GroupController::class, 'storeGroupAction']);
    Route::get('/get-single-group/{id}', [App\Http\Controllers\Group\GroupController::class, 'getSingleGroup']);
    Route::post('/edit-group-action', [App\Http\Controllers\Group\GroupController::class, 'editGroupAction']);
    Route::post('/archived-un-archived-group-action', [App\Http\Controllers\Group\GroupController::class, 'archivedUnArchivedGroupAction']);
    Route::get('/associate-groups-details/{id}', [App\Http\Controllers\Group\GroupController::class, 'associateGroupsDetails'])->name('groups_group-details');
    Route::post('/get-group-workers', [App\Http\Controllers\Group\GroupController::class, 'getGroupWorkers']);
    Route::post('/search-worker-to-add-group', [App\Http\Controllers\Group\GroupController::class, 'searchWorkerToAddGroup']);
    Route::post('/store-group-worker-action', [App\Http\Controllers\Group\GroupController::class, 'storeGroupWorkerAction']);
    Route::post('/link-group-to-job-action', [App\Http\Controllers\Group\GroupController::class, 'linkGroupToJobAction']);

    Route::post('/store-group-with-worker-action', [App\Http\Controllers\Group\GroupController::class, 'storeGroupWithWorkerAction']);
    Route::post('/get-group-with-worker', [App\Http\Controllers\Group\GroupController::class, 'getGroupWithWorker']);
    Route::post('/archived-un-archived-group-action', [App\Http\Controllers\Group\GroupController::class, 'archivedUnArchivedGroupAction']);
    Route::post('/unlink-group-with-worker/{id}', [App\Http\Controllers\Group\GroupController::class, 'unlinkGroupWithWorkerAction']);

    Route::post('/add-worker-to-existing-group', [App\Http\Controllers\Workers\WorkerSearchController::class, 'addWorkerToExistingGroup']);
    Route::post('/add-worker-to-new-created-group', [App\Http\Controllers\Workers\WorkerSearchController::class, 'addWorkerToNewCreatedGroup']);

    Route::post('/unlink-group-to-job-action', [App\Http\Controllers\Group\GroupController::class, 'unlinkGroupToJobAction']);

    Route::get('/holiday-request', [App\Http\Controllers\PendingRequest\PendingRequestController::class, 'getAbsenceRequest'])->name('dashboard_holiday-requests');
    Route::post('/absence-request', [App\Http\Controllers\PendingRequest\PendingRequestController::class, 'storeAbsenceRequest']);
    Route::post('/approved-pending-request', [App\Http\Controllers\PendingRequest\PendingRequestController::class, 'approvedPendingRequest']);
    Route::post('/declined-pending-request', [App\Http\Controllers\PendingRequest\PendingRequestController::class, 'declinedPendingRequest']);

    Route::get('/address-request', [App\Http\Controllers\PendingRequest\PendingRequestController::class, 'getAddressRequest'])->name('dashboard_address-requests');
    Route::post('/address-request', [App\Http\Controllers\PendingRequest\PendingRequestController::class, 'storeAddressRequest']);

    Route::get('/new-starters', [App\Http\Controllers\Payroll\NewStartersController::class, 'index'])->name('dashboard_new-starters');
    Route::post('/new-starters-action', [App\Http\Controllers\Payroll\NewStartersController::class, 'newStartersAction']);

    Route::get('/payroll-reference-uploader', [App\Http\Controllers\Payroll\PayrollReferenceUploaderController::class, 'index'])->name('dashboard_payroll-reference-uploader');
    Route::post('/upload-payroll-reference-number', [App\Http\Controllers\Payroll\PayrollReferenceUploaderController::class, 'uploadPayrollReferenceNumber']);
});
