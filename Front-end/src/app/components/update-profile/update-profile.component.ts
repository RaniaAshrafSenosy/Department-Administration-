import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { AbstractControl, FormArray, FormControl, FormGroup,Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { User } from 'src/app/interfaces/user';
import { ProfileService } from 'src/app/services/profile.service';
@Component({
  selector: 'app-update-profile',
  templateUrl: './update-profile.component.html',
  styleUrls: ['./update-profile.component.css']
})
export class UpdateProfileComponent implements OnInit {
  @ViewChild("building")
  building :ElementRef;
  @ViewChild("floor")
  floor:ElementRef;
  @ViewChild("room")
  room:ElementRef;
  @ViewChild("phone")
  phone:ElementRef;
  @ViewChild("relative")
  relative:ElementRef;
  @ViewChild("start")
  start :ElementRef;
  @ViewChild("end")
  end :ElementRef;
  @ViewChild("link")
  link :ElementRef;
  @ViewChild("day_time")
  day_time :ElementRef;
  location:string[];
  titles= ['Professor', 'Associate Professor',"Assistant Professor", "Teaching Assistant","Assintant Lecturer","Secretary"];
  buildings:string[]=["New Building","مبنى الاحصاء","Building 3"]
  floors:string[]=["Floor 1","Floor 2","Floor 3","Floor 4"]
  imageUrl:string;
  hasImage:boolean=true;
  daysOfWeek:string[]=["Saturday","Sunday","Monday","Tuesday","Wednesday","Thursday","Friday"];
  disabled:boolean=true;
  public match:boolean=true;
  registerForm!: FormGroup;
  officehours:[{start:string,end:string,day_time:string}]
  profile_links:string[]=[];
  roles= ['Professor', 'Teaching Assistant ',"Secretary","Head of Department","Dean","Vice Dean"];
  user:User;
  user_time:any[];

  userTimeKeys!:any;
  constructor(private profile:ProfileService,private _Router:Router){}
  ngOnInit(): void {
    this.registerForm= new FormGroup({
      full_name: new FormControl(),
      user_name: new FormControl(),
      relative_name: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      title: new FormControl(),
      main_email: new FormControl(),
      time_range:new FormControl([]),
      profile_links:new FormControl([]),
      additional_email: new FormControl(),
      phone_number: new FormControl(null,[Validators.pattern("^01[0-2,5]{1}[0-9]{8}$"),
      Validators.minLength(11),Validators.maxLength(11)]),
      relative_number: new FormControl(null,[Validators.pattern("^01[0-2,5]{1}[0-9]{8}$"),Validators.minLength(11),Validators.maxLength(11)]),
      role:new FormControl(),
    });
    this.profile.getProfile().subscribe({
      next:(data)=>{
        // console.log("data",data);
        this.imageUrl=data.imageUrl;
        this.user=data.user;
        // console.log(data.user);


        this.profile_links = this.user?.profile_links || [];
        // console.log( this.profile_links);

        this.registerForm.controls.time_range.setValue(data.user.time_range);
        this.registerForm.controls.full_name.setValue(this.user?.full_name);
        this.registerForm.controls.user_name.setValue(this.user?.user_name);
        this.registerForm.controls.relative_name.setValue(this.user?.relative_name);
        this.registerForm.controls.main_email.setValue(this.user?.main_email);
        this.registerForm.controls.additional_email.setValue(this.user?.additional_email);
        this.registerForm.controls.title.setValue(this.user?.title);
        this.registerForm.controls.phone_number.setValue(this.user?.phone_number);
        this.registerForm.controls.relative_number.setValue(this.user?.relative_number);
        this.registerForm.controls.role.setValue(this.user?.role);
        if(data.imageUrl.endsWith("media")){
          // console.log("dggzd,")
          this.hasImage=false;
        }
        else{
          console.log("should");
          console.log(data.imageUrl)

          this.imageUrl=data.imageUrl
        }
        this.officehours=data.user.time_range;
        this.location= this.user.office_location.split("*");
        // console.log(this.location?.length);
        if(this.user?.office_location==''){
          this.user.office_location= ' * *room1*';
        }
        this.location= this.user.office_location.split("*");
      }
    })


  }
  concateLocation(){
    var whole=`${this.building?.nativeElement.value}*${this.floor?.nativeElement.value}*${this.room?.nativeElement.value}*`;

    return whole;
  }
  submitForm(data:FormGroup){
    console.log(this.user.time_range)
    data.controls.time_range.setValue(this.officehours)
    data.controls.profile_links.setValue(this.profile_links);
    data.value.time_range=this.officehours;
    data.value.profile_links=this?.profile_links;
    data.value.office_location=this.concateLocation();


    this.profile.update(data.value).subscribe({
      next:(res)=>{
        // console.log("res",res);
        if(res.message=='User profile updated successfully!'){
          console.log("success");

          this._Router.navigate(['/profile']);

        }
        else{

        }
      }
    })
  }
  Add(){
    let element:{start: string, end: string, day_time: string} = {start: "", end: "", day_time: ""};
    // this.user.time_range.push(element)
    // console.log(this.user.time_range);
    console.log(this.start.nativeElement.value)
    console.log(this.end.nativeElement.value)
    console.log(this.day_time.nativeElement.value)
    element.start= this.start.nativeElement.value
    element.end= this.end.nativeElement.value
    element.day_time= this.day_time.nativeElement.value
    this.officehours.push(element);

  }
  AddLink(){
    let link='';
    link= this.link.nativeElement.value
    console.log(link);
    this.profile_links?.push(link);
    console.log("profile links",this.profile_links)
  }
  show(item:any){
    // console.log(1);
    // console.log(item);
  }
  remove(index:number){
    this.user.time_range.splice(index,1);
    // console.log(this.user.time_range);

  }
  removeLink(index:number){
    this.user.profile_links.splice(index,1);
    // console.log(this.user.profile_links);
  }
  log(){
    // console.log(this?.user.time_range[0])
  }




}
