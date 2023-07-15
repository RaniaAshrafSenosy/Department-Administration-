import { HttpClient, HttpHeaders, HttpXsrfTokenExtractor } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';




@Injectable({
  providedIn: 'root'
})
export class Secretary {

  constructor(private http:HttpClient) {}
  getAllVacation():Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/showVacationsDept`,{ headers })
  }
  getAllSecondment():Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/showSecondmentDept`,{ headers })
  }
  getAllLegation():Observable<any>{
    const token = localStorage.getItem('token');
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`
    });
    return this.http.get(`http://127.0.0.1:8000/api/showLegationDept`,{ headers })
  }
  getVactionById(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/getVacationData/${id}`)

  }
  updateVacation(id:number,data:any):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/updateVacationStatus/${id}`,data)
  }
  getLegationById(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/getLegationData/${id}`)

  }
  updateLegation(id:number,data:any):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/updateLegationStatus/${id}`,data)
  }
  getSecondmentById(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/getSecondmentData/${id}`)

  }
  updateSecondment(id:number,data:any):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/updateSecondmentStatus/${id}`,data)
  }
  acceptVaction(id:number):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/acceptVacation/${id}`,{})
  }
  rejectVaction(id:number):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/rejectVacation/${id}`,{})
  }
  acceptSecondment(id:number):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/acceptSecondment/${id}`,{})
  }
  rejectSecondment(id:number):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/rejectSecondment/${id}`,{})
  }

  acceptLegation(id:number):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/acceptLegation/${id}`,{})
  }
  rejectLegation(id:number):Observable<any>{
    return this.http.post(`http://127.0.0.1:8000/api/rejectLegation/${id}`,{})
  }

  viewPdf(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/viewVacationPDF/${id}`, {
      responseType: 'blob' as 'json' // Set the response type to 'blob'
    })
  }
  downloadPdf(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/exportVacationPDF/${id}`, {
      responseType: 'blob' as 'json' // Set the response type to 'blob'
    })
  }
  viewPdfLegation(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/viewLegationPDF/${id}`, {
      responseType: 'blob' as 'json' // Set the response type to 'blob'
    })
  }
  downloadPdfLegation(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/exportLegationPDF/${id}`, {
      responseType: 'blob' as 'json' // Set the response type to 'blob'
    })
  }
  viewPdfSecondment(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/viewSecondmentPDF/${id}`, {
      responseType: 'blob' as 'json' // Set the response type to 'blob'
    })
  }
  downloadPdfSecondment(id:number):Observable<any>{
    return this.http.get(`http://127.0.0.1:8000/api/exportSecondmentPDF/${id}`, {
      responseType: 'blob' as 'json' // Set the response type to 'blob'
    })
  }
}
