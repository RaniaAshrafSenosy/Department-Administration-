import { department } from 'src/app/interfaces/department';
import { AnnouncementService } from './../../services/announcement.service';
import { DepartmentService } from 'src/app/services/department.service';
import { Component, OnInit } from '@angular/core';
import { FormArray, FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AdminService } from 'src/app/services/admin.service';

@Component({
  selector: 'app-creat-announcement',
  templateUrl: './creat-announcement.component.html',
  styleUrls: ['./creat-announcement.component.css']
})
export class CreatAnnouncementComponent implements OnInit {
  hasfile=false;
  emailErrorMessage:string='';

  dept:any[]=[Validators.required];
  file: File;
  Form!: FormGroup;
  mydept:string;
  semseterSelect:string;
  ispriviledeged:boolean=true;
  Roles:any[]= ["Dean","Vice Dean","Head of Department","Professor", "Teaching Assistant","Secretary"];
  depts:any[];
  constructor(private admin:AdminService,private department:DepartmentService,private announcementService:AnnouncementService,private _Router:Router,) { }

  ngOnInit(): void {
    if(localStorage.getItem("role")=="Professor"||localStorage.getItem("role")=="Teaching Assistant"||localStorage.getItem("role")=="Secretary"){
      this.ispriviledeged=false;
      this.department.getDepartmentByUserID().subscribe({
        next:(res)=>{
          this.mydept=res[0]?.dept_code;
          if(res.message=='success'){
          }
          else{
          }
        }
      })
    }
    this.Form= new FormGroup({
      title: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$'),Validators.minLength(2)]),
      body: new FormControl(null,[Validators.required,Validators.minLength(5)]),
      file: new FormControl(null, [Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
      target_dept:new FormArray([],[Validators.required]),
      target_role:new FormArray([],[Validators.required])
    });


    if(this.ispriviledeged){
      this.admin.gedDeptCodes().subscribe({
        next:(res)=>{
          this.depts=res.departments_codes;
          if(res.message=='success'){

          }
          else{
          }
        }
      })
    }




  }
  submitForm(data:FormGroup){

    const formData = new FormData();
    if(this.hasfile){
      formData.append('file',this.file );
    }
 console.log("target role",data.get('target_role').value);

    formData.append('body', data.get('body').value);
    formData.append('title', data.get('title').value);

    const targetRoleValues = data.get('target_role').value;
targetRoleValues.forEach((value: string) => {
  formData.append('target_role[]', value);
});
const targetDeptValues = data.get('target_dept').value;
targetDeptValues.forEach((value: string) => {
  formData.append('target_dept[]', value);
});

    // console.log(formData);

    this.announcementService.postAnnouncment(formData).subscribe({
      next:(res)=>{
        // console.log(res);
        if(res.message=='Announcement has been stored successfully'){
          this._Router.navigate(["/myannouncements"]);
          // console.log('done');
        }
        else{
          console.log(res);
          // this.emailErrorMessage=res.message;

        }
      }
    })
  }
  onbookletFileSelected(event){
    this.hasfile=true
    this.file = event.target.files[0];
}
onCheckboxChangerole(event: any) {

  const professors = (this.Form.controls['target_role'] as FormArray);
  if (event.target.checked) {
    professors.push(new FormControl(event.target.value));
  } else {
    const index = professors.controls
    .findIndex(x => x.value === event.target.value);
    professors.removeAt(index);
  }

}
onCheckboxChangedept(event: any) {
  const teaching_Assistants = (this.Form.controls['target_dept'] as FormArray);
  if (event.target.checked) {
    teaching_Assistants?.push(new FormControl(event.target.value));
  } else {
    const index = teaching_Assistants.controls
    .findIndex(x => x.value === event.target.value);
    teaching_Assistants.removeAt(index);
  }
}

}
