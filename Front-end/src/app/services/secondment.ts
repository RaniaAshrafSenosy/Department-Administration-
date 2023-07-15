import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';




@Injectable({
  providedIn: 'root'
})
export class secondment {

  constructor(private http:HttpClient) {}
  sendSecondment(data:any){
    const token = localStorage.getItem('token');

  // Set the headers with the token
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${token}`
  });
  return this.http.post(`http://127.0.0.1:8000/api/createSecondment`,data,{ headers })
  }
  sendLegation(data:any){
    const token = localStorage.getItem('token');

  // Set the headers with the token
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${token}`
  });
  return this.http.post(`http://127.0.0.1:8000/api/createLegation`,data,{ headers })
  }
  sendVacation(data:any){
    const token = localStorage.getItem('token');

  // Set the headers with the token
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${token}`
  });
  return this.http.post(`http://127.0.0.1:8000/api/createVacation`,data,{ headers })
  }
  getAllUserLegations(){
    const token = localStorage.getItem('token');

  // Set the headers with the token
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${token}`
  });
  return this.http.get(`http://127.0.0.1:8000/api/showMyLegations`,{ headers })
  }
  getAllUserVacations(){
    const token = localStorage.getItem('token');

  // Set the headers with the token
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${token}`
  });
  return this.http.get(`http://127.0.0.1:8000/api/showMyVacations`,{ headers })
  }
  getAllUserSecondments(){
    const token = localStorage.getItem('token');

  // Set the headers with the token
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${token}`
  });
  return this.http.get(`http://127.0.0.1:8000/api/showSecondments`,{ headers })
  }


}
