<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Course;
use App\Http\Controllers\userController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LegationController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\SecondmentController;
use App\Http\Controllers\PostgraduateStudiesController;
use App\Http\Controllers\ExternalPostgraduateStudyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\deptCRUD;
use App\Http\Controllers\courseCRUD;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\HasAController;
use App\Http\Controllers\AssignedCoursesController;
use App\Http\Controllers\ProgramController;

use Illuminate\Support\Facades\Storage;

Route::post('/users', [userController::class, 'createUser']);
Route::post('/updateUser/{id}', [userController::class, 'updateUser']);
Route::get('/showUsers', [userController::class, 'showAllUsers']);
Route::get('/getProfessorsFullNames/{dept_code}', [userController::class, 'getProfessorsFullNames']);
Route::get('/getProfessorsFullNames', [userController::class, 'getAllProfessorsFullNames']);
Route::get('/getAllProfessorsFullNamesAndTitles', [userController::class, 'getAllProfessorsFullNamesAndTitles']);
Route::get('/getTAsFullNames/{dept_code}', [userController::class, 'getTAsFullNames']);
Route::post('/archiveUser/{id}', [userController::class, 'archiveUser']);


Route::post('/login',[LoginController::class, 'login']);
Route::get('/logout',[LoginController::class, 'logout']);
Route::post('/refresh',[LoginController::class, 'refresh']);

Route::get('/profile/{id}', [ProfileController::class, 'show']);
Route::post('/updateProfile/{id}', [ProfileController::class, 'update']);
Route::post('/changePassword/{id}', [ProfileController::class, 'changePassword']);
Route::get('/search/{full_name}', [ProfileController::class, 'search']);
Route::get('/getPhoneNumber/{id}', [ProfileController::class, 'getPhoneNumber']);
Route::post('/updateImage/{id}', [ProfileController::class, 'updateImage']);

Route::post('/department', [deptCRUD::class, 'createDepartment']);
Route::post('/updateDepartment/{id}', [deptCRUD::class, 'updateDepartment']);
Route::get('/showDepartment/{id}', [deptCRUD::class, 'showDepartment']);
Route::get('/showDepartments', [deptCRUD::class, 'showAllDepts']);
Route::get('/getDistinctDeptCodes', [deptCRUD::class, 'getDistinctDeptCodes']);
Route::post('/archiveDepartment/{id}', [deptCRUD::class, 'archiveDepartment']);
Route::get('/getDeptAcademic', [deptCRUD::class, 'getDeptAcademic']);
Route::get('/getDeptCourses', [deptCRUD::class, 'getDeptCourses']);
Route::get('/getDeptInfoUser/{id}', [deptCRUD::class, 'getDeptInfoUser']);
Route::get('/getDeptInfoDept/{id}', [deptCRUD::class, 'getDeptInfoDept']);


Route::post('/course', [courseCRUD::class, 'createCourse']);
Route::post('/updateCourse/{id}', [courseCRUD::class, 'updateCourse']);
Route::get('/showCourse/{id}', [courseCRUD::class, 'showCourse']);
Route::get('/showCourses', [courseCRUD::class, 'showAllCourses']);
Route::get('/getDistinctCourseCodes/{dept_code}', [courseCRUD::class, 'getDistinctCourseCodes']);
Route::get('/getCourseDetails/{id}', [courseCRUD::class, 'getCourseDetails']);
Route::post('/archiveCourse/{id}', [courseCRUD::class, 'archiveCourse']);

Route::post('/createProgram', [ProgramController::class, 'createProgram']);
Route::post('/updateProgram/{id}', [ProgramController::class, 'updateProgram']);
Route::get('/showProgram/{id}', [ProgramController::class, 'showProgram']);
Route::get('/getDistinctProgramName', [ProgramController::class, 'getDistinctProgramName']);
Route::get('/getDeptCourses/{id}', [ProgramController::class, 'getDeptCourses']);
Route::post('/archiveProgram/{id}', [ProgramController::class, 'archiveProgram']);
Route::get('/showAllPrograms', [ProgramController::class, 'showAllPrograms']);

Route::post('/postAnnouncement', [AnnouncementController::class, 'postAnnouncement']);
Route::get('/getAllAnnouncementsForApproval', [AnnouncementController::class, 'getAllAnnouncementsForApproval']);
Route::get('/getUserAnnouncements', [AnnouncementController::class, 'getUserAnnouncements']);
Route::get('/approveAnnouncement/{id}', [AnnouncementController::class, 'approveAnnouncement']);
Route::get('/rejectAnnouncement/{id}', [AnnouncementController::class, 'rejectAnnouncement']);
Route::post('/updateAnnouncement/{id}', [AnnouncementController::class, 'updateAnnouncement']);
Route::get('/archiveAnnouncement/{id}', [AnnouncementController::class, 'archiveAnnouncement']);
Route::get('/getMyAnnouncements', [AnnouncementController::class, 'getMyAnnouncements']);
Route::get('/getAnnouncementByID/{id}', [AnnouncementController::class, 'getAnnouncementByID']);
Route::get('/AdminGetAnnounncements', [AnnouncementController::class, 'AdminGetAnnounncements']);
Route::get('/AdminArchiveAnnouncement/{id}', [AnnouncementController::class, 'AdminArchiveAnnouncement']);
Route::post('/createAdmin', [AdminController::class, 'createAdmin']);
Route::post('/updateAdmin/{id}', [AdminController::class, 'updateAdmin']);
Route::post('/promoteAdmin/{id}', [AdminController::class, 'promoteAdmin']);
Route::post('/promoteUser/{id}', [AdminController::class, 'promoteUser']);
Route::get('/getAdminProfile/{id}', [AdminController::class, 'getProfile']);
Route::get('/getAllAdmins', [AdminController::class, 'getAllAdmins']);

