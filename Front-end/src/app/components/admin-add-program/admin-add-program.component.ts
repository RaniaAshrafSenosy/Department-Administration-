import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AdminService } from 'src/app/services/admin.service';

@Component({
  selector: 'app-admin-add-program',
  templateUrl: './admin-add-program.component.html',
  styleUrls: ['./admin-add-program.component.css']
})
export class AdminAddProgramComponent implements OnInit {

  public match:boolean=true;
  codee:string;
  deptCodes!:any[];
  Form!: FormGroup;
  bylaw: File;
  emailErrorMessage:string;
  booklet: File;

  // roles= ['Professor', 'Teaching Assistant ',"Secretary","Head of Department","Dean","Vice Dean"];
  constructor(private admin:AdminService,private _Router:Router) {}
  ngOnInit(): void {
    this.admin.gedDeptCodes().subscribe({
      next:(res)=>{
        if(res.message=='success'){
          this.deptCodes=res.departments_codes

        }
        else{

        }
      }
    })
    this.Form= new FormGroup({
      program_name  : new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z0-9 ]*$'),Validators.minLength(3),Validators.maxLength(30)]),
      program_desc: new FormControl(null,[Validators.required]),
      program_head : new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$')]),
      bylaw  : new FormControl(null,[Validators.required,Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
      booklet : new FormControl(null,[Validators.required,Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
      dept_code : new FormControl(null,[Validators.required,Validators.pattern("^[a-zA-Z]*$")]),
    });
  }
  submitForm(data:FormGroup){
    const formData = new FormData();
    formData.append('bylaw',this.bylaw );
    formData.append('booklet',this.booklet );
    formData.append('program_name', data.get('program_name').value);
    formData.append('program_desc', data.get('program_desc').value);
    formData.append('program_head', data.get('program_head').value);
    formData.append('dept_code', data.get('dept_code').value);
    this.admin.createProgram(formData).subscribe({
      next:(res)=>{
        if(res.message=='success'){
          this._Router.navigate(['/alldepartments']);
        }
        else{
          this.emailErrorMessage=res.message;

        }
      }
    })
  }
onBylawFileSelected(event){
    this.bylaw = event.target.files[0];
}
onBookletFileSelected(event){
  this.booklet = event.target.files[0];
}
}
