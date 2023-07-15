import { AllCoursesService } from 'src/app/services/all-courses.service';
import { Component, OnInit } from '@angular/core';
import { allcourse } from 'src/app/interfaces/allcourse';
import { Router } from '@angular/router';
import { CourseService } from 'src/app/services/course.service';
import { course } from 'src/app/interfaces/course';

@Component({
  selector: 'app-all-courses',
  templateUrl: './all-courses.component.html',
  styleUrls: ['./all-courses.component.css']
})
export class AllCoursesComponent implements OnInit {
  array=[];
  constructor(private CourseService:CourseService,private router:Router) { }
  courses:course[];
  ngOnInit(): void {
    this.CourseService.getAllCourses().subscribe({
      next:(data)=>{
        this.array=data.course_specs        ;
        console.log(data.course_specs[1]);

      }
    })

  }
  opencourse(id:number){
    console.log(id);

    this.router.navigate(['/course'],{ state:{ id:id } });
  }

}
