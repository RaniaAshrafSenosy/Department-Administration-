import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { department } from 'src/app/interfaces/department';
import { DepartmentService } from 'src/app/services/department.service';

@Component({
  selector: 'app-logged-in-department',
  templateUrl: './logged-in-department.component.html',
  styleUrls: ['./logged-in-department.component.css']
})
export class LoggedInDepartmentComponent implements OnInit {
  deptid:number;
  deptname:string;
  department:department;
  courseSpecs
  constructor( private router: Router,private deptservice:DepartmentService ) {
   }
   openProfile(id:number){

    this.router.navigate(['/viewotherprofiles'],{ state:{ id:id } });

   }
   openCourse(id:number){
    console.log("my id");
    console.log(id);


    this.router.navigate(['/course'],{ state:{ id:id } });
   }
  ngOnInit(): void {
    this.deptservice.getDepartmentByUserID().subscribe({
      next:(data)=>{
        console.log(data);
        this.department=data;
        console.log("id");
        console.log(this.department[0].head_id);
      }
    })

  }

}
