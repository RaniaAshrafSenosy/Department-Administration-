import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormControl, FormGroup, Validators,FormBuilder, FormArray } from '@angular/forms';
import { Router } from '@angular/router';
import { Country } from 'src/app/interfaces/country';
import { User } from 'src/app/interfaces/user';
import { postgradService } from 'src/app/services/postgrad';

@Component({
  selector: 'app-postgrad-guest',
  templateUrl: './postgrad-guest.component.html',
  styleUrls: ['./postgrad-guest.component.css']
})
export class PostgradGuestComponent implements OnInit {
  codee:string;
  supervisors!:any[];
  @ViewChild("internalName")
  internalName :ElementRef;
  @ViewChild("externalName")
  externalName :ElementRef;
  @ViewChild("externalTitle")
  externalTitle :ElementRef;
  title:string;
  isUserSignedIn: boolean = false;
  shouldShowPlaceholder:boolean=false;
  userId:number;
  user:User;
  attachFile: File;
  certificateFile:File;
  @ViewChild("option")
  option :ElementRef;
  @ViewChild("phoneNumber")
  phoneNumberInput :ElementRef;
  phoneNumber:string;
  phoneNumberValidator:any[]=[Validators.required];
  dialog:string;
  selectedCountry: Country;
  registerForm!: FormGroup;
  disabled:boolean=true;
  loggedDisabled:boolean=false
  externalSupervisorNames:string[]=[];
  internalSupervisorNames:string[]=[];
  externalSupervisorTitles:string[]=[];
  emailErrorMessage:string='';
  titles= ['Professor', 'Associate Professor',"Assistant Professor"];
  gender=['male','female'];
  grades=['A','A+','A-','B','B+','B-','C','C+','C-','D','D+','D-'];
  countries: Country[] = [
    { name: 'United States', dialCode: '+1' },
    { name: 'Canada', dialCode: '+1' },
    { name: 'United Kingdom', dialCode: '+44' },
    {name:'Egypt', dialCode:'+20'}

  ];


  constructor(private postgrad:postgradService,private _Router:Router ) {}

