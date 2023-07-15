import { Component, OnInit } from '@angular/core';
import { Department, ResponseData } from 'src/app/interfaces/academics';
import { academicService } from 'src/app/services/all-academic.service';
import { NavigationExtras, Router } from '@angular/router';
import { FormControl, FormGroup } from '@angular/forms';
import { JsonPipe } from '@angular/common';
import { User } from 'src/app/interfaces/user';

@Component({
  selector: 'app-academics',
  templateUrl: './academics.component.html',
  styleUrls: ['./academics.component.css']
})
export class AcademicsComponent implements OnInit {
  base:string="http://localhost:8000/media";
  visible=true;

  array:any[];
  academics:ResponseData[];
  departmentNames: string[];
  professorNames:Department['professor_names'];
  constructor(private _academicService:academicService,private _Router:Router) { }

  ngOnInit(): void {
    console.log(this.base.length);

    this._academicService.getAcademics().subscribe({
      next:(data)=>{
        console.log(data);

        this.academics=data;
        this.departmentNames=this.academics.map((name) => Object.keys(name)[0]);
      }
    })
  }
  search(data:any){
    if(data.target.value.length!=0){
      this.visible=false;
    }
    else{
      this.visible=true;
    }
    this._academicService.searchUser(data.target.value).subscribe({
      next:(res)=>{
        console.log(res[0] );
        this.array=res;
      }
    })

  }
  getProfessors(department: string): Department['professor_names'] {
    return this.academics.find((entry) => Object.keys(entry)[0] === department)[department].professor_names;
  }
  getTAs(department: string): Department['ta_names'] {
    return this.academics.find((entry) => Object.keys(entry)[0] === department)[department].ta_names;
  }

  getSecretaries(department: string): Department['secretary_names'] {
    return this.academics.find((entry) => Object.keys(entry)[0] === department)[department].secretary_names;
  }
  getProfile(id:number){
    this._Router.navigate(['/viewotherprofiles'],{ state:{ id:id } });
  }
}
