import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class SecretaryGuard implements CanActivate {
  constructor( private router: Router){}

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
    console.log(localStorage.getItem("role")=="Secretary");
      if(localStorage.getItem("role")=="Secretary")
      {
        console.log("sec");
        return true
      }
      else{
        console.log("not sec");

        this.router.navigate(['/login']);
      }
    return true;
  }

}
