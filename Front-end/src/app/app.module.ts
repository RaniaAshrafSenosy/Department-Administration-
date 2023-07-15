import { UpdateProgComponent } from './components/update-prog/update-prog.component';
import { ProgramComponent } from './components/program/program.component';
import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './components/home/home.component';
import { LoginComponent } from './components/login/login.component';
import { NavbarComponent } from './components/navbar/navbar.component';
import { ProfileComponent } from './components/profile/profile.component';
import { ViewotherprofileComponent } from './components/viewotherprofile/viewotherprofile.component';
import { UpdateProfileComponent } from './components/update-profile/update-profile.component';
import { AcademicsComponent } from './components/academics/academics.component';
import { DepartmentComponent } from './components/department/department.component';
import { CourseComponent } from './components/course/course.component';
import { UserCoursesComponent } from './components/user-courses/user-courses.component';
import { ReactiveFormsModule } from '@angular/forms';
import { HttpClient, HttpClientModule } from '@angular/common/http';
import { AdminAddUserComponent } from './components/admin-add-user/admin-add-user.component';
import { AdminAddCourseComponent } from './components/admin-add-course/admin-add-course.component';
import { AllCoursesComponent } from './components/all-courses/all-courses.component';
import { ChangePasswordComponent } from './components/change-password/change-password.component';
import { AllDepartmentComponent } from './components/all-department/all-department.component';
import { LoggedInDepartmentComponent } from './components/logged-in-department/logged-in-department.component';
import { AddAdminComponent } from './components/add-admin/add-admin.component';
import { UpdateadminComponent } from './components/updateadmin/updateadmin.component';
import { AdminAllAcademicComponent } from './components/admin-all-academic/admin-all-academic.component';
import { AdminUpdateUserComponent } from './components/admin-update-user/admin-update-user.component';
import { PostgradGuestComponent } from './components/postgrad-guest/postgrad-guest.component';
import { FormsModule } from '@angular/forms';
import { SecondmentComponent } from './components/secondment/secondment.component';
import { AdminAddDepartmentComponent } from './components/admin-add-department/admin-add-department.component';
import { AssignCourseComponent } from './components/assign-course/assign-course.component';
import { AdminUpdateDeparmentComponent } from './components/admin-update-deparment/admin-update-deparment.component';
import { SecViewAllReqComponent } from './components/sec-view-all-req/sec-view-all-req.component';
import { SecViewAllSecondmentComponent } from './components/sec-view-all-secondment/sec-view-all-secondment.component';
import { SecViewAllLegationComponent } from './components/sec-view-all-legation/sec-view-all-legation.component';
import { NotificationsComponent } from './components/notifications/notifications.component';
import { CreatAnnouncementComponent } from './components/creat-announcement/creat-announcement.component';
import { AnnouncmentApprovalComponent } from './components/announcment-approval/announcment-approval.component';
import { AdminUpdateCourseComponent } from './components/admin-update-course/admin-update-course.component';
import { AllUserRequestsComponent } from './components/all-user-requests/all-user-requests.component';
import { MyannouncementsComponent } from './components/myannouncements/myannouncements.component';
import { UpdateannouncementComponent } from './components/updateannouncement/updateannouncement.component';



import {TranslateLoader, TranslateModule} from '@ngx-translate/core';
import {TranslateHttpLoader} from '@ngx-translate/http-loader';
import { AdminAddProgramComponent } from './components/admin-add-program/admin-add-program.component';
import { ChartsComponent } from './components/charts/charts.component';
import { MyPostGraduateComponent } from './components/my-post-graduate/my-post-graduate.component';


export function HttpLoaderFactory(http: HttpClient): TranslateHttpLoader {
  return new TranslateHttpLoader(http);
}

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    NavbarComponent,
    LoginComponent,
    ProfileComponent,
    ViewotherprofileComponent,
    UpdateProfileComponent,
    AcademicsComponent,
    DepartmentComponent,
    CourseComponent,
    UserCoursesComponent,
    AdminAddUserComponent,
    AdminAddCourseComponent,
    AllCoursesComponent,
    ChangePasswordComponent,
    AllDepartmentComponent,
    LoggedInDepartmentComponent,
    AddAdminComponent,
    UpdateadminComponent,
    AdminAllAcademicComponent,
    AdminUpdateUserComponent,
    PostgradGuestComponent,
    SecondmentComponent,
    AdminAddDepartmentComponent,
    AssignCourseComponent,
    AdminUpdateDeparmentComponent,
    SecViewAllReqComponent,
    SecViewAllSecondmentComponent,
    SecViewAllLegationComponent,
    NotificationsComponent,
    CreatAnnouncementComponent,
    AnnouncmentApprovalComponent,
    AdminUpdateCourseComponent,
    AllUserRequestsComponent,
    MyannouncementsComponent,
    UpdateannouncementComponent,
    ProgramComponent,
    UpdateProgComponent,
    AdminAddProgramComponent,
    ChartsComponent,
    MyPostGraduateComponent

  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    ReactiveFormsModule,
    FormsModule,
    TranslateModule.forRoot({
      loader: {
          provide: TranslateLoader,
          useFactory: HttpLoaderFactory,
          deps: [HttpClient]
      }
  })

  ],
  providers: [],

  bootstrap: [AppComponent]
})
export class AppModule { }
