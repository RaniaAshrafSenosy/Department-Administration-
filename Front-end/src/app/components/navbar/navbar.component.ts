import { Component, EventEmitter, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { TranslateService } from '@ngx-translate/core';
import { AuthService } from 'src/app/services/auth.service';
import { LoginService } from 'src/app/services/login-service.service';
import { ProfileService } from 'src/app/services/profile.service';

@Component({
  selector: 'app-navbar',
  templateUrl: './navbar.component.html',
  styleUrls: ['./navbar.component.css']
})
export class NavbarComponent implements OnInit {

  constructor(private auth :AuthService, private route:Router,private profile:ProfileService, private translate: TranslateService) { }
  language:string;
  loggedIn:boolean=false;
  username:string;
  hasaphoto:boolean=false;
  imageUrl:string;
  NumberOfNotifications:number;
  role:string;
  privilageUser:string;
  ngOnInit(): void {

    this.language=this.translate.currentLang
    this.auth.isLoggedInChanged.subscribe(isLoggedIn => {
      // console.log(localStorage.getItem("token"));
      if (isLoggedIn) {
        // console.log("called");
        this.hasaphoto = true;
        // this.imageUrl= localStorage.getItem('user_imagPath')
        this.showPhoto();
        this.loggedIn=true;

        this.getNumberOfNotifications();
        this.role=localStorage.getItem('role');
        this.privilageUser=localStorage.getItem('privileged_user');
        this.displayUserInfo();
      } else {
        this.loggedIn = false;
      }
    });
    if(this.auth.isLoggedIn){
   this.displayUserInfo();
   this.getNumberOfNotifications();
   this.role=localStorage.getItem('role');
   this.privilageUser=localStorage.getItem('privileged_user');
    }
    else{
      this.auth._isLoggedIn=false;
      this.loggedIn = false;
      this.auth.logout();
      this.route.navigate(['/login'])
    }
  }
  changeLanguage(){
    // this.auth.changeLanguage();
    if(this.translate.currentLang=="ar"){
      this.language="en"
      this.translate.use("en")
    }
    else if(this.translate.currentLang=="en"){
      this.translate.use("ar")
      this.language="ar"
    }
  }
  displayUserInfo() {
    if (localStorage.getItem('token') != null) {
      this.username = localStorage.getItem('username');
      // console.log("local storage user name",localStorage.getItem('token'));

      this.username = this.username?.split(' ')[0];
        this.showPhoto();
        // console.log(this.imageUrl);

      if (this.imageUrl?.endsWith('media')) {
        // console.log("media");
        this.hasaphoto = false;
      } else {
        this.hasaphoto = true;
        // this.imageUrl = localStorage.getItem('user_imagPath');
        this.showPhoto();
      }
      this.loggedIn = true;
    } else {
      this.loggedIn = false;
    }
  }
  getNumberOfNotifications(){
    this.auth.getNumOfNotififcations().subscribe({
      next:(data)=>{
        // console.log(data);
        this.NumberOfNotifications=data.number_of_unread_notifications;

      }
    });
  }
  logout(){
    this.loggedIn=false;
    this.auth.logout();
  }
  showPhoto(){

 if(localStorage.getItem("role")!="Admin"){
  this.profile.getUserProfile(Number(localStorage.getItem('user_id'))).subscribe({
    next:(res)=>{
      // console.log(res);
      this.imageUrl=res.imageUrl;
      // console.log(this.imageUrl);
    }
    })
 }
// this.imageUrl=res.



  }
}
