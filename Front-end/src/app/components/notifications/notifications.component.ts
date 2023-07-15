import { Component, OnInit } from '@angular/core';
import { AnnouncementService } from 'src/app/services/announcement.service';
import { LoginService } from 'src/app/services/login-service.service';
import { NotificationsService } from 'src/app/services/notifications.service';

@Component({
  selector: 'app-notifications',
  templateUrl: './notifications.component.html',
  styleUrls: ['./notifications.component.css']
})
export class NotificationsComponent implements OnInit {

  constructor(private loginService:LoginService,private notification:NotificationsService) { }
  array:any[];
  object:any;
  ngOnInit(): void {
    this.loginService.getNotifications().subscribe(
      {
        next:(res)=>{
        console.log(res);
        this.object=res
        // console.log(this.object.notifications[0].notification);
        this.array=this.object.notifications;

      }
    }
    )
  }
  archive(id:number){
    this.notification.archiveNotification(id).subscribe(
      {
        next:(res)=>{
        console.log(res);
      }
    }
    )
  }

}
