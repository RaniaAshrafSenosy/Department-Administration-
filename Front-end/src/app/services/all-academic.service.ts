import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';



@Injectable({
  providedIn: 'root'
})
export class academicService{
  constructor(private http:HttpClient){}
  getAcademics():Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/getDeptAcademic`);
  }
  getAllAcademics():Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/showUsers`);
  }
  searchUser(full_name:any):Observable<any>{
    // console.log(data);

    return this.http.get(`http://127.0.0.1:8000/api/search/${full_name}`);
  }
  archiveUser(id:number){
    return this.http.post(`http://127.0.0.1:8000/api/archiveUser/${id}`,{});

  }
  

}
