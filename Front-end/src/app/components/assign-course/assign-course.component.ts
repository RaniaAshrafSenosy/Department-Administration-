import { Component, OnInit } from '@angular/core';
import { FormArray, FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AdminService } from 'src/app/services/admin.service';

@Component({
  selector: 'app-assign-course',
  templateUrl: './assign-course.component.html',
  styleUrls: ['./assign-course.component.css']
})
export class AssignCourseComponent implements OnInit {
  Form!: FormGroup;
  codee:string;
  deptCodes!:any[];
  semseterSelect:string;
  professors!:any[];

  deptCourses!:any[];
  semester= ['first', 'second',"summer"];
  TAs:any[];
  constructor(private adminservice:AdminService,private router:Router ) { }
  submitForm(data:FormGroup){


    this.adminservice.assignCourse(data.value).subscribe({
      next:(res)=>{


        if(res.message=='success'){
          // this.router.navigate(['/a']);
        }
        else{

        }
      }
    })


  }
  getSemster(semseterSelect:string){
    this.Form.controls.semester.setValue(semseterSelect);

  }
  get(code:any){


    this.adminservice.getProfessorsNames(code).subscribe({
      next:(res)=>{

        if(res.message=='success'){
          this.professors=res.users_full_names;


        }
        else{
        }
      }
    })
    this.adminservice.getTasFullName(code).subscribe({
      next:(res)=>{
        if(res.message=='success'){

          this.TAs=res.users_full_names;

        }
        else{
        }
      }
    })
    this.adminservice.gedDeptCourses(code).subscribe({
      next:(res)=>{
        if(res.message=='success'){
          this.deptCourses=res.courses;
        }
        else{
        }
      }
    })
  }
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


   this.Form= new FormGroup({
    course_code: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z0-9 ]*$'),Validators.minLength(3),Validators.maxLength(10)]),
    dept_code: new FormControl(null,[Validators.required,Validators.pattern("^[a-zA-Z]*$")]),
    academic_year:new FormControl(null, [Validators.required,Validators.pattern("^[0-9]{4}-[0-9]{4}$")]),
    semester:new FormControl(null,[Validators.required]),
    professors:new FormArray([],[Validators.required]),
    teaching_Assistants:new FormArray([])

   });


 }
 onCheckboxChangeP(event: any) {

    const professors = (this.Form.controls['professors'] as FormArray);
    if (event.target.checked) {
      professors.push(new FormControl(event.target.value));
    } else {
      const index = professors.controls
      .findIndex(x => x.value === event.target.value);
      professors.removeAt(index);
    }

  }
  onCheckboxChangeTA(event: any) {
    const teaching_Assistants = (this.Form.controls['teaching_Assistants'] as FormArray);
    if (event.target.checked) {
      teaching_Assistants.push(new FormControl(event.target.value));
    } else {
      const index = teaching_Assistants.controls
      .findIndex(x => x.value === event.target.value);
      teaching_Assistants.removeAt(index);
    }
  }
}
