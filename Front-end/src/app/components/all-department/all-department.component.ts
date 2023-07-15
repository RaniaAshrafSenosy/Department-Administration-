import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { department } from 'src/app/interfaces/department';
import { programs } from 'src/app/interfaces/programs';
// import { programs } from 'src/app/interfaces/programs';
import { DepartmentService } from 'src/app/services/department.service';
import { ProgramService } from 'src/app/services/program';

@Component({
  selector: 'app-all-department',
  templateUrl: './all-department.component.html',
  styleUrls: ['./all-department.component.css']
})
export class AllDepartmentComponent implements OnInit {


  constructor(private departmentService:DepartmentService ,private _Router:Router, private program:ProgramService) {

  }
  Departments:department[];
  Programs:programs[];
  admin:boolean=false;
  archive:boolean=false;
  ngOnInit(): void {
    this.getdata()
    this.getProgramsData();
    if(localStorage.getItem("role")!="Admin"&&localStorage.getItem("privileged_user")!="1"){
      this.admin=false;
    }
    else{
      this.admin=true;
    }
  }
  open(id:number,name:string,code:string){
    console.log("not program");

    if(localStorage.getItem("role")!="Admin"&&localStorage.getItem("privileged_user")!="1"){
      this._Router.navigate(['/department'],{ state:{ id:id,name:name,code:code } });
    }
    else{
      this.admin=true;
      this.archive=true
    }
  }
  openProgram(id:number){
    console.log("program");

    if(localStorage.getItem("role")!="Admin"&&localStorage.getItem("privileged_user")!="1"){
      this._Router.navigate(['/program'],{ state:{ id:id} });
    }
    else{
      this.admin=true;
      this.archive=true
    }
  }
  getdata(){
    this.departmentService.getAllDepartments().subscribe(
      {
        next:(res)=>{
        this.Departments=res.departments;
      }
    })
  }
  getProgramsData(){
    this.program.getAllprograms().subscribe(
      {
        next:(res)=>{
          console.log("prog",res);

        this.Programs=res.programs;
      }
    })
  }
  updateDept(id:number){
    this._Router.navigate(['/adminaupdatedepartment'],{ state:{ id:id} });
  }
  updateProg(id:number){
    this._Router.navigate(['/AdminUpdateProgram'],{ state:{ id:id} });
  }
  archiveDept(id:number){

    this.departmentService.archiveDepartment(id).subscribe({
      next:(res)=>{
        console.log(res);
        if(res.message=="success"){
          this.getdata()
        }
        else{
          alert("this Department Cant be Archived Please Try Again");
        }

      }
    })
  }
  archiveProgram(id:number){
    this.program.archiveProgram(id).subscribe({
      next:(res)=>{
        console.log(res);
        if(res.message=="success"){
          this.getProgramsData()
        }
        else{
          alert("this Department Cant be Archived Please Try Again");
        }

      }
    })
  }
}
