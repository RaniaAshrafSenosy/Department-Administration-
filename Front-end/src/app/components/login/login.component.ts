import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from 'src/app/services/auth.service';
import { LoginService } from 'src/app/services/login-service.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  role:string;
  loginMessageError:string='';
  loginForm:FormGroup=new FormGroup({
    main_email: new FormControl(null,[Validators.required,Validators.pattern('[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}')]),
    password:new FormControl(null,[Validators.required,Validators.pattern("^(?=.*[-\#\$\.\%\&\@\!\+\=\<\>\*])?(?=.*[a-zA-Z])?(?=.*\d)?.{8,14}$")])
  })
  submitForm(data:FormGroup){
    // console.log(data.value);
    this._AuthService.login(data.value).subscribe({
      next:(res)=>{
        if(res.state==='success'){


          localStorage.setItem('token',res.token);
          localStorage.setItem('role',res.role);
       
          this._AuthService.saveCurrentUser();
          this._AuthService.log();
          this.role=res.role;
          if(this.role=="Admin"){
            this._Router.navigate(['/home']);
          }else{
            this._Router.navigate(['/home']);
        }
        }
        else{
          this.loginMessageError=res.message;

        }
      }
    })

  }

  constructor(private _AuthService:AuthService, private _Router:Router,private loginservice:LoginService) { }

  ngOnInit(): void {
    if(localStorage.getItem('token')!=null){
      this._AuthService.logout()
    }

  }



}
