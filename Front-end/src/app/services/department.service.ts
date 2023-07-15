import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class DepartmentService {

  constructor(private http:HttpClient) { }
  getAllDepartments():Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/showDepartments`)
  }
  getDepartmentByID(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/getDeptInfoDept/${id}`)
  }
  getDepartmentByUserID():Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/getDeptInfoUser/${localStorage.getItem("user_id")}`)
  }
  archiveDepartment(id:number):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/archiveDepartment/${id}`,{});
  }
  getFullNames(){
    return this.http.get(`http://127.0.0.1:8000/api/getProfessorsFullNames`)
  }
  getNumOFLeagations(code:String){
    return this.http.get(`http://127.0.0.1:8000/api/getLegationStatistics/${code}`)
  }
  getNumOFSecondments(code:String){
    return this.http.get(`http://127.0.0.1:8000/api/getSecondmentStatistics/${code}`)
  }
}
