import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class CourseService {

  id:string;

  constructor(private http:HttpClient) {

  }

  getCourse():Observable<any>{
    this.id=localStorage.getItem('');
    return this.http.get(`http://127.0.0.1:8000/api/profile/${this.id}`)
  }
  getCoursebyid(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/showCourse/${id}`)
  }
  getAllCourses():Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/showCourses`)
  }
  archiveCourse(id:number):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/archiveCourse/${id}`,{})
  }
  getMyAssignedCourse():Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/getMyAssignedCourses`,{ headers })
  }
}
