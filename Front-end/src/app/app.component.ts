import { Component } from '@angular/core';
import { AuthService } from './services/auth.service';
import { Router } from '@angular/router';
import {TranslateService} from "@ngx-translate/core";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
export class AppComponent {
  //
  // constructor(private translate: TranslateService) {

  // }
  constructor(private auth:AuthService , private _route:Router,private translate: TranslateService){
    translate.setDefaultLang('en');
    translate.use('en');
  }
  title = 'department_administration';
  ngOnInit(): void {

    //Called after the constructor, initializing input properties, and the first call to ngOnChanges.
    //Add 'implements OnInit' to the class.
    if(localStorage.getItem('token')!=null){
      this.auth.saveCurrentUser();
      this.auth.log();
    }
    else{
      // this.auth._isLoggedIn=false;
     this._route.navigate(['/login']);
    }


  }
}
