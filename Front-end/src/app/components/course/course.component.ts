import { course } from 'src/app/interfaces/course';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CourseService } from 'src/app/services/course.service';

@Component({
  selector: 'app-course',
  templateUrl: './course.component.html',
  styleUrls: ['./course.component.css']
})
export class CourseComponent implements OnInit {
courseId:number;
  constructor(private courseservice:CourseService, private router: Router) {
    this.courseId=this.router.getCurrentNavigation().extras.state.id;
   }
  id:number;
  course:course;
  prerequisites;
  courseSpecs:File;
  courseSprecsName:string;
  admin:boolean=false;

  ngOnInit(): void {

      this.courseservice.getCoursebyid(this.courseId).subscribe({
        next:(data)=>{

          this.course=data.course;
          this.prerequisites=this.course.prerequisites.split(",");
          this.courseSpecs=data.course_specs_tUrl;
          this.courseSprecsName=String(data.course_specs_tUrl).substring(String(data.course_specs_tUrl).lastIndexOf("/") + 1);

          if(localStorage.getItem("role")!="Admin"&&localStorage.getItem("privileged_user")!="1"){
            this.admin=false;
          }
          else{
            this.admin=true;

          }


        }
      })
  }

  update(id:number){
    this.router.navigate(['/updateCourse'],{ state:{ id:id } });
  }
  Archive(id:number){
    this.courseservice.archiveCourse(id).subscribe({
      next:(res)=>{

if(res.message=="The course has been archived successfully!"){
  this.router.navigate(['/allcourses']);
}
      }
    })
  }
}
