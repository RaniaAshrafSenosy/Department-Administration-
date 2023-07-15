import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AnnouncmentGuard implements CanActivate {
  constructor(private router: Router){

  }
  canActivate(
    route: ActivatedRouteSnapshot,
    // ['Secretary', 'Dean', 'Vice Dean']
    state: RouterStateSnapshot): Observable<boolean | UrlTree> | Promise<boolean | UrlTree> | boolean | UrlTree {
      if(localStorage.getItem("role")=="Head of Department"||localStorage.getItem("role")=="Dean"||localStorage.getItem("role")=="Vice Dean")
      {
        return true
      }
      else{
        this.router.navigate(['/login']);
      }
    return true;
  }

}
