import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { programs } from 'src/app/interfaces/programs';
import { ProgramService } from 'src/app/services/program';

@Component({
  selector: 'app-update-prog',
  templateUrl: './update-prog.component.html',
  styleUrls: ['./update-prog.component.css']
})
export class UpdateProgComponent implements OnInit {
  progId:number;
  bylaw:File;
  booklet:File;
  oldbylaw:File;
  oldbooklet:File;
  deptBylaw:string;
  deptBooklet:string;
  disabled:boolean=true;
  registerForm!: FormGroup;
  programInstance:programs

  constructor(private router: Router,private program:ProgramService, private http: HttpClient) {
    this.progId=this.router.getCurrentNavigation().extras.state?.id;
  }

  ngOnInit(): void {
    this.program.getProgramByID(this.progId).subscribe({
      next:(data)=>{
        console.log("id");
         console.log(this.progId);
        this.programInstance=data.department;
        console.log("here");
        this.booklet=data?.bookletURL;
        this.oldbooklet=data?.bookletURL;
        this.bylaw=data.bylawURL;
        this.oldbylaw=data.bylawURL;

        this.deptBylaw=String(data.bylawURL).substring(String(data.bylawURL).lastIndexOf("/") + 1);
        console.log(this.deptBylaw);
        this.deptBooklet=String(data.bookletURL).substring(String(data.bookletURL).lastIndexOf("/") + 1);
        this.registerForm.controls.dept_name.setValue(this.programInstance?.program_name);
        this.registerForm.controls.desc.setValue(this.programInstance?.program_desc);
        this.registerForm.controls.head.setValue(this.programInstance?.program_head);
      }
    })
    this.registerForm= new FormGroup({
      program_name: new FormControl(null,[Validators.pattern('^[a-zA-Z ]*$'),Validators.minLength(5)]),
      program_desc: new FormControl(null,[Validators.pattern("^[a-zA-Z ]*$")]),
      program_head: new FormControl(null,[Validators.pattern("^[a-zA-Z ]*$"),Validators.minLength(5)]),
      bylaw: new FormControl(null, [Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
      booklet: new FormControl(null, [Validators.pattern('^.+(.pdf|.doc|.docx)$')]),
    });
  }
  submitForm(data:FormGroup){
    const formData = new FormData();
    if(this.bylaw!=this.oldbylaw){
      formData.append('bylaw',this.bylaw);
    }
    if(this.booklet!=this.oldbooklet){
      formData.append('booklet',this.booklet);
    }

    formData.append('program_desc',data.get('program_desc').value);
    formData.append('program_head',data.get('program_head').value );

    this.program.updateProgram(formData,this.progId).subscribe({
      next:(res)=>{
        if(res.message=='success'){
          // this._Router.navigate(['/profile']);
          console.log("success");
          console.log(res);
        }
        else{
          // this.emailErrorMessage=res.message;

        }
      }
    })

  }
  onbylawFileSelected(event: any) {
    this.bylaw = event.target.files[0];
    this.deptBylaw = this.bylaw ? this.bylaw.name : '';

  }

   onbookletFileSelected(event){
    this.booklet = event.target.files[0];
    this.deptBooklet = this.booklet ? this.booklet.name : '';
   }



}
