import { department } from 'src/app/interfaces/department';
import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { AdminService } from 'src/app/services/admin.service';
import { DepartmentService } from 'src/app/services/department.service';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-admin-update-deparment',
  templateUrl: './admin-update-deparment.component.html',
  styleUrls: ['./admin-update-deparment.component.css'],
})
export class AdminUpdateDeparmentComponent implements OnInit {
  deptId: number;
  bylaw: File;
  booklet: File;

  deptBylaw: string;
  deptBooklet: string;
  oldbooklet: File;
  oldbylaw: File;
  codee: string;
  fullNames!: any[];
  registerForm!: FormGroup;

  departmentInstance: department;
  constructor(
    private router: Router,
    private admin: AdminService,
    private department: DepartmentService,
    private http: HttpClient
  ) {
    this.deptId = this.router.getCurrentNavigation().extras.state?.id;
  }
  submitForm(data: FormGroup) {
    const formData = new FormData();
    if (this.bylaw != this.oldbylaw) {
      formData.append('bylaw', this.bylaw);
    }
    if (this.booklet != this.oldbooklet) {
      formData.append('booklet', this.booklet);
    }

    formData.append('dept_name', data.get('dept_name').value);
    formData.append('desc', data.get('desc').value);
    formData.append('head', data.get('head').value);

    this.admin.updateDepartment(formData, this.deptId).subscribe({
      next: (res) => {
        if (res.message == 'success') {
          this.router.navigate(['/alldepartments']);
        } else {
          // this.emailErrorMessage=res.message;
        }
      },
    });
  }
  ngOnInit(): void {
    this.registerForm = new FormGroup({
      dept_name: new FormControl(null, [
        Validators.pattern('^[a-zA-Z ]*$'),
        Validators.minLength(5),
      ]),
      desc: new FormControl(null, [Validators.pattern('^[a-zA-Z ]*$')]),
      head: new FormControl(null, [
        Validators.pattern('^[a-zA-Z ]*$'),
        Validators.minLength(5),
      ]),
      bylaw: new FormControl(null, [
        Validators.pattern('^.+(.pdf|.doc|.docx)$'),
      ]),
      booklet: new FormControl(null, [
        Validators.pattern('^.+(.pdf|.doc|.docx)$'),
      ]),
    });
    this.department.getFullNames().subscribe({
      next: (res) => {
        if (res['message'] == 'success') {
          this.fullNames = res['users_full_names'];
        } else {
        }
      },
    });
    this.department.getDepartmentByID(this.deptId).subscribe({
      next: (data) => {
        this.departmentInstance = data;

        this.booklet = this.departmentInstance?.bookletURL;
        this.bylaw = this.departmentInstance?.bylawURL;
        this.oldbooklet = this.departmentInstance?.bookletURL;

        this.oldbylaw = this.departmentInstance?.bylawURL;

        this.deptBylaw = String(this.departmentInstance.bylawURL).substring(
          String(this.departmentInstance.bylawURL).lastIndexOf('/') + 1
        );

        this.deptBooklet = String(this.departmentInstance.bookletURL).substring(
          String(this.departmentInstance.bookletURL).lastIndexOf('/') + 1
        );
        this.registerForm.controls.dept_name.setValue(
          this.departmentInstance?.dept_name
        );
        this.registerForm.controls.desc.setValue(this.departmentInstance?.desc);
        this.registerForm.controls.head.setValue(this.departmentInstance?.head);
      },
    });
  }
  onbylawFileSelected(event: any) {
    this.bylaw = event.target.files[0];
    this.deptBylaw = this.bylaw ? this.bylaw.name : '';
  }

  onbookletFileSelected(event) {
    this.booklet = event.target.files[0];
    this.deptBooklet = this.booklet ? this.booklet.name : '';
  }
}
