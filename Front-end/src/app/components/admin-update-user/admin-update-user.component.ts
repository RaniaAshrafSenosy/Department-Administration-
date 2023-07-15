import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { User } from 'src/app/interfaces/user';
import { AdminService } from 'src/app/services/admin.service';
import { academicService } from 'src/app/services/all-academic.service';
import { ProfileService } from 'src/app/services/profile.service';

@Component({
  selector: 'app-admin-update-user',
  templateUrl: './admin-update-user.component.html',
  styleUrls: ['./admin-update-user.component.css']
})
export class AdminUpdateUserComponent implements OnInit {


  date:Date;
  deptCodes!:any[];
  registerForm!: FormGroup;
  emailErrorMessage:string='';
roles= ['Professor', 'Teaching Assistant ',"Secretary","Head of Department","Dean","Vice Dean"];

  user:User;
  user_time:any[];
  userTimeKeys!:any;
  userId:number;
  constructor(private profile:ProfileService,private _Router:Router,private admin :AdminService){
    this.userId=this._Router.getCurrentNavigation().extras.state.id;
  }

  submitForm(data:FormGroup){


    this.admin.adminUpdateUser(this.userId,data.value).subscribe({
      next:(res)=>{
        console.log(res);

        if(res['message']=="success"){
          this._Router.navigate(['/adminacademics']);


        }
        else{
          // this.emailErrorMessage=res.message;

        }
      }
    })
  }


  ngOnInit(): void {
    this.profile.getUserProfile(this.userId).subscribe({
      next:(data)=>{

        this.user=data.user;
        console.log(this.user.dept_code);

        this.registerForm.controls.full_name.setValue(this.user?.full_name);
        this.registerForm.controls.main_email.setValue(this.user?.main_email);
        this.registerForm.controls.role.setValue(this.user?.role);
        this.registerForm.controls.dept_code.setValue(this.user?.dept_code);
      }
    })
    this.admin.gedDeptCodes().subscribe({
      next:(res)=>{
        if(res.message=='success'){
          this.deptCodes=res.departments_codes
          console.log(this.user.dept_code==this.deptCodes[0].dept_code);
        }
        else{

        }
      }
    })

    this.registerForm= new FormGroup({
      full_name: new FormControl(),
      main_email: new FormControl(),
      role:new FormControl(),
      dept_code:new FormControl(),

    });



  }

}
