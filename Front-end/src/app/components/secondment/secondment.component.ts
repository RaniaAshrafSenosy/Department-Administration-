import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { User } from 'src/app/interfaces/user';
import { secondment } from 'src/app/services/secondment';

@Component({
  selector: 'app-secondment',
  templateUrl: './secondment.component.html',
  styleUrls: ['./secondment.component.css']
})
export class SecondmentComponent implements OnInit {
  registerForm!: FormGroup;
  attachFile: File;
  legationForm:boolean=false;
  vacationForm:boolean=false;
  secondmentForm:boolean=false;
  isCountry:boolean=false;
  type:string;
  types= ['Internal', 'External'];

  constructor(private _Router:Router,private secondment:secondment,private route: ActivatedRoute ) { }
  ngOnInit(): void {
    this.route.data.subscribe(data => {
      const formType = data.formType;
      if (formType === 'secondment') {
        this.legationForm=true;
        this.vacationForm=false;
        this.secondmentForm=false;

      } else if (formType === 'legation') {
        this.secondmentForm=true;
        this.vacationForm=false;
        this.legationForm=false;


      }
      else if (formType === 'vacation') {
        this.vacationForm=true;
        this.legationForm=false;
        this.secondmentForm=false;

      }
    });
    this.registerForm= new FormGroup({
      attachment: new FormControl(null, [Validators.required,Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
      desc:new FormControl(null, [Validators.required]),
      start_date:new FormControl(null, [Validators.required]),
      end_date:new FormControl(null, [Validators.required]),
      type:new FormControl(null, [Validators.required]),
      country:new FormControl(null,),
    })
    console.log(this.type);
  }
  submitForm(data:FormGroup){
    const formData = new FormData();
    if(this.isCountry){
      formData.append('country',data.get('country').value );
    }
    formData.append('attachment',this.attachFile);
    formData.append('desc',data.get('desc').value);
    formData.append('start_date',data.get('start_date').value );
    formData.append('end_date',data.get('end_date').value);
    formData.append('type',data.get('type').value );

    if(this.legationForm){
      this.secondment.sendSecondment(formData).subscribe({
        next:(res)=>{
          console.log("message");
          console.log(res);
          if(res['message']=='success'){
            this._Router.navigate(['/allUserRequests']);

          }
          else{

          }
        }
      })


    }
    else if(this.secondmentForm){
      this.secondment.sendLegation(formData).subscribe({
        next:(res)=>{
          console.log("message");
          console.log(res);
          if(res['message']=='success'){
            this._Router.navigate(['/allUserRequests']);
          }
          else{

          }
        }
      })
    }
    else if(this.vacationForm){
      this.secondment.sendVacation(formData).subscribe({
        next:(res)=>{
          console.log("message");
          console.log(res);
          if(res['message']=='success'){
            this._Router.navigate(['/allUserRequests']);

          }
          else{

          }
        }
      })
    }

  }
  onattachementFileSelected(event){
    this.attachFile = event.target.files[0];
}
onChangeType(type:string){
if(type=="External"){

  this.isCountry=true;
  console.log("external",this.isCountry);
}
else if(type=="Internal"){
  this.isCountry=false;
  console.log("internal",this.isCountry);
}

}
changeValidator(){
  const countryControl=this.registerForm.get('country');
if(this.isCountry){
countryControl.setValidators([Validators.required,Validators.pattern("^[a-zA-Z]*$")]);
console.log("setvalidation",this.isCountry);
}
else{
  countryControl.clearValidators();
  countryControl.updateValueAndValidity();
}
}
}
