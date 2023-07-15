import { academicYear } from 'src/app/interfaces/academicYear';
import { Component, OnInit} from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import jwtDecode from 'jwt-decode';
import { User } from 'src/app/interfaces/user';
import { AuthService } from 'src/app/services/auth.service';
import { ProfileService } from 'src/app/services/profile.service';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent implements OnInit {
  user:User;
  imageUrl:string;
  imageFile:File;
  showAverage:boolean =false;
  Form!: FormGroup;
  average!: FormGroup;
  academicYears:string[]=["2016-2017","2017-2018","2018-2019","2019-2020","2020-2021","2021-2022","2022-2023"];
  saveBtn:boolean=false;
  fileName:string;
  user_time:[{start:string,end:string,day_time:string}];
  location:string;
  hasImage:boolean=true;
  averageValue:number;
  constructor(private _profile:ProfileService, private auth:AuthService ) {
   }
  ngOnInit(): void {
    this.average =new FormGroup({
      academic_year: new FormControl( Validators.required),
    });

    this.Form= new FormGroup({
      imageUrl: new FormControl(),

    });
    this.getData();

  }
  getData(){
    this._profile.getProfile().subscribe({
      next:(data)=>{
        this.user=data.user;
        // console.log( this.user.profile_links[0]);
        this.imageUrl=data.imageUrl;
        // console.log(data);

        // console.log(data.imageUrl);
        if(this.imageUrl?.endsWith("media")){
          this.hasImage=false;
          this.user.imageUrl="";
        }
        this.location=data.user.office_location.replaceAll("*"," ")
        this.user_time=data.user.time_range;
      }
    })
  }
changeImage(event){
this.imageFile=event.target.files[0]
// console.log("hhhhhhhhhhhh");
this.saveBtn=true;
this.fileName=this.imageFile.name;
}
sumbitAverageForm(data:FormGroup){
  this._profile.getMyPostgraduatesAverageGrades(data.value).subscribe({
    next:(res)=>{
      console.log(res['message']);
      if(res['message']){
        this.showAverage=true;
      this.averageValue=res['average_grade'].average_grad_in_numbers


      }
      console.log(this.averageValue);

      // console.log(res.message);

    }
  });
}
submitForm(){
  const formData = new FormData();
  formData.append('image',this.imageFile );
  this._profile.updatImage(formData).subscribe({
    next:(res)=>{
      console.log(res);
      if(res['message']==' success'){
      this.imageUrl=res['imageUrl'];
      this.getData();
      let token=localStorage.getItem('token') ;
      let decode:any=jwtDecode(token);
      localStorage.setItem('user_imagPath',this.imageUrl);
      decode.user_imagPath=this.imageUrl;
      // let encode:any=jwtEncode(token);
     this.auth.isLoggedInChanged.emit(true)
      this.saveBtn=false;
      this.hasImage=true;
      }
    }
  })

}
}
