import { Component, OnInit } from '@angular/core';
import {
  AbstractControl,
  FormControl,
  FormGroup,
  ValidationErrors,
  ValidatorFn,
  Validators,
} from '@angular/forms';
import { Router } from '@angular/router';
import { ProfileService } from 'src/app/services/profile.service';

@Component({
  selector: 'app-change-password',
  templateUrl: './change-password.component.html',
  styleUrls: ['./change-password.component.css'],
})
export class ChangePasswordComponent implements OnInit {
  loginMessageError: string = '';
  loginForm: FormGroup = new FormGroup(
    {
      current_password: new FormControl(null, [
        Validators.required,
        Validators.pattern(
          '^(?=.*[-#$.%&@!+=<>*])?(?=.*[a-zA-Z])?(?=.*d)?.{8,14}$'
        ),
      ]),
      new_password: new FormControl(null, [
        Validators.required,
        Validators.pattern(
          '^(?=.*[-#$.%&@!+=<>*])?(?=.*[a-zA-Z])?(?=.*d)?.{8,14}$'
        ),
      ]),
      confirmed_password: new FormControl(null, [
        Validators.required,
        Validators.pattern(
          '^(?=.*[-#$.%&@!+=<>*])?(?=.*[a-zA-Z])?(?=.*d)?.{8,14}$'
        ),
      ]),
    },
    [MustMatch('new_password', 'confirmed_password')]
  );
  submitForm(data: FormGroup) {
    console.log(data.value);

    this.profile.ChangePassword(data.value).subscribe({
      next: (res) => {
        console.log(res);

        if (res.success === 'Password updated successfully') {
          this._Router.navigate(['/profile']);
        } else {
          this.loginMessageError = res.message;
        }
      },
    });
  }
  constructor(private profile: ProfileService, private _Router: Router) {}

  ngOnInit(): void {}
}
export function MustMatch(
  controlName: string,
  matchingControlName: string
): ValidatorFn {
  return (formGroup: AbstractControl): ValidationErrors | null => {
    const control = formGroup.get(controlName);
    const matchingControl = formGroup.get(matchingControlName);
    if (control?.value !== matchingControl?.value) {
      console.log(2);

      return { mustMatch: true };
    } else {
      console.log(3);

      return null;
    }
  };
}
