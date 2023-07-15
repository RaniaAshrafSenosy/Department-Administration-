// import { AdminService } from './../../services/admin.service';
import { Component, OnInit } from '@angular/core';
import { AbstractControl, FormControl, FormGroup, ValidationErrors, ValidatorFn, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AdminService } from 'src/app/services/admin.service';

@Component({
  selector: 'app-add-admin',
  templateUrl: './add-admin.component.html',
  styleUrls: ['./add-admin.component.css']
})
export class AddAdminComponent implements OnInit {

  public match:boolean=true;
  registerForm!: FormGroup;
  emailErrorMessage:string='';

  submitForm(data:FormGroup){
    data.removeControl("cpassword")
    console.log(data.value);

    this.AdminService.register(data.value).subscribe({
      next:(res)=>{
        console.log(res.message);

        if(res.message=='success'){
          this._Router.navigate(['/home']);
        }
        else{
          this.emailErrorMessage=res.message;
        }
      }
    })
  }
  showerrors(){
    console.log(this.registerForm)
  }
  constructor(private AdminService:AdminService,private _Router:Router) {}

  ngOnInit(): void {
    this.registerForm= new FormGroup({
      name: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$'),Validators.minLength(5),Validators.maxLength(40)]),
      email: new FormControl(null,[Validators.required,Validators.pattern('[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}')]),

      password: new FormControl(null,[Validators.required,Validators.pattern("^(?=.*[-\#\$\.\%\&\@\!\+\=\<\>\*])?(?=.*[a-zA-Z])?(?=.*\d)?.{8,14}$")]),
      cpassword: new FormControl(null,[Validators.required,Validators.pattern("^(?=.*[-\#\$\.\%\&\@\!\+\=\<\>\*])?(?=.*[a-zA-Z])?(?=.*\d)?.{8,14}$")]),
    },[MustMatch("password","cpassword")]);
  }
}
export function MustMatch(controlName: string, matchingControlName: string):ValidatorFn {
  return  (formGroup: AbstractControl):ValidationErrors|null => {
  const control = formGroup.get(controlName);
  const matchingControl =formGroup.get(matchingControlName);
  if (control?.value !== matchingControl?.value) {
    console.log(2);

    return { mustMatch: true }
  } else
  {
    console.log(3);

  return null;
  }
}
}