Route::post('/createLegation', [LegationController::class, 'createLegation']);
Route::get('/getLegation/{id}', [LegationController::class, 'showLegation']);
Route::get('/showLegations', [LegationController::class, 'showAllLegations']);
Route::get('/showMyLegations', [LegationController::class, 'showUserLegations']);
Route::get('/getLegationData/{id}', [LegationController::class, 'getLegationData']);
//Route::post('/updateLegationStatus/{id}/{status}', [LegationController::class, 'updateLegationStatus']);
Route::post('/acceptLegation/{id}', [LegationController::class, 'acceptLegation']);
Route::post('/rejectLegation/{id}', [LegationController::class, 'rejectLegation']);
Route::get('/showLegationDept', [LegationController::class, 'showLegationDept']);
Route::get('/getLegationStatistics/{dept_code}', [LegationController::class, 'getLegationStatistics']);
Route::get('/exportLegationPDF/{id}', [LegationController::class, 'exportLegationPDF']);
Route::get('/viewLegationPDF/{id}', [LegationController::class, 'viewLegationPDF']);


Route::post('/createVacation', [VacationController::class, 'createVacation']);
Route::get('/getVacation/{id}', [VacationController::class, 'showVacation']);
Route::get('/showVacations', [VacationController::class, 'showAllVacations']);
Route::get('/showMyVacations', [VacationController::class, 'showUserVacations']);
Route::get('/getVacationData/{id}', [VacationController::class, 'getVacationData']);
//Route::post('/updateVacationStatus/{id}/{status}', [VacationController::class, 'updateVacationStatus']);
Route::post('/acceptVacation/{id}', [VacationController::class, 'acceptVacation']);
Route::post('/rejectVacation/{id}', [VacationController::class, 'rejectVacation']);
Route::get('/showVacationsDept', [VacationController::class, 'showVacationsDept']);
Route::get('/exportVacationPDF/{id}', [VacationController::class, 'exportVacationPDF']);
Route::get('/viewVacationPDF/{id}', [VacationController::class, 'viewVacationPDF']);

Route::post('/createSecondment', [SecondmentController::class, 'createSecondment']);
Route::get('/getSecondment/{id}', [SecondmentController::class, 'showSecondment']);
Route::get('/showSecondments', [SecondmentController::class, 'showAllSecondments']);
Route::get('/showMySecondments', [SecondmentController::class, 'showUserSecondments']);
Route::get('/getSecondmentData/{id}', [SecondmentController::class, 'getSecondmentData']);
//Route::post('/updateSecondmentStatus/{id}/{status}', [SecondmentController::class, 'updateSecondmentStatus']);
Route::post('/acceptSecondment/{id}', [SecondmentController::class, 'acceptSecondment']);
Route::post('/rejectSecondment/{id}', [SecondmentController::class, 'rejectSecondment']);
Route::get('/showSecondmentDept', [SecondmentController::class, 'showSecondmentDept']);
Route::get('/getSecondmentStatistics/{dept_code}', [SecondmentController::class, 'getSecondmentStatistics']);
Route::get('/exportSecondmentPDF/{id}', [SecondmentController::class, 'exportSecondmentPDF']);
Route::get('/viewSecondmentPDF/{id}', [SecondmentController::class, 'viewSecondmentPDF']);


Route::post('/createPostgradApplication', [PostgraduateStudiesController::class, 'CreatePostgraduateApplication']);
Route::get('/getPostgradApplication/{id}', [PostgraduateStudiesController::class, 'showPostgraduateApplication']);
Route::get('/showPostgradApplications', [PostgraduateStudiesController::class, 'showAllPostgraduateApplications']);
Route::get('/showMyPostgradApplications', [PostgraduateStudiesController::class, 'showUserPostgraduateApplications']);
Route::get('/showAllMyPostgraduates', [PostgraduateStudiesController::class, 'showAllMyPostgraduates']);
Route::post('/getMyPostgraduatesAverageGrades', [PostgraduateStudiesController::class, 'getMyPostgraduatesAverageGrades']);
Route::get('/exportPostgradPDF/{id}', [PostgraduateStudiesController::class, 'exportPostgradPDF']);
Route::get('/viewPostgradPDF/{id}', [PostgraduateStudiesController::class, 'viewPostgradPDF']);


Route::post('/createExternalPostgradApplication', [ExternalPostgraduateStudyController::class, 'CreateExternalPostgraduateApplication']);
Route::get('/getExternalPostgradApplication/{id}', [ExternalPostgraduateStudyController::class, 'showExternalPostgraduateApplication']);
Route::get('/showExternalPostgradApplications', [ExternalPostgraduateStudyController::class, 'showAllExternalPostgraduateApplications']);

Route::get('/getMyNotifications', [NotificationController::class, 'getMyNotifications']);
Route::get('/getUnreadNotificationsCountForUser', [NotificationController::class, 'getUnreadNotificationsCountForUser']);
Route::get('/archiveNotification/{id}', [NotificationController::class, 'archiveNotification']);

Route::post('/assignCourse', [AssignedCoursesController::class, 'assignCourse']);
Route::get('/getMyAssignedCourses', [AssignedCoursesController::class, 'getMyAssignedCourses']);

Route::get('/', function(){
    Storage::disk('departmentAdministration')->put('test.txt', 'welcome');
    return 'ok';
});

// Route::post('/image', [ProfileController::class, 'store']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'jwt.xyz'], function () {
    Route::get('/user', 'AuthController@getAuthUser');
});

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  //  return $request->user();
//});
