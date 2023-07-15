import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { AbstractControl, FormControl, FormGroup,Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { User } from 'src/app/interfaces/user';
import { ProfileService } from 'src/app/services/profile.service';
@Component({
  selector: 'app-updateadmin',
  templateUrl: './updateadmin.component.html',
  styleUrls: ['./updateadmin.component.css']
})
export class UpdateadminComponent implements OnInit {

  @ViewChild("building")
  building :ElementRef;
  @ViewChild("floor")
  floor:ElementRef;
  relative:ElementRef;
  location:string[];
  public match:boolean=true;
  date:Date;
  registerForm!: FormGroup;
  emailErrorMessage:string='';
roles= ['Professor', 'Teaching Assistant ',"Secretary","Head of Department","Dean","Vice Dean"];

  user:User;
  user_time:any[];
  userTimeKeys!:any;
  constructor(private profile:ProfileService,private _Router:Router){}
  submitForm(data:FormGroup){
    this.profile.update(data.value).subscribe({
      next:(res)=>{
        if(res.message=='success'){
          this._Router.navigate(['/profile']);
        }
        else{
          this.emailErrorMessage=res.message;
        }
      }
    })
  }
  ngOnInit(): void {
    this.registerForm= new FormGroup({
      full_name: new FormControl(),
      main_email: new FormControl(),
    });
    this.profile.getProfile().subscribe({
      next:(data)=>{
        this.user=data.user;
        this.registerForm.controls.full_name.setValue(this.user?.full_name);
        this.registerForm.controls.main_email.setValue(this.user?.main_email);
      }
    })
  }
}
