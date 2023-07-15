import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { AdminService } from 'src/app/services/admin.service';

@Component({
  selector: 'app-admin-add-department',
  templateUrl: './admin-add-department.component.html',
  styleUrls: ['./admin-add-department.component.css']
})
export class AdminAddDepartmentComponent implements OnInit {
  registerForm!: FormGroup;
  ErrorMessage1:string='';
  ErrorMessage2:string='';
  booklet: File;
  bylaw: File;
  constructor(private admin:AdminService,private _Router:Router ) { }

  ngOnInit(): void {
    this.registerForm= new FormGroup({
      dept_code: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$'),Validators.minLength(2)]),
      dept_name: new FormControl(null,[Validators.required,Validators.pattern('^[a-zA-Z ]*$'),Validators.minLength(5)]),
      desc: new FormControl(null,[Validators.required]),
      bylaw: new FormControl(null, [Validators.required,Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
      booklet: new FormControl(null, [Validators.required,Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
    });
  }
  submitForm(data:FormGroup){
    const formData = new FormData();
    formData.append('bylaw',this.bylaw );
    formData.append('booklet',this.booklet );
    formData.append('dept_code', data.get('dept_code').value);
    formData.append('dept_name', data.get('dept_name').value);
    formData.append('desc', data.get('desc').value);

    this.admin.createDepartment(formData).subscribe({
      next:(res)=>{


        if(res.message=='success'){
          this._Router.navigate(['/alldepartments']);

        }

        else if(res.message=='department name exist'){
          this.ErrorMessage2=res.message;
        }
        else {
          this.ErrorMessage1=res.message;

        }
      }
    })
  }

  onbookletFileSelected(event){
    this.booklet = event.target.files[0];
}
onbylawFileSelected(event){
 this.bylaw = event.target.files[0];
}

}
