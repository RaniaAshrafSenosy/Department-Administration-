import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ProgramService {

  constructor(private http:HttpClient) { }
  getAllprograms():Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/showAllPrograms`)
  }
  getProgramByID(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/showProgram/${id}`)
  }
  // getDepartmentByUserID():Observable<any>{
  //   return this.http.get(`http://127.0.0.1:8000/api/getDeptInfoUser/${localStorage.getItem("user_id")}`)
  // }
  archiveProgram(id:number):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/archiveProgram/${id}`,{});
  }
  updateProgram(data:any,id:number):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/updateProgram/${id}`,data)
  }
  // getFullNames(){
  //   return this.http.get(`http://127.0.0.1:8000/api/getProfessorsFullNames`)
  // }
}
