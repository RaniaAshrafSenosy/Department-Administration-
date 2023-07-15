import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { AcademicsComponent } from './components/academics/academics.component';
import { CourseComponent } from './components/course/course.component';
import { DepartmentComponent } from './components/department/department.component';
import { HomeComponent } from './components/home/home.component';
import { LoginComponent } from './components/login/login.component';
import { ProfileComponent } from './components/profile/profile.component';
import { UpdateProfileComponent } from './components/update-profile/update-profile.component';
import { UserCoursesComponent } from './components/user-courses/user-courses.component';
import { ViewotherprofileComponent } from './components/viewotherprofile/viewotherprofile.component';
import { AdminAddUserComponent } from './components/admin-add-user/admin-add-user.component';
import { LoginguardGuard } from './services/loginguard.guard';
import { AllCoursesComponent } from './components/all-courses/all-courses.component';
import { ChangePasswordComponent } from './components/change-password/change-password.component';
import { AllDepartmentComponent } from './components/all-department/all-department.component';
import { LoggedInDepartmentComponent } from './components/logged-in-department/logged-in-department.component';
import { AddAdminComponent } from './components/add-admin/add-admin.component';
import { AdminAllAcademicComponent } from './components/admin-all-academic/admin-all-academic.component';
import { PostgradGuestComponent } from './components/postgrad-guest/postgrad-guest.component';
import { SecondmentComponent } from './components/secondment/secondment.component';
import { AdminAddDepartmentComponent } from './components/admin-add-department/admin-add-department.component';
import { AdminAddCourseComponent } from './components/admin-add-course/admin-add-course.component';
import { AssignCourseComponent } from './components/assign-course/assign-course.component';
import { AdminUpdateDeparmentComponent } from './components/admin-update-deparment/admin-update-deparment.component';
import { SecViewAllReqComponent } from './components/sec-view-all-req/sec-view-all-req.component';
import { SecViewAllSecondmentComponent } from './components/sec-view-all-secondment/sec-view-all-secondment.component';
import { SecViewAllLegationComponent } from './components/sec-view-all-legation/sec-view-all-legation.component';
import { AdminGuard } from './services/admin.guard';
import { NotificationsComponent } from './components/notifications/notifications.component';
import { AnnouncmentApprovalComponent } from './components/announcment-approval/announcment-approval.component';
import { CreatAnnouncementComponent } from './components/creat-announcement/creat-announcement.component';
import { AdminUpdateCourseComponent } from './components/admin-update-course/admin-update-course.component';
import { AllUserRequestsComponent } from './components/all-user-requests/all-user-requests.component';
import { AdminUpdateUserComponent } from './components/admin-update-user/admin-update-user.component';
import { SecretaryGuard } from './services/secretary.guard';
import { MyannouncementsComponent } from './components/myannouncements/myannouncements.component';
import { UpdateannouncementComponent } from './components/updateannouncement/updateannouncement.component';
import { UpdateProgComponent } from './components/update-prog/update-prog.component';
import { AdminAddProgramComponent } from './components/admin-add-program/admin-add-program.component';
import { ProgramComponent } from './components/program/program.component';
import { ChartsComponent } from './components/charts/charts.component';
import { MyPostGraduateComponent } from './components/my-post-graduate/my-post-graduate.component';
const routes: Routes = [
  // user
  { path: 'login', component: LoginComponent },

  {
    path: '',
    component: LoginComponent,
    pathMatch: 'full',
    // canActivate: [LoginguardGuard],
  },
  //new
  { path: 'program', component: ProgramComponent, pathMatch: 'full' },
  {
    path: 'updateAnnouncment',
    component: UpdateannouncementComponent,
    canActivate: [LoginguardGuard],
  },
  {
    path: 'mypostgraduate',
    component: MyPostGraduateComponent,
    canActivate: [LoginguardGuard],
  },
  {
    path: 'home',
    component: HomeComponent,
    pathMatch: 'full',
    canActivate: [LoginguardGuard],
  },
  {
    path: 'viewotherprofiles',
    component: ViewotherprofileComponent,
    canActivate: [],
  },
  {
    path: 'updateprofile',
    component: UpdateProfileComponent,
    canActivate: [LoginguardGuard],
  },
  {
    path: 'profile',
    component: ProfileComponent,
    canActivate: [LoginguardGuard],
  },
  { path: 'department', component: DepartmentComponent, pathMatch: 'full' },
  {
    path: 'changepassword',
    component: ChangePasswordComponent,
    canActivate: [LoginguardGuard],
  },
  { path: 'course', component: CourseComponent },
  {
    path: 'alldepartments',
    component: AllDepartmentComponent,
    pathMatch: 'full',
  },
  {
    path: 'myannouncements',
    component: MyannouncementsComponent,
    pathMatch: 'full',
  },
  { path: 'allcourses', component: AllCoursesComponent },
  {
    path: 'usercourses',
    component: UserCoursesComponent,
    canActivate: [LoginguardGuard],
  },
  { path: 'Academics', component: AcademicsComponent },
  {
    path: 'createannouncment',
    component: CreatAnnouncementComponent,
    canActivate: [LoginguardGuard],
  },
  {
    path: 'secondment',
    component: SecondmentComponent,
    canActivate: [LoginguardGuard],
    data: { formType: 'secondment' },
  },
  {
    path: 'legation',
    component: SecondmentComponent,
    canActivate: [LoginguardGuard],
    data: { formType: 'legation' },
  },

  {
    path: 'vacation',
    component: SecondmentComponent,
    canActivate: [LoginguardGuard],
    data: { formType: 'vacation' },
  },
  {
    path: 'mydepartment',
    component: LoggedInDepartmentComponent,
    pathMatch: 'full',
    canActivate: [LoginguardGuard],
  },
  {
    path: 'notifications',
    component: NotificationsComponent,
    canActivate: [LoginguardGuard],
  },
  {
    path: 'allUserRequests',
    component: AllUserRequestsComponent,
    canActivate: [LoginguardGuard],
  },


  // secretary
  {
    path: 'viewAllVacation',
    component: SecViewAllReqComponent,
    canActivate: [SecretaryGuard],
  },
  {
    path: 'viewAllSecondment',
    component: SecViewAllSecondmentComponent,
    canActivate: [SecretaryGuard],
  },
  {
    path: 'viewAllLegation',
    component: SecViewAllLegationComponent,
    canActivate: [SecretaryGuard],
  },
  {
    path: 'viewAllVacationNotPending',
    component: SecViewAllReqComponent,
    canActivate: [SecretaryGuard],
    data: { formType: 'viewAllVacationNotPending' },
  },
  {
    path: 'viewAllSecondmentNotPending',
    component: SecViewAllSecondmentComponent,
    canActivate: [SecretaryGuard],
    data: { formType: 'viewAllSecondmentNotPending' },
  },
  {
    path: 'viewAllLegationNotPending',
    component: SecViewAllLegationComponent,
    canActivate: [SecretaryGuard],
    data: { formType: 'viewAllLegationNotPending' },
  },
  /////////////////////////////////////////// admin ////////////////////////////////////////////////////
  {
    path: 'AdminAddUser',
    component: AdminAddUserComponent,
    canActivate: [AdminGuard],
  },
  //new
  {
    path: 'AdminCreateProgram',
    component: AdminAddProgramComponent,
    canActivate: [AdminGuard],
  },
  {
    path: 'AdminUpdateProgram',
    component: UpdateProgComponent,
    canActivate: [AdminGuard],
  },
  {
    path: 'adminviewcourse',
    component: AdminAddCourseComponent,
    canActivate: [AdminGuard],
  },
  {
    path: 'updateCourse',
    component: AdminUpdateCourseComponent,
    canActivate: [AdminGuard],
  },
  {
    path: 'adminaddcourse',
    component: AdminAddCourseComponent,
    canActivate: [AdminGuard],
  },
  {
    path: 'addadmin',
    component: AddAdminComponent,
    canActivate: [AdminGuard],
  }, //
  {
    path: 'adminacademics',
    component: AdminAllAcademicComponent,
    canActivate: [AdminGuard],
  },
  {
    path: 'adminadddepartment',
    component: AdminAddDepartmentComponent,
    canActivate: [AdminGuard],
  },
  {
    path: 'adminupdateuser',
    component: AdminUpdateUserComponent,
    canActivate: [ AdminGuard],
  },
  {
    path: 'adminaupdatedepartment',
    component: AdminUpdateDeparmentComponent,
    canActivate: [AdminGuard],
  },
  {
    path: 'assigncourse',
    component: AssignCourseComponent,
    canActivate: [LoginguardGuard],
  },

  { path: 'postgradguest', component: PostgradGuestComponent },
  {
    path: 'announcementAprroval',
    component: AnnouncmentApprovalComponent,
    canActivate: [LoginguardGuard],
  },
  {path:"chart",component:ChartsComponent},
  { path: '**', redirectTo: 'login' },
];
@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule],
})
export class AppRoutingModule { }
