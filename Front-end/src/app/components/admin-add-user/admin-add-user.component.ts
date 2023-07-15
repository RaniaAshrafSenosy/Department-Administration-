import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormControl, FormGroup, ValidationErrors, ValidatorFn, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AdminService } from 'src/app/services/admin.service';
import { AuthService } from 'src/app/services/auth.service';

@Component({
  selector: 'app-admin-add-user',
  templateUrl: './admin-add-user.component.html',
  styleUrls: ['./admin-add-user.component.css']
})
export class AdminAddUserComponent implements OnInit {
  public match:boolean=true;
  registerForm!: FormGroup;
  emailErrorMessage:string='';
  deptCodes!:any[];
  codee:string;
  titles= ['Professor', 'Associate Professor',"Assistant Professor", "Teaching Assistant","Assintant Lecturer","Secretary"];
  roles= ['Professor', 'Teaching Assistant ',"Secretary","Dean","Vice Dean"];
  submitForm(data:FormGroup){


    this._AuthService.register(data.value).subscribe({
      next:(res)=>{


        if(res.message=='success'){
          this._Router.navigate(['/adminacademics']);

        }
        else{
          this.emailErrorMessage=res.message;
        }
      }
    })


  }

  constructor(private _AuthService:AuthService,private _Router:Router,private adminservice:AdminService ) {}

  ngOnInit(): void {
    this.adminservice.gedDeptCodes().subscribe({
      next:(res)=>{
        if(res.message=='success'){
          this.deptCodes=res.departments_codes
        }
        else{

        }
      }
    })
    this.registerForm= new FormGroup({
      full_name: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$'),Validators.minLength(3),Validators.maxLength(30)]),
      main_email: new FormControl(null,[Validators.required,Validators.pattern('[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}')]),
      password: new FormControl(null,[Validators.required,Validators.pattern("^(?=.*[-\#\$\.\%\&\@\!\+\=\<\>\*])?(?=.*[a-zA-Z])?(?=.*\d)?.{8,14}$")]),
     dept_code: new FormControl(null,[Validators.required,Validators.pattern("^[a-zA-Z]*$")]),
      role:new FormControl('Professor'),
      title:new FormControl('title'),

      cpassword: new FormControl(null,[Validators.required,Validators.pattern("^(?=.*[-\#\$\.\%\&\@\!\+\=\<\>\*])?(?=.*[a-zA-Z])?(?=.*\d)?.{8,14}$")]),
    },[MustMatch("password","cpassword")]);
  }

}

export function MustMatch(controlName: string, matchingControlName: string):ValidatorFn {
  return  (formGroup: AbstractControl):ValidationErrors|null => {
  const control = formGroup.get(controlName);
  const matchingControl =formGroup.get(matchingControlName);
  if (control?.value !== matchingControl?.value) {
    return { mustMatch: true }
  } else
  {
  return null;
  }

}

}
