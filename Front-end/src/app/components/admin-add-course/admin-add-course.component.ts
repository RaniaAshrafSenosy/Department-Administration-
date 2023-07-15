import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, ValidatorFn, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AdminService } from 'src/app/services/admin.service';


@Component({
  selector: 'app-admin-add-course',
  templateUrl: './admin-add-course.component.html',
  styleUrls: ['./admin-add-course.component.css']
})
export class AdminAddCourseComponent implements OnInit {

  public match:boolean=true;
  registerForm!: FormGroup;
  courseSpecs: File;
  codee:string;
  deptCodes!:any[];
  program:string;
  selected:string;
  programCodes!:any[];
  roles= ['Professor', 'Teaching Assistant ',"Secretary","Head of Department","Dean","Vice Dean"];
  constructor(private admin:AdminService,private _Router:Router) {}
  ngOnInit(): void {
    this.admin.gedDeptCodes().subscribe({
      next:(res)=>{
        if(res.message=='success'){
          this.deptCodes=res.departments_codes

        }
        else{

        }
      }
    })
    this.admin.getDeptPrograms().subscribe({
      next:(res)=>{
        if(res.message=='success'){
          this.programCodes=res.programs_codes

        }
        else{

        }
      }
    })
    this.registerForm= new FormGroup({
      course_code  : new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z0-9 ]*$'),Validators.minLength(3),Validators.maxLength(10)]),
      prerequisites: new FormControl(null,[Validators.pattern('^[a-zA-Z, ]*$')]),
      credit_hours : new FormControl(null,[Validators.required,Validators.pattern('^[0-6]{1}$')]),
      course_name  : new FormControl(null,[Validators.required,Validators.pattern("^[a-zA-Z ]*$")]),
      program_name  : new FormControl(null,[Validators.pattern("^[a-zA-Z ]*$")]),
      course_desc  : new FormControl(null,[Validators.required,Validators.pattern("^[a-zA-Z0-9 ]*$")]),
      course_specs : new FormControl(null,[Validators.required,Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
      dept_code    : new FormControl(null,[Validators.required,Validators.pattern("^[a-zA-Z]*$")]),
    });
  }
  submitForm(data:FormGroup){
    const formData = new FormData();
if( data.get('program_name').value!=null){
  formData.append('program_name', data.get('program_name').value);
}

    formData.append('course_specs',this.courseSpecs );
    formData.append('course_code', data.get('course_code').value);
    formData.append('credit_hours', data.get('credit_hours').value);

    formData.append('course_name', data.get('course_name').value);
    formData.append('prerequisites', data.get('prerequisites').value);
    formData.append('course_desc', data.get('course_desc').value);
    formData.append('dept_code', data.get('dept_code').value);

    this.admin.createCourse(formData).subscribe({
      next:(res)=>{

        if(res.message=='success'){
          this._Router.navigate(['/allcourses']);
        }
        else{

        }
      }
    })
  }
  oncoursespecsFileSelected(event){
    this.courseSpecs = event.target.files[0];
}


}

