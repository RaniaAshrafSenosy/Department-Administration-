import { Component,ElementRef,OnInit, ViewChild} from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Secretary } from 'src/app/services/sec';

@Component({
  selector: 'app-sec-view-all-req',
  templateUrl: './sec-view-all-req.component.html',
  styleUrls: ['./sec-view-all-req.component.css']
})
export class SecViewAllReqComponent implements OnInit {
modaltype:boolean;
showMore1:boolean;
vacations:Request[];
vacationPart:any;
noPendingPage:boolean=false

all:[{vacations:any,attachment:string,full_name:string,value:boolean}];
  constructor(private secretary:Secretary,private _Router:Router,private route: ActivatedRoute) { }
  ngOnInit(): void {
    this.route.data.subscribe(data => {
      const formType = data.formType;
      if (formType === 'viewAllVacationNotPending') {
        this.noPendingPage=true;
        console.log("secondment form");
      }


    })
this.getData();

  }
  getData(){
    this.secretary.getAllVacation().subscribe(
      {
        next:(res)=>{
        console.log(res);
        this.all=res.vacations;
        this.name();
      }
    }
    )
  }
  name() {
    for (let index = 0; index < this.all?.length; index++) {
        this.all[index].value=false;
    }
  }
  show(item:any,index:number){
    for (let loopindex = 0; loopindex < this.all?.length; loopindex++) {
      if( loopindex!=index){
        this.all[loopindex].value=false;
      }
    }
    item.value=!item.value;
    this.showMore1=!this.showMore1;
    this.onclick(item.vacations.id)
  }
  changemodal(condition:boolean){
    this.modaltype=condition;
  }
  onclick(id:number){
    this.secretary.getVactionById(id).subscribe(
      {
        next:(res)=>{
        this.vacationPart=res["Vacation data"];
      }
    }
    )
  }
  acceptRequest(id:number){
    console.log("id",id);
    this.secretary.acceptVaction(id).subscribe({
      next:(res)=>{
        console.log("accept");

        console.log(res);
        if(res.message=="accepted"){
          this.noPendingPage==false;
      //    this._Router.navigate(['/viewAllVacationNotPending']);  //navigate to the Sec panel
        }
      }
    })
    this.getData();
  }
  rejectRequest(id:number){
    console.log("id",id);
    this.secretary.rejectVaction(id).subscribe({
      next:(res)=>{
        console.log("reject");
        console.log(res);
        if(res.message=="rejected"){
          this.noPendingPage==false;
        //  this._Router.navigate(['/viewAllVacationNotPending']); //navigate to the Sec panel
        }

      }
    })
    this.getData();

  }
  checkAllItemsNotPending(): boolean {
    return !this.all?.some(request => request.vacations?.status === 'Pending');
  }
  panel(){
    this._Router.navigate(["/viewAllVacationNotPending"]);// //navigate to the Sec panel
  }
  viewPDF(id:number): void {
    // const vacationId = 123; // Replace with the actual vacation ID
    this.secretary.viewPdf(id).subscribe(
      (response: Blob) => {
        const fileURL = URL.createObjectURL(response);
        window.open(fileURL, '_blank');
      },
      (error: any) => {
        console.error('Failed to fetch PDF', error);
      }
    );
  }

  downloadPdf(id:number,name:string,date:string){
    this.secretary.downloadPdf(id).subscribe(
      (response: Blob) => {
        const downloadURL = window.URL.createObjectURL(response);
        const link = document.createElement('a');
        link.href = downloadURL;
        link.download = 'vacation-'+name+date+'.pdf';
        link.click();
        window.URL.revokeObjectURL(downloadURL);
      },
      (error: any) => {
        console.error('Failed to export PDF', error);
      }
    );
  }
}
