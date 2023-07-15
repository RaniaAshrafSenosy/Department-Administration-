import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';



@Injectable({
  providedIn: 'root'
})
export class postgradService {
  id:string;

  constructor(private http:HttpClient) {

  }
  getPostgradGuest(data:any):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/createExternalPostgradApplication`,data)
  }
  getUserInfo(userId:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/profile/${userId}`)
  }
  sendPostGrad(data:any){
    const token = localStorage.getItem('token');

  // Set the headers with the token
  const headers = new HttpHeaders({
    'Authorization': `Bearer ${token}`
  });
  return this.http.post(`http://127.0.0.1:8000/api/createPostgradApplication`,data,{ headers })
  }
  getFullNamesTitles(){
    return this.http.get(`http://127.0.0.1:8000/api/getAllProfessorsFullNamesAndTitles`)
  }
}
