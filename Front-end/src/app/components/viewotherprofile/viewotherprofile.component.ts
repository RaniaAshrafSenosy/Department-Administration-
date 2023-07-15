import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { User } from 'src/app/interfaces/user';
import { UserTime } from 'src/app/interfaces/user-time';
import { ProfileService } from 'src/app/services/profile.service';

@Component({
  selector: 'app-viewotherprofile',
  templateUrl: './viewotherprofile.component.html',
  styleUrls: ['./viewotherprofile.component.css']
})
export class ViewotherprofileComponent implements OnInit {
  userId:number;
  user:User;
  imageUrl:string;
  user_time:[{start:string,end:string,day_time:string}];;
  location:string;
  hasImage:boolean=true;
  variable:string;
  constructor( private router: Router,private route: ActivatedRoute,private _profile:ProfileService) {
    this.userId=this.router.getCurrentNavigation().extras.state.id;
    console.log("userinlink",this.userId);

   }

  ngOnInit(): void {

    console.log(this.userId);
    this._profile.getUserProfile(this?.userId).subscribe({
      next:(data)=>{
        console.log(data);

        this.user=data.user;
        console.log(this.user?.phone_number);

        this.variable=`https://wa.me/2${this.user?.phone_number}`;
        if(data.imageUrl?.endsWith("media")){
          this.hasImage=false;
          this.imageUrl="";
        }
        else{
          this.imageUrl=data.imageUrl;
        }
        this.location=data.user.office_location.replaceAll("*"," ")
        this.user_time=data.user.time_range;
      }
    })

}
}
