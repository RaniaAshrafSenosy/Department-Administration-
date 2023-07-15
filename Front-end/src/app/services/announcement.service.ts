import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class AnnouncementService {

  constructor(private http:HttpClient) { }
  postAnnouncment(data:any):Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.post(`http://127.0.0.1:8000/api/postAnnouncement`,data,{ headers })
  }
  getAllAnnouncments():Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/getUserAnnouncements`,{ headers })
  }
  getAllAnnouncmentsForApproval():Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/getAllAnnouncementsForApproval`,{ headers })
  }
  getMyAnnouncments():Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/getMyAnnouncements`,{ headers })
  }
  updateAnnouncement(data:any,id:number):Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.post(`http://127.0.0.1:8000/api/updateAnnouncement/${id}`,data,{ headers })
  }
  approveAnnouncments(id:Number):Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/approveAnnouncement/${id}`,{ headers })
  }
  getAnnouncmentByid(id:Number):Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/getAnnouncementByID/${id}`,{ headers })
  }
  rejectAnnouncments(id:Number):Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/rejectAnnouncement/${id}`,{ headers })
  }

  adminAnnouncement():Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/AdminGetAnnounncements`)
  }
  //{id}
  ArchiveAnnouncement(id:number){
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/archiveAnnouncement/${id}`,{headers})
  }
  AdminArchiveAnnouncement(id:number){
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/AdminArchiveAnnouncement/${id}`,{headers})
  }

}
