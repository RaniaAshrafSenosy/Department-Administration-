import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Secretary } from 'src/app/services/sec';

@Component({
  selector: 'app-sec-view-all-secondment',
  templateUrl: './sec-view-all-secondment.component.html',
  styleUrls: ['./sec-view-all-secondment.component.css']
})
export class SecViewAllSecondmentComponent implements OnInit {

  modaltype:boolean;
  showMore1:boolean;
  noPendingPage:boolean=false
  secondmentPart:any;

  all:[{Secondment:any,attachment:string,full_name:string,value:boolean}];
    constructor(private secretary:Secretary,private _Router:Router,private route: ActivatedRoute) { }
    ngOnInit(): void {
      this.route.data.subscribe(data => {
        const formType = data.formType;
        if (formType === 'viewAllSecondmentNotPending') {
          this.noPendingPage=true;

        }


      })
this.getData();
    }
    getData(){
      this.secretary.getAllSecondment().subscribe(
        {
          next:(res)=>{
          this.all=res.secondments;
          console.log(this.all[0].Secondment.desc);
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
      this.onclick(item.Secondment.id)
    }
    changemodal(condition:boolean){
      this.modaltype=condition;
    }
    onclick(id:number){
      console.log(id);

      this.secretary.getSecondmentById(id).subscribe(
        {
          next:(res)=>{
          this.secondmentPart=res["Secondment data"];
          console.log(this?.secondmentPart);
          console.log("ffffff");


        }
      }
      )
    }
    acceptRequest(id:number){
      console.log("id",id);
      this.secretary.acceptSecondment(id).subscribe({
        next:(res)=>{
          console.log("accept");

          console.log(res);
          if(res.message=="accepted"){
this.noPendingPage==false;

          //  this._Router.navigate(['/viewAllSecondmentNotPending']);  //navigate to the Sec panel
          }
        }
      })
      this.getData()
    }
    rejectRequest(id:number){
      console.log("id",id);
      this.secretary.rejectSecondment(id).subscribe({
        next:(res)=>{
          console.log("reject");
          console.log(res);
          if(res.message=="rejected"){
            this.noPendingPage==false;
          //  this._Router.navigate(['/viewAllSecondmentNotPending']); //navigate to the Sec panel
          }

        }
      })
      this.getData()
    }

    checkAllItemsNotPending(): boolean {
      return !this.all?.some(request => request.Secondment?.status === 'Pending');
    }
    panel(){
      this._Router.navigate(["/viewAllSecondmentNotPending"]);// //navigate to the Sec panel
    }
    viewPDF(id:number): void {
      // const vacationId = 123; // Replace with the actual vacation ID
      this.secretary.viewPdfSecondment(id).subscribe(
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
      this.secretary.downloadPdfSecondment(id).subscribe(
        (response: Blob) => {
          const downloadURL = window.URL.createObjectURL(response);
          const link = document.createElement('a');
          link.href = downloadURL;
          link.download = 'secondment-'+name+date+'.pdf';
          link.click();
          window.URL.revokeObjectURL(downloadURL);
        },
        (error: any) => {
          console.error('Failed to export PDF', error);
        }
      );
    }

}
