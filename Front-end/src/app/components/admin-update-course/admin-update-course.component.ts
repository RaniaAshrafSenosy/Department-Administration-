import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { course } from 'src/app/interfaces/course';
import { AdminService } from 'src/app/services/admin.service';
import { CourseService } from 'src/app/services/course.service';


@Component({
  selector: 'app-admin-update-course',
  templateUrl: './admin-update-course.component.html',
  styleUrls: ['./admin-update-course.component.css']
})
export class AdminUpdateCourseComponent implements OnInit {
courseId:number;
oldCourseSpecs:File;
courseSpecs:File;
courseSpecsName:string;
Form!: FormGroup;
courseInstance:course

  constructor(private admin:AdminService, private router: Router,private course:CourseService) {
    this.courseId=this.router.getCurrentNavigation().extras.state.id;
  }

  ngOnInit(): void {
    console.log(this.courseId);
    this.Form= new FormGroup({
      prerequisites: new FormControl(null,[Validators.pattern('^[a-zA-Z, ]*$')]),
      credit_hours: new FormControl(null,[Validators.pattern('^[0-6]{1}$')]),
      course_desc: new FormControl(null,[Validators.pattern("^[a-zA-Z0-9 ]*$")]),
      course_specs: new FormControl(null,[Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
    });
    this.course.getCoursebyid(this.courseId).subscribe({
      next:(data)=>{
        console.log("fattaaa");
        console.log(data);
        this.courseInstance=data.course;
        this.oldCourseSpecs=data?.course_specs_tUrl;
        this.courseSpecs=data?.course_specs_tUrl;
          this.courseSpecsName=String(this.courseSpecs).substring(String(this.courseSpecs).lastIndexOf("/") + 1);


        this.Form.controls.prerequisites.setValue(this.courseInstance?.prerequisites);
        this.Form.controls.credit_hours.setValue(this.courseInstance?.credit_hours);
        this.Form.controls.course_desc.setValue(this.courseInstance?.course_desc);

      }
    })

  }
  submitForm(data:FormGroup){

    const formData = new FormData();
    if(this.courseSpecs!=this.oldCourseSpecs){
      console.log("kkkkk");

      formData.append('course_specs',this.courseSpecs);
    }

    formData.append('credit_hours',data.get('credit_hours').value );
    formData.append('course_desc',data.get('course_desc').value);
    formData.append('prerequisites',data.get('prerequisites').value);
    console.log("alooo");

    console.log(formData);
    this.admin.updateCourse(formData,this.courseId).subscribe({
      next:(res)=>{
        if(res.message=='success'){
          // console.log("yala nnam");
          // console.log(res);
          this.router.navigate(["/allcourses"]);
        }
        else{

        }
      }
    })
  }
  oncourseSpecsFileSelected(event){
    this.courseSpecs = event.target.files[0];
    this.courseSpecsName = this.courseSpecs ? this.courseSpecs.name : '';
   }
//    onClick(){
//   console.log("validation");
//   Object.keys(this.Form.controls).forEach(controlName => {
//     const control = this.Form.get(controlName);
//     if (control.invalid ) {
//       console.log(this.Form.get(controlName));
//       console.log('notvalid');

//     }
//   });

// }
}
