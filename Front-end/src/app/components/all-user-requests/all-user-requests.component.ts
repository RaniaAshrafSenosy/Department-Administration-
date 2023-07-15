import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { secondment } from 'src/app/services/secondment';

@Component({
  selector: 'app-all-user-requests',
  templateUrl: './all-user-requests.component.html',
  styleUrls: ['./all-user-requests.component.css']
})
export class AllUserRequestsComponent implements OnInit {
  allSecondments:[{secondments:any,attachment:string}];
  allLegaions:[{Legations:any,attachment:string}];
  allVacations:[{vacations:any,attachment:string}];
  showMore1:boolean;
  constructor(private secondment:secondment,private _Router:Router) { }

  ngOnInit(): void {
    this.secondment.getAllUserVacations().subscribe(
      {
        next:(res)=>{
      
          this.allVacations=res['vacations'];

      }
    })
    this.secondment.getAllUserSecondments().subscribe(
      {
        next:(data)=>{

          this.allSecondments=data['secondments'];

      }
    })
    this.secondment.getAllUserLegations().subscribe(
      {
        next:(resp)=>{

          this.allLegaions=resp['Legations'];

      }
    })
  }
}
