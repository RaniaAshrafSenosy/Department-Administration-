import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AllCoursesService {
  // id:any;
  constructor(private http:HttpClient) { }
  getProfile():Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/showCourses`)

  }
}