  ngOnInit(): void {
    this.postgrad.getFullNamesTitles().subscribe({
      next:(res)=>{
        if(res['message']=='success'){
          this.supervisors=res['users_full_names']
        }

        else{

        }
      }
    })
     this.userId =Number(localStorage.getItem('user_id')) ;
    if(this.userId){
      this.isUserSignedIn = true;
      this.loggedDisabled=true;
    }
    else{
      this.isUserSignedIn = false;
      this.shouldShowPlaceholder=false;
    }


    if(this.isUserSignedIn){


      this.postgrad.getUserInfo(this.userId).subscribe({
        next:(res)=>{

          this.user=res.user;
          this.registerForm.controls.student_name.setValue(res.user.full_name);
          this.registerForm.controls.department.setValue(res.user.dept_code);
          this.registerForm.controls.phone_number.setValue(res.user.phone_number);
          this.registerForm.controls.email.setValue(res.user.main_email);
          if(res.user?.phone_number?.length==0){
            this.phoneNumberValidator=[Validators.required,Validators.pattern("^\\+201[0-9]{9}$")];
            this.registerForm.controls['phone_number'].setValidators(this.phoneNumberValidator);

            this.shouldShowPlaceholder=true;

          }
        }
      })

    }

    const studentValidators = this.userId ? [] : [Validators.required, Validators.pattern('^[a-zA-Z ]*$'), Validators.minLength(2), Validators.maxLength(30)];
    const departmentValidators = this.userId ? [] : [Validators.required,Validators.pattern('^[a-zA-Z ]*$'),Validators.minLength(2)];

    this.registerForm= new FormGroup({
      student_name: new FormControl(null,studentValidators),
      department: new FormControl(null,departmentValidators),
      email: new FormControl(null,[Validators.required,Validators.pattern('[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,3}')]),
      phone_number: new FormControl(null,this.phoneNumberValidator),
      telephone_number: new FormControl(null,[Validators.required,Validators.minLength(3),Validators.pattern("^[^A-Za-z][0-9]+$")]),
      credit_hours: new FormControl(null,[Validators.required,Validators.pattern("^[0-9]+?$")]),
      gender:new FormControl('male'),
      attachment: new FormControl(null, [Validators.required,Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
      bachelor_certificate: new FormControl(null, [Validators.required]),
      academic_year:new FormControl(null, [Validators.required,Validators.pattern("^[0-9]{4}-[0-9]{4}$")]),
      registration_date:new FormControl(null, [Validators.required]),
      preliminary_date:new FormControl(null, [Validators.required]),
      graduation_date:new FormControl(null, [Validators.required]),
      faculty_name: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      university_name: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      nationality: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      research_topic_AR: new FormControl(null,[Validators.required,Validators.pattern('^[ء-ي\s]+$')]),
      research_topic_EN: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      research_interest: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      target: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      specialization: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      field_of_research: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      external_supervisor_names:new FormArray([]),
      external_supervisor_titles:new FormArray([],),
      internal_supervisor_names: new FormArray([]),
      employer: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      employer_address: new FormControl(null,[Validators.required,Validators.pattern('^[0-9a-zA-Z\\s/-]*[0-9a-zA-Z][0-9a-zA-Z\\s/-]*$')]),
      grade:new FormControl('B'),
      number:new FormControl('Egypt'),
    });


  }
  submitForm(data:FormGroup){
    data.removeControl("number");

    const formData = new FormData();

    this.phoneNumber=this.concate(this.dialog,this.phoneNumberInput.nativeElement.value);

    formData.append('attachment',this.attachFile );
    formData.append('phone_number', this.phoneNumber);
    formData.append('telephone_number', data.get('telephone_number').value);
    formData.append('credit_hours', data.get('credit_hours').value);
    formData.append('gender', data.get('gender').value);
    formData.append('bachelor_certificate', this.certificateFile);
    formData.append('academic_year', data.get('academic_year').value);
    formData.append('registration_date', data.get('registration_date').value);
    formData.append('preliminary_date', data.get('preliminary_date').value);
    formData.append('graduation_date', data.get('graduation_date').value);
    formData.append('faculty_name', data.get('faculty_name').value);
    formData.append('university_name', data.get('university_name').value);
    formData.append('nationality', data.get('nationality').value);
    formData.append('research_topic_AR', data.get('research_topic_AR').value);
    formData.append('research_topic_EN', data.get('research_topic_EN').value);
    formData.append('research_interest', data.get('research_interest').value);
    formData.append('target', data.get('target').value);
    formData.append('specialization', data.get('specialization').value);
    formData.append('field_of_research', data.get('field_of_research').value);
    this.internalSupervisorNames.forEach((value: string) => {
      formData.append('internal_supervisor_names[]', value);
    });
    this.externalSupervisorNames.forEach((value: string) => {
      formData.append('external_supervisor_names[]', value);
    });
    this.externalSupervisorTitles.forEach((value: string) => {
      formData.append('external_supervisor_titles[]', value);
    });

    formData.append('employer', data.get('employer').value);
    formData.append('employer_address', data.get('employer_address').value);
    formData.append('grade', data.get('grade').value);
    if(!this.isUserSignedIn){
      formData.append('email', data.get('email').value);
      formData.append('student_name', data.get('student_name').value);
      formData.append('department', data.get('department').value);

      this.postgrad.getPostgradGuest(formData).subscribe({
        next:(res)=>{

          if(res.message=='success'){
            this._Router.navigate(['/login']);

          }
          else{
            this.emailErrorMessage=res.message;

          }
        }
      })

    }
    else{
      data.removeControl("department");
      data.removeControl("email");
      data.removeControl("student_name");
      this.postgrad.sendPostGrad(formData).subscribe({
        next:(response)=>{

          if(response['message']=='success'){
            this._Router.navigate(['/home']);
          }
          else{
            this.emailErrorMessage=response['message'];

          }
        }
      })

    }
  }
  onattachementFileSelected(event){
       this.attachFile = event.target.files[0];
  }
  oncertificateFileSelected(event){
    this.certificateFile = event.target.files[0];
}
open(dialog:string){
  this.dialog=dialog;
  if (this.dialog== '+20') {
  this.phoneNumberValidator=[Validators.required,Validators.pattern("^1[0-2,5]{1}[0-9]{8}$")];
  this.registerForm.controls['phone_number'].setValidators(this.phoneNumberValidator);
  this.registerForm.controls['phone_number'].updateValueAndValidity();
  }


}
AddexternalName(){
  let externalName='';
  externalName= this.externalName.nativeElement.value

  this.externalSupervisorNames?.push(externalName);

}
AddexternalTitle(){
  let externalTitle='';
  externalTitle= this.externalTitle.nativeElement.value
  this.externalSupervisorTitles?.push(externalTitle);

}
AddinternalName(){
  let internalName='';
  internalName= this.internalName.nativeElement.value
  this.internalSupervisorNames?.push(internalName);
}
removeExternalName(index:number){
 this.externalSupervisorNames.splice(index,1);

}
removeInternalName(index:number){
  this.internalSupervisorNames.splice(index,1);
  const myArray = this.registerForm.get('internal_supervisor_names') as FormArray;
 myArray.removeAt(index);
}
removeExternalTitle(index:number){
  this.externalSupervisorTitles.splice(index,1);
  const myArray = this.registerForm.get('external_supervisor_titles') as FormArray;
 myArray.removeAt(index);


 }
concate(dialog:string,phoneNumber:string){
  const number=`${dialog}${phoneNumber}`
  return number
}

onCheckboxChangeInternalName(event: any) {


  const professors = (this.registerForm.controls['internal_supervisor_names'] as FormArray);
  if (event.target.value) {


    professors.push(new FormControl(event.target.value));
  } else {
    const index = professors.controls
    .findIndex(x => x.value === event.target.value);
    professors.removeAt(index);
  }

}
onCheckboxChangeExternalName(event: any) {
  const professors = (this.registerForm.controls['external_supervisor_names'] as FormArray);
  if (event.target.value) {
    professors.push(new FormControl(event.target.value));
  } else {
    const index = professors.controls
    .findIndex(x => x.value === event.target.value);
    professors.removeAt(index);
  }


}
onCheckboxChangeExternalTitles(event: any) {

  const professors = (this.registerForm.controls['external_supervisor_titles'] as FormArray);
  if (event.target.value) {
    professors.push(new FormControl(event.target.value));
  } else {
    const index = professors.controls
    .findIndex(x => x.value === event.target.value);
    professors.removeAt(index);
  }

}
}

