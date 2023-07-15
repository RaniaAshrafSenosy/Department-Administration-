import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';



@Injectable({
  providedIn: 'root'
})
export class ProfileService {
  id:string;

  constructor(private http:HttpClient) {

  }
  getProfile():Observable<any>{
    this.id=localStorage.getItem('user_id');
    return this.http.get(`http://127.0.0.1:8000/api/profile/${this.id}`)

  }
  update(data:any):Observable<any>{
    this.id=localStorage.getItem('user_id');
    return this.http.post(`http://127.0.0.1:8000/api/updateProfile/${this.id}`,data)
  }
  ChangePassword(data:any):Observable<any>{
    this.id=localStorage.getItem('user_id');
    return this.http.post(`http://127.0.0.1:8000/api/changePassword/${this.id}`,data)
  }

  getUserProfile(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/profile/${id}`)
  }
  updatImage(data:any){
    this.id=localStorage.getItem('user_id');
    return this.http.post(`http://127.0.0.1:8000/api/updateImage/${this.id}`,data)
  }
  getMyPostgraduatesAverageGrades(data:any){
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`});
    return this.http.post(`http://127.0.0.1:8000/api/getMyPostgraduatesAverageGrades`,data,{headers})
  }
  getMyPostgraduates(){
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`});
    return this.http.get(`http://127.0.0.1:8000/api/showAllMyPostgraduates`,{headers})
  }


}
