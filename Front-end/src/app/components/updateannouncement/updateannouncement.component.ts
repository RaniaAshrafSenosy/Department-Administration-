import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormArray, FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { department } from 'src/app/interfaces/department';
import { AdminService } from 'src/app/services/admin.service';
import { AnnouncementService } from 'src/app/services/announcement.service';
import { DepartmentService } from 'src/app/services/department.service';

@Component({
  selector: 'app-updateannouncement',
  templateUrl: './updateannouncement.component.html',
  styleUrls: ['./updateannouncement.component.css']
})
export class UpdateannouncementComponent implements OnInit {

  deptId:number;
  file:File;
  oldfile:File;
  registerForm!: FormGroup;
  deptBylaw:string;
  announcmentInstance:any;
    constructor( private router: Router,private announcment:AnnouncementService, private http: HttpClient) {
    this.deptId=this.router.getCurrentNavigation().extras.state?.id;

   }
  submitForm(data:FormGroup){
    const formData = new FormData();
    if(this.file!=this.oldfile){
      formData.append('file',this.file);
    }
    formData.append('title',data.get('title').value);
    formData.append('body',data.get('body').value );
    // console.log("alooo");

    // console.log(formData);

    this.announcment.updateAnnouncement(formData,this.deptId).subscribe({
      next:(res)=>{
        if(res.message=='success'){
          this.router.navigate(['/myannouncements']);
          console.log("yala nnam");
          console.log(res);

        }
        else{
          // this.emailErrorMessage=res.message;

        }
      }
    })
  }
  ngOnInit(): void {
    this.registerForm= new FormGroup({
      title: new FormControl(null,[Validators.required,Validators.minLength(2)]),
      body: new FormControl(null,[Validators.required,Validators.minLength(5)]),
      file: new FormControl(null, [Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
    });
    this.announcment.getAnnouncmentByid(this.deptId).subscribe({
      next:(data)=>{
        console.log(data);

        this.announcmentInstance=data.announcement[0];
        console.log(this.announcmentInstance.title);

        this.file=this.announcmentInstance?.file;
        this.oldfile=this.announcmentInstance?.file;
        this.deptBylaw=String(this.announcmentInstance?.file).substring(String(this.announcmentInstance?.file).lastIndexOf("/") + 1);
        this.registerForm.controls.title?.setValue(this.announcmentInstance?.title);
        this.registerForm.controls.body?.setValue(this.announcmentInstance?.body);
      }
    })
  }
  onbylawFileSelected(event: any) {
    this.file = event.target.files[0];
    this.deptBylaw = this.file ? this.file.name : '';
  }
}
