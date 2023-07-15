import { Component, OnInit } from '@angular/core';

import { CourseService } from 'src/app/services/course.service';

@Component({
  selector: 'app-user-courses',
  templateUrl: './user-courses.component.html',
  styleUrls: ['./user-courses.component.css']
})
export class UserCoursesComponent implements OnInit {
myCourse:any
academicYears:any[]
semester:any[]
firstSemester:any[];
secondSemester:any[];
summerSemester:any[];

  constructor(private course:CourseService) { }

  ngOnInit(): void {
    this.course.getMyAssignedCourse().subscribe({
      next:(data)=>{
       console.log(data);
       this.myCourse=data['assigned courses'];
        this.academicYears = Object.keys(this.myCourse);

      }
    });
  }
  // Object.keys(data['assigned courses'][academicYear])
  getSemsters(academicYear: string): any[] {
    console.log(Object.keys(this.myCourse[academicYear]));
    this.semester=Object.keys(this.myCourse[academicYear])
    return this.semester
    // return this.academics.find((entry) => Object.keys(entry)[0] === department)[department].professor_names;
  }
  getFirstSemsters(academicYear:string,semester:string): any[]{
    this.firstSemester=Object.keys(this.myCourse[academicYear][semester])
    return Object.values(this.myCourse[academicYear][semester])
    // return this.academics.find((entry) => Object.keys(entry)[0] === department)[department].professor_names;
  }

}

