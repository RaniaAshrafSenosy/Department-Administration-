import { ErrorHandler, EventEmitter, Injectable, Output } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { map, Observable } from 'rxjs';
import { Router } from '@angular/router';
@Injectable({
  providedIn: 'root'
})
export class LoginService {
  logging:boolean=false;
  constructor(private _HttpClient:HttpClient,private _Router:Router) { }

  userLogout() {
    this.logging=false;
    return localStorage.removeItem("token");
  }
  checkLoggin(){
    return this.logging;
  }
  isLoggedIn() {
    return !!localStorage.getItem("token");
  }

  getToken() {
    return localStorage.getItem("token");
  }
  getNotifications(){
    const token = localStorage.getItem('token');
    // Set the headers with the token
    const headers = new HttpHeaders({'Authorization': `Bearer ${token}`});
  return this._HttpClient.get(`http://127.0.0.1:8000/api/getMyNotifications`,{ headers })
  }
}
