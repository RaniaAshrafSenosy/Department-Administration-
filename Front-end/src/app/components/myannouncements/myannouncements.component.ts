import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AnnouncementService } from 'src/app/services/announcement.service';

@Component({
  selector: 'app-myannouncements',
  templateUrl: './myannouncements.component.html',
  styleUrls: ['./myannouncements.component.css']
})
export class MyannouncementsComponent implements OnInit {

  base:string="http://localhost:8000/media";
  announcmentArray:any[];
  dept:string;
  date:string[];
  file:File;
  fileName:string;
  allAnnouncement:boolean=false;
  successMessage:string;
  // announcements:User;
  constructor(private announcment:AnnouncementService,private _Router:Router) { }
  ngOnInit(): void {
    this.getannouncments();
  }
  update(id:number){
    console.log(id);

    this._Router.navigate(['/updateAnnouncment'],{ state:{ id:id } });
  }
  Archive(id:number){
    this.announcment.ArchiveAnnouncement(id).subscribe({
      next:(res)=>{
        if (res['message']=='The announcement has been archived successfully!'){
          this.successMessage='The Announcement has been archived successfully!';
          this.getannouncments();
        }
      }
    })
  }
  getannouncments(){
    this.announcment.getMyAnnouncments().subscribe({
      next:(res)=>{
        console.log("announcement",res.announcements);
        // this.dept=localStorage.getItem('dept');
        // console.log(this.dept);
        this.announcmentArray=res.announcements;

      }
    })
  }
  splitData(date:string){
 this.date=date.split(' ');
 console.log(this.date);

  }
  FileName(fileName:File):string{
    this.file=fileName;
    this.fileName=String(this.file).substring(String(this.file).lastIndexOf("/") + 1);
    return this.fileName
  }


}
