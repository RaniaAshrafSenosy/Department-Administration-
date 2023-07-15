import { EventEmitter, Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable,BehaviorSubject } from 'rxjs';
import jwtDecode from 'jwt-decode';
import { Router } from '@angular/router';



@Injectable({
  providedIn: 'root'
})
export class AuthService {
  // baseUrl:string="http://localhost:8000/api/login";
  currentUser=new BehaviorSubject(null);//notlogin
   _isLoggedIn = false;
  isLoggedInChanged = new EventEmitter<boolean>();

  constructor(private _HttpClient:HttpClient, private _Router:Router) {
    if(localStorage.getItem('token') !=null){
      this.saveCurrentUser();
    }
  }
  register(data:any):Observable<any>
  {
    return this._HttpClient.post(`http://127.0.0.1:8000/api/users`,data)
  }
  login(data:any):Observable<any>{

    return this._HttpClient.post(`http://127.0.0.1:8000/api/login`,data)
  }
  log(){

    this._isLoggedIn = true;

    this.isLoggedInChanged.emit(this._isLoggedIn);
  }
  getNumOfNotififcations():Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({'Authorization': `Bearer ${token}`});
    return this._HttpClient.get(`http://127.0.0.1:8000/api/getUnreadNotificationsCountForUser`,{headers })
  }
  get isLoggedIn() {
    return this._isLoggedIn;
  }
  saveCurrentUser(){
  let token=localStorage.getItem('token') ;
  let decode:any=jwtDecode(token);
  // console.log("decode");
  // console.log(decode);
  this.currentUser.next(decode);
  localStorage.setItem("user_imagPath",decode.user_imagPath)
  localStorage.setItem("privileged_user",decode.privileged_user)
  localStorage.setItem('dept',decode.dept_code);
  localStorage.setItem("username",decode.full_name)
  localStorage.setItem('user_id',decode.user_id);
  }
  logout(){
    this._isLoggedIn = false;
    this.isLoggedInChanged.emit(this._isLoggedIn);
    localStorage.clear();

    this._Router.navigate(['login']);
  }
}
