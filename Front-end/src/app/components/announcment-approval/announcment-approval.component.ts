import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { User } from 'src/app/interfaces/user';
import { academicService } from 'src/app/services/all-academic.service';
import { AnnouncementService } from 'src/app/services/announcement.service';

@Component({
  selector: 'app-announcment-approval',
  templateUrl: './announcment-approval.component.html',
  styleUrls: ['./announcment-approval.component.css']
})
export class AnnouncmentApprovalComponent implements OnInit {

  base:string="http://localhost:8000/media";
  announcmentArray:any[];
  dept:string;
  date:string[];
  modaltype:boolean;
  allAnnouncement:boolean=false;
  // announcements:User;
  constructor(private announcment:AnnouncementService,private _Router:Router) { }
  ngOnInit(): void {
    if(localStorage.getItem('role')=="Dean"||localStorage.getItem('role')=="Vice Dean"){
       this.allAnnouncement=true;
    }
    else{
      this.allAnnouncement=false;
    }
    this.getannouncments();
  }
  getProfile(id:number){
    this._Router.navigate(['/viewotherprofiles'],{ state:{ id:id } });
  }
  accept(id:number){
    this.announcment.approveAnnouncments(id).subscribe({
      next:(res)=>{
        // console.log(res);
        if(res.message=="Announcement approved successfully"){
          this.getannouncments();
        }
      }
    })
  }
  reject(id:number){
    this.announcment.rejectAnnouncments(id).subscribe({
      next:(res)=>{
        // console.log(res);
        if(res.message=="Announcement rejected successfully"){
          this.getannouncments();
        }

      }
    })
  }
  getannouncments(){
    this.announcment.getAllAnnouncmentsForApproval().subscribe({
      next:(res)=>{
        console.log("announcement",res.announcements);
        this.dept=localStorage.getItem('dept');
        console.log(this.dept);
        this.announcmentArray=res.announcements;

      }
    })
  }
  splitData(date:string){
 this.date=date.split(' ');
 console.log(this.date);

  }
  changemodal(condition:boolean){
    this.modaltype=condition;
  }

}
