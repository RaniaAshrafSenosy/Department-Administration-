import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AdminService {

  constructor(private _HttpClient:HttpClient) { }
  register(data:any):Observable<any>
  {
    return this._HttpClient.post(`http://127.0.0.1:8000/api/createAdmin`,data)
  }
  adminUpdateUser(id:number,data:any){
    return this._HttpClient.post(`http://127.0.0.1:8000/api/updateUser/${id}`,data);

  }
  promoteUser(id:number){
    return this._HttpClient.post(`http://127.0.0.1:8000/api/promoteUser/${id}`,{});
  }
  createDepartment(data:any):Observable<any>
  {
    return this._HttpClient.post(`http://127.0.0.1:8000/api/department`,data)
  }
  createCourse(data:any):Observable<any>
  {
    return this._HttpClient.post(`http://127.0.0.1:8000/api/course`,data)
  }
  createProgram(data:any):Observable<any>
  {
    return this._HttpClient.post(`http://127.0.0.1:8000/api/createProgram`,data)
  }
  assignCourse(data:any):Observable<any>{
    return this._HttpClient.post(`http://127.0.0.1:8000/api/assignCourse`,data)
  }
  gedDeptCodes():Observable<any>
  {
    return this._HttpClient.get(`http://127.0.0.1:8000/api/getDistinctDeptCodes`)
  }
  getDeptPrograms():Observable<any>
  {
    return this._HttpClient.get(`http://127.0.0.1:8000/api/getDistinctProgramName`)
  }
  gedDeptCourses(dept_code:number):Observable<any>
  {
    return this._HttpClient.get(`http://127.0.0.1:8000/api/getDistinctCourseCodes/${dept_code}`)
  }
  getProfessorsNames(dept_code:number):Observable<any>
  {
    return this._HttpClient.get(`http://127.0.0.1:8000/api/getProfessorsFullNames/${dept_code}`)
  }
  getTasFullName(dept_code:number):Observable<any>
  {
    return this._HttpClient.get(`http://127.0.0.1:8000/api/getTAsFullNames/${dept_code}`)
  }
  updateDepartment(data:any,id:number):Observable<any>{
    return this._HttpClient.post(`http://127.0.0.1:8000/api/updateDepartment/${id}`,data)
  }
  updateCourse(data:any,id:number):Observable<any>{
    return this._HttpClient.post(`http://127.0.0.1:8000/api/updateCourse/${id}`,data)
  }
}
