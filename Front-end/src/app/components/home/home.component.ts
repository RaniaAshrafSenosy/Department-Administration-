import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AnnouncementService } from 'src/app/services/announcement.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {

  condition:boolean=false;
  announcmentArray:any[];
  archived:string;
  adminRole:boolean=false;
  role:string;
  file:File;
  fileName:string;

    constructor(private annoucmentservice:AnnouncementService,private _Router:Router) { }
    ngOnInit(): void {
      this.role=localStorage.getItem("role")
      if(this.role=="Admin"){
    this.adminRes();
      }
      else {
        this.annoucmentservice.getAllAnnouncments().subscribe(
          {
            next:(res)=>{
              console.log(res);

              if(res.message=="success"){
                this.adminRole=false;
                this.announcmentArray=res.announcements
                console.log(this.announcmentArray);



                console.log("announcmentArray",this.announcmentArray);
              }
              else{

              }
          }
        }
        )
      }

    }
    AdminArchiveAnnouncement(id:number){
      this.annoucmentservice.AdminArchiveAnnouncement(id).subscribe({
        next:(res)=>{
          if(res['message']=='The announcement has been archived successfully!'){
           this.archived='The Announcement has been archived successfully!';
           this.adminRes();
          }
        }
      })
    }
    adminRes(){
      this.annoucmentservice.adminAnnouncement().subscribe({
        next:(res)=>{
          if(res.message=="success"){
            this.adminRole=true;
            this.announcmentArray=res.announcement
          }

        }
      })
    }
    FileName(fileName:File):string{
      this.file=fileName;
      this.fileName=String(this.file).substring(String(this.file).lastIndexOf("/") + 1);
      return this.fileName
    }

}
