import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { User } from 'src/app/interfaces/user';
import { AdminService } from 'src/app/services/admin.service';

import { academicService } from 'src/app/services/all-academic.service';
import { ProfileService } from 'src/app/services/profile.service';


@Component({
  selector: 'app-admin-all-academic',
  templateUrl: './admin-all-academic.component.html',
  styleUrls: ['./admin-all-academic.component.css']
})
export class AdminAllAcademicComponent implements OnInit {
  base:string="http://localhost:8000/media";
  visible=true;
  userData:any[];
  updateData:User;
  constructor(private _academicService:academicService,private _Router:Router,private admin:AdminService) { }
  ngOnInit(): void {
    this.getdata()
  }
  search(data:any){
    if(data.target.value.length!=0){
      this.visible=false;
      this._academicService.searchUser(data.target.value).subscribe({
        next:(res)=>{


          if(res.statusText!="Not Found"){

            this.userData=res;

          }
          else{

          }


        }
      })

    }
    else{
      this.visible=true;
      this.getdata();
    }


  }
  getProfile(id:number){
    this._Router.navigate(['/viewotherprofiles'],{ state:{ id:id } });
  }

  Archive(id:number){


    this._academicService.archiveUser(id).subscribe({
      next:(res)=>{
        if(res['message']=="User has been archived successfully!"){
          this.getdata();
        }
        else{
          alert("there is an Error Try Again")
        }
      }
    })

  }
  getdata(){
    this._academicService.getAllAcademics().subscribe({
      next:(res)=>{


        this.userData=res.users;
      }
    })
  }
  update(id:number){
    this._Router.navigate(['/adminupdateuser'],{ state:{ id:id } });
  }
  promoteUser(id:number){
    this.admin.promoteUser(id).subscribe({
      next:(res)=>{
  
        if(res['msg']=="user has been assigned admin successfully"){
          // this.getdata()
          // alert("user has been assigned admin successfully")

        }


      }
    })

  }
}
